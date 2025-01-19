<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OLT;
use RouterOS\Query;
use App\Models\Port;
use App\Models\User;
use RouterOS\Client;
use App\Models\Paket;
use App\Models\Invoice;
use Xendit\Configuration;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;
use App\Models\CoinTransaction;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\PaymentMethod\PaymentMethodApi;

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

    // public function processPayment($id)
    // {
    //     // Temukan data transaksi berdasarkan ID
    //     $data = CoinTransaction::findOrFail($id);

    //     // Generate UUID untuk transaksi
    //     $uuid = (string) Str::uuid();

    //     // Temukan user berdasarkan user_id dari data transaksi
    //     $user = User::find($data->user_id);

    //     // Pastikan data user ditemukan
    //     if (!$user) {
    //         return redirect()->back()->withErrors('User not found.');
    //     }

    //     // Ambil data user
    //     $nama = $user->name;
    //     $email = $user->email;

    //     // Set API Key Xendit
    //     Configuration::setXenditKey("xnd_development_89F4BMkA1W7GcAeU6vu5l07Dh7Y5Y15bXhxJICLGlk0SfeKPhdaGR78ejP0SAhF");

    //     // Siapkan data untuk invoice
    //     $create_invoice_request = new CreateInvoiceRequest([
    //         'external_id' => $uuid,
    //         'description' => 'Test Invoice',
    //         'amount' => $data->price,
    //         'currency' => 'IDR',
    //         'customer' => [
    //             'given_names' => $nama, // Menggunakan nama dari user
    //             'email' => $email, // Menggunakan email dari user
    //         ],
    //         'success_redirect_url' => route('home'), // URL sukses setelah pembayaran
    //         'failure_redirect_url' => route('home'), // URL gagal setelah pembayaran
    //     ]);

    //     try {
    //         // Buat invoice menggunakan Xendit API
    //         $apiInstance = new InvoiceApi();
    //         $invoice = $apiInstance->createInvoice($create_invoice_request);

    //         // Ambil metode pembayaran menggunakan Xendit Payment Methods API
    //         $payment_method_api = new PaymentMethodApi();

    //         // Dapatkan metode pembayaran untuk invoice yang telah dibuat
    //         $payment_methods = $payment_method_api->getPaymentMethods([
    //             'invoice_id' => $invoice->getInvoiceId(),  // ID invoice yang baru dibuat
    //         ]);

    //         // Dapatkan URL untuk metode pembayaran
    //         $payment_url = $payment_methods->getLinks()[0]->getUrl();

    //         // Redirect ke URL pembayaran Xendit
    //         return redirect($payment_url);
    //     } catch (\Xendit\XenditSdkException $e) {
    //         // Log error untuk debugging
    //         \Log::error('Error creating invoice: ' . $e->getMessage());

    //         // Tampilkan pesan kesalahan
    //         return redirect()->back()->withErrors('Failed to create invoice. Please try again.');
    //     }
    // }



    public function processPayment($id)
    {
        // Temukan data transaksi berdasarkan ID
        $data = CoinTransaction::findOrFail($id);

        // Generate UUID untuk transaksi
        $uuid = (string) Str::uuid();

        // Temukan user berdasarkan user_id dari data transaksi
        $user = User::find($data->user_id);

        // Pastikan data user ditemukan
        if (!$user) {
            return redirect()->back()->withErrors('User not found.');
        }

        // Ambil data user
        $nama = $user->name;
        $email = $user->email;

        // Set API Key Xendit
        Configuration::setXenditKey("xnd_development_89F4BMkA1W7GcAeU6vu5l07Dh7Y5Y15bXhxJICLGlk0SfeKPhdaGR78ejP0SAhF");
        $apiInstance = new InvoiceApi();

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $uuid,
            'description' => 'Nama : ' . $nama . ' Membeli Coin Dengan Harga ' . $data->price,
            'amount' => $data->price,
            'currency' => 'IDR',
            'customer' => [
                'given_names' => $nama,
                'email' => $email,
            ],
            'success_redirect_url' => route('payment.success', ['external_id' => $uuid]), // URL sukses setelah pembayaran
            'failure_redirect_url' => 'http://localhost:8000', // URL gagal setelah pembayaran
        ]);

        try {
            $invoice = $apiInstance->createInvoice($create_invoice_request);
            // dd($uuid, $invoice);
            $data->update([
                'status' => $invoice['status'],
                'external_id' => $uuid,
                'invoice_url' => $invoice['invoice_url']
            ]);


            // Redirect ke URL pembayaran Xendit
            return redirect($invoice['invoice_url']); // Gunakan URL pembayaran yang benar
        } catch (\Xendit\XenditSdkException $e) {
            // Log error untuk debugging
            \Log::error('Error creating invoice: ' . $e->getMessage());

            // Tampilkan pesan kesalahan
            return redirect()->back()->withErrors('Failed to create invoice. Please try again.');
        }
    }


    public function paymentSuccess(Request $req)
    {
        // Set API Key Xendit
        Configuration::setXenditKey("xnd_development_89F4BMkA1W7GcAeU6vu5l07Dh7Y5Y15bXhxJICLGlk0SfeKPhdaGR78ejP0SAhF");
        $apiInstance = new InvoiceApi();

        try {
            // Dapatkan invoice berdasarkan external_id
            $result = $apiInstance->getInvoices(null, $req->external_id);

            // Periksa status pembayaran
            $status = $result[0]['status'];
            //dd($status);
            // Temukan transaksi berdasarkan external_id
            $transaction = CoinTransaction::where('external_id', $req->external_id)->first();

            if ($transaction) {
                if ($status == 'SETTLED') {
                    // Jika pembayaran berhasil, perbarui status transaksi menjadi 'completed'
                    $transaction->update(['status' => 'complete']);

                    // Ambil user terkait transaksi
                    $user = User::find($transaction->user_id);

                    if ($user) {
                        // Tambahkan jumlah coin berdasarkan transaksi ke total_coin user
                        $user->update([
                            'total_coin' => $user->total_coin + $transaction->coin_amount,
                        ]);
                    }

                    // Tampilkan notifikasi sukses
                    return redirect()->route('coin.history')->with('success', 'Transaksi telah selesai dan berhasil.');
                } else {
                    // Jika status pembayaran tidak 'SETTLED', tampilkan pesan gagal
                    return redirect()->route('coin.history')->with('error', 'Transaksi tidak berhasil.');
                }
            } else {
                // Jika transaksi tidak ditemukan
                return redirect()->route('coin.history')->withErrors('Transaksi tidak ditemukan.');
            }
        } catch (\Xendit\XenditSdkException $e) {
            // Log error jika ada masalah dengan API Xendit
            \Log::error('Error fetching invoice: ' . $e->getMessage());

            return redirect()->route('coin.history')->withErrors('Terjadi kesalahan saat memverifikasi pembayaran.');
        }
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
            21,
            22,
            23,
            25,
            53,
            67,
            68,
            80,
            123,
            161,
            443,
            1723,
            3128,
            8080,
            8291,
            8728,
            8729,
            1194,
            500,
            4500
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
    public function perpanjangPaket($paket, $port, $unique_id)
    {
        // Ambil user berdasarkan unique_id
        $user = User::where('unique_id', $unique_id)->first();

        // Cek apakah user ditemukan
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Ambil data paket berdasarkan paket, port dan unique_id
        $data = Paket::where('paket', $paket)->where('port', $port)->where('unique_id', $unique_id)->first();
        $olts = OLT::where('unique_id', $unique_id)->where('portvpn', $port)->first();

        // Cek apakah data paket ditemukan
        if (!$data) {
            return redirect()->back()->with('error', 'Paket tidak ditemukan.');
        }

        // Logika untuk mengurangi atau menambah sisa coin user (misalnya mengurangi coin untuk perpanjangan)
        $sisaCoin = $user->total_coin; // Ambil total coin yang dimiliki user

        // Misalnya, kita mengurangi 10 coin untuk perpanjangan
        $coinPerpanjang = $data->coin;

        // Periksa apakah user memiliki cukup coin
        if ($sisaCoin < $coinPerpanjang) {
            return redirect()->back()->with('error', 'Saldo coin tidak cukup untuk perpanjangan.');
        }

        // Kurangi coin user
        $sisaCoin -= $coinPerpanjang;

        // Perpanjang masa berlaku paket
        if ($paket === 'bulan') {
            $data->expire = Carbon::now()->addMonth(); // Tambahkan 1 bulan ke expire
        } elseif ($paket === 'tahun') {
            $data->expire = Carbon::now()->addYear(); // Tambahkan 1 tahun ke expire
        }

        // Simpan perubahan pada data paket
        $data->save();

        // Update total coin user
        $user->total_coin = $sisaCoin;
        $user->save();

        // Enable kembali rule NAT di MikroTik setelah paket diperpanjang
        if ($data->expire !== null && Carbon::parse($data->expire)->isFuture()) {
            try {
                // Koneksi ke MikroTik
                $client = new Client([
                    'host' => 'id-1.aqtnetwork.my.id',
                    'user' => 'admin',
                    'pass' => 'bakpao1922',
                ]);

                // Cari NAT rule berdasarkan comment dan to-ports
                $query = new Query('/ip/firewall/nat/print');
                $query->where('comment', 'AQT_' . $olts->site . '_OLT')
                    ->where('to-ports', $port);
                $rules = $client->query($query)->read();

                foreach ($rules as $rule) {
                    // Enable rule jika paket sudah diperpanjang
                    $enableQuery = new Query('/ip/firewall/nat/set');
                    $enableQuery->equal('.id', $rule['.id'])
                        ->equal('disabled', 'no');
                    $client->query($enableQuery)->read();
                }
            } catch (\Exception $e) {
                // Tangani error jika koneksi MikroTik gagal
                session()->flash('error', 'Gagal mengaktifkan NAT: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Paket berhasil diperpanjang dan saldo coin terupdate. NAT rule diaktifkan kembali.');
    }
}
