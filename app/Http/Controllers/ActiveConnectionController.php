<?php

namespace App\Http\Controllers;

use App\Models\ActiveMod;
use App\Models\VPN;
use RouterOS\Query;
use RouterOS\Client;
use App\Models\Mikrotik;
use Illuminate\Http\Request;

class ActiveConnectionController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data MikroTik berdasarkan IP
            $ipmikrotik = $request->input('ipmikrotik');
            $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
    
            // Ambil data VPN untuk port API
            $datavpn = VPN::where('ipaddress', $data->ipmikrotik)
                ->where('unique_id', auth()->user()->unique_id)
                ->first();
            $portapi = $datavpn->portapi ?? null;
    
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'MikroTik tidak ditemukan.',
                ], 404);
            }
    
            // Konfigurasi MikroTik API Client
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi,
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000
            ]);
    
            // Query ke MikroTik untuk data koneksi aktif
            $query = new Query('/ppp/active/print');
            $activeConnections = $client->query($query)->read();
    
            // Ambil username yang sudah ada di database
            $existingUsers = ActiveMod::where('ipmikrotik', $ipmikrotik)
                ->where('unique_id', auth()->user()->unique_id)
                ->pluck('username')
                ->toArray();
    
            // Hanya cari user yang tidak aktif (gangguan)
            $offlineUsers = [];
            $isolir = []; // Menyimpan user dengan IP yang dimulai dengan 172
    
            // Cocokkan data koneksi aktif dan data di database
            foreach ($existingUsers as $username) {
                $isUserActive = false;
    
                // Periksa apakah username ada di activeConnections
                foreach ($activeConnections as $connection) {
                    if (isset($connection['name']) && $connection['name'] == $username) {
                        $isUserActive = true;
    
                        // Cek apakah IP address dimulai dengan 172 dan tambahkan ke isolir
                        if (isset($connection['address']) && strpos($connection['address'], '172') === 0) {
                            $isolir[] = $username; // Menambahkan user ke isolir jika IP dimulai dengan 172
                        }
    
                        break;
                    }
                }
    
                // Jika user tidak aktif, tambahkan ke daftar offlineUsers
                if (!$isUserActive) {
                    $offlineUsers[] = $username;
                }
            }
    
            // Kirim data ke view, hanya menampilkan user yang mengalami gangguan dan isolir
            return view('Dashboard/CEKDOWN/index', [
                'offlineUsers' => $offlineUsers,
                'isolir' => $isolir // Mengirim data isolir ke view
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    



    public function syncActiveConnection(Request $request)
    {
        try {
            // Ambil data MikroTik berdasarkan IP
            $ipmikrotik = $request->input('ipmikrotik');

            // Ambil data MikroTik berdasarkan IP
            $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

            // Ambil data VPN untuk port API
            $datavpn = VPN::where('ipaddress', $data->ipmikrotik)
                ->where('unique_id', auth()->user()->unique_id)
                ->first();
            $portapi = $datavpn->portapi ?? null;

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'MikroTik not found.',
                ], 404);
            }

            // Konfigurasi MikroTik API Client
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000
            ]);

            // Query ke MikroTik untuk data koneksi aktif
            $query = new Query('/ppp/active/print');
            $activeConnections = $client->query($query)->read();

            // Ambil username yang sudah ada di database
            $existingUsers = ActiveMod::where('ipmikrotik', $ipmikrotik)
                ->pluck('username')
                ->toArray();

            $newUsers = 0;
            $existingUsernames = [];

            // Sinkronisasi data
            foreach ($activeConnections as $connection) {
                // Masukkan data baru jika belum ada di database
                if (isset($connection['name']) && !in_array($connection['name'], $existingUsers)) {
                    ActiveMod::create([
                        'username' => $connection['name'],
                        'unique_id' => auth()->user()->unique_id,
                        'ipmikrotik' => $ipmikrotik,
                        'site' => $data->site,
                    ]);
                    $newUsers++;
                } else {
                    // Simpan username yang sudah ada untuk update last_sync_at
                    $existingUsernames[] = $connection['name'];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => $newUsers . ' new users synced successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getActiveConnections(Request $request)
    {
        try {
            // Ambil data MikroTik berdasarkan IP
            $ipmikrotik = $request->input('ipmikrotik');
            $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

            // Ambil data VPN untuk port API
            $datavpn = VPN::where('ipaddress', $data->ipmikrotik)
                ->where('unique_id', auth()->user()->unique_id)
                ->first();
            $portapi = $datavpn->portapi ?? null;

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'MikroTik not found.',
                ], 404);
            }

            // Konfigurasi MikroTik API Client
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi,
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000
            ]);

            // Query ke MikroTik untuk data koneksi aktif
            $query = new Query('/ppp/active/print');
            $activeConnections = $client->query($query)->read();

            // Ambil username yang sudah ada di database
            $existingUsers = ActiveMod::where('ipmikrotik', $ipmikrotik)
                ->pluck('username', 'status')
                ->toArray();

            $offlineUsers = [];
            $onlineUsers = [];

            foreach ($activeConnections as $connection) {
                if (isset($connection['name']) && array_key_exists($connection['name'], $existingUsers)) {
                    // Menandakan user sedang aktif
                    $onlineUsers[] = $connection['name'];
                    unset($existingUsers[$connection['name']]); // Hapus yang sudah ditemukan
                }
            }

            // Sisanya adalah user yang terdaftar di DB tapi tidak aktif (gangguan)
            foreach ($existingUsers as $username => $status) {
                $offlineUsers[] = $username;
            }

            return response()->json([
                'status' => 'success',
                'online_users' => $onlineUsers,
                'offline_users' => $offlineUsers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // public function cekDown(Request $req)
    // {
    //     $ipmikrotik = $req->input('ipmikrotik');

    //     // Ambil data MikroTik berdasarkan IP
    //     $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
    //     $totalvpn = VPN::where('unique_id', auth()->user()->unique_id)->count();
    //     $totalmikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->count();
    //     $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();
    //     $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

    //     $portapi = $datavpn->portapi ?? null;

    //     // $config = [
    //     //     'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
    //     //     'user' => $data->username,
    //     //     'pass' => $data->password,
    //     //     'port' => 9000

    //     // ];
    //     // Query ke MikroTik untuk data koneksi aktif
    //     try {
    //         // Konfigurasi MikroTik API Client
    //         $client = new Client([
    //             'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
    //             'user' => $data->username,
    //             'pass' => $data->password,
    //             'port' => 9000
    //         ]);

    //         // Query ke MikroTik untuk data koneksi aktif
    //         $query = new Query('/ppp/active/print');
    //         $activeConnections = $client->query($query)->read();

    //         // Ambil hanya username dari setiap koneksi dan simpan ke database
    //         foreach ($activeConnections as $connection) {
    //             if (isset($connection['name'])) {
    //                 // Simpan atau perbarui data ke database
    //                 $crot = ActiveMod::updateOrCreate(
    //                     [
    //                         'username' => $connection['name'], // Gunakan username dari koneksi
    //                         'unique_id' => auth()->user()->unique_id,
    //                         'ipmikrotik' => $ipmikrotik,
    //                         'site' => $data->site,
    //                     ]
    //                 );
    //             }
    //         }

    //         // Debug atau tampilkan hasil
    //         dd('Data berhasil disimpan atau diperbarui.');

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Data aktif koneksi berhasil diambil dan disimpan ke database.',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }

    // }
}
