<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Gate;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserInfo::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userinfo = UserInfo::find($request->id);
        
        // if (! Gate::allows('isUser', $userinfo)) {
        //     return array('success'=>false,'message'=>'Anda tidak memiliki akses');
        // }

        $request->validate([
            // 'user_id' => 'required',
            // 'avatar' => '',
            'company' => '',
            'phone' => '',
            'website' => '',
            'country' => '',
            'language' => '',
            'timezone' => '',
            'currency' => '',
            'communication' => '',
            'marketing' => ''
        ]);

        return UserInfo::create($request->all(), $userinfo);
        // return $userinfo;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userinfo = UserInfo::find($id);

        if (! Gate::allows('isUser', $userinfo)) {
            return array('success'=>false,'message'=>'Anda tidak memiliki akses');
        }

        return $userinfo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $userinfo = UserInfo::find($id);

        

        if (! Gate::allows('isUser', $userinfo)) {
            return array('success'=>false,'message'=>'Anda tidak memiliki akses');
        }
        // $this->authorize('isUser');


        $userinfo->update($request->all());
        return $userinfo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userinfo = UserInfo::find($id);

        if (! Gate::allows('isUser', $userinfo)) {
            return array('success'=>false,'message'=>'Anda tidak memiliki akses');
        }

        return UserInfo::destroy($id);
    }
}
