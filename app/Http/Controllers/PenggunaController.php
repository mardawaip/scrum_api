<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\EncryptPassword;
use App\Models\Pengguna;
use App\Models\Berita;
use DB;

class PenggunaController extends Controller
{
	/////////// UKS ///////////////
	public function get(Request $request)
	{
		$limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

		$pengguna = DB::connection('sqlsrv')
			->table('pengguna')
			->select(
				"pengguna.pengguna_id AS id",
				"pengguna.*",
				"peran.nama AS peran"
			)
			->leftJoin("ref.peran AS peran", "pengguna.peran_id", "=", "peran.peran_id");

		if($cari != ""){ $pengguna->where("pengguna.nama", "like", "%{$cari}%"); }

		$count_all = $pengguna->count();
        $penggunas = $pengguna->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($penggunas),
            'count_all' => $count_all,
            'rows'      => $penggunas
        ];
	}

	public function getPerpengguna(Request $request)
	{
		$pengguna = DB::connection('sqlsrv')
			->table('pengguna')
			->select(
				"pengguna.pengguna_id",
				"pengguna.nama",
				"pengguna.email",
				"pengguna.peran_id"
			)
			->where('pengguna_id', $request->pengguna_id)
			->first();

		return (array)$pengguna;
	}

	public function getSelectPengguna(Request $request)
	{
		return [
			"peran" => $this->getPeran()
		];
	}

	public function getPeran()
	{
		$peran = DB::table("ref.peran")->select("peran_id AS value", "nama AS label")->where('soft_delete', 0)->get();

		return $peran;
	}

	public function add(Request $request)
	{
		$pengguna_id = (string)Str::uuid();

		$data = [
			'pengguna_id' 	=> $pengguna_id,
			'nama' 			=> $request->nama,
			'peran_id' 		=> $request->peran_id,
			'soft_delete' 	=> 0,
			'create_date' 	=> DB::raw("GETDATE()"),
			'last_update' 	=> DB::raw("GETDATE()"),
			'email' 		=> $request->email,
			'password' 		=> md5($request->password)
		];

		$pengguna = DB::table('pengguna')->insert($data);

		return $pengguna ? "success" : "error";
	}

	public function update(Request $request)
	{
		$data = [
			'nama' 			=> $request->nama,
			'peran_id' 		=> $request->peran_id,
			'last_update' 	=> DB::raw("GETDATE()"),
			'email' 		=> $request->email,
		];

		if($request->password OR $request->password != ''){
			$data['password'] = md5($request->password);
		}

		$pengguna = DB::table('pengguna')->where('pengguna_id', $request->pengguna_id)->update($data);
		return $pengguna ? "success" : "error";
	}

	public function delete(Request $request)
	{
		$pengguna = DB::table('pengguna')->where('pengguna_id', $request->pengguna_id)->update(['soft_delete' => 1]);
		return $pengguna ? "success" : "error";
	}

	//////////// PMP /////////
	public function getSekolah(Request $request){
		$sekolah_id = $request->sekolah_id ? $request->sekolah_id : null;

		$fetch = DB::connection('sqlsrv')->table('sekolah')
		->where('sekolah_id','=',$sekolah_id)
		->where('soft_delete','=',0)
		;

		return response(
			[
				// 'rows' => $fetch->get(),
				'rows' => $fetch->get(),
				'total' => $fetch->count()
			],
			200
		);
	}

	public function getPengawas(Request $request){
		$pengguna_id = $request->pengguna_id ? $request->pengguna_id : null;
		$value = $request->value ? $request->value : null;

		$fetch = DB::connection('sqlsrv')->table('pengguna')
		->leftJoin('ref.mst_wilayah as wilayah','wilayah.kode_wilayah','=','pengguna.bidang_studi_id')
		->where('soft_delete','=',0)
		->where('peran_id','=',13)
		->select(
			'pengguna.*',
			'wilayah.nama as wilayah'
		)
		// ->where('pengguna_id','=', $pengguna_id)
		// ->get()
		;

		if($pengguna_id){
			$fetch->where('pengguna_id','=', $pengguna_id);
		}

		if($value){
			$fetch->where(function($query) use ($value){
                $query->where('pengguna.nama','like',DB::raw("'%".$value."%'"))
                	->orWhere('pengguna.email','like',DB::raw("'%".$value."%'"));
            });
		}

		$rows = $fetch->get();

		for ($i=0; $i < sizeof($rows); $i++) { 
			$fetch_sekolah_binaan = DB::connection('sqlsrv')
			->table('sekolah_binaan')
			->join('sekolah','sekolah.sekolah_id','=','sekolah_binaan.sekolah_id')
			->join('ref.mst_wilayah as kec','kec.kode_wilayah','=', DB::raw("LEFT(sekolah.kode_wilayah, 6)"))
			->join('ref.mst_wilayah as kab','kab.kode_wilayah','=', 'kec.mst_kode_wilayah')
			->join('ref.mst_wilayah as prov','prov.kode_wilayah','=', 'kab.mst_kode_wilayah')
			->where('sekolah_binaan.soft_delete','=',0)
			->where('sekolah.soft_delete','=',0)
			->where('sekolah_binaan.pengguna_id','=',$rows[$i]->pengguna_id)
			->select(
				'sekolah_binaan.*',
				'sekolah.*',
				'kec.nama as kecamatan',
				'kab.nama as kabupaten',
				'prov.nama as provinsi'
			)
			->get()
			;

			$rows[$i]->sekolah_binaan = array('rows'=> $fetch_sekolah_binaan, 'total' => sizeof($fetch_sekolah_binaan));
		}

		return response(
			[
				// 'rows' => $fetch->get(),
				'rows' => $rows,
				'total' => $fetch->count()
			],
			200
		);
	}

	public function server(Request $request)
	{
		$kolom = $request->kolom;
		$value = $request->value;
		$pengawas = $request->pengawas ? $request->pengawas : null;

		$arr = [
			'pmp_nama' => 'pengguna.nama',
			'pmp_email' => 'pengguna.email',
			'pmp_sekolah' => 'sekolah.nama',
			'pmp_npsn' => 'sekolah.npsn',
		];

		if(($kolom == null) OR ($value == null)){
			return ['rows'=>[]];
		}else{
			$pengguna = DB::connection('sqlsrv')
			->table(DB::raw('pengguna WITH(NOLOCK)'))
			->select(
				'pengguna.pengguna_id AS pmp_pengguna_id',
				'pengguna.nama AS pmp_nama',
				'pengguna.email AS pmp_email',
				'pengguna.soft_delete AS pmp_soft_delete',
				'pengguna.password AS pmp_password',
				'pengguna.password_lama AS pmp_password_lama',
				'pengguna.nik AS pmp_nik',
				'pengguna.nip AS pmp_nip',
				'sekolah.sekolah_id AS pmp_sekolah_id',
				'sekolah.nama AS pmp_sekolah',
				'sekolah.npsn AS pmp_npsn',
				'peran.peran_id AS pmp_peran_id',
				'peran.nama AS pmp_peran',
				'pengguna.bidang_studi_id AS pmp_bidang_studi_id',
				'wilayah.nama AS pmp_bidang_studi',
				'pengguna.bidang_studi_id_str AS pmp_bidang_studi_id_str',
				'pengguna.create_date AS pmp_create_date',
				'pengguna.last_update AS pmp_last_update',
				'pengguna.last_sync AS pmp_last_sync'
			)
			->leftJoin(DB::raw('sekolah WITH(NOLOCK)'), 'pengguna.sekolah_id', '=', 'sekolah.sekolah_id')
			->leftJoin(DB::raw('ref.peran AS peran WITH(NOLOCK)'), 'pengguna.peran_id', '=', 'peran.peran_id')
			->leftJoin(DB::raw('ref.wilayah AS wilayah WITH(NOLOCK)'), 'pengguna.bidang_studi_id', '=', 'wilayah.kode_wilayah')
			// ->limit(100)
			->whereIn('pengguna.peran_id', [10, 53])
			->orderBy('pengguna.soft_delete', 'ASC')
			->orderBy('pengguna.peran_id', 'ASC')
			->where('sekolah.soft_delete', 0)
			->where($arr[$kolom], 'like', '%'.$value.'%');

			if($pengawas){
				$pengguna->where('peran.peran_id','=', 13);
			}

			$rows = $pengguna->get();
			
			$count_kepsek = 0;
			$count_kepsek_aktif = 0;
			$count_ptk = 0;
			$count_ptk_aktif = 0;

			foreach ($rows as $key) {
				if($key->pmp_peran_id == "10"){
					$count_kepsek++;
					if($key->pmp_soft_delete == "0"){
						$count_kepsek_aktif++;
					}
				}elseif($key->pmp_peran_id == "53"){
					$count_ptk++;
					if($key->pmp_soft_delete == "0"){
						$count_ptk_aktif++;
					}
				}
			}

			$return = [
				'rows' => $rows,
				'count_kepsek' => [
					'aktif' => $count_kepsek_aktif,
					'count' => $count_kepsek
				],
				'count_ptk' => [
					'aktif' => $count_ptk_aktif,
					'count' => $count_ptk
				]
			];

			return $return;
		}
	}


	public function server_pegawas(Request $request)
	{
		$pengawas =  DB::connection('sqlsrv')
		->table(DB::raw('sekolah_binaan WITH(NOLOCK)'))
		->select(
			'sekolah.npsn',
			'sekolah.nama AS sekolah',
			'pengguna.pengguna_id AS pengguna_id',
			'pengguna.nama AS nama',
			'pengguna.email AS email',
			'peran.peran_id AS peran_id',
			'peran.nama AS peran',
			'sekolah_binaan.soft_delete AS soft_delete',
			'pengguna.password AS password',
			'pengguna.password_lama AS password_lama',
			'pengguna.nik AS nik',
			'pengguna.nip AS nip',
			'pengguna.bidang_studi_id AS bidang_studi_id',
			'wilayah.nama AS bidang_studi',
			'pengguna.bidang_studi_id_str AS bidang_studi_id_str',
			'pengguna.create_date AS create_date',
			'pengguna.last_update AS last_update',
			'pengguna.last_sync AS last_sync'
		)
		->leftJoin(DB::raw('sekolah WITH(NOLOCK)'), 'sekolah_binaan.sekolah_id', '=', 'sekolah.sekolah_id')
		->leftJoin(DB::raw('pengguna WITH(NOLOCK)'), 'sekolah_binaan.pengguna_id', '=', 'pengguna.pengguna_id')
		->leftJoin(DB::raw('ref.peran AS peran WITH(NOLOCK)'), 'pengguna.peran_id', '=', 'peran.peran_id')
		->leftJoin(DB::raw('ref.wilayah AS wilayah WITH(NOLOCK)'), 'pengguna.bidang_studi_id', '=', 'wilayah.kode_wilayah')
		->where('sekolah.npsn', $request->q)
		->orderBy('sekolah.npsn', 'ASC')
		->orderBy('sekolah_binaan.soft_delete', 'ASC')
		->limit(100);

		$sekolah = DB::connection('sqlsrv')
		->table(DB::raw("sekolah WITH(NOLOCK)"))
		->select(
			'sekolah.nama',
			'sekolah.npsn',
			'sekolah.alamat_jalan',
			'bentuk_pendidikan.nama AS bentuk_pendidikan',
			'sekolah.status_sekolah',
			'kecamatan.kode_wilayah AS kode_kecamatan',
			'kecamatan.nama AS kecamatan',
			'kabupaten.kode_wilayah AS kode_kabupaten',
			'kabupaten.nama AS kabupaten',
			'provinsi.kode_wilayah AS kode_provinsi',
			'provinsi.nama AS provinsi'
		)
		->leftJoin(DB::raw('ref.bentuk_pendidikan AS bentuk_pendidikan WITH(NOLOCK)'), 'sekolah.bentuk_pendidikan_id', '=', 'bentuk_pendidikan.bentuk_pendidikan_id')
		->leftJoin(DB::raw('ref.wilayah AS kecamatan WITH(NOLOCK)'), DB::raw('LEFT(sekolah.kode_wilayah, 6)'), '=',  DB::raw('LEFT(kecamatan.kode_wilayah, 6)'))
		->leftJoin(DB::raw('ref.wilayah AS kabupaten WITH(NOLOCK)'), DB::raw('LEFT(sekolah.kode_wilayah, 4)'), '=',  DB::raw('LEFT(kabupaten.kode_wilayah, 4)'))
		->leftJoin(DB::raw('ref.wilayah AS provinsi WITH(NOLOCK)'), DB::raw('LEFT(sekolah.kode_wilayah, 2)'), '=',  DB::raw('LEFT(provinsi.kode_wilayah, 2)'))
		->where('sekolah.npsn', $request->q)
		->first();

		return [
			'sekolah' => $sekolah == null ? [] : $sekolah,
			'count' => $pengawas->count(),
			'rows' => $pengawas->get(),
		];
	}

	public function ref_wilayah(Request $request)
	{
		$wilayah = DB::connection('sqlsrv')
		->table(DB::raw('ref.wilayah WITH(NOLOCK)'))
		->select(
			"kode_wilayah AS value",
			"nama AS label"
		)
		->where("level_wilayah_id", 2)
		->limit(10);

		if($request->nama != ""){
			$wilayah->where('nama', 'like', '%'.$request->nama.'%');
		}else{
			$wilayah->where('kode_wilayah', 'like', '%'.$request->sel.'%');
		}

		return $wilayah->get();
	}

	public function ref_sekolah(Request $request)
	{
		$wilayah = DB::connection('sqlsrv')
		->table(DB::raw('sekolah WITH(NOLOCK)'))
		->select(
			"sekolah_id AS value",
			"nama AS label"
		)
		->where("soft_delete", 0)
		->limit(10);

		if($request->nama != ""){
			$wilayah->where('nama', 'like', '%'.$request->nama.'%');
		}else{
			$wilayah->where('sekolah_id', 'like', '%'.$request->sel.'%');
		}

		return $wilayah->get();
	}

	public function update_pengguna(Request $request)
	{
		$Pengguna = Pengguna::find($request->pmp_pengguna_id);
		$Pengguna->nama = $request->pmp_nama;
		$Pengguna->email = $request->pmp_email;
		$Pengguna->nik = $request->pmp_nik;
		$Pengguna->nip = $request->pmp_nip;
		$Pengguna->sekolah_id = $request->pmp_sekolah_id;
		$Pengguna->bidang_studi_id = $request->pmp_bidang_studi_id;
		$Pengguna->bidang_studi_id_str = $request->pmp_bidang_studi_id_str;
		$Pengguna->last_update = DB::connection('sqlsrv')->select("SELECT GETDATE() AS jam")[0]->jam;
		$Pengguna->save();
	}

	public function ubah_status(Request $request)
	{
		$time = DB::connection('sqlsrv')->select("SELECT GETDATE() AS jam")[0]->jam;
		DB::connection('sqlsrv')
		->table("pengguna")
		->where('pengguna_id', $request->pmp_pengguna_id)
		->update([
			'soft_delete' => $request->soft_delete,
			'last_update' => $time,
		]);
	}

	public function password_cek($password)
    {
        $encrypt = new EncryptPassword();
        $encrypt->setString($password);
        return $password_encrypt = $encrypt->doEncrypt();
    }

	public function ubah_password(Request $request)
	{	
		if($request->password){
			if($request->type_pass == 'md5'){
				$password = md5($request->password);
			}elseif($request->type_pass == 'encrypter'){
				$password = $this->password_cek($request->password);
			}
		}

		if($request->password_lama){
			if($request->type_pass_lama == 'md5'){
				$password_lama = md5($request->password_lama);
			}elseif($request->type_pass_lama == 'encrypter'){
				$password_lama = $this->password_cek($request->password_lama);
			}
		}

		$Pengguna = Pengguna::find($request->pmp_pengguna_id);
		if(@$password != ""){
			$Pengguna->password = $password;
			$Pengguna->last_update = DB::connection('sqlsrv')->select("SELECT GETDATE() AS jam")[0]->jam;
		}

		if(@$password_lama != ""){
			$Pengguna->password_lama = $password_lama;
			$Pengguna->last_update = DB::connection('sqlsrv')->select("SELECT GETDATE() AS jam")[0]->jam;
		}

		$Pengguna->save();
	}

	public function cek_password_pengguna(Request $request)
	{
		return [
			'gen_1' => md5($request->params),
			'gen_2' => $this->password_cek($request->params),
		];
	}

	public function server_2(Request $request)
	{
		$limit = $request->limit ? $request->limit : 10;
		$offset = $request->offset ? $request->offset : 0;
		$search = $request->search ? intval($request->search) : '';
		$searchText = $request->searchText ? $request->searchText : '';
		$peran_id = $request->peran_id ? intval($request->peran_id) : 1;
		$kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : '000000';

		$pengguna = DB::connection('sqlsrv')
			->table('pengguna')
			->lock("WITH(NOLOCK)")
			->select('pengguna.*', 'peran.nama AS peran')
			->leftJoin('ref.peran As peran', 'pengguna.peran_id', '=', 'peran.peran_id')
			->orderBy('pengguna.email', 'ASC')
			->where('pengguna.soft_delete', 0);

		if($peran_id == 1){
			$pengguna->whereIn('pengguna.peran_id', [6, 8, 10, 13, 54]);
		}elseif($peran_id == 54){
			if($search == 13){
				$pengguna = DB::connection('sqlsrv')
					->table('pengguna')
					->DISTINCT()
					->lock("WITH(NOLOCK)")
					->select(
						// DB::raw("ROW_NUMBER () OVER ( PARTITION BY pengguna.pengguna_id ORDER BY pengguna.create_date DESC ) AS urutan"),
						"pengguna.*",
						'peran.nama AS peran'
					)
					->leftJoin('sekolah_binaan', 'pengguna.pengguna_id', '=', 'sekolah_binaan.pengguna_id')
					->leftJoin('sekolah', 'sekolah_binaan.sekolah_id', '=', 'sekolah.sekolah_id')
					->leftJoin('ref.peran As peran', 'pengguna.peran_id', '=', 'peran.peran_id')
					->where(DB::raw('LEFT(sekolah.kode_wilayah, 2)'), substr($kode_wilayah, 0, 2))
					->where('pengguna.soft_delete', 0)
					->where('sekolah.soft_delete', 0)
					->where('sekolah_binaan.soft_delete', 0)
					->orderBy('pengguna.nama', 'ASC');
			}elseif($search == 10){
				$pengguna->leftJoin('sekolah', 'pengguna.sekolah_id', '=', 'sekolah.sekolah_id')
				->select('pengguna.*', 'peran.nama AS peran', 'sekolah.npsn', 'sekolah.nama AS sekolah')
				->where(DB::raw('LEFT(sekolah.kode_wilayah, 2)'), substr($kode_wilayah, 0, 2));
			}else{
				$pengguna->where(DB::raw('LEFT(pengguna.bidang_studi_id, 2)'), substr($kode_wilayah, 0, 2));
			}
		}

		if($search != 'semua'){
			$pengguna->where('pengguna.peran_id', $search);

			if($search == 10){
				if($peran_id == 1){
					$pengguna->leftJoin('sekolah', 'pengguna.sekolah_id', '=', 'sekolah.sekolah_id');
				}
				$pengguna->select('pengguna.*', 'peran.nama AS peran', 'sekolah.npsn', 'sekolah.nama AS sekolah');
			}
		}

		if($searchText != ''){
			$pengguna->where('pengguna.nama', 'like', "%{$searchText}%");
		}

		$sql = $pengguna->tosql();
		$count_all = $pengguna->count();
		$pengguna2 = $pengguna->limit($limit)->offset($offset)->get();

		return [
			'rows' => $pengguna2,
			'count' => count($pengguna2),
			'count_all' => $count_all,
			'sql' => $sql
		];
	}

	public function pengguna_detail(Request $request)
	{
		$pengguna_id = $request->pengguna_id ? $request->pengguna_id : '';
		
		$pengguna = DB::connection('sqlsrv')
			->table('pengguna')
			->lock('WITH(NOLOCK)')
			->select('pengguna.*', 'sekolah.nama AS sekolah')
			->where('pengguna_id', $pengguna_id)
			->leftJoin('sekolah', 'pengguna.sekolah_id', '=', 'sekolah.sekolah_id')
			->first();

		$sekolah_binaan = [];

		if($pengguna->bidang_studi_id != null){
			$wilayah = DB::connection('sqlsrv')
				->table('ref.mst_wilayah')
				->lock('WITH(NOLOCK)')
				->where('kode_wilayah', $pengguna->bidang_studi_id)
				->first();

			if($wilayah->id_level_wilayah == '1'){
				$pengguna->kode_propinsi 	= trim($wilayah->kode_wilayah);
				$pengguna->propinsi 		= trim($wilayah->nama);
			}elseif($wilayah->id_level_wilayah == '2'){
				$prop = DB::connection('sqlsrv')
					->table('ref.mst_wilayah')
					->lock("WITH(NOLOCK)")
					->where('kode_wilayah', $wilayah->mst_kode_wilayah)
					->first();

				$pengguna->kode_kabupaten 	= trim($wilayah->kode_wilayah);
				$pengguna->kabupaten 		= trim($wilayah->nama);
				$pengguna->kode_propinsi 	= trim($prop->kode_wilayah);
				$pengguna->propinsi 		= trim($prop->nama);
			}
		}

		if($pengguna->peran_id == '13'){
			$sekolah_binaan = DB::connection('sqlsrv')
				->table('sekolah_binaan')
				->lock('WITH(NOLOCK)')
				->select(
					'sekolah.nama',
					'sekolah.npsn',
					'sekolah.alamat_jalan',
					'sekolah.last_update',
					'bentuk_pendidikan.nama AS bentuk_pendidikan'
				)
				->where('sekolah_binaan.pengguna_id', $pengguna_id)
				->where('sekolah_binaan.soft_delete', 0)
				->where('sekolah.soft_delete', 0)
				->leftJoin('sekolah', 'sekolah_binaan.sekolah_id', '=', 'sekolah.sekolah_id')
				->leftJoin('ref.bentuk_pendidikan AS bentuk_pendidikan', 'sekolah.bentuk_pendidikan_id', '=', 'bentuk_pendidikan.bentuk_pendidikan_id')
				->get();
		}

		return [
			'pengguna' => (array)$pengguna,
			'sekolah_binaan' => $sekolah_binaan
		];
	}

	public function update_pengguna_2(Request $request)
	{
		$where = [
			'pengguna_id' 			=> $request->pengguna_id
		];

		$data = [
			'bidang_studi_id' 		=> $request->bidang_studi_id,
			'bidang_studi_id_str' 	=> $request->bidang_studi_id_str,
			'nama' 					=> $request->nama,
			'nik' 					=> $request->nik,
			'nip' 					=> $request->nip,
			'nuptk' 				=> $request->nuptk,
			'peran_id' 				=> $request->peran_id,
			'sekolah_id' 			=> $request->sekolah_id,
			'soft_delete' 			=> $request->soft_delete,
			'tanggal_lahir' 		=> $request->tanggal_lahir,
			'last_update' 			=> DB::connection('sqlsrv')->select("SELECT GETDATE() AS jam")[0]->jam,
		];		

		$db = DB::connection('sqlsrv')->table('pengguna')->where($where)->update($data);
		return $db;
	}

	public function filter_sekolah(Request $request)
	{
		$sekolah_id = $request->sekolah_id ? $request->sekolah_id : "";
		$kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : "";
		
		$sekolahMy = $sekolah_id != "" ? DB::connection('sqlsrv')->table('sekolah')->lock('WITH(NOLOCK)')->where('sekolah_id', $sekolah_id)->first() : [];
		$kode_wilayah = $kode_wilayah != "" ? $kode_wilayah : $sekolahMy->kode_wilayah;

		$sekolah = DB::connection('sqlsrv')
			->table('sekolah')
			->lock('WITH(NOLOCK)')
			->select('sekolah_id', 'nama')
			->where(DB::raw('LEFT(kode_wilayah, 4)'), substr($kode_wilayah, 0, 4));

		if($sekolah_id != ""){
			$sekolah->orWhere('sekolah_id', $sekolah_id);
		}

		$sekolah2 = $sekolah->get();


		return ['rows' => $sekolah2, 'count' => count($sekolah2)];
	}

	public function ubah_password_2(Request $request)
	{
		$pengguna_id = $request->pengguna_id;
		$peran_id = $request->peran_id;
		$email = $request->email;
		$password = $request->password;
		$password_lama = $request->password_lama;
		$password_baru = $request->password_baru;

		$pengguna = DB::connection('sqlsrv')
			->table('pengguna')
			->lock('WITH(NOLOCK)')
			->where('pengguna_id', $pengguna_id)
			->first();

		$msg = 'success';

		if($request->type == 'reset_password'){
			if($peran_id == '10'){
				$password = $this->password_cek(12345);
			}else if($peran_id == '13'){
				$password = $this->password_cek($pengguna->nik);
			}else if($peran_id == '54'){
				$password = $this->password_cek($pengguna->bidang_studi_id);
			}else if($peran_id == '6'){
				$password = $this->password_cek($pengguna->bidang_studi_id);
			}elseif($peran_id == '8'){
				$password = $this->password_cek($pengguna->bidang_studi_id);
			}

			$db = DB::connection('sqlsrv')->table('pengguna')->where('pengguna_id', $pengguna_id)->update(['password' => $password]);

			if(!$db){
				$msg = 'reset-error';
			}

		}else if($request->type == 'ubah_password'){
			$db = DB::connection('sqlsrv')
				->table('pengguna')
				->where('pengguna_id', $pengguna_id)
				->where('password', $this->password_cek($password_lama))
				->update([
					'password' => $this->password_cek($password_baru)
				]);

			if(!$db){
				$msg = 'ubah-pass-error';
			}

		}else if($request->type == 'ubah_login'){
			$db = DB::connection('sqlsrv')
				->table('pengguna')
				->where('pengguna_id', $pengguna_id)
				->where('password', $this->password_cek($password))
				->update([
					'email' => $email
				]);

			if(!$db){
				$msg = 'ubah-login-error';
			}
		}else{
			$msg = 'not-action';
		}

		return ['msg' => $msg];
	}
}
