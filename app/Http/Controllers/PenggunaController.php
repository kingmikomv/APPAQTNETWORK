<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Query;
use App\Models\Port;
use App\Models\User;
use RouterOS\Client;
use App\Models\Mikrotik;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(){
        $members = User::get();

        foreach ($members as $member) {
            $member->vpn = VPN::where('unique_id', $member->unique_id)->count();
            $member->mikrotik = Mikrotik::where('unique_id', $member->unique_id)->count();
        }

        $port2 = Port::distinct('pembelian_id')  // Ambil pembelian_id yang unik
        ->get(['pembelian_id']);  // Ambil hanya kolom pembelian_id
        
        //dd($port2);
        return view('Dashboard/PENGGUNA/MEMBER/index', compact('members', 'port2'));
    }
    public function daftarvpn(Request $request){
        $unique_uid = $request->unique_id;
        $dataVPN = VPN::where('unique_id', $unique_uid)->get();
        return view('Dashboard/PENGGUNA/MEMBER/VPN/daftarvpn', compact('dataVPN', 'unique_uid'));

    }
    public function daftarmikrotik(Request $request){
        $unique_uid = $request->unique_id;
        $dataMikrotik = Mikrotik::where('unique_id', $unique_uid)->get();
        return view('Dashboard/PENGGUNA/MEMBER/MIKROTIK/daftarmikrotik', compact('dataMikrotik'));

    }
    public function toggleVPN(Request $request)
{
    // Validasi input
    $request->validate([
        'ipaddr' => 'required|ip',
        'disabled' => 'required|in:yes,no',
    ]);

    $ipAddr = $request->input('ipaddr');
    $disabled = $request->input('disabled');

    try {
        // Konfigurasi koneksi MikroTik
        $client = new Client([
            'host' => 'id-1.aqtnetwork.my.id',
            'user' => 'admin',
            'pass' => 'bakpao1922',
        ]);

        // Cari PPP Secret berdasarkan IP address
        $querySecret = new Query('/ppp/secret/print');
        $querySecret->where('remote-address', $ipAddr);

        $responseSecret = $client->query($querySecret)->read();

        if (empty($responseSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'PPP Secret dengan IP address tersebut tidak ditemukan.',
            ]);
        }

        // Ambil ID PPP Secret
        $vpnId = $responseSecret[0]['.id'];

        // Update status enable/disable pada PPP Secret
        $updateSecret = new Query('/ppp/secret/set');
        $updateSecret->equal('.id', $vpnId);
        $updateSecret->equal('disabled', $disabled === 'yes' ? 'true' : 'false');

        $client->query($updateSecret)->read();

        // Jika status adalah 'disable', hapus active connection berdasarkan IP address
        if ($disabled === 'yes') {
            $queryActive = new Query('/ppp/active/print');
            $queryActive->where('address', $ipAddr);

            $responseActive = $client->query($queryActive)->read();

            if (!empty($responseActive)) {
                foreach ($responseActive as $active) {
                    // Hapus active connection
                    $removeActive = new Query('/ppp/active/remove');
                    $removeActive->equal('.id', $active['.id']);
                    $client->query($removeActive)->read();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $disabled === 'yes'
                ? 'VPN berhasil dinonaktifkan dan koneksi aktif dihapus.'
                : 'VPN berhasil diaktifkan.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ]);
    }
}


}
