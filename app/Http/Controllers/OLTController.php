<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OLT;
use App\Models\VPN;
use RouterOS\Query;
use App\Models\Port;
use App\Models\User;
use RouterOS\Client;
use App\Models\Paket;
use App\Models\VPNOLT;
use App\Models\Mikrotik;
use Illuminate\Http\Request;
use App\Models\CoinTransaction;
use GuzzleHttp\Exception\ClientException;

class OLTController extends Controller
{
    public function index()
    {
        $coin = CoinTransaction::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->get();

        $datavpn = VPN::where('unique_id', auth()->user()->unique_id)->get();
        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        $uniqueId = auth()->user()->unique_id;

        // Ambil semua OLT
        $olts = OLT::where('unique_id', $uniqueId)->get();

        // Map data OLT dan tambahkan data Paket
        $olts = $olts->map(function ($oltp) {
            $paket = Paket::where('port', $oltp->portvpn)->first();
            $oltp->paket = $paket ? $paket->paket : 'Tidak Ditemukan';
            $oltp->expire = $paket ? $paket->expire : 'Tidak Ditemukan';
            $oltp->coin = $paket ? $paket->coin : 'Tidak Ditemukan';

            return $oltp; // Return as the original Eloquent object
        });

        // Ambil semua port dari Paket
        $allPorts = Paket::where('unique_id', $uniqueId)->pluck('port')->toArray();

        // Ambil semua port yang sudah digunakan di OLT
        $usedPorts = OLT::where('unique_id', $uniqueId)->pluck('portvpn')->toArray();

        // Hitung port yang belum digunakan
        $availablePorts = array_diff($allPorts, $usedPorts);




        foreach ($olts as $olt) {
            // Cek apakah tanggal expire sudah lewat
            if ($olt->expire !== null && Carbon::parse($olt->expire)->isPast()) {
                try {
                    // Koneksi ke MikroTik
                    $client = new Client([
                        'host' => 'id-1.aqtnetwork.my.id',
                        'user' => 'admin',
                        'pass' => 'bakpao1922',
                    ]);

                    // Cari NAT rule berdasarkan comment dan to-ports
                    $query = new Query('/ip/firewall/nat/print');
                    $query->where('comment', 'AQT_' . $olt->site . '_OLT')
                        ->where('to-ports', $olt->portvpn);
                    $rules = $client->query($query)->read();

                    foreach ($rules as $rule) {
                        // Disable rule jika expired
                        $disableQuery = new Query('/ip/firewall/nat/set');
                        $disableQuery->equal('.id', $rule['.id'])
                            ->equal('disabled', 'yes');
                        $client->query($disableQuery)->read();
                    }
                } catch (\Exception $e) {
                    // Tangani error jika koneksi MikroTik gagal
                    session()->flash('error', 'Gagal menonaktifkan NAT: ' . $e->getMessage());
                }
            }
        }



        return view('Dashboard.OLT.index', compact('datavpn', 'mikrotik', 'coin', 'availablePorts', 'olts'));
    }

    public function tambaholt(Request $req)
    {
        $ipolt = $req->input('ipolt');
        $portolt = $req->input('portolt');
        $site = $req->input('site');
        $ipvpn = $req->input('ipvpn');
        $portvpn = $req->input('portvpn');

        $unique_id = auth()->user()->unique_id;

        //dd($site, $ipolt ,$ipolt, $ipvpn, $portvpn);

        try {
            // Konfigurasi koneksi ke MikroTik
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id',
                'user' => 'admin',
                'pass' => 'bakpao1922',
            ]);
            // $CEKOLTIP = OLT::where('unique_id', $unique_id)
            //        ->where(function($query) use ($ipvpn, $portvpn) {
            //            $query->Where('portvpn', $portvpn);
            //        })
            //        ->exists();

            // if ($CEKOLTIP) {
            //     session()->flash('error', "IP OLT atau Port VPN sudah ada");
            //     return redirect()->back();
            // }
            // Validasi input
            if (empty($ipolt) || empty($portolt) || empty($site) || empty($ipvpn) || empty($portvpn)) {
                session()->flash('error', "IP OLT, Port OLT, atau Site tidak boleh kosong.");
                return redirect()->back();
            }

            // Mendapatkan data IP MikroTik berdasarkan site
            $ipmikrotik = VPN::where('unique_id', $unique_id)->where('ipaddress', $ipvpn)->first();

