<?php

namespace App\Http\Controllers;

use App\Models\OLT;
use RouterOS\Query;
use App\Models\Port;
use App\Models\User;
use RouterOS\Client;
use App\Models\Mikrotik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;
use GuzzleHttp\Exception\ClientException;

class OLTController extends Controller
{
    public function index(){
        $olts = OLT::where('unique_id', auth()->user()->unique_id)->get();
        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        $port = Port::where('unique_id', auth()->user()->unique_id)->orderBy('id', 'DESC')->get();
        $port2 = Port::where('unique_id', auth()->user()->unique_id)
        ->whereNotIn('status_pembelian', [1, 2])  // Filter berdasarkan status_pembelian
        ->distinct('pembelian_id')  // Ambil pembelian_id yang unik
        ->get(['pembelian_id']);  // Ambil hanya kolom pembelian_id
    
//dd($port2);  // Melihat apa yang ada di $port2
        return view('Dashboard.OLT.index', compact('olts', 'mikrotik', 'port', 'port2'));
    }
    public function tambaholt(Request $req)
{
    $ipolt = $req->input('ipolt');
    $portolt = $req->input('portolt');
    $site = $req->input('site');
    $sitemikrotik = $req->input('sitemikrotik');
    
    $unique_id = auth()->user()->unique_id;
    
    try {
        // Konfigurasi koneksi ke MikroTik
        $client = new Client([
            'host' => 'id-1.aqtnetwork.my.id',
            'user' => 'admin',
            'pass' => 'bakpao1922',
        ]);
        
        // Validasi input
        if (empty($ipolt) || empty($portolt) || empty($site)) {
            session()->flash('error', "IP OLT, Port OLT, atau Site tidak boleh kosong.");
            return redirect()->back();
        }
        
        // Mengecek apakah kombinasi IP OLT dan Port OLT sudah ada di database
        $oltExists = OLT::where('ipolt', $ipolt)
                        ->where('portolt', $portolt)
                        ->exists();
        
        if ($oltExists) {
            session()->flash('error', "Kombinasi IP OLT dan Port OLT sudah digunakan.");
            return redirect()->back();
        }
        
        // Mendapatkan semua port yang sudah digunakan dari database
        $usedPorts = OLT::pluck('portolt')->toArray();
        
        // Mulai dari port 3500
        $dstPort = 35000;
        
        // Cari port yang belum digunakan
        while (in_array($dstPort, $usedPorts)) {
            $dstPort++;
            if ($dstPort > 65535) {
                throw new \Exception("Tidak ada port tujuan yang tersedia.");
            }
        }
        
        // Mendapatkan data IP MikroTik berdasarkan site
        $ipmikrotik = Mikrotik::where('unique_id', $unique_id)->where('site', $sitemikrotik)->first();
        
        if (!$ipmikrotik) {
            session()->flash('error', "Mikrotik untuk site $sitemikrotik tidak ditemukan.");
            return redirect()->back();
        }

        $ipmk = $ipmikrotik->ipmikrotik;

        // Tentukan aturan NAT untuk IP OLT
        $natQueryOLT = new Query('/ip/firewall/nat/add');
        $natQueryOLT->equal('chain', 'dstnat')
                    ->equal('protocol', 'tcp')
                    ->equal('dst-port', $dstPort)
                    ->equal('dst-address-list', 'ip-public')
                    ->equal('action', 'dst-nat')
                    ->equal('to-addresses', $ipolt)
                    ->equal('to-ports', $portolt)
                    ->equal('comment', 'AQT_'. $site . '_OLT');
        
        $natResponseOLT = $client->query($natQueryOLT)->read();
        
        // Cek jika ada kesalahan dalam response NAT
        if (isset($natResponseOLT['!trap'])) {
            session()->flash('error', $natResponseOLT['!trap'][0]['message']);
            return redirect()->back();
        }
        
        // Tentukan routing untuk OLT
        $routeQuery = new Query('/ip/route/add');
        $routeQuery->equal('dst-address', $ipolt)
                   ->equal('gateway', $ipmk)
                   ->equal('comment', 'Routing_OLT_'. $site);
        
        $routeResponse = $client->query($routeQuery)->read();
        
        // Cek jika ada kesalahan dalam response route
        if (isset($routeResponse['!trap'])) {
            session()->flash('error', $routeResponse['!trap'][0]['message']);
            return redirect()->back();
        }
        
        // Menyimpan data ke database
        OLT::create([
            'unique_id' => $unique_id,
            'ipmikrotik' => $ipmk,
            'ipolt' => $ipolt,
            'portolt' => $dstPort, // Simpan dstPort yang baru di database
            'site' => $site,
        ]);
        
        session()->flash('success', "Konfigurasi OLT Berhasil Ditambahkan dengan port $dstPort!");
        return redirect()->back();
        
    } catch (ClientException $e) {
        session()->flash('error', "Gagal terhubung ke MikroTik: " . $e->getMessage());
        return redirect()->back();
    } catch (\Exception $e) {
        session()->flash('error', "Terjadi kesalahan: " . $e->getMessage());
        return redirect()->back();
    }
}

