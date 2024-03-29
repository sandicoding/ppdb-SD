<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

// Load Models
use App\Models\PesertaPPDB;
use App\Models\Hasil;
use App\Models\BiodataOrtu;


class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = \Validator::make($request->all(), [
                'id_jenis_kelamin' => 'required|exists:tbl_jenis_kelamin,id',
                'id_agama' => 'required|exists:tbl_agama,id',
                'nama' => 'required',
                'tanggal_lahir' => 'date|before:yesterday',
                'tempat_lahir' => 'required',
                'alamat' => 'required',
                'no_telp' => 'required',
                'nama_ayah' => 'required',
                'nama_ibu' => 'required',
                'id_pekerjaan_ayah' => 'required|exists:tbl_pekerjaan_ortu,id',
                'id_pekerjaan_ibu' => 'required|exists:tbl_pekerjaan_ortu,id',
                'id_penghasilan_ayah' => 'required|exists:tbl_penghasilan_ortu,id',
                'id_penghasilan_ibu' => 'required|exists:tbl_penghasilan_ortu,id',
                'no_telp_ortu' => 'required'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error($validator->errors(), 'Check please input');
            }

            $data = [
                'nama' => $request->nama,
                'id_jenis_kelamin' => $request->id_jenis_kelamin,
                'id_agama' => $request->id_agama,
                'id_jurusan' => $request->id_jurusan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tempat_lahir' => $request->tempat_lahir,
                'asal_sekolah' => $request->asal_sekolah,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'nama_ortu' => $request->nama_ayah,
                'id_pekerjaan_ortu' => $request->id_pekerjaan_ayah,
                'id_penghasilan_ortu' => $request->id_penghasilan_ayah,
            ];

            $daftar = PesertaPPDB::create($data);

            if (!$daftar) {
                DB::rollback();
                return ResponseFormatter::error([
                    'message' => 'Please check your form again!'
                ], 'Please check your form again!', 401);
            }

            $data2 = [
                'id_peserta_ppdb' => $daftar->id,
                'id_pekerjaan_ayah' => $request->id_pekerjaan_ayah,
                'id_penghasilan_ayah' => $request->id_penghasilan_ayah,
                'id_pekerjaan_ibu' => $request->id_pekerjaan_ibu,
                'id_penghasilan_ibu' => $request->id_penghasilan_ibu,
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'no_tlp' => $request->no_telp_ortu
            ];

            $ortu = BiodataOrtu::create($data2);
            if (!$ortu) {
                DB::rollback();
                return ResponseFormatter::error([
                    'message' => 'Please check your form again!'
                ], 'Please check your form again!', 401);
            }

            $data3 = [
                'nis' => $daftar->id
            ];

            $hasil = Hasil::create($data3);
            if (!$hasil) {
                DB::rollback();
                return ResponseFormatter::error([
                    'message' => 'Please check your form again!'
                ], 'Please check your form again!', 401);
            }

            $result = Hasil::with(['peserta.orang_tua'])->find($hasil->id);

            DB::commit();
            return ResponseFormatter::success($result, 'Registration success', 200);

        } catch (Exception $error) {
            DB::log($error);
            DB::rollBack();
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Registration Failed', 500);
        }
    }
}
