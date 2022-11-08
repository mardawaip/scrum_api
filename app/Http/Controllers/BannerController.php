<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Banner;
use DB;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;        

        $request->validate([
            'limit' => 'integer',
            'offset' => 'integer'
        ]);
            
        // $limit = $request->limit && $request->limit<=100 ? $request->limit : 25;

        return Banner::orderBy('banner_id', 'DESC')->paginate($limit);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'banner_file' => 'required',
            'link' => 'required',
            'position' => 'required',
            'status' => 'required',
            'jenis_benner' => 'required',
        ]);

        return Banner::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner, Request $request)
    {
        $banner = Banner::find($request->banner_id);

        return $banner;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner, $id)
    {
        $banner = Banner::find($id);
        $banner->update($request->all());
        return $banner;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner, $id)
    {
        $banner = Banner::find($id)->delete();
        return $banner;
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

        $upload = Storage::putFileAs('banner', $request->file('image'), $name);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg, 'link' => $name]);
    }

    public function getBanner()
    {
        $banner = DB::table('banner')
            ->whereNull('deleted_at')
            ->where('jenis_benner', 'portal')
            ->where('status', 1)
            ->get();

        return [
            'rows' => $banner
        ];
    }
}
