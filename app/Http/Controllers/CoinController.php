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
use Barryvdh\DomPDF\Facade\Pdf;
use Xendit\Configuration;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Xendit\Invoice\InvoiceApi;
use Xendit\XenditSdkException;
use App\Models\CoinTransaction;
use Illuminate\Support\Facades\Log;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\PaymentMethod\PaymentMethodApi;

class CoinController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $transactions = CoinTransaction::where('user_id', $userId)
            ->whereIn('status',['complete', 'COMPLETE', 'canceled', 'CANCELED'])
            ->orderBy('created_at', 'desc')
            ->get();

        $transactionsPending = CoinTransaction::where('user_id', $userId)
            ->whereIn('status', ['pending', 'PENDING'])
            ->orderBy('created_at', 'desc')
            ->get();

        $transactionPort = Paket::where('unique_id', auth()->user()->unique_id)->get();

        $totalPrice = $transactionsPending->where('payment_proof', null)->sum('price'); // Hanya untuk pending transaksi tanpa payment proof
        return view('Dashboard/SHOP/index', compact('transactions', 'transactionsPending', 'totalPrice', 'transactionPort'));
    }

    public function purchase(Request $request)
    {
        // Validasi input
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

        // Validasi apakah jumlah coin ada di daftar harga
        if (!isset($priceList[$coinAmount])) {
            return redirect()->back()->with('error', 'Jumlah coin tidak valid.');
        }

        try {
            // Pesan WhatsApp
            $message = sprintf(
                "Halo %s,\n\nTerima kasih telah melakukan pembelian coin di platform kami. Berikut detail transaksi Anda:\n\n💰 *Jumlah Coin*: %d Coin\n💳 *Total Pembayaran*: Rp %s\n📅 *Tanggal Pembelian*: %s\n🔄 *Status Transaksi*: %s\n\nSilakan lakukan pembayaran untuk melanjutkan proses. Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.\n\nSalam hangat,\nTim AQT Network",
                auth()->user()->name ?? 'Pelanggan', // Nama pengguna jika tersedia
                $coinAmount,
                number_format($priceList[$coinAmount], 0, ',', '.'),
                now()->format('d M Y H:i'), // Menampilkan tanggal dan waktu pembelian
                'Pending' // Status transaksi, Anda bisa menyesuaikan jika sudah ada status lain
            );
            
            

            // Menggunakan Guzzle untuk menggantikan cURL
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.fonnte.com/send', [
                'headers' => [
                    'Authorization' => 'g3ZXCoCHeR1y75j4xJoz', // Ganti dengan token Anda
                ],
                'form_params' => [
                    'target' => auth()->user()->telefon,
                    'message' => $message,
                    'countryCode' => '62', // Optional
                ],
            ]);

            // Periksa respons dari API Fonnte
            $responseBody = json_decode($response->getBody(), true);
            //dd($responseBody);
            // Tambahkan validasi respons (opsional)
            if (!isset($responseBody['status']) || $responseBody['status'] != 'success') {
                return redirect()->back()->with('error', 'Gagal mengirim notifikasi WhatsApp.');
            }

            // Simpan transaksi ke database
            $transaction = CoinTransaction::create([
                'user_id' => auth()->id(),
                'coin_amount' => $coinAmount,
                'price' => $priceList[$coinAmount],
                'status' => 'pending',
            ]);

            // Redirect dengan pesan sukses
            return redirect()->route('shop')->with('success', 'Transaksi coin berhasil dibuat.');
        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $userId = auth()->id();

        $transactions = CoinTransaction::where('user_id', $userId)
            ->whereIn('status',['complete', 'COMPLETE', 'canceled', 'CANCELED'])
            ->orderBy('created_at', 'desc')
            ->get();

        $transactionsPending = CoinTransaction::where('user_id', $userId)
            ->whereIn('status', ['pending', 'PENDING'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPrice = $transactionsPending->where('payment_proof', null)->sum('price'); // Hanya untuk pending transaksi tanpa payment proof

        return view('Dashboard/OLT/riwayat', compact('transactions', 'transactionsPending', 'totalPrice'));
    }

    public function cancelPayment($id)
    {
        $transaction = CoinTransaction::findOrFail($id);
        //dd($transaction);
        // Check if the transaction can be canceled
        if ($transaction->status == 'pending' || $transaction->status == 'PENDING') {
            $transaction->status = 'canceled';  // Update the status to canceled
            $transaction->save();  // Save the changes

            return redirect()->route('coin.history')->with('success', 'Transaksi berhasil dibatalkan.');
        }

        return redirect()->route('coin.history')->with('error', 'Transaksi tidak dapat dibatalkan.');
    }

    public function processPayment($id)
    {
        // Temukan data transaksi berdasarkan ID
        $data = CoinTransaction::findOrFail($id);

        // Generate UUID untuk transaksi
        $date = date('Ymd'); // Format tanggal: TahunBulanHari
        $uuid = $date . '-' . substr((string) Str::uuid(), 0, 8); // Ambil 8 karakter pertama dari UUID
        
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
        Configuration::setXenditKey("xnd_development_pvO9UBoEWcj1zWGdIVBmO0CLkiPGgIIZEHXfHL42EZIy5TA3CtuaXp39AUzB55xC");
        $apiInstance = new InvoiceApi();

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $uuid,
            'description' => 'Nama : ' . $nama . ' Membeli Coin Dengan Harga ' . $data->price,
            'amount' => $data->price,
            'currency' => 'IDR',
            'customer' => [
                'given_names' => $nama,
                'payer_email' => $email,
            ],
            "success_redirect_url" => route('shop'),
            "failure_redirect_url" => route('shop'),

        ]);

        try {
            $invoice = $apiInstance->createInvoice($create_invoice_request);

            if (isset($invoice['invoice_url'])) {
                $data->update([
                    'status' => $invoice['status'],
                    'external_id' => $uuid,
                    'invoice_url' => $invoice['invoice_url']
                ]);
            }
            return redirect($invoice['invoice_url']);

            // Redirect ke URL pembayaran Xendit
        } catch (\Xendit\XenditSdkException $e) {
            // Log error untuk debugging

            // Tampilkan pesan kesalahan
            return redirect()->back()->withErrors('Failed to create invoice. Please try again.');
        }
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        //dd($data);
        // Validasi status dari Xendit
        $statusJson = strtolower($data['status'] ?? ''); // Pastikan 'status' ada di data
        if ($statusJson !== 'paid') {
            return response()->json(['message' => 'Pembayaran Di Pending.'], 400);
        }
    
        $external_id = $data['external_id'];
        $paid_at = $data['paid_at'];
        $payment_method = $data['payment_method'];
        $payment_channel = $data['payment_channel'];
    
        // Menemukan order berdasarkan external_id
        $order = CoinTransaction::where('external_id', $external_id)->first();
    
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }
    
        // Update jumlah coin untuk pengguna
        $coin = User::find($order->user_id);
        $sisaCoin = $coin->update([
            'total_coin' => $coin->total_coin + $order->coin_amount,
        ]);
    
        // Memperbarui status order
        $order->status = 'complete';
        $order->paid_at = $paid_at;
    
        // Menangani jenis metode pembayaran
        $nn = '';
        switch ($payment_method) {
            case 'BANK_TRANSFER':
                $order->payment_method = 'BANK_TRANSFER';
                $order->payment_channel = $payment_channel;
                $nn = 'Bank Transfer';
                break;
    
            case 'EWALLET':
                $order->payment_method = 'EWALLET';
                $order->payment_channel = $payment_channel;
                if (isset($data['ewallet_type'])) {
                    $order->ewallet_type = $data['ewallet_type'];
                }
                $nn = 'E-Wallet';
                break;
    
            case 'RETAIL_OUTLET':
                $order->payment_method = 'RETAIL_OUTLET';
                $order->payment_channel = $payment_channel;
                $nn = 'Retail Outlet';
                break;
    
            case 'QR_CODE':
                $order->payment_method = 'QR_CODE';
                $order->payment_channel = $payment_channel;
                if (isset($data['payment_details']['source'])) {
                    $order->payment_source = $data['payment_details']['source'];
                }
                $nn = 'QR Code';
                break;
    
            default:
                return response()->json(['message' => 'Unknown payment method.'], 400);
        }
    
        // Update order data
        $order->save();
    
        // Persiapan pesan WhatsApp
        $message = sprintf(
            "Halo %s,\n\nTerima kasih telah melakukan pembelian coin di platform kami. Berikut detail transaksi Anda:\n\n💰 *Jumlah Coin*: %d Coin\n💳 *Total Pembayaran*: Rp %s\n📅 *Tanggal Pembelian*: %s\n🔄 *Status Transaksi*: %s\n💵 *Pembayaran Dilakukan Menggunakan*: %s By %s sebesar Rp %s\n\nPembayaran Anda telah berhasil kami terima. Terima kasih telah melakukan transaksi dengan kami.\n\nJika Anda memiliki pertanyaan atau butuh bantuan lebih lanjut, jangan ragu untuk menghubungi kami.\n\nSalam hangat,\nTim AQT Network",
            $coin->name ?? 'Pelanggan',
            $order->coin_amount,
            number_format($order->price, 0, ',', '.'),
            now()->format('d M Y H:i'),
            'Sukses',
            $nn,
            $payment_channel,
            number_format($order->price, 0, ',', '.')
        );
    
        // Menggunakan Guzzle untuk mengirim pesan WhatsApp
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.fonnte.com/send', [
            'headers' => [
                'Authorization' => 'g3ZXCoCHeR1y75j4xJoz', // Ganti dengan token Anda
            ],
            'form_params' => [
                'target' => $coin->telefon,
                'message' => $message,
                'countryCode' => '62', // Optional
            ],
        ]);
    
        // Periksa respons dari API Fonnte
        $responseBody = json_decode($response->getBody(), true);
    
        if (!isset($responseBody['status']) || $responseBody['status'] != 'success') {
            return response()->json(['message' => 'Gagal mengirim notifikasi WhatsApp.'], 500);
        }
    
        return response()->json(['message' => 'Webhook handled successfully.'], 200);
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
            if ($coin < 200) {
                return redirect()->route('dataolt')->with('error', 'Coin tidak cukup.');
            }

            $sisaCoin = $coin - 200;

            do {
                $newPort = rand(6300, 65535);
                $isPortExists = Paket::where('port', $newPort)->exists();
            } while ($isPortExists || in_array($newPort, $excludedPorts));

            Paket::create([
                'nama' => $user,
                'coin' => 200,
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
                session()->flash('error', 'Oopss... Ada yang error : ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Paket berhasil diperpanjang dan saldo coin terupdate');
    }
    public function generatePDF($external_id)
    {
        // Ambil data transaksi berdasarkan external_id
        $transaction = CoinTransaction::where('external_id', $external_id)->first();
        $user = User::find($transaction->user_id);
        // Jika transaksi tidak ditemukan, tampilkan halaman 404
        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        // Data yang akan ditampilkan di view PDF
        $data = [
            'transaction' => $transaction,
            'user' => $user->name,
            'email' => $user->email,
        ];

        // Load view dengan data
        $pdf = Pdf::loadView('Dashboard.SHOP.pdf', $data)->setPaper('a4', 'portrait');

        // Unduh atau tampilkan PDF
        return $pdf->stream('Invoice_Transaksi_' . $transaction->external_id . '.pdf');
    }
    public function generateReport(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Query transactions based on the selected date range
    $transactions = CoinTransaction::whereBetween('paid_at', [$startDate, $endDate])
        ->orderBy('paid_at', 'desc')
        ->get();
        return view('Dashboard/SHOP/laporan', compact('transactions'));
    // Generate report logic (e.g., generate a PDF)
    // Example: return view('report', compact('transactions'));
}

    
}
