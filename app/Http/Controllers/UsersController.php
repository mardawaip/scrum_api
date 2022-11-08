<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use DB;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchText = $request->searchText ? $request->searchText : '';

        $request->validate([
            'limit' => 'integer']);
            
        $limit = $request->limit && $request->limit<=100 ? $request->limit : 25;

        return User::with(['info'])->where('first_name', 'like', "%$searchText%")->orderBy('created_at', 'DESC')->paginate($limit);
        // return $return;
    }

    public function countries(Request $request)
    {
        return [];
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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                // 'confirmed',
                Rules\Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
                    // ->uncompromised(),
            ]
        ],
        [
            'first_name.required' => 'nama depan wajib diisi',
            'first_name.required' => 'nama akhir wajib diisi',
            'email.required' => 'email wajib diisi',
            'password.*' => 'Kata sandi harus minimal 8 karakter. Setidaknya satu huruf besar dan satu huruf kecil, mengandung setidaknya satu simbol, harus berisi setidaknya satu nomor'
        ]
        );

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $config = theme()->getOption('page');

        return User::with(['info'])->find($id);
    }

    public function getPer($id)
    {
        return User::with(['info'])->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config = theme()->getOption('page', 'edit');

        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userinfo = DB::table('user_infos')->where('user_id', $id)->delete();
        $user = DB::table('users')->where('id', $id)->delete();

        if($user){
            return ['msg' => 'success'];
        }else{
            return ['msg' => 'error'];
        }
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

        $upload = Storage::putFileAs('users', $request->file('image'), $name);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg, 'link' => $name]);
    }
}
