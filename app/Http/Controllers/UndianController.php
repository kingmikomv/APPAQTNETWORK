<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Client;
use App\Models\Undian;
use App\Models\Mikrotik;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UndianController extends Controller
{
    public function index(){
        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        $daftarundian = Undian::orderBy('id', 'DESC')->get();
        return view('Dashboard/HIBURAN/UNDIAN/index', compact('mikrotik', 'daftarundian'));
    }
    public function buatundian(Request $request)
    {
        // Validasi input
        $request->validate([
            'site' => 'required|string|max:255',
            'hadiah' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal' => 'required|date',
        ]);
    
        // Ambil data dari request
        $foto = $request->file('foto');
        $folderPath = public_path('GAMBARUNDIAN');
        $fileName = time() . '_' . $foto->getClientOriginalName();
            // Buat folder jika belum ada
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        // Pindahkan file ke folder GAMBARUNDIAN
        $foto->move($folderPath, $fileName);
    
            // Simpan data ke database menggunakan mass assignment
            Undian::create([
                'site' => $request->input('site'),
                'hadiah' => $request->input('hadiah'),
                'foto' => 'GAMBARUNDIAN/' . $fileName,
                'tanggal' => $request->input('tanggal'),
                'unique_undian' => Str::random(10), // Menghasilkan teks acak sepanjang 10 karakter

            ]);
            session()->flash('success', 'Undian Berhasil Di Buat');
        return redirect()->back();
       
    }
    
    
    public function caripemenang(Request $request)
    {
        $dataUndian = Undian::where('unique_undian', $request->unique_undian)->first();
        
    $unique_id = auth()->user()->unique_id;

    // Ambil data MikroTik berdasarkan unique_id dan site
    $ipmikrotik = Mikrotik::where('unique_id', $unique_id)
        ->where('site', $dataUndian->site)
        ->first();

    if (!$ipmikrotik) {
        return response()->json(['error' => 'MikroTik not found'], 404);
    }

    $ip = $ipmikrotik->ipmikrotik;
    $username = $ipmikrotik->username;
    $password = $ipmikrotik->password;

    // Ambil data VPN untuk port API (opsional, tergantung kebutuhan)
    $dataVPN = VPN::where('ipaddress', $ip)->first();
    $dataPORTAPIVPN = $dataVPN->portapi ?? 8728; // Default port 8728 jika tidak ditemukan

    try {
        // Hubungkan ke MikroTik menggunakan RouterOS API
        $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:'.$dataPORTAPIVPN,
                'user' => $username,
                'pass' => $password,
        ]);
        //dd($client);
        // Ambil data active PPPoE connections
        $query = new \RouterOS\Query('/ppp/active/print');
        $response = $client->query($query)->read();

        if (empty($response)) {
            return response()->json(['error' => 'No active PPPoE connections found'], 404);
        }

        // Ambil username dari koneksi aktif
        $activeUsers = array_map(function ($connection) {
            return $connection['name'] ?? null;
        }, $response);

        // Filter hanya username yang valid
        $activeUsers = array_filter($activeUsers);

        if (empty($activeUsers)) {
            return response()->json(['error' => 'No valid PPPoE usernames found'], 404);
        }

        // Pilih pemenang secara acak
        $winner = $activeUsers[array_rand($activeUsers)];


        if ($dataUndian) {
            $dataUndian->update([
                'pemenang' => $winner, // Update hanya kolom `pemenang`
            ]);
        }
        // return response()->json(['success' => true, 'message' => 'Selamat kepada '.$winner.' Mendapatkan Hadiah '. $dataUndian->hadiah]);

        session()->flash('success', 'Selamat kepada '.$winner.' Mendapatkan Hadiah '. $dataUndian->hadiah);
        return redirect()->back();

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to connect to MikroTik',
            'details' => $e->getMessage(),
        ], 500);
        }
    }
}
