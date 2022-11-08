<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KontenController extends Controller
{
    public function halaman(Request $request)
    {
        $where = [
            'category.category_id' => $request->category_id,
            'category.parent' => $request->parent
        ];
        $db = DB::table('category')
            ->select('category.*', 'pt.category_name AS parent_name')
            ->leftJoin('category as pt', 'category.parent', '=', 'pt.category_id')
            ->where($where)
            ->first();

        return $db;
    }

    public function get_(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? $request->offset : 0;
        $searchText = $request->searchText ? $request->searchText : '';

        $sql = DB::table('post')
            ->select("post.*")
            ->leftJoin('category', 'post.category_id', '=', 'category.category_id')
            ->where('post.category_id', $request->category_id)
            ->where('post.title', 'like', "%$searchText%")
            ->orderBy('post.date_publish', 'DESC');

        $count_all = $sql->count();
        $rows = $sql->limit($limit)->offset($offset)->get();

        $i = 0;
        foreach ($rows as $opt) {
            $rows[$i]->description = substr(strip_tags($opt->body), 0, 400);
            $i++;
        }

        return [
            'rows' => $rows,
            'count'=> count($rows),
            'count_all' => $count_all
        ];
    }

    public function get_per(Request $request)
    {
        $db = DB::table('post')->where('post_id', $request->post_id)->first();

        return ['rows' => $db];
    }

    public function get(Request $request)
    {
        $db = DB::table('post')
            ->select('post_id AS id', 'post.*', 'category.category_name')
            ->leftJoin('category', 'post.category_id', '=', 'category.category_id')
            ->where('post.category_id', $request->category_id)
            ->orderBy('post_date', 'DESC')
            ->get();

        return [
            'rows' => $db,
            'count'=> count($db),
            'count_all' => count($db)
        ];
    }

    public function per_get(Request $request)
    {
        $db = DB::table('post')
            ->where('post_id', $request->post_id)
            ->first();

        return $db;
    }

    public function get_izin_lingkungan(Request $request)
    {
        $category_id = 33;

        $db = DB::table('category')
            ->select("category.*", "post.title", "post.image", "post.date_publish")
            ->leftJoin("post", "category.category_id", "=", "post.category_id")
            ->where('category.parent', $category_id)
            ->get();

        return [
            "rows" => $db,
            "count" => count($db)
        ];
    }

    public function add(Request $request)
    {
        $id = DB::table('post')->orderby('post_id', 'DESC')->first()->post_id;

        $data = [
            'post_id'       => ($id + 1),
            'user_id'       => 13,
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'body'          => $request->body,
            'post_date'     => date("Y-m-d H:i:s"),
            'date_publish'  => date_format(date_create($request->date_publish), date("y-m-d")),
            'status'        => $request->status,
            'image'         => $request->image,
            'view'          => 0,            
        ];

        $db = DB::table('post')->insert($data);
        $msg = $db ? 'success' : 'error';


        return [
            'id' => $data['post_id'],
            'msg' => $msg
        ];
    }

    public function update(Request $request)
    {

        $data = [
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'body'          => $request->body,
            'date_publish'  => date_format(date_create($request->date_publish), date("y-m-d")),
            'status'        => $request->status,
            'image'         => $request->image,
        ];

        $db = DB::table('post')->where('post_id', $request->post_id)->update($data);
        $msg = $db ? 'success' : 'error';


        return [
            'id' => $request->post_id,
            'msg' => $msg
        ];
    }

    public function delete(Request $request)
    {
        $db = DB::table('post')->where('post_id', $request->post_id)->delete();
        $msg = $db ? 'success' : 'error';

        return [ 'msg' => $msg ];
    }

    public function getSelectKonten(Request $request)
    {
        $jenis_kategori = DB::table('category')
            ->select('category_id AS value', 'category_name AS label')
            ->get();
        $status_berita = [['value' => null, 'label' => 'Tidak'], ['value' => 1, 'label' => 'Publis']];

        return [
            'jenis_kategori' => $jenis_kategori,
            'status_berita' => $status_berita
        ];
    }

    public function upload_images(Request $request)
    {
        $file = $request->image;
        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        //$name = $file->getClientOriginalName();
        $name = Str::uuid().".".$ext;

        $format = ['jpeg', 'jpg', 'png', 'svg', 'webp'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }

        $upload = Storage::putFileAs('beritaThumbnail', $request->file('image'), $name);
        // $path = Storage::put('manajemen_konten/files/images', $file, [
        //     'visibility' => 'public',
        //     'mimetype' => $file->getClientMimeType(),
        // ]);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg, 'link' => $name]);
    }

    public function upload(Request $request)
    {
        $data = $request->all();
        $file = $data['image'];

        $ext  = $file->getClientOriginalExtension(); //extensi
        $size = $file->getSize(); //size
        // $name = $file->getClientOriginalName(); //namefile
        $name = Str::uuid().".".$ext;

        $format = ['jpeg', 'jpg', 'png', 'svg', 'gif'];
        if(!in_array($ext, $format)){ return response(['data' => [], 'success' => false, 'status' => 200]); }

        //Move Uploaded File
        // $destinationPath = 'storage/beritaConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        $upload = Storage::putFileAs('Content', $request->file('image'), $name);
        // $path = Storage::put('manajemen_konten/files/images', $file, [
        //     'visibility' => 'public',
        //     'mimetype' => $file->getClientMimeType(),
        // ]);

        // $link = 'https://is3.cloudhost.id/storagedirectus1/'.$path;
        $host = "http://localhost:8000";
        $link = $host."/storage/Content/".$name;

        $proses['data'] = array(
            "id"            => "GJFuCG5",
            "title"         => null,
            "description"   => null,
            "datetime"      => time(),
            "type"          => "image/".$ext,
            "animated"      => false,
            "width"         => 1600,
            "height"        => 900,
            "size"          => $size,
            "views"         => 1,
            "bandwidth"     => 0,
            "vote"          => null,
            "favorite"      => false,
            "nsfw"          => null,
            "section"       => null,
            "account_url"   => null,
            "account_id"    => 0,
            "is_ad"         => false,
            "in_most_viral" => false,
            "has_sound"     => false,
            "tags"          => [],
            "ad_type"       => 0,
            "ad_url"        => "",
            "in_gallery"    => false,
            "deletehash"    => "67eK80Moa77ONlW",
            "name"          => "",
            "link"          => $link,
        );

        $proses['success']  = true;
        $proses['status']   = 200;

        return response()->json($proses);
    }

    public function getLoadData(Request $request, $table)
    {
        $table = DB::table($table)->where($request->all())->get();

        return [
            'rows' => $table,
            'count' => count($table)
        ];
    }
}
