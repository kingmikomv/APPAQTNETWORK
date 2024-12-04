<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Models\OLT;
use Illuminate\Http\Request;
use RouterOS\Query;
use RouterOS\Client;
use GuzzleHttp\Exception\ClientException;

class OLTController extends Controller
{
    public function index(){
        $olts = OLT::where('unique_id', auth()->user()->unique_id)->get();
        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        return view('Dashboard.OLT.index', compact('olts', 'mikrotik'));
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

}
