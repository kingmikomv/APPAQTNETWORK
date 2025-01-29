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

    public function update(Request $request)
    {
        
        // Validasi data
        $request->validate([
            'id' => 'required|exists:olt,id', // Pastikan ID ada di tabel olts
            'site' => 'required|string|max:255',
            'ipolt' => 'required|ip', // Validasi IP address
            'portolt' => 'required|numeric',
            'ipvpn' => 'required|ip', // Validasi IP VPN
        ]);

        try {
            // Cari OLT berdasarkan ID
            $olt = OLT::findOrFail($request->id);

            // Update data OLT
            $olt->site = $request->site;
            $olt->ipolt = $request->ipolt;
            $olt->portolt = $request->portolt;
            $olt->ipvpn = $request->ipvpn;
            $olt->save();

            // Redirect dengan pesan sukses
            return redirect()->back()->with('success', 'Data OLT berhasil diperbarui.');
        } catch (\Exception $e) {
            // Redirect dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data OLT: ' . $e->getMessage());
        }
    }

}
