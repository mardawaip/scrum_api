<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use JWTFactory;
use JWTAuth;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Models\EncryptPassword;

class LoginController extends Controller
{
    public function password_cek(Request $request)
    {
        return $this->password_string($request->password);
    }

    public function password_string($password)
    {
        $encrypt = new EncryptPassword();
        $encrypt->setString($password);
        return $password_encrypt = $encrypt->doEncrypt();
    }

    public function Login(Request $request, $password='', $email='')
    {
        $email = $email ? $email : $request->email;
        $password = $password ? $password : $request->password;

        $user = Pengguna::where('email', $email)
            ->where('soft_delete', '0')
            ->whereNotIn('soft_delete',[1,2])
            ->first();

        $passCode = md5($password);
        $passCode2 = $this->password_string($password);

        if($user){
            if(($passCode == $user->{'password'}) || ($passCode2 == $user->{'password'})){
                if($user){
                    $token = json_decode(json_encode($this->_authenticate($user, $request)))->original->token;
                    $menu = $this->_menu($user->peran_id);
                    $menu2 = $this->_menu2($user->peran_id);
                    $peran = $this->_peran($user->peran_id, $user->pengguna_id);

                    if($peran == 'Unknown'){
                        return response()->json(['error' => 'pengguna_tidak_ditemukan peran'], 401);
                    }

                    $params = [
                        'peran_nama' => $peran,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'rows' => $user,
                        'menu' => $menu,
                        'menu2' => $menu2,
                        'token' => $token,
                    ];

                    return $this->_formatFuse($params);
                }else{
                    return response()->json(['error' => 'password_salah'], 401);
                }

            }else{
                return response()->json(['error' => 'pengguna_tidak_ditemukan pass'], 401);
            }
        }else{
            return response()->json(['error' => 'pengguna_tidak_ditemukan user'], 401);
        }
    }

    public function CekToken(Request $request)
    {
        // $token = $request->header('Authorization');
        $user  = JWTAuth::parseToken()->authenticate();

        if($user){
            $token = json_decode(json_encode($this->_authenticate($user, $request)))->original->token;
            $menu = $this->_menu($user->peran_id);
            $menu2 = $this->_menu2($user->peran_id);
            $peran = $this->_peran($user->peran_id, $user->pengguna_id);

            if($peran == 'Unknown'){
                return response()->json(['error' => 'pengguna_tidak_ditemukan'], 401);
            }

            $params = [
                'peran_nama' => $peran,
                'nama' => $user->nama,
                'email' => $user->email,
                'rows' => $user,
                'menu' => $menu,
                'menu2' => $menu2,
                'token' => $token,
            ];

            return $data = $this->_formatFuse($params);

        }else{
            return response()->json(['error' => 'password_salah'], 401);
        }
    }

    public function _peran($peran_id, $pengguna_id)
    {
        $cek = Cache::has(":_peran:".$peran_id);
        if($cek){
            return Cache::get(":_peran:".$peran_id);die;
        }

        $peran = DB::table(DB::raw('ref.peran with(nolock)'))->where(['peran_id' => $peran_id])->select("nama")->get();
        $peran = count($peran) == 0 ? 'Unknown' : $peran[0]->nama;

        Cache::put(":_peran:".$peran_id, $peran, 3600);
        return Cache::get(":_peran:".$peran_id);
    }

    public function _authenticate($user, $request) { 
        try { 
            if (! $token = JWTAuth::fromUser($user)) { 
                return response()->json(['error' => 'invalid_credentials'], 401);
            } 
        } catch (JWTException $e) { 
            return response()->json(['error' => 'could_not_create_token'], 500); 
        } 
        return response()->json(compact('token')); 

    }

