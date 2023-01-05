<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userAuth = Auth::user();

        $user = User::where('id', $userAuth->id)->first();
        $userInfo = UserInfo::where('user_id', $userAuth->id)->first();

        return [
            'user' => $user ? $user : [],
            'user_info' => $userInfo ? $userInfo : []
        ];
    }

    public function get_log(Request $request)
    {
        $userAuth = Auth::user();
        $log = DB::table('activity_log')->where('subject_id',$userAuth->id)->orderby('created_at', 'DESC')->limit(10)->get();

        return $log;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
