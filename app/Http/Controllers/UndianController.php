<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Client;
use App\Models\Mikrotik;
use Illuminate\Http\Request;

class UndianController extends Controller
{
    public function index(){
        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        return view('Dashboard/HIBURAN/UNDIAN/index', compact('mikrotik'));
    }
    public function buatundian(Request $request)
{
    $unique_id = auth()->user()->unique_id;

    // Ambil data MikroTik berdasarkan unique_id dan site
    $ipmikrotik = Mikrotik::where('unique_id', $unique_id)
        ->where('site', $request->input('site'))
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
        dd($client);
        // // Ambil data active PPPoE connections
        // $query = new \RouterOS\Query('/ppp/active/print');
        // $response = $client->query($query)->read();

        // if (empty($response)) {
        //     return response()->json(['error' => 'No active PPPoE connections found'], 404);
        // }

        // // Ambil username dari koneksi aktif
        // $activeUsers = array_map(function ($connection) {
        //     return $connection['name'] ?? null;
        // }, $response);

        // // Filter hanya username yang valid
        // $activeUsers = array_filter($activeUsers);

        // if (empty($activeUsers)) {
        //     return response()->json(['error' => 'No valid PPPoE usernames found'], 404);
        // }

        // // Pilih pemenang secara acak
        // $winner = $activeUsers[array_rand($activeUsers)];

        // return response()->json([
        //     'message' => 'Winner selected successfully',
        //     'winner' => $winner,
        // ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to connect to MikroTik',
            'details' => $e->getMessage(),
        ], 500);
    }
}

}
