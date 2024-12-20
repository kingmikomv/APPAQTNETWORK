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
    public function index()
{
    $members = User::get();

    foreach ($members as $member) {
        $member->vpn = VPN::where('unique_id', $member->unique_id)->count();
        $member->mikrotik = Mikrotik::where('unique_id', $member->unique_id)->count();
    }

    // Ambil data dengan pembelian_id yang unik dan hitung total per ID
    $port2 = Port::select('pembelian_id', 'created_at', 'status_pembelian', 'bukti')
        ->get()
        ->groupBy('pembelian_id')
        ->map(function ($group) {
            return [
                'pembelian_id' => $group->first()->pembelian_id,
                'created_at' => $group->first()->created_at,
                'status_pembelian' => $group->first()->status_pembelian,
                'bukti' => $group->first()->bukti,
                'total_count' => $group->count(), // Hitung jumlah pembelian_id yang sama
                'total_price' => $group->count() * 10000, // Kalkulasi total harga
            ];
        });

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
// public function acc(Request $request, $pembelianId)
// {
//     // Cari pembelian berdasarkan ID
//     $port = Port::where('pembelian_id', $pembelianId)->first();
//     if (!$port) {
//         return response()->json(['success' => false, 'message' => 'Data pembelian tidak ditemukan!'], 404);
//     }

//     // Perbarui status pembelian
//     $port->status_pembelian = 3; // Status ACC
//     $port->save();

//     // Generate port MikroTik dimulai dari 6300
//     do {
//         $newPort = rand(6300, 65535); // Port diacak
//         $isPortExists = Port::where('port', $newPort)->exists();
//     } while ($isPortExists);

//     // Simpan port MikroTik ke database
//     $port->port = $newPort;
//     $port->save();

//     return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui dan port MikroTik telah dibuat!', 'port' => $newPort]);
// }
public function acc(Request $request, $pembelianId)
{
    // Cari semua pembelian dengan pembelian_id yang sama
    $ports = Port::where('pembelian_id', $pembelianId)->get();

    if ($ports->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Data pembelian tidak ditemukan!'], 404);
    }

    // Inisialisasi array untuk menyimpan port yang dibuat
    $generatedPorts = [];

    foreach ($ports as $port) {
        // Perbarui status pembelian
        $port->status_pembelian = 3; // Status ACC
        $port->status_port = 1;

        // Generate port MikroTik dimulai dari 6300
        do {
            $newPort = rand(49152, 65535); // Port diacak
            $isPortExists = Port::where('port', $newPort)->exists();
        } while ($isPortExists);

        // Simpan port MikroTik ke database
        $port->port = $newPort;
        $port->save();

        // Tambahkan port ke array hasil
        $generatedPorts[] = $newPort;
    }

    return response()->json([
        'success' => true,
        'message' => 'Status berhasil diperbarui dan port MikroTik telah dibuat!',
        'ports' => $generatedPorts // Kembalikan semua port yang dibuat
    ]);
}

}
