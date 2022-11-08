<?php

namespace App\Http\Controllers;
use App\Models\RolesPermission;
use Illuminate\Http\Request;

class RolesPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RolesPermission::all();
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
        $request->validate([
            'permission_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        $rolesPermission = RolesPermission::firstOrCreate(
            $request->all()
        );

        return $rolesPermission;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return RolesPermission::find($id);
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
        $rolesPermission = RolesPermission::find($id);
        $rolesPermission->update($request->all());
        return $rolesPermission;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($permissionId, $roleId)
    {
        $rolesPermission = RolesPermission::where('permission_id', $permissionId)->where('role_id', $roleId)->delete();
        // return $rolesPermission;
        if($rolesPermission){
            $return = array('success'=>'true', 'message'=>'Data Berhasil Dihapus');
        }else{
            $return = array('success'=>'false', 'message'=>'Data Tidak Ditemukan');
        }

        return $return;
    }
}
