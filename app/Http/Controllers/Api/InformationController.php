<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agama;
use App\Models\JenisKelamin;
use App\Models\PenghasilanOrangtua;
use App\Models\PekerjaanOrangtua;
use App\Models\Jurusan;
use App\Models\PesertaPPDB;
use App\Models\BiodataOrtu;
use App\Models\Hasil;
use App\Helpers\ResponseFormatter;

/**
 * Information Controller.
 */

class InformationController extends Controller
{
    /**
     * Get all information.
     */
    public function all(Request $request)
    {
        $agama = Agama::all();
        $jenkel = JenisKelamin::all();
        $hasil_ortu = PenghasilanOrangtua::all();
        $pekerjaan_ortu = PekerjaanOrangtua::all();
        $jurusan = Jurusan::all();

        return ResponseFormatter::success([
            'agama' => $agama,
            'jenkel' => $jenkel,
            'hasil_ortu' => $hasil_ortu,
            'pekerjaan_ortu' => $pekerjaan_ortu,
            'jurusan' => $jurusan,
        ], 'Data berhasil diambil');
    }

    public function registrations()
    {
        $items = Hasil::with(['peserta.orang_tua'])->get();
        return ResponseFormatter::success($items, 'Data berhasil diambil');
    }

}

