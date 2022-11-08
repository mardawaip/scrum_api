<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Galeri;
use App\Models\Album;
use DB;

class GaleriController extends Controller
{
    public function getAlbum0(Request $request)
    {
        $parent = $request->parent ? $request->parent : 0;

        $album0 = DB::table('album')
            ->select(
                "album.*",
                "galeri.image",
                "galeri.image",
                "galeri.upload_date",
                "galeri.description",
                DB::raw("(SELECT count(1) FROM album alb WHERE alb.parent = album.album_id) AS count ")
            )
            ->leftJoin("galeri", "galeri.galeri_id", "=", DB::raw("(SELECT g.galeri_id FROM galeri g WHERE g.album_id = album.album_id ORDER BY g.upload_date DESC LIMIT 1)"))
            ->where('album.parent', $parent)
            ->whereNull('album.deleted_at')
            ->get();

        $perAlbum = DB::table('album')
            ->select('album.*', 'h.album_title AS parent_name')
            ->leftJoin('album AS h', 'album.parent', '=', 'h.album_id')
            ->where('album.album_id', $parent)
            ->whereNull('album.deleted_at')
            ->first();

        $images = Galeri::where('album_id', $parent)->get();

        return [
            'perAlbum'  => $perAlbum,
            'rows'      => $album0,
            'images'    => $images
        ];
    }

    public function getGaleri(Request $request)
    {
        $album_id = $request->album_id ? $request->album_id : 2;

        $galeri = DB::table('galeri')
            ->where('album_id', $album_id)
            ->get();

        $perAlbum = DB::table('album')
            ->select('album.*', 'h.album_title AS parent_name')
            ->leftJoin('album AS h', 'album.parent', '=', 'h.album_id')
            ->where('album.album_id', $album_id)
            ->first();

        return [
            'perAlbum'  => $perAlbum,
            'rows'      => $galeri,
            'count'     => count($galeri)
        ];
    }

    public function get(Request $request)
    {
        $album_id = $request->album_id ? $request->album_id : 0;

        $galeri = Galeri::select()->where('album_id', $album_id)->get();
        $album = Album::select(
            "album.*",
            DB::raw("(SELECT COUNT(1) FROM album AS a WHERE a.parent = album.album_id) AS count_folder"),
            DB::raw("(SELECT COUNT(1) FROM galeri WHERE galeri.album_id = album.album_id) AS count_file")
        )->where('parent', $album_id)->get();

        return [
            'files' => $galeri,
            'folders' => $album
        ];
    }

    public function store_album(Request $request)
    {
        $request->validate([
            'album_title' => 'required',
            'parent' => 'required',
        ]);

        return Album::create($request->all());
    }

    public function delete_album($id)
    {
        $album = Album::find($id)->delete();
        return $album;
    }

    public function update_album(Request $request, $id)
    {
        $album = Album::find($id);
        $album->update($request->all());
        return $album;
    }

    public function store_galeri(Request $request)
    {
        $request->validate([
            'galeri_title' => 'required',
            'tipe' => 'required',
            'image' => 'required'
        ]);

        return Galeri::create($request->all());
    }

    public function delete_galeri($id)
    {
        $galeri = Galeri::find($id)->delete();
        return $galeri;
    }

    public function update_galeri(Request $request, $id)
    {
        $galeri = Galeri::find($id);
        $galeri->update($request->all());
        return $galeri;
    }

    public function upload(Request $request)
    {
        $file = $request->image;
        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = Str::uuid().".".$ext;

        $format = ['jpeg', 'jpg', 'png', 'svg', 'webp'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }

        $upload = Storage::putFileAs('galeri', $request->file('image'), $name);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg, 'link' => $name]);
    }
}