    public function getArrayFiltered($aFilterKey, $aFilterValue, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value->$aFilterKey)) {
                if ($value->$aFilterKey == $aFilterValue) {
                    $filtered_array[] = $value;
                }
            }
        }

        return $filtered_array;
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

    public function _menu2($peranId)
    {
        $cek = Cache::has("Menu2::".$peranId);

        if($cek){
            return Cache::get("Menu2::".$peranId);
        }

        $menu2 = DB::connection('sqlsrv')
            ->table(DB::raw('auth.menu_peran AS menu_peran'))
            ->select('menu.url')
            ->lock("WITH(NOLOCK)")
            ->leftJoin(DB::raw('auth.menu AS menu'), 'menu_peran.menu_id', '=', 'menu.menu_id')
            ->where('menu_peran.peran_id', $peranId)
            ->where('menu_peran.soft_delete', 0)
            ->where('menu.soft_delete', 0)
            ->where('menu.type', 'item')
            ->orderBy('menu.url', 'ASC')
            ->get();

        $i = 0;
        foreach ($menu2 as $key) {
            $menu2[$i] = str_replace('/', '', $key->url);
            $i++;
        }

        $menu2[$i] = "sekolah";

        Cache::put("Menu2::".$peranId, $menu2, 3600);
        return Cache::get("Menu2::".$peranId);
    }

    public function _menu($peran_id)
    {
        $cek = Cache::has(":_menu:".$peran_id);
        if($cek){
            return Cache::get(":_menu:".$peran_id);
        }else{

            // $menu_all = DB::table('view_menu')
            $menu_all = DB::connection('sqlsrv')
                ->table('auth.menu_peran AS menu_peran')
                ->select(
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
                ->leftJoin('auth.menu AS menu', 'menu_peran.menu_id', '=', 'menu.menu_id')
                ->where('menu_peran.peran_id', $peran_id)
                ->where('menu_peran.soft_delete', 0)
                ->where('menu.soft_delete', 0)
                ->orderBy("menu.nomor_urut", "ASC")
                ->get()
                ->toArray();

            $menu = $this->getArrayFiltered('tingkat_menu', '1', $menu_all);

            $i = 0;
            foreach ($menu as $key) {
                if($key->type == 'collapse'){
                    $submenu = $this->getArrayFiltered_2(['tingkat_menu' => '2', 'induk_menu_id' => $key->menu_id], $menu_all);

                    $a = 0;
                    foreach ($submenu as $sub) {
                        if($sub->type == 'collapse'){
                            $subsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '3', 'induk_menu_id' => $sub->menu_id], $menu_all);
                            $submenu[$a]->children = $subsubmenu;
                        }

                        if(in_array($sub->status, ['new', 'dev'])){ $submenu[$a]->badge = $this->setBadge($sub->status); }
                        // unset($menu[$a]->menu_id);
                        $a++;
                    }

                    $menu[$i]->children = $submenu;;
                }

                if(in_array($key->status, ['new', 'dev'])){ $menu[$i]->badge = $this->setBadge($key->status); }
                // unset($menu[$i]->menu_id);
                $i++;
            }

            // return $menu;
            Cache::put(":_menu:".$peran_id, $menu, 3600);
            return Cache::get(":_menu:".$peran_id);
        }
    }

    public function setBadge($status)
    {
        return ['title' => $status, 'bg' => $status == 'new' ? "#006565" : "#ffbe2c", 'fg' => '#FFFFFF'];
    }

    public function _menu_portal()
    {
        $peran_id = "99";
        $cek = Cache::has(":_menu:".$peran_id);
        if($cek){
            return Cache::get(":_menu:".$peran_id);
        }else{
            $menu_all = DB::connection('sqlsrv')
                ->table('auth.menu')
                ->select(
                    "title AS name",
                    "type",
                    "icon",
                    "url AS route",
                    "menu_id",
                    "tingkat_menu",
                    "induk_menu_id"                    
                )
                ->where('type_menu', 'portal')
                ->where('soft_delete', 0)
                ->orderBy("nomor_urut", "ASC")
                ->get()
                ->toArray();

            $menu = $this->getArrayFiltered('tingkat_menu', '1', $menu_all);

            $i = 0;
            foreach ($menu as $key) {
                if($key->type == 'collapse'){
                    $submenu = $this->getArrayFiltered_2(['tingkat_menu' => '2', 'induk_menu_id' => $key->menu_id], $menu_all);

                    $a = 0;
                    foreach ($submenu as $sub) {
                        if($sub->type == 'collapse'){
                            $subsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '3', 'induk_menu_id' => $sub->menu_id], $menu_all);

                            $b=0;
                            foreach ($subsubmenu as $subsub) {
                                if(in_array($subsub->icon, [null, ''])){ unset($subsubmenu[$b]->icon); }
                                if($subsub->type == "collapse"){ unset($subsubmenu[$b]->route); }
                                elseif(str_contains($subsub->route, 'https://')){ $subsubmenu[$b]->href = $subsub->route; unset($subsubmenu[$b]->route); }
                                // unset($subsubmenu[$b]->induk_menu_id);
                                // unset($subsubmenu[$b]->tingkat_menu);
                                $b++;
                            }

                            $submenu[$a]->collapse = $subsubmenu;;
                            $submenu[$a]->dropdown = true;
                        }

                        if(in_array($submenu[$a]->icon, [null, ''])){ unset($submenu[$a]->icon); }
                        if($sub->type == "collapse"){ unset($submenu[$a]->route); }
                        elseif(str_contains($sub->route, 'https://')){ $submenu[$a]->href = $sub->route; unset($submenu[$a]->route); }
                        // unset($submenu[$a]->induk_menu_id);
                        // unset($submenu[$a]->tingkat_menu);
                        $a++;
                    }

                    $menu[$i]->collapse = $submenu;
                    $menu[$a]->dropdown = true;
                }

                if(in_array($menu[$i]->icon, [null, ''])){ unset($menu[$i]->icon);}
                if($key->type == "collapse"){ unset($menu[$i]->route); }
                elseif(str_contains($key->route, 'https://')){ $menu[$i]->href = $key->route; unset($menu[$i]->route); }
                if(in_array($key->name, [null, ''])){ $menu[$i]->name = ''; }
                // unset($menu[$i]->menu_id);
                // unset($menu[$i]->induk_menu_id);
                // unset($menu[$i]->tingkat_menu);
                // unset($menu[$i]->type);
                $i++;
            }

            // return $menu;
            Cache::put(":_menu:".$peran_id, $menu, 3600);
            return Cache::get(":_menu:".$peran_id);
        }
    }

    public function _formatFuse($params)
    {
        // ['peran_nama', 'nama', 'email', 'menu', 'token'], 

        $data['user'] = array(
            'from' => 'sim-musda-backend',
            'role' => $params['peran_nama'],
            'rows' => $params['peran_nama'] == "Administrator" ? @$params['rows'] : $params['rows'],
            'menu' => $params['menu2'],
            'data' => array(
                'displayName' => $params['nama'],
                'photoURL' => 'https://icons.iconarchive.com/icons/mahm0udwally/all-flat/256/User-icon.png',
                'email' => $params['email'],
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
                                'display' => true,
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
}