            if (!$ipmikrotik) {
                session()->flash('error', "IP VPN $ipvpn tidak ditemukan.");
                return redirect()->back();
            }


            // Tentukan aturan NAT untuk IP OLT
            $natQueryOLT = new Query('/ip/firewall/nat/add');
            $natQueryOLT->equal('chain', 'dstnat')
                ->equal('protocol', 'tcp')
                ->equal('dst-port', $portvpn)
                ->equal('dst-address-list', 'ip-public')
                ->equal('action', 'dst-nat')
                ->equal('to-addresses', $ipmikrotik->ipaddress)
                ->equal('to-ports', $portvpn)
                ->equal('comment', 'AQT_' . $site . '_OLT');

            $natResponseOLT = $client->query($natQueryOLT)->read();

            // Cek jika ada kesalahan dalam response NAT
            if (isset($natResponseOLT['!trap'])) {
                session()->flash('error', $natResponseOLT['!trap'][0]['message']);
                return redirect()->back();
            }

            // Menyimpan data ke database
            OLT::create([
                'unique_id' => $unique_id,
                'ipolt' => $ipolt,
                'portolt' => $portolt, // Simpan dstPort yang baru di database
                'ipvpn' => $ipvpn, // Simpan dstPort yang baru di database
                'portvpn' => $portvpn, // Simpan dstPort yang baru di database

                'site' => $site,
            ]);

            $paket = Paket::where('port', $portvpn)->first();
            if ($paket) {
                // Default durasi adalah 1 bulan
                $expireDate = $paket->created_at->addMonth();

                // Jika durasi adalah "tahunan" (berdasarkan $paket->durasi)
                if (isset($paket->paket)) {
                    if (strtolower($paket->paket) === 'tahun') {
                        $expireDate = $paket->created_at->addYear();
                    } elseif (strtolower($paket->paket) === 'bulan') {
                        $expireDate = $paket->created_at->addMonth();
                    } elseif (strtolower($paket->paket) === 'permanen') {
                        $expireDate = null;
                    }
                }

                //dd($expireDate);
                // // Update expire di database
                $paket->expire = $expireDate;
                $paket->save();
            }
            session()->flash('success', "Konfigurasi OLT Berhasil Ditambahkan !");
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