    public function aksesOLT(Request $request)
    {
        $ipolt = $request->query('ipolt');
        return view('Dashboard.OLT.olt-akses', compact('ipolt'));
    }
    public function hapusolt($id)
{
    try {
        // Konfigurasi koneksi ke MikroTik
        $client = new Client([
            'host' => 'id-1.aqtnetwork.my.id',
            'user' => 'admin',
            'pass' => 'bakpao1922',
        ]);

        // Cari data OLT di database berdasarkan ID
        $vpnData = OLT::find($id);

        if (!$vpnData) {
            session()->flash('error', "Data tidak ditemukan di database.");
            return redirect()->back();
        }

        // Cari aturan NAT di MikroTik berdasarkan dst-port dan to-addresses
        $findNatQuery = new Query('/ip/firewall/nat/print');
        $findNatQuery->where('dst-port', $vpnData->portolt)
                     ->where('to-addresses', $vpnData->ipolt);

        $natRules = $client->query($findNatQuery)->read();

        // Hapus aturan NAT jika ditemukan
        if (!empty($natRules)) {
            foreach ($natRules as $rule) {
                if (isset($rule['.id'])) {
                    $deleteNatQuery = new Query('/ip/firewall/nat/remove');
                    $deleteNatQuery->equal('.id', $rule['.id']);
                    $client->query($deleteNatQuery)->read();
                }
            }
        } else {
            session()->flash('warning', "Aturan NAT tidak ditemukan di MikroTik.");
        }

        // Cari dan hapus aturan route di MikroTik berdasarkan gateway
        $findRouteQuery = new Query('/ip/route/print');
        $findRouteQuery->where('gateway', $vpnData->ipmikrotik);

        $routes = $client->query($findRouteQuery)->read();

        if (!empty($routes)) {
            foreach ($routes as $route) {
                if (isset($route['.id'])) {
                    $deleteRouteQuery = new Query('/ip/route/remove');
                    $deleteRouteQuery->equal('.id', $route['.id']);
                    $client->query($deleteRouteQuery)->read();
                }
            }
        } else {
            session()->flash('warning', "Aturan route tidak ditemukan di MikroTik.");
        }

        // Hapus data dari database
        $vpnData->delete();

        session()->flash('success', "Data berhasil dihapus");
        return redirect()->back();
    } catch (ClientException $e) {
        session()->flash('error', "Gagal terhubung ke MikroTik: " . $e->getMessage());
        return redirect()->back();
    } catch (\Exception $e) {
        session()->flash('error', "Terjadi kesalahan: " . $e->getMessage());
        return redirect()->back();
    }
}
public function beli(Request $request)
{
    $namainput = $request->input('nama');
    $banyaknya = $request->input('banyaknya');

    // Generate unique ID for pembelian
    $pembelian_id = 'AQT-' . uniqid() . mt_rand(100, 9999);

    // Cek user berdasarkan unique_id
    $cek = User::where('unique_id', $namainput)->first();

    if (!$cek) {
        return redirect()->back()->with('error', 'Data Tidak Ada!');
    }

    // Jika user ditemukan, tampilkan halaman invoice
    $billed = $cek->name;
    $unique = $cek->unique_id;
    $email = $cek->email;

    return view('Dashboard.OLT.co', compact('namainput', 'banyaknya', 'pembelian_id', 'billed', 'unique', 'email'));
}

public function prosespembayaran(Request $request, $unique_id, $pembelian_id)
{
    // Validasi input
    $validated = $request->validate([
        'unique_id' => 'required|exists:users,unique_id',
        'pembelian_id' => 'required|string',
        'banyaknya' => 'required|integer|min:1',
    ]);
    $cek = User::where('unique_id', $unique_id)->first();
    // Ambil jumlah yang dibeli
    $banyaknya = $validated['banyaknya'];

    // Proses logika pembayaran di sini
    // Masukkan ke database satu per satu berdasarkan jumlah yang dibeli
    for ($i = 0; $i < $banyaknya; $i++) {
        Port::create([
            'nama' => $cek->name,
            'unique_id' => $validated['unique_id'],
            'pembelian_id' => $validated['pembelian_id'],
            'status_pembelian' => '0',
            'status_port' => '0',
            'port' => null,
            'bukti' => null
        ]);
    }

    // Redirect kembali dengan pesan sukses
    return redirect()->route('dataolt')->with('success', 'Pembayaran berhasil diproses dan data berhasil disimpan!');
}

public function bayar(Request $request)
{
    // Ambil parameter pembelian_id dari URL
    $pembelian_id = $request->query('pembelian_id');
    $dataPort = Port::where('unique_id', auth()->user()->unique_id)->where('pembelian_id', $pembelian_id)->get();
    $hitung = 10000*$dataPort->count();
    //dd($hitung);
    return view('Dashboard.OLT.bayar', compact('pembelian_id', 'hitung', 'dataPort')); // Ganti 'bayar' dengan nama view yang sesuai
}
public function submitPayment(Request $request)
{
    // Validasi file upload dan pembelian_id
    $request->validate([
        'paymentProof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // Max 10MB
        'pembelian_id' => 'required|exists:port,pembelian_id', // Validasi pembelian_id harus ada di tabel ports
    ]);

    // Proses upload file
    if ($request->hasFile('paymentProof')) {
        $file = $request->file('paymentProof');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Menyimpan file ke storage public
        $path = $file->move('payment_proofs', $fileName);

        // Ambil pembelian_id dari form
        $pembelian_id = $request->input('pembelian_id');

        // Update status untuk semua entri dengan pembelian_id yang sama
        $dataPort = Port::where('unique_id', auth()->user()->unique_id)->where('pembelian_id', $pembelian_id)->get();

        foreach ($dataPort as $port) {
            // Update status_pembelian dan status_port untuk setiap entri
            $port->status_pembelian = 2; // Update status_pembelian menjadi 1
            $port->status_port = 2; // Update status_port menjadi 1
            $port->save(); // Simpan perubahan ke database
        }

        // Menyimpan path file atau data lainnya ke database jika diperlukan
        // PaymentProof::create(['file_path' => $path]);

        // Menampilkan pesan sukses setelah upload
        return redirect()->route('dataolt')->with('success', 'Pembayaran berhasil diproses tunggu admin mengecek pembayaranmu');
    }

    // Menampilkan pesan error jika tidak ada file
    return redirect()->back()->with('error', 'Gagal mengirim bukti pembayaran!');
}


}
