<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Menu;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MipCaptchaController;
use DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Handle an incoming api authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiStore(LoginRequest $request)
    {   

        // $mipCaptcha = MipCaptchaController::checkCaptcha($request);
        // if(!$mipCaptcha['success']){
        //     return array(
        //         'error' => array('captcha'=>array(
        //             'Mohon isi captcha dengan benar'
        //         )),
        //         'login' => 0, //error
        //     );
        // }
        
        $request->authenticate();
        
        $token = Auth::attempt($request->only('email', 'password')); 

        if (!$token) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        $user = User::where('email', $request->email)->first();
        // $request->visitor()->visit();
        // return $user->visitLogs()->count();
        // $response = array('user'=>$user,'token'=>$token);
        $menu = $this->_menu($user->roles_id);
        $menu2=array();
            $params = [
                'peran_nama' => 'admin',
                'nama' => $user->first_name,
                'username' => $user->email,
                'rows' => $user,
                'menu' => $menu,
                'menu2' => $menu2,
                'token' => $token,
                'avatar' =>$user->getAvatarUrlAttribute()
            ];

        return $this->_formatFuse($params);
    }

    /**
     * Verifies user token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiVerifyToken(Request $request)
    {
        return $request->all();
        $request->validate([
            'api_token' => 'required'
        ]);

        $user = User::where('api_token', $request->api_token)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'token' => ['Invalid token']
            ]);
        }
        return response($user);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function _formatFuse($params)
    {
        // ['peran_nama', 'nama', 'username', 'menu', 'token'], 

        $data['user'] = array(
            'from' => 'backend',
            'role' => $params['peran_nama'],
            'rows' => $params['peran_nama'] == "Administrator" ? @$params['rows'] : $params['rows'],
            'menu' => $params['menu2'],
            'data' => array(
                'displayName' => $params['username'],
                'photoURL' => $params['avatar'],
                'username' => $params['username'],
                'settings' => array(
                    'menu' => $params['menu'],
                    'layout' => array(
                        'style' => 'layout1',
                        'config'=> array(
                            'mode'  => 'fullwidth',
                            'scroll' => 'content',
                            'navbar' => array(
                                'display' => true
                            ),
                            'toolbar' => array(
                                'display' => true,
                                'position' => 'below'
                            ),
                            'footer' => array(
                                'display' => false,
                                'style'  => 'fixed'
                            )
                        )
                    ),
                    'customScrollbars' => false,
                    // 'theme'  => array(
                    //     'main' => 'default',
                    //     'navbar' => 'mainThemeDark',
                    //     'toolbar' => 'mainThemeLight',
                    //     'footer' => 'mainThemeDark'
                    // )
                ),
                'shortcuts' => null
            )
        );

        $data['token'] = str_replace([' ', 'Bearer'], '', $params['token']);
        $data['access_token'] = str_replace([' ', 'Bearer'], '', $params['token']);

        return $data;
    }

    public function _menu($role_id)
    {
        $cek = Cache::has(":_menu:".$role_id);
        $cek = false; // disable cache
        if($cek){
            return Cache::get(":_menu:".$role_id);
        }else{

            // $menu_all = DB::table('view_menu')
            $menu_all = RoleMenu::select(
                    "menu.kode AS id",
                    "menu.title",
                    "menu.type",
                    "menu.icon",
                    "menu.url",
                    "menu.menu_id",
                    "menu.tingkat_menu",
                    "menu.induk_menu_id",
                    "menu.status",
                )
                ->leftJoin('menu AS menu', 'role_menu.menu_id', '=', 'menu.menu_id')
                ->where('role_menu.role_id', $role_id)
                // ->where('role_menu.soft_delete', 0)
                ->where('menu.soft_delete', 0)
                ->orderBy("menu.nomor_urut", "ASC")
                ->get();

            $menu = $this->getArrayFiltered('tingkat_menu', '1', $menu_all);
            // $submenu_ext = $this->getMenuExt();

            $i = 0;
            foreach ($menu as $key) {
                if($key->type == 'collapse'){
                    unset($menu[$i]->url);
                    if($key->id == 'manajemen-konten'){
                        $submenu = $submenu_ext;
                    }else{
                        $submenu = $this->getArrayFiltered_2(['tingkat_menu' => '2', 'induk_menu_id' => $key->menu_id], $menu_all);
                    }

                    $a = 0;
                    foreach ($submenu as $sub) {
                        if($sub->type == 'collapse'){
                            unset($submenu[$a]->url);
                            // if($key->id == 'manajemen-konten'){
                            //     $subsubmenu = $this->getMenuExt($sub->id);
                            //     $submenu[$a]->children = $subsubmenu;
                            // }else{
                                $subsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '3', 'induk_menu_id' => $sub->menu_id], $menu_all);

                                $lv4=0;
                                foreach ($subsubmenu as $subsub) {
                                    if($subsub->type === 'collapse'){
                                        unset($subsubmenu[$lv4]->url);
                                        $subsubsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '4', 'induk_menu_id' => $subsub->menu_id], $menu_all);
                                        $subsubmenu[$lv4]->children = $subsubsubmenu;
                                    }

                                    if(in_array($subsub->status, ['new', 'dev'])){ $subsubmenu[$lv4]->badge = $this->setBadge($subsub->status); }
                                    $lv4++;
                                }

                                $submenu[$a]->children = $subsubmenu;
                            // }
                        }

                        if(in_array($sub->status, ['new', 'dev'])){ $submenu[$a]->badge = $this->setBadge($sub->status); }
                        $a++;
                    }

                    $menu[$i]->children = $submenu;
                }

                if(in_array($key->status, ['new', 'dev'])){ $menu[$i]->badge = $this->setBadge($key->status); }
                $i++;
            }

            Cache::put(":_menu:".$role_id, $menu, 3600);
            return Cache::get(":_menu:".$role_id);
        }
    }

    public function setBadge($status)
    {
        return ['title' => $status, 'bg' => $status == 'new' ? "#006565" : "#ffbe2c", 'fg' => '#FFFFFF'];
    }

    public function getMenuExt($parent='')
    {
        $sql = "(CASE WHEN (SELECT count(1) from category AS category_d WHERE category_d.parent = category.category_id) >= 1 THEN 'collapse' ELSE 'item' END) AS type";
        $parent_ = 0;

        if($parent != ''){
            $sql = "'item' AS type";
            $parent_ = $parent;
        }

        $db = DB::table('category')
            ->select(
                DB::raw("category_id AS id"),
                DB::raw("category_name AS title"),
                DB::raw($sql),
                DB::raw("'toc' AS icon"),
                DB::raw(" CONCAT('/manajemen-konten/', parent, '/', category_id) AS url"),
                DB::raw("category_id AS menu_id"),
                DB::raw("'2' AS tingkat_menu"),
                DB::raw("'' AS induk_menu_id"),
                DB::raw("'' AS status")
            )
            ->where('parent', $parent_)
            ->orderBy('sort', 'ASC');

            $db_ = $db->get();

        return $db_;
    }

    public function _peran($peran_id, $pengguna_id)
    {
        $cek = Cache::has(":_peran:".$peran_id);
        if($cek){
            return Cache::get(":_peran:".$peran_id);
        }

        $peran = DB::table(DB::raw('ref.peran with(nolock)'))->where(['peran_id' => $peran_id])->select("nama")->get();
        $peran = count($peran) == 0 ? 'Unknown' : $peran[0]->nama;

        Cache::put(":_peran:".$peran_id, $peran, 3600);
        return Cache::get(":_peran:".$peran_id);
    }

    public function getArrayFiltered_2($arrayFilter, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (($value->tingkat_menu == $arrayFilter['tingkat_menu']) && ($value->induk_menu_id == $arrayFilter['induk_menu_id'])) {
                $filtered_array[] = $value;
            }
        }

        return $filtered_array;
    }

    public function getArrayFiltered($aFilterKey, $aFilterValue, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value[$aFilterKey])) {
                if ($value[$aFilterKey] == $aFilterValue) {
                    $filtered_array[] = $value;
                }
            }
        }

        return $filtered_array;
    }

        /**
     * Handle an incoming api authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function accessToken(Request $request)
    {
        $user = Auth::user();
        $token = auth()->refresh(); 
        $response = array('user'=>$user,'token'=>$token);
        $menu = $this->_menu($user->roles_id);
        $menu2=array();
            $params = [
                'peran_nama' => 'admin',
                'nama' => $user->first_name,
                'username' => $user->email,
                'rows' => $user,
                'menu' => $menu,
                'menu2' => $menu2,
                'token' => $token,
                'avatar' =>$user->getAvatarUrlAttribute()
            ];

        return $this->_formatFuse($params);
    }
}