            // Cari aturan NAT di MikroTik berdasarkan 'to-addresses' yang sesuai dengan ipolt
            $findNatQuery = new Query('/ip/firewall/nat/print');
            $findNatQuery->where('dst-port', $vpnData->portvpn);

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
                session()->flash('success', "Aturan NAT di MikroTik berhasil dihapus.");
            } else {
                session()->flash('warning', "Aturan NAT tidak ditemukan di MikroTik.");
            }

            // Hapus data dari database
            $vpnData->delete();

            session()->flash('success', "Data berhasil dihapus dari database.");
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
        //dd($banyaknya);
        // Proses logika pembayaran di sini
        // Masukkan ke database satu per satu berdasarkan jumlah yang dibeli

        if ($banyaknya == 1) {
            Port::create([
                'nama' => $cek->name,
                'unique_id' => $validated['unique_id'],
                'pembelian_id' => $validated['pembelian_id'],
                'status_pembelian' => '0',
                'status_port' => '0',
                'port' => null,
                'bukti' => null,
                'banyaknya' => $banyaknya,
            ]);
        } else {
            for ($i = 0; $i < $banyaknya; $i++) {

                Port::create([
                    'nama' => $cek->name,
                    'unique_id' => $validated['unique_id'],
                    'pembelian_id' => $validated['pembelian_id'],
                    'status_pembelian' => '0',
                    'status_port' => '0',
                    'port' => null,
                    'bukti' => null,
                    'banyaknya' => $banyaknya,

                ]);
            }
        }


        // Redirect kembali dengan pesan sukses
        return redirect()->route('dataolt')->with('success', 'Pembayaran berhasil diproses dan data berhasil disimpan!');
    }

    public function bayar(Request $request)
    {
        // Ambil parameter pembelian_id dari URL
        $pembelian_id = $request->query('pembelian_id');
        $dataPort = Port::where('unique_id', auth()->user()->unique_id)->where('pembelian_id', $pembelian_id)->get();
        $hitung = 10000 * $dataPort->count();
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
            $fileName = time() . '_' . auth()->user()->unique_id . "_" . $request->input('pembelian_id') . "_" . $file->getClientOriginalExtension();

            // Menyimpan file ke storage public
            $path = $file->move('payment_proofs', $fileName);

            // Ambil pembelian_id dari form
            $pembelian_id = $request->input('pembelian_id');

            // Update status untuk semua entri dengan pembelian_id yang sama
            $dataPort = Port::where('unique_id', auth()->user()->unique_id)->where('pembelian_id', $pembelian_id)->get();

            foreach ($dataPort as $port) {
                // Update status_pembelian dan status_port untuk setiap entri
                $port->bukti = $fileName;
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





    public function uploadvpnolt(Request $req)
    {
        // Validasi input
        $validated = $req->validate([
            'namaakun' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'namaakun.required' => 'Nama akun harus diisi.',
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);
        $unique = auth()->user()->unique_id;
        $namaakun = $req->input('namaakun');
        $username = $req->input('username');
        $password = $req->input('password');

        $akuncomment = "AQT_OLTVPN_" . $namaakun . "_@_" . auth()->user()->name . "_" . date('H:i:s');

        try {
            // Konfigurasi koneksi ke MikroTik
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id',
                'user' => 'admin',
                'pass' => 'bakpao1922',
            ]);

            // Mengambil semua PPP secrets untuk memeriksa username yang sudah ada
            $queryAllSecrets = new Query('/ppp/secret/print');
            $response = $client->query($queryAllSecrets)->read();

            // Cek apakah username sudah ada
            $existingUsernames = array_column($response, 'name');

            if (in_array($username, $existingUsernames)) {
                session()->flash('error', 'Username sudah ada, silakan gunakan username lain.');
                return redirect()->back();
            }

            // Oktet yang tetap
            $firstOctet = '176';
            $secondOctet = 20;

            // Ambil daftar thirdOctets yang sudah digunakan
            $usedThirdOctets = array_map(function ($secret) {
                return explode('.', $secret['local-address'])[2];
            }, $response);

            // Tentukan thirdOctet yang baru
            $thirdOctetBase = 11;
            $thirdOctet = $thirdOctetBase;
            while (in_array($thirdOctet, $usedThirdOctets)) {
                $thirdOctet++;
                if ($thirdOctet > 254) {
                    throw new \Exception("Tidak ada third octet yang tersedia untuk IP addresses.");
                }
            }

            // Tentukan fourthOctet untuk lokal dan remote
            $existingCount = count($response);
            $fourthOctetLocal = 1;
            $fourthOctetRemote = 10 + ($existingCount % 255);

            // Generate IP addresses
            $localIp = "$firstOctet.$secondOctet.$thirdOctet.$fourthOctetLocal";
            $remoteIp = "$firstOctet.$secondOctet.$thirdOctet.$fourthOctetRemote";

            // Membuat query untuk menambahkan PPP secret
            $query = new Query('/ppp/secret/add');
            $query->equal('name', $username)
                ->equal('password', $password)
                ->equal('comment', $akuncomment)
                ->equal('profile', 'IP-Tunnel-VPN')
                ->equal('local-address', $localIp)
                ->equal('remote-address', $remoteIp);

            // Eksekusi query
            $response = $client->query($query)->read();

            if (isset($response['!trap'])) {
                session()->flash('error', $response['!trap'][0]['message']);
                return redirect()->back();
            } else {
                // Buat aturan NAT
                $queryAllNAT = new Query('/ip/firewall/nat/print');
                $natResponse = $client->query($queryAllNAT)->read();

                // Cek jika response NAT tidak kosong dan ambil port yang digunakan
                $usedPorts = [];
                foreach ($natResponse as $natRule) {
                    if (isset($natRule['dst-port'])) {
                        $usedPorts[] = $natRule['dst-port'];
                    }
                }

                // Atur port tujuan (dstPort) yang akan digunakan
                $dstPort = 20000;
                while (in_array($dstPort, $usedPorts)) {
                    $dstPort++;
                    if ($dstPort > 65535) {
                        throw new \Exception("Tidak ada port tujuan yang tersedia.");
                    }
                }

                VPNOLT::create([
                    'unique_id' => $unique,
                    'namaakun' => $namaakun,
                    'username' => $username,
                    'password' => $password,
                    'ipaddress' => $remoteIp,
                ]);

                session()->flash('success', "VPN OLT Berhasil Dibuat!");
                return redirect()->back();

                //dd($unique);
            }
        } catch (ClientException $e) {
            session()->flash('error', "Gagal terhubung ke MikroTik: " . $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', "Terjadi kesalahan: " . $e->getMessage());
            return redirect()->back();
        }
    }
}
