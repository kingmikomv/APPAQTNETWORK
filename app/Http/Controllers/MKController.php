<?php

namespace App\Http\Controllers;

use App\Models\Mikrotik;
use App\Models\VPN;
use RouterOS\Query;
use RouterOS\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class MKController extends Controller
{
    public function index()
    {

        $mikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        return view('Dashboard.MIKROTIK.index', compact('mikrotik'));
    }
    public function tambahmikrotik(Request $req)
    {
        $ipmikrotik = $req->input('ipmikrotik');
        $site = $req->input('site');
        $username = $req->input('username');
        $password = $req->input('password');
        $unique_id = auth()->user()->unique_id;

        try {
            // Assuming Mikrotik::create() method exists
            $data = Mikrotik::create([
                'ipmikrotik' => $ipmikrotik,
                'site' => $site,
                'username' => $username,
                'password' => $password,
                'unique_id' => $unique_id
            ]);

            session()->flash('success', "Mikrotik Site " . $site . " Berhasil Di Tambahkan");
            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data: ' . $e->getMessage(),
            ]);
        }
    }

    //  DATA DASAR MIKROTIK
    public function aksesMikrotik(Request $request)
    {
        $ipmikrotik = $request->query('ipmikrotik');
        $username = $request->query('username');
        $password = $request->query('password');
    
        // Daftar port yang akan diuji koneksi
        $availablePorts = [9000, 2043, 2046, 2045, 2200];
    
        $dataport = VPN::where('ipaddress', $ipmikrotik)->first();
    
        if (is_null($dataport)) {
            // Jika tidak ada data port di database
            foreach ($availablePorts as $port) {
                try {
                    // Coba koneksi dengan masing-masing port
                    $connection = new Client([
                        'host' => $ipmikrotik,
                        'user' => $username,
                        'pass' => $password,
                        'port' => $port
                    ]);
    
                    // Jika koneksi berhasil
                    session()->flash('success', "Mikrotik Terhubung pada port $port");
                    return redirect()->back();
                } catch (\Exception $e) {
                    // Lanjutkan ke port berikutnya jika gagal
                    continue;
                }
            }
    
            // Jika semua port gagal
            session()->flash('error', 'Gagal terhubung ke MikroTik router dengan semua port yang tersedia.');
            return redirect()->back();
        } else {
            // Jika data port ada di database
            $portFromDb = $dataport->portapi;
    
            try {
                // Coba koneksi dengan port dari database jika tersedia
                $connection = new Client([
                    'host' => $ipmikrotik,
                    'user' => $username,
                    'pass' => $password,
                    'port' => $portFromDb
                ]);
    
                // Jika koneksi berhasil
                session()->flash('success', "Mikrotik Terhubung dengan port $portFromDb dari database");
                return redirect()->back();
            } catch (\Exception $e) {
                // Jika koneksi dengan port database gagal, coba semua port
                foreach ($availablePorts as $port) {
                    try {
                        // Coba koneksi dengan port yang tersedia
                        $connection = new Client([
                            'host' => $ipmikrotik,
                            'user' => $username,
                            'pass' => $password,
                            'port' => $port
                        ]);
    
                        // Jika koneksi berhasil
                        session()->flash('success', "Mikrotik Terhubung pada port $port");
                        return redirect()->back();
                    } catch (\Exception $e) {
                        // Lanjutkan ke port berikutnya jika gagal
                        continue;
                    }
                }
    
                // Jika semua port gagal
                session()->flash('error', 'Gagal terhubung ke MikroTik router dengan semua port yang tersedia.');
                return redirect()->back();
            }
        }
    }
    
    public function edit($id)
    {
        $mikrotik = Mikrotik::find($id);
        if (!$mikrotik) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($mikrotik);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'ipmikrotik' => 'required|ip',
            'site' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $mikrotik = Mikrotik::find($id);
        if ($mikrotik) {
            $mikrotik->ipmikrotik = $request->input('ipmikrotik');
            $mikrotik->site = $request->input('site');
            $mikrotik->username = $request->input('username');
            $mikrotik->password = $request->input('password');
            $mikrotik->save();

            return redirect()->back()->with('success', 'MikroTik updated successfully.');
        }

        return redirect()->back()->with('error', 'MikroTik not found.');
    }
    public function destroy($id)
    {
        $mikrotik = Mikrotik::find($id);
        if ($mikrotik) {
            $mikrotik->delete();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }
    public function masukmikrotik(Request $request)
    {
        // Ambil data MikroTik dari database berdasarkan parameter 'ipmikrotik'
        $ipmikrotik = $request->input('ipmikrotik');
        $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

        // Cek apakah data MikroTik ditemukan
        if (!$data) {
            return redirect()->back()->with('error', 'MikroTik data not found.');
        }

        $username = $data->username; // Ambil username dari database
        $password = $data->password; // Ambil password dari database
        $site = $data->site;

        // Cek data VPN berdasarkan IP address yang diberikan
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->first();

        // Set 'portweb' dari input request atau data VPN (jika ada)
        $portweb = $request->input('portweb') ?? ($datavpn->portweb ?? null);
        // Set 'portapi' dari data VPN jika tersedia
        $portapi = $datavpn->portapi ?? null;

        // Membangun konfigurasi koneksi berdasarkan data yang ada
        if (is_null($portapi)) {
            // Jika 'portapi' tidak ditemukan, gunakan IP publik dan port default
            return redirect()->back()->with('error', 'Untuk Masuk Ke Mikrotik Harus Melalui Jaringan VPN Yang Kami Sediakan');
        } else {
            // Jika data VPN ditemukan, gunakan 'portapi' dari VPN
            $config = [
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
                'user' => $username,
                'pass' => $password,
                'port' => 9000

            ];

            // Sertakan 'portweb' jika ada
            if ($portweb) {
                $config['port'] = $portweb;
            }
        }

        try {
            // Koneksi ke MikroTik menggunakan konfigurasi yang telah dibuat
            $client = new Client($config);
            $query = (new Query('/ppp/active/print'));
            $response = $client->query($query)->read();

            // Set variabel session untuk menandai bahwa koneksi berhasil
            session([
                'mikrotik_connected' => true,
                'ipmikrotik' => $ipmikrotik,
                'portapi' => $portapi
            ]);

            // Hapus session 'session_disconnected' jika ada
            session()->forget('session_disconnected');

            // Arahkan ke halaman dashboardmikrotik setelah berhasil terkoneksi
            return redirect()->route('dashboardmikrotik', ['ipmikrotik' => $ipmikrotik]);
        } catch (\Exception $e) {
            // Jika terjadi error saat koneksi, hapus session dan tampilkan pesan error
            session()->forget('mikrotik_connected');
            session(['session_disconnected' => true]);

            return redirect()->back()->with('error', 'Error connecting to MikroTik: ' . $e->getMessage());
        }
    }


    public function keluarmikrotik(Request $request)
    {
        // Clear MikroTik session variables
        $request->session()->forget(['mikrotik_connected', 'session_disconnected']);

        // Redirect to login or another page
        return redirect()->route('datamikrotik')->with('success', 'Berhasil Logout');
    }


    /////////////////////////////
    public function dashboardmikrotik(Request $request)
    {
        $ipmikrotik = $request->input('ipmikrotik');

        // Ambil data MikroTik berdasarkan IP
        $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
        $totalvpn = VPN::where('unique_id', auth()->user()->unique_id)->count();
        $totalmikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->count();
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();
        $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

        // Set 'portweb' dari input request atau data VPN (jika ada)
        $portweb = $request->input('portweb') ?? ($datavpn->portweb ?? null);
        // Set 'portapi' dari data VPN jika tersedia
        $portapi = $datavpn->portapi ?? null;

        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
            'user' => $data->username,
            'pass' => $data->password,
            'port' => 9000

        ];



        $client = new Client($config);

        // Query untuk mendapatkan data secret di PPP
        $query = (new Query('/ppp/secret/print'));
        $response = $client->query($query)->read();

        $totaluser = count($response);

        $query2 = (new Query('/ppp/active/print'));
        $response2 = $client->query($query2)->read();

        $totalactive = count($response2);

        $query = (new Query('/system/resource/print'));

        // Jalankan query dan baca respons
        $response = $client->query($query)->read();

        $version = $response[0]['version'] ?? 'Unknown version';
        $model = $response[0]['board-name'] ?? 'Unknown model';

        $queryDateTime = (new Query('/system/clock/print'));
        $responseDateTime = $client->query($queryDateTime)->read();

        // Query untuk mengambil daftar interface Ethernet dari MikroTik
        $queryInterfaces = (new Query('/interface/print'));
        $responseInterfaces = $client->query($queryInterfaces)->read();

        $interfaces = [];
        $physicalInterfaces = ['ether', 'sfp', 'wifi', 'bonding']; // Common physical interface types

        foreach ($responseInterfaces as $interface) {
            if (isset($interface['name']) && isset($interface['type'])) {
                // Check if the interface type indicates a physical interface
                if (in_array($interface['type'], $physicalInterfaces)) {
                    $interfaces[] = $interface['name'];
                }
            }
        }

        $queryHotspotUsers = (new Query('/ip/hotspot/user/print'));
        $responseHotspotUsers = $client->query($queryHotspotUsers)->read();

        // Initialize an array to store the hotspot users
        $hotspotUsers = [];

        // Iterate over the response to extract the user data
        foreach ($responseHotspotUsers as $user) {
            if (isset($user['name'])) {
                $hotspotUsers[] = $user;
            }
        }


        $ttuser = count($hotspotUsers);




        $queryActiveHotspotUsers = (new Query('/ip/hotspot/active/print'));

        // Execute the query
        $responseActiveHotspotUsers = $client->query($queryActiveHotspotUsers)->read();

        // Initialize an array to store active hotspot users
        $activeHotspotUsers = [];

        // Iterate over the response to extract user data
        foreach ($responseActiveHotspotUsers as $user) {
            if (isset($user['name'])) {
                $activeHotspotUsers[] = $user;
            }
        }

        // Count the number of active users
        $activeUserCount = count($activeHotspotUsers);


        Log::info($activeUserCount);



        if (!empty($responseDateTime)) {
            // Ambil date
            $date = isset($responseDateTime[0]['date']) ? $responseDateTime[0]['date'] : 'N/A';


            if (!$data) {
                return redirect()->back()->with('error', 'MikroTik data not found.');
            }


            // Ambil informasi lain yang dibutuhkan untuk ditampilkan di dashboard
            $site = $data->site;
            $username = $data->username;

            // Tampilkan dashboard dengan data yang relevan
            return view('Dashboard.MIKROTIK.dashboardmikrotik', compact('ipmikrotik', 'site', 'username', 'totalvpn', 'totalmikrotik', 'totaluser', 'totalactive', 'date', 'interfaces', 'version', 'model', 'ttuser', 'activeUserCount'));
        } else {
            return back()->with('error', 'Data tidak ditemukan.');
        }
    }

    public function getUptime($ipmikrotik)
    {
        $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();
        if (!$data) {
            return response()->json(['error' => 'Data MikroTik tidak ditemukan.']);
        }

        try {
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi, // Menggunakan domain VPN dan port API dari data VPN

                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000

            ]);

            $query = new Query('/system/resource/print');
            $response = $client->query($query)->read();

            if (isset($response[0]['uptime'])) {
                return response()->json(['uptime' => $response[0]['uptime']]);
            } else {
                return response()->json(['error' => 'Uptime tidak ditemukan.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal terhubung ke MikroTik: ' . $e->getMessage()]);
        }
    }


    public function getCurrentTime($ipmikrotik, Request $request)
    {


        $data = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
        $totalvpn = VPN::where('unique_id', auth()->user()->unique_id)->count();
        $totalmikrotik = Mikrotik::where('unique_id', auth()->user()->unique_id)->count();
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

        // Set 'portweb' dari input request atau data VPN (jika ada)
        $portweb = $request->input('portweb') ?? ($datavpn->portweb ?? null);
        // Set 'portapi' dari data VPN jika tersedia
        $portapi = $datavpn->portapi ?? null;
        try {
            // Membuat koneksi ke MikroTik API menggunakan IP dari parameter URL
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000

            ]);
            // Query untuk mengambil waktu dari MikroTik
            $queryDateTime = (new Query('/system/clock/print'));
            $responseDateTime = $client->query($queryDateTime)->read();

            // Memeriksa dan mengambil data dari response
            if (!empty($responseDateTime)) {
                $time = isset($responseDateTime[0]['time']) ? $responseDateTime[0]['time'] : 'N/A';

                // Mengirim data sebagai JSON
                return response()->json(['time' => $time]);
            }

            return response()->json(['time' => 'N/A']);
        } catch (\Exception $e) {
            return response()->json(['time' => 'Error']);
        }
    }

    public function getTraffic(Request $request)
    {

        $interfaceName = $request->input('interface');
        $ipmikrotikreq = $request->input('ipmikrotik'); // Ambil ipmikrotik dari request
        $data = Mikrotik::where('ipmikrotik', $ipmikrotikreq)->first();

        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();
        // Log input yang diterima
        Log::info('Interface name: ' . $interfaceName);


        // Debug apakah data MikroTik ditemukan
        if (!$data) {
            Log::error('MikroTik data not found for IP: ' . $data->ipmikrotik);
            return response()->json(['error' => 'Data MikroTik tidak ditemukan.'], 404);
        }


        // Debug apakah data VPN ditemukan
        if (!$datavpn) {
            Log::error('VPN data not found for IP: ' . $data->ipmikrotik);
            return response()->json(['error' => 'Data VPN tidak ditemukan.'], 404);
        }

        $portapi = $datavpn->portapi ?? null;
        Log::info('Port API: ' . $portapi);

        try {
            // Membuat koneksi ke MikroTik API
            Log::info('Attempting connection to MikroTik API...');

            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Pastikan menggunakan IP dan port yang benar
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000

            ]);

            // Query untuk mengambil traffic dari interface yang dipilih
            $queryTraffic = (new Query('/interface/monitor-traffic'))
                ->equal('interface', $interfaceName)
                ->equal('once', true);  // Hanya sekali ambil data

            Log::info('Executing query to MikroTik API...');
            $responseTraffic = $client->query($queryTraffic)->read();
            Log::info('MikroTik API Response: ', $responseTraffic);

            if (empty($responseTraffic)) {
                return response()->json(['error' => 'Tidak ada data traffic yang tersedia'], 400);
            }

            // Cek apakah ada data rx-bytes dan tx-bytes
            $traffic = [
                'rx' => isset($responseTraffic[0]['rx-bits-per-second']) ? $responseTraffic[0]['rx-bits-per-second'] : 0,
                'tx' => isset($responseTraffic[0]['tx-bits-per-second']) ? $responseTraffic[0]['tx-bits-per-second'] : 0,
            ];
            Log::info($traffic);
            return response()->json($traffic);
        } catch (\Exception $e) {
            Log::error('Failed to connect to MikroTik: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal terhubung ke MikroTik: ' . $e->getMessage()], 500);
        }
    }





    public function getActiveConnection(Request $request)
    {

        $data = Mikrotik::where('ipmikrotik', $request->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

        // Cek apakah data MikroTik ditemukan
        if (!$data) {
            return redirect()->back()->with('error', 'MikroTik data not found.');
        }
        // dd($data);
        $username = $data->username; // Ambil username dari database
        $password = $data->password; // Ambil password dari database
        $site = $data->site;

        // Cek data VPN berdasarkan IP address yang diberikan
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

        // Set 'portweb' dari input request atau data VPN (jika ada)
        $portweb = $request->input('portweb') ?? ($datavpn->portweb ?? null);
        // Set 'portapi' dari data VPN jika tersedia
        $portapi = $datavpn->portapi ?? null;
        //dd($portapi);
        //dd($portapi);
        // Membangun konfigurasi koneksi berdasarkan data yang ada

        // Jika data VPN ditemukan, gunakan 'portapi' dari VPN
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $portapi, // Menggunakan domain VPN dan port API dari data VPN
            'user' => $username,
            'pass' => $password,
            'port' => 9000

        ];

        try {
            // Koneksi ke MikroTik menggunakan konfigurasi yang telah dibuat
            $client = new Client($config);
            $query = (new Query('/ppp/active/print'));
            $response = $client->query($query)->read();

            //dd($response);

            //dd($query);
            return view('Dashboard.MIKROTIK.active-connection', ['ipmikrotik' => $data->ipmikrotik, 'response' => $response, 'portweb' => $portweb, 'portapi' => $portapi]);
            // Arahkan ke halaman dashboardmikrotik setelah berhasil terkoneksi
            // return redirect()->route('dashboardmikrotik', ['ipmikrotik' => $ipmikrotik]);
        } catch (\Exception $e) {
            // Jika terjadi error saat koneksi, hapus session dan tampilkan pesan error

            //dd($e->getMessage());
            return redirect()->back()->with('error', 'Error connecting to MikroTik: ' . $e->getMessage());
        }
    }
    public function addFirewallRule(Request $request)
    {
        $request->validate([
            'ipaddr' => 'required',
            'port' => 'required',
            'ipmikrotik' => 'required',
        ]);

        $ipAddress = $request->input('ipaddr');
        $port = $request->input('port');
        $ipMikrotik = $request->input('ipmikrotik');

        // Ambil data MikroTik berdasarkan IP
        $data = Mikrotik::where('ipmikrotik', $request->ipmikrotik)->first();

        // Cek apakah data MikroTik ditemukan
        if (!$data) {
            return redirect()->back()->with('error', 'MikroTik data not found.');
        }

        $username = $data->username;
        $password = $data->password;
        $site = $data->site;

        // Ambil data VPN terkait berdasarkan IP MikroTik
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->first();

        // Cek apakah data VPN ditemukan
        if (!$datavpn) {
            return redirect()->back()->with('error', 'VPN data not found.');
        }

        // Set 'portapi' dan 'portweb' dari data VPN
        $portapi = $datavpn->portapi ?? '8728'; // Default '8728' jika 'portapi' tidak ditemukan
        $portweb = $datavpn->portweb ?? '80'; // Default '80' jika 'portweb' tidak ditemukan

        try {
            // Konfigurasi client MikroTik API
            $config = [
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi,
                'user' => $username,
                'pass' => $password,
                'port' => 9000

            ];

            $client = new Client($config);

            // Periksa apakah ada aturan firewall NAT dengan port tertentu
            $query = (new Query('/ip/firewall/nat/print'))
                ->where('dst-port', $portweb);
            $existingRules = $client->query($query)->read();

            if (!empty($existingRules)) {
                // Update aturan NAT yang sudah ada
                $id = $existingRules[0]['.id'];
                $updateQuery = (new Query('/ip/firewall/nat/set'))
                    ->equal('.id', $id)
                    ->equal('dst-port', $portweb)
                    ->equal('to-addresses', $ipAddress)
                    ->equal('to-ports', $port);

                $client->query($updateQuery)->read();
            } else {
                // Tambahkan aturan NAT baru
                $addQuery = (new Query('/ip/firewall/nat/add'))
                    ->equal('chain', 'dstnat')
                    ->equal('protocol', 'tcp')
                    ->equal('dst-port', $portweb)
                    ->equal('action', 'dst-nat')
                    ->equal('to-addresses', $ipAddress)
                    ->equal('to-ports', $port)
                    ->equal('comment', 'Remote-web');

                $client->query($addQuery)->read();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function restartmodem(Request $request)
    {
        // Validate request data
        $request->validate([
            'ipaddr' => 'required|ip',
            'port' => 'required|numeric',
            'ipmikrotik' => 'required|ip',
        ]);

        $ipAddress = $request->input('ipaddr');
        $port = $request->input('port');
        $ipMikrotik = $request->input('ipmikrotik');
        // Ambil data MikroTik berdasarkan IP
        $data = Mikrotik::where('ipmikrotik', $request->ipmikrotik)->first();

        // Cek apakah data MikroTik ditemukan
        if (!$data) {
            return redirect()->back()->with('error', 'MikroTik data not found.');
        }

        $username = $data->username;
        $password = $data->password;
        $site = $data->site;

        // Ambil data VPN terkait berdasarkan IP MikroTik
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)->first();

        // Cek apakah data VPN ditemukan
        if (!$datavpn) {
            return redirect()->back()->with('error', 'VPN data not found.');
        }

        // Set 'portapi' dan 'portweb' dari data VPN
        $portapi = $datavpn->portapi ?? '8728'; // Default '8728' jika 'portapi' tidak ditemukan
        $portweb = $datavpn->portweb ?? '80'; // Default '80' jika 'portw
        try {
            // MikroTik API client configuration
            $config = [
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi,
                'user' => $username,
                'pass' => $password,
                'port' => 9000

            ];

            $client = new Client($config);

            // Get the list of active PPPoE connections
            $query = new Query('/ppp/active/print');
            $query->where('address', $ipAddress);

            $pppActiveConnections = $client->query($query)->read();

            if (count($pppActiveConnections) > 0) {
                $pppId = $pppActiveConnections[0]['.id'];

                // Remove the PPP active connection
                $removeQuery = new Query('/ppp/active/remove');
                $removeQuery->equal('.id', $pppId);

                $result = $client->query($removeQuery)->read();

                if (!isset($result['!trap'])) {
                    return response()->json(['success' => true, 'message' => 'PPPoE connection removed successfully.']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Failed to remove PPPoE connection: ' . $result['!trap'][0]['message']]);
                }
            } else {
                return response()->json(['success' => false, 'message' => "PPPoE connection with IP address '$ipAddress' not found."]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function getTrafficData(Request $request)
    {
        $interfaceName = $request->input('interface');
        $ipmikrotikreq = $request->input('ipmikrotik');

        $data = Mikrotik::where('ipmikrotik', $ipmikrotikreq)->first();
        $datavpn = VPN::where('ipaddress', $data->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();

        if (!$data) {
            return response()->json(['error' => 'Data MikroTik tidak ditemukan.'], 404);
        }

        if (!$datavpn) {
            return response()->json(['error' => 'Data VPN tidak ditemukan.'], 404);
        }

        $portapi = $datavpn->portapi ?? null;

        try {
            $client = new Client([
                'host' => 'id-1.aqtnetwork.my.id:' . $portapi,
                'user' => $data->username,
                'pass' => $data->password,
                'port' => 9000
            ]);

            $queryTraffic = (new Query('/interface/monitor-traffic'))
                ->equal('interface', "<pppoe-" . $interfaceName . ">")
                ->equal('once', true);

            $responseTraffic = $client->query($queryTraffic)->read();

            if (empty($responseTraffic)) {
                return response()->json(['error' => 'Tidak ada data traffic yang tersedia.'], 404);
            }

            $traffic = [
                'rx' => isset($responseTraffic[0]['rx-bits-per-second']) ? $responseTraffic[0]['rx-bits-per-second'] : 0,
                'tx' => isset($responseTraffic[0]['tx-bits-per-second']) ? $responseTraffic[0]['tx-bits-per-second'] : 0,
            ];

            return response()->json(['traffic' => $traffic]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal terhubung ke MikroTik: ' . $e->getMessage()], 500);
        }
    }
}
