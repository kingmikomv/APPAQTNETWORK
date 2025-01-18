<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Port;
use App\Models\User;
use App\Models\Paket;
use Illuminate\Http\Request;
use App\Models\CoinTransaction;

class CoinController extends Controller
{
    public function purchase(Request $request)
    {
        $request->validate([
            'coin_amount' => 'required|integer',
        ]);

        $coinAmount = $request->input('coin_amount');
        $priceList = [
            5 => 10500,
            10 => 21000,
            20 => 39500,
            50 => 97000,
            100 => 152500,
            200 => 295000,
        ];

        if (!isset($priceList[$coinAmount])) {
            return redirect()->back()->with('error', 'Jumlah coin tidak valid.');
        }

        // Simpan transaksi
        $transaction = CoinTransaction::create([
            'user_id' => auth()->id(),
            'coin_amount' => $coinAmount,
            'price' => $priceList[$coinAmount],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Transaksi coin berhasil dibuat.');
    }
    public function history()
    {
        $transactions = CoinTransaction::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        // In your controller method
        $totalPrice = $transactions->where('status', 'pending')->where('payment_proof', null)->sum('price'); // Sum all transaction prices

        return view('Dashboard/OLT/riwayat', compact('transactions', 'totalPrice'));
    }
    public function cancelTransaction($id)
    {
        $transaction = CoinTransaction::findOrFail($id);

        // Check if the transaction can be canceled
        if ($transaction->status === 'pending') {
            $transaction->status = 'canceled';  // Update the status to canceled
            $transaction->delete();

            return redirect()->route('coin.history')->with('success', 'Transaksi berhasil dibatalkan.');
        }

        return redirect()->route('coin.history')->with('error', 'Transaksi tidak dapat dibatalkan.');
    }
    public function processPayment($id)
    {

        $data = CoinTransaction::findOrFail($id);
        return view('Dashboard/OLT/proses', compact('data'));
    }
    public function bank($id)
    {
        $data = CoinTransaction::findOrFail($id);
        return view('Dashboard/OLT/bank', compact('data'));
    }
    public function upload(Request $request, $id)
    {

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $data = CoinTransaction::findOrFail($id);
        $file = $request->file('payment_proof');
        $rand = random_int(1, 1000000);
        $paymentDate = Carbon::now()->format('Ymd');
        $fileName = "{$rand}_{$paymentDate}_user{$data->user_id}_bukti_pembayaran." . $file->getClientOriginalExtension();

        $file->move(public_path('pembayaran'), $fileName);

        $data->payment_proof = $fileName;

        $data->update();

        return redirect()->route('coin.history')->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function beliPaket($paket)
{
    $user = auth()->user()->name;
    $coin = auth()->user()->total_coin;
    $unique_id = auth()->user()->unique_id;
    $paket = $paket;

    // Daftar port layanan Mikrotik yang harus dikecualikan
    $excludedPorts = [
        21, 22, 23, 25, 53, 67, 68, 80, 123, 161, 443, 1723,
        3128, 8080, 8291, 8728, 8729, 1194, 500, 4500
    ];

    // Validasi paket dan proses pembelian
    if ($paket == 'bulan') {
        if ($coin < 20) {
            return redirect()->route('dataolt')->with('error', 'Coin tidak cukup.');
        }

        $sisaCoin = $coin - 20;

        do {
            $newPort = rand(6300, 65535);
            $isPortExists = Paket::where('port', $newPort)->exists();
        } while ($isPortExists || in_array($newPort, $excludedPorts));

        Paket::create([
            'nama' => $user,
            'coin' => 20,
            'unique_id' => $unique_id,
            'paket' => $paket,
            'port' => $newPort,
        ]);

        User::where('unique_id', $unique_id)->update([
            'total_coin' => $sisaCoin,
        ]);

        return redirect()->back()->with('success', 'Paket berhasil dibeli.');
    } elseif ($paket == 'tahun') {
        if ($coin < 60) {
            return redirect()->route('dataolt')->with('error', 'Coin tidak cukup.');
        }

        $sisaCoin = $coin - 60;

        do {
            $newPort = rand(6300, 65535);
            $isPortExists = Paket::where('port', $newPort)->exists();
        } while ($isPortExists || in_array($newPort, $excludedPorts));

        Paket::create([
            'nama' => $user,
            'coin' => 60,
            'unique_id' => $unique_id,
            'paket' => $paket,
            'port' => $newPort,
        ]);

        User::where('unique_id', $unique_id)->update([
            'total_coin' => $sisaCoin,
        ]);

        return redirect()->back()->with('success', 'Paket berhasil dibeli.');
    } elseif ($paket == 'permanen') {
        if ($coin < 250) {
            return redirect()->route('dataolt')->with('error', 'Coin tidak cukup.');
        }

        $sisaCoin = $coin - 250;

        do {
            $newPort = rand(6300, 65535);
            $isPortExists = Paket::where('port', $newPort)->exists();
        } while ($isPortExists || in_array($newPort, $excludedPorts));

        Paket::create([
            'nama' => $user,
            'coin' => 250,
            'unique_id' => $unique_id,
            'paket' => $paket,
            'port' => $newPort,
        ]);

        User::where('unique_id', $unique_id)->update([
            'total_coin' => $sisaCoin,
        ]);

        return redirect()->back()->with('success', 'Paket berhasil dibeli.');
    } else {
        return redirect()->back()->with('error', 'Paket tidak valid.');
    }
}

}
