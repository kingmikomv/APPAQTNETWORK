<?php

namespace App\Http\Controllers;

use App\Models\Undian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DepanController extends Controller
{
    public function index()
    {
        return view('Depan/index');
    }
    public function undian()
{
    // Token untuk autentikasi API (gantilah dengan token yang benar)
    $token = "123456";

    // URL API
    $url = "https://biller.aqtnetwork.my.id/api/undianapi";

    // Panggil API menggunakan HTTP Client Laravel dengan Bearer Token
    $response = Http::withToken($token)->get($url);

    // Cek apakah respons dari API berhasil
    if ($response->successful()) {
        $daftarundian = $response->json(); // Pastikan API mengembalikan format dengan 'data'
    } else {
        $daftarundian = []; // Jika gagal, tampilkan array kosong
    }
    //dd($daftarundian);
    // Hapus dd() agar tidak menghentikan eksekusi
    return view('Depan.INFORMASI.undian', compact('daftarundian'));
}

}
