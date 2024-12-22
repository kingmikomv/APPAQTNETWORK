<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Query;
use RouterOS\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;
use RealRashid\SweetAlert\Facades\Alert;

class VPNController extends Controller
{
    public function index() {

        $user = auth()->user();
        
        $uniqueId = $user->unique_id;

        $data = VPN::where('unique_id', $uniqueId)->get(); 
        //dd($data);
        return view('Dashboard.VPN.index', compact('data'));
    }
    public function uploadvpn(Request $req)
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
    
        $namaakun = $req->input('namaakun');
        $username = $req->input('username');
        $password = $req->input('password');
        $portmkt = $req->input('portmk');
    
        $akuncomment = "AQT_" . $namaakun;
    
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
            $firstOctet = '172';
            $secondOctet = 16;
    
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
                $dstPort = 5000;
                while (in_array($dstPort, $usedPorts)) {
                    $dstPort++;
                    if ($dstPort > 65535) {
                        throw new \Exception("Tidak ada port tujuan yang tersedia.");
                    }
                }
    
                
                // Increment dstPort by 1 for the MikroTik NAT rule
                $tambahsatu = $dstPort + 1;
    
                // Tentukan portwbx dan cek apakah ada konflik
                if ($portmkt == null) {
                    $portwbx = 8291;
                } else {
                    $portwbx = $portmkt;
                }
 // Cek apakah kombinasi port sudah ada di dalam database
 $portExists = VPN::where('portapi', $portmkt)
 ->orWhere('portweb', $portmkt)
 ->orWhere('portmikrotik', $portmkt)
 ->exists();

if ($portExists || $portmkt == 9000) {
session()->flash('error', 'Port API, Web, atau MikroTik sudah digunakan. Silakan coba lagi dengan port lain.');
return redirect()->back();
}else{
    //////////

    // Buat aturan NAT pertama
    $natQuery1 = new Query('/ip/firewall/nat/add');
    $natQuery1->equal('chain', 'dstnat')
              ->equal('protocol', 'tcp')
              ->equal('dst-port', $dstPort)
              ->equal('dst-address-list', 'ip-public')
              ->equal('action', 'dst-nat')
              ->equal('to-addresses', $remoteIp)
              ->equal('to-ports', 9000)
              ->equal('comment', $akuncomment . '_API');

    $natResponse1 = $client->query($natQuery1)->read();

    if (isset($natResponse1['!trap'])) {
        session()->flash('error', $natResponse1['!trap'][0]['message']);
        return redirect()->back();
    }

    // Increment dstPort for the second NAT rule
    $dstPort2 = $dstPort + 10;

    // Buat aturan NAT kedua
    $natQuery2 = new Query('/ip/firewall/nat/add');
    $natQuery2->equal('chain', 'dstnat')
              ->equal('protocol', 'tcp')
              ->equal('dst-port', $dstPort2)
              ->equal('dst-address-list', 'ip-public')
              ->equal('action', 'dst-nat')
              ->equal('to-addresses', $remoteIp)
              ->equal('to-ports', $dstPort2)
              ->equal('comment', $akuncomment . '_WEB');

    $natResponse2 = $client->query($natQuery2)->read();

    if (isset($natResponse2['!trap'])) {
        session()->flash('error', $natResponse2['!trap'][0]['message']);
        return redirect()->back();
    }
///////////
$natQuery3 = new Query('/ip/firewall/nat/add');
                $natQuery3->equal('chain', 'dstnat')
                          ->equal('protocol', 'tcp')
                          ->equal('dst-port', $tambahsatu)
                          ->equal('dst-address-list', 'ip-public')
                          ->equal('action', 'dst-nat')
                          ->equal('to-addresses', $remoteIp)
                          ->equal('to-ports', $portwbx)
                          ->equal('comment', $akuncomment . '_MikroTik');
    
                $natResponse3 = $client->query($natQuery3)->read();
    
                if (isset($natResponse3['!trap'])) {
                    session()->flash('error', $natResponse3['!trap'][0]['message']);
                    return redirect()->back();
                }
    
                // Menyimpan data ke database
                $unique = auth()->user();
                VPN::create([
                    'unique_id' => $unique->unique_id,
                    'namaakun' => $namaakun,
                    'username' => $username,
                    'password' => $password,
                    'ipaddress' => $remoteIp,
                    'portapi' => $dstPort,
                    'portweb' => $dstPort2,
                    'portmikrotik' => $tambahsatu,
                    'portwbx' => $portwbx
                ]);
    
                session()->flash('success', "PPP Secret Berhasil Dibuat!");
                return redirect()->back();






}

                // Buat aturan NAT ketiga untuk MikroTik
                
            }
    
        } catch (ClientException $e) {
            session()->flash('error', "Gagal terhubung ke MikroTik: " . $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', "Terjadi kesalahan: " . $e->getMessage());
            return redirect()->back();
        }
    }
    
    
    public function hapusvpn(Request $request, $id)
    {
        $username = $request->input('username');
    
        if (!$username) {
            return response()->json(['error' => 'Username is required.'], 400);
        }
    
        $client = new Client([
            'host' => 'id-1.aqtnetwork.my.id',
            'user' => 'admin',
            'pass' => 'bakpao1922',
        ]);
    
        $vpn = VPN::findOrFail($id);
    
        if ($vpn->username !== $username) {
            return response()->json(['error' => 'Username does not match.'], 400);
        }
    
        try {
            // Search for PPP Secret by name
            $query = new Query('/ppp/secret/print');
            $response = $client->query($query)->read();
    
            $matchedSecret = null;
            foreach ($response as $secret) {
                if (isset($secret['name']) && $secret['name'] === $username) {
                    $matchedSecret = $secret;
                    break;
                }
            }
    
            if ($matchedSecret) {
                $secretId = $matchedSecret['.id'];
    
                $removeQuery = new Query('/ppp/secret/remove');
                $removeQuery->equal('.id', $secretId);
                $client->query($removeQuery)->read();
            } else {
                return response()->json(['error' => 'PPP Secret not found.'], 404);
            }
    
            // Search for and remove Firewall NAT rules by name
            $natComments = ['AQT_'. $username . '_API', 'AQT_'. $username . '_WEB', 'AQT_' .$username. '_MikroTik'];
            foreach ($natComments as $comment) {
                $query = new Query('/ip/firewall/nat/print');
                $response = $client->query($query)->read();
    
                foreach ($response as $rule) {
                    if (isset($rule['comment']) && $rule['comment'] && $rule['comment'] === $comment) {
                        $ruleId = $rule['.id'];
    
                        $removeQuery = new Query('/ip/firewall/nat/remove');
                        $removeQuery->equal('.id', $ruleId);
                        $client->query($removeQuery)->read();
                    }
                }
            }
            //Log::info();
            // Delete the VPN record from the database
            $vpn->delete();
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete PPP Secret or Firewall NAT rules: ' . $e->getMessage()], 500);
        }
    
        return response()->json(['success' => 'Data berhasil dihapus']);
    }
    
    


}
