<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Query;
use App\Models\Port;
use App\Models\User;
use RouterOS\Client;
use App\Models\Paket;
use App\Models\Mikrotik;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CoinTransaction;

class PenggunaController extends Controller
{
    public function index()
    {
        $members = User::get();

        foreach ($members as $member) {
            $member->vpn = VPN::where('unique_id', $member->unique_id)->count();
            $member->mikrotik = Mikrotik::where('unique_id', $member->unique_id)->count();
        }

//dd($summary);
        return view('Dashboard/PENGGUNA/MEMBER/index', compact('members'));
    }


    public function sendCoin(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'coin_amount' => 'required|integer|min:1',
    ]);

    // Fetch the recipient user
    $user = User::find($validated['user_id']);

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    // Update the user's coin amount
    $user->total_coin += $validated['coin_amount'];
    $user->save();

    return back()->with('success', "Successfully sent {$validated['coin_amount']} coins to {$user->name}.");
}


    public function daftarvpn(Request $request)
    {
        $unique_uid = $request->unique_id;
        $dataVPN = VPN::where('unique_id', $unique_uid)->get();
        return view('Dashboard/PENGGUNA/MEMBER/VPN/daftarvpn', compact('dataVPN', 'unique_uid'));
    }
    public function daftarmikrotik(Request $request)
    {
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
    public function acc(Request $request, $id)
    {
       $datacoin = CoinTransaction::findOrFail($id);
        $datacoin->status = 'complete';
        $datacoin->update();

        $user = User::findOrFail($datacoin->user_id);
        $user->total_coin += $datacoin->coin_amount;
        $user->update();

        return redirect()->route('coin.history')->with('success', 'Transaksi berhasil diproses.');


    }
    public function transaksiCoin()
    {
        // Mengambil semua transaksi dengan status 'complete'
        $transactions = CoinTransaction::orderBy('id', 'desc')->get();
    
        // Variabel untuk menyimpan total pemasukan
        $pemasukan = 0;
    
        // Menentukan bulan dan tahun saat ini
        $currentMonthYear = Carbon::now()->format('m-Y');
    
        // Melakukan iterasi untuk setiap transaksi
        foreach ($transactions as $transaction) {
            // Pastikan 'paid_at' tidak null dan merupakan tanggal yang valid
            if ($transaction->paid_at && Carbon::hasFormat($transaction->paid_at, 'Y-m-d\TH:i:s.u\Z')) {
                // Memeriksa apakah bulan dan tahun transaksi sama dengan bulan dan tahun saat ini
                if (Carbon::parse($transaction->paid_at)->setTimezone('Asia/Jakarta')->format('m-Y') == $currentMonthYear) {
                    // Menambahkan harga transaksi ke total pemasukan
                    $pemasukan += $transaction->price;
                }
            }
        }
    
        // Menampilkan pemasukan untuk debugging
        //dd($pemasukan);
    
        // Return ke tampilan jika perlu
        return view('Dashboard/PENGGUNA/MEMBER/TRANSAKSI/transaksiCoin', compact('transactions', 'pemasukan')); 
    }
  

}
