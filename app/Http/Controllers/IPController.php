<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use RouterOS\Query;
use RouterOS\Client;
use App\Models\Mikrotik;
use Illuminate\Http\Request;

class IPController extends Controller
{
    public function nighbore(){
        $data = Mikrotik::where('unique_id', auth()->user()->unique_id)->get();
        return view('Dashboard/IP/nighbore', compact('data'));
    }
    public function aksesnightbore(Request $request)
    {
         $ipmikrotik = $request->query('ipmikrotik');

         // Cek apakah IP Mikrotik valid di database
         $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

      
         $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)->where('unique_id', auth()->user()->unique_id)->first();

        //dd($datavpn);
         if (!$mikrotik) {
             return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }

    // Ambil username dan password dari database (atau hardcode jika diperlukan)
        $username = $mikrotik->username; // Asumsi ada field username
        $password = $mikrotik->password; // Asumsi ada field password

    // Konfigurasi koneksi MikroTik
        $config = [
        'host' => 'id-1.aqtnetwork.my.id:'.$datavpn->portapi,
        'user' => $username,
        'pass' => $password,
       // 'port' => 8714
     ];

    try {
        // Membuat koneksi dengan MikroTik
        $client = new Client($config);

        // Query untuk mendapatkan neighbor dari MikroTik
        $query = (new Query('/ip/neighbor/print')); // Menggunakan neighbor command
        $response = $client->query($query)->read();
        //dd($response);
        // Jika neighbor ditemukan, arahkan ke view dan tampilkan hasil
       return view('Dashboard.IP.aksesnighbore', compact('response'));
    } catch (\Exception $e) {
        // Jika ada error saat koneksi ke MikroTik
        return redirect()->back()->with('error', 'Error connecting to MikroTik: ' . $e->getMessage());
    }

    }
    public function aksessecret(Request $request) {
        $ipmikrotik = $request->query('ipmikrotik');
        
        // Cek apakah IP Mikrotik valid di database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
        
        if (!$mikrotik) {
            return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }
        
        // Ambil data VPN berdasarkan IP dan user unik
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();
        
        if (!$datavpn) {
            return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
        }
    
        // Ambil username dan password dari database
        $username = $mikrotik->username;
        $password = $mikrotik->password;
    
        // Konfigurasi koneksi MikroTik
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];
    
        // Inisialisasi koneksi ke MikroTik menggunakan RouterOS-PHP
        try {
            // Membuat koneksi dengan MikroTik
            $client = new \RouterOS\Client([
                'host' => $config['host'],
                'user' => $config['user'],
                'pass' => $config['pass'],
                'port' => $datavpn->portapi,
            ]);
    
            // Kirim perintah untuk mengambil PPP secrets
            $query = new \RouterOS\Query('/ppp/secret/print');
            $secrets = $client->query($query)->read();

            $query = new Query('/ppp/profile/print');
            $profiles = $client->query($query)->read();
            // Mengirim data secrets ke view
            
            //dd($profiles);
            return view('Dashboard.IP.aksessecret', compact('secrets', 'profiles', 'ipmikrotik'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke MikroTik: ' . $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        $ipmikrotik = $request->input('ipmikrotik');
    
        // Check if MikroTik IP exists in the database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
    
        if (!$mikrotik) {
            return redirect()->back()->with('error', 'MikroTik with this IP not found.');
        }
    
        // Fetch VPN data based on IP and unique user ID
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();
    
        if (!$datavpn) {
            return redirect()->back()->with('error', 'VPN data not found for this IP.');
        }
    
        // Validate the request data
        $validated = $request->validate([
            'service' => 'required|string',
            'profile' => 'required|string',
            'name' => 'required|string',
            'comment' => 'nullable|string',
        ]);
    
        // Extract credentials
        $username = $mikrotik->username;
        $password = $mikrotik->password;
    
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:'.$datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];
    
        try {
            $client = new Client($config);
    
            // Prepare the query for adding PPP secret
            $query = new Query('/ppp/secret/add');
            $query->equal('service', $validated['service']);
            $query->equal('profile', $validated['profile']);
            $query->equal('name', $validated['name']);
            $query->equal('comment', $validated['comment']);
    
            // Execute the query
            $response = $client->query($query)->read();
    
            return redirect()->back()->with('success', 'Secret added successfully.');
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add secret: ' . $e->getMessage());
        }
    }
    public function destroy(Request $request, $id)
    {
        // Fetch MikroTik details based on IP
        $ipmikrotik = $request->input('ipmikrotik');
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
    
        if (!$mikrotik) {
            return redirect()->back()->with('error', 'MikroTik with this IP not found.');
        }
    
        // Fetch VPN data based on IP and unique user ID
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();
    
        if (!$datavpn) {
            return redirect()->back()->with('error', 'VPN data not found for this IP.');
        }
    
        // Extract credentials
        $username = $mikrotik->username;
        $password = $mikrotik->password;
    
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:'.$datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];
    
        try {
            $client = new Client($config);
    
            // Prepare the query for deleting the PPP secret
            $query = new Query('/ppp/secret/remove');
            $query->equal('.id', $id);
    
            // Execute the query
            $client->query($query)->read();
    
            // Optionally delete the record from the local database
            // Secret::where('.id', $id)->delete(); 
    
            return redirect()->back()->with('success', 'Secret deleted successfully.');
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete secret: ' . $e->getMessage());
        }
    }
    public function aksesinterface(Request $request) {
        $ipmikrotik = $request->query('ipmikrotik');
        
        // Cek apakah IP Mikrotik valid di database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
        
        if (!$mikrotik) {
            return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }
        
        // Ambil data VPN berdasarkan IP dan user unik
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();
        
        if (!$datavpn) {
            return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
        }
    
        // Ambil username dan password dari database
        $username = $mikrotik->username;
        $password = $mikrotik->password;
    
        // Konfigurasi koneksi MikroTik
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];
    
        // Inisialisasi koneksi ke MikroTik menggunakan RouterOS-PHP
        try {
            // Membuat koneksi dengan MikroTik
            $client = new \RouterOS\Client([
                'host' => $config['host'],
                'user' => $config['user'],
                'pass' => $config['pass'],
                'port' => $datavpn->portapi,
            ]);
    
          
            $query = new Query('/interface/print');
            $interface = $client->query($query)->read();
            // Mengirim data secrets ke view
            
            //dd($interface);
            return view('Dashboard.IP.aksesinterface', compact('interface', 'ipmikrotik'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke MikroTik: ' . $e->getMessage());
        }
    }
    public function enable(Request $request, $id)
    {
        $ipmikrotik = $request->input('ipmikrotik');

        // Cek apakah IP MikroTik valid di database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

        if (!$mikrotik) {
            return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }

        // Ambil data VPN berdasarkan IP dan user unik
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();

        if (!$datavpn) {
            return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
        }

        // Ambil username dan password dari database
        $username = $mikrotik->username;
        $password = $mikrotik->password;

        // Konfigurasi koneksi MikroTik
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];

        // Inisialisasi koneksi ke MikroTik menggunakan RouterOS-PHP
        try {
            // Membuat koneksi dengan MikroTik
            $client = new Client($config);
            
            // Enable the interface
            $query = (new Query('/interface/set'))
                ->equal('disabled', 'no') // Enable the interface
                ->equal('.id', $id); // Use the interface name or identifier

                $interface = $client->query($query)->read();

            return redirect()->back()->with('success', 'Interface enabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to enable interface: ' . $e->getMessage());
        }
    }

    public function disable(Request $request, $id)
    {
        $ipmikrotik = $request->input('ipmikrotik');
       // dd($ipmikrotik);
        // Cek apakah IP MikroTik valid di database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

        if (!$mikrotik) {
            return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }

        // Ambil data VPN berdasarkan IP dan user unik
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();

        if (!$datavpn) {
            return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
        }

        // Ambil username dan password dari database
        $username = $mikrotik->username;
        $password = $mikrotik->password;

        // Konfigurasi koneksi MikroTik
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];

        // Inisialisasi koneksi ke MikroTik menggunakan RouterOS-PHP
        try {
            // Membuat koneksi dengan MikroTik
            $client = new Client($config);
            
            // Disable the interface
            $query = (new Query('/interface/set'))
                ->equal('disabled', 'yes') // Disable the interface
                ->equal('.id', $id); // Use the interface name or identifier

                $interface = $client->query($query)->read();

            return redirect()->back()->with('success', 'Interface disabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to disable interface: ' . $e->getMessage());
        }
    }
    public function aksesschedule(Request $request){
        $ipmikrotik = $request->query('ipmikrotik');
        
        // Cek apakah IP Mikrotik valid di database
        $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();
        
        if (!$mikrotik) {
            return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
        }
        
        // Ambil data VPN berdasarkan IP dan user unik
        $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
            ->where('unique_id', auth()->user()->unique_id)
            ->first();
        
        if (!$datavpn) {
            return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
        }
    
        // Ambil username dan password dari database
        $username = $mikrotik->username;
        $password = $mikrotik->password;
    
        // Konfigurasi koneksi MikroTik
        $config = [
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
        ];
    
        try {
            $client = new \RouterOS\Client([
                'host' => $config['host'],
                'user' => $config['user'],
                'pass' => $config['pass'],
                'port' => $datavpn->portapi,
            ]);
            // Your existing MikroTik connection code...
            $query = new Query('/system/scheduler/print');
            $interface = $client->query($query)->read();
            
            $formattedData = array_map(function($item) {
                return [
                    '.id' => $item['.id'],
                    'name' => $item['name'],
                    'start_date' => $item['start-date'],
                    'start_time' => $item['start-time'],
                    'interval' => $item['interval'],
                    'run_count' => isset($item['run-count']) ? $item['run-count'] : 'N/A', // Add run_count
                ];
            }, $interface);
    
            // Check if the request is an AJAX request
            if ($request->ajax()) {
                return response()->json(['formattedData' => $formattedData]);
            }
    
            // If not AJAX, return view as before
            return view('Dashboard.IP.aksesschedule', compact('formattedData', 'ipmikrotik'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke MikroTik: ' . $e->getMessage());
        }
    }
    
    // HOTSPOT 
// HOTSPOT
public function aksesactivehotspot(Request $request) {
    // Ambil IP Mikrotik dari query parameter
    $ipmikrotik = $request->query('ipmikrotik');

    // Cek apakah IP Mikrotik valid di database
    $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

    if (!$mikrotik) {
        return redirect()->back()->with('error', 'Mikrotik dengan IP tersebut tidak ditemukan.');
    }

    // Ambil data VPN berdasarkan IP dan user unik
    $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
        ->where('unique_id', auth()->user()->unique_id)
        ->first();

    if (!$datavpn) {
        return redirect()->back()->with('error', 'Data VPN tidak ditemukan untuk IP ini.');
    }

    // Ambil username dan password dari database
    $username = $mikrotik->username;
    $password = $mikrotik->password;

    try {
        // Membuat koneksi ke MikroTik dengan RouterOS PHP Client
        $client = new \RouterOS\Client([
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
            'port' => $datavpn->portapi,
        ]);

        // Query untuk mendapatkan data hotspot yang aktif
        $query = new \RouterOS\Query('/ip/hotspot/active/print');
        $activeHotspots = $client->query($query)->read();
       
        if ($request->ajax()) {
            return response()->json(['activeHotspots' => $activeHotspots]);
        }
        // Tampilkan hasil di view
        return view('Dashboard.HOTSPOT.aksesactivehotspot', compact('activeHotspots', 'ipmikrotik'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal terhubung ke MikroTik: ' . $e->getMessage());
    }
}
public function disconnectHotspot(Request $request)
{
    // Get MAC address and MikroTik IP from the request
    $macAddress = $request->input('mac_address');
    $ipmikrotik = $request->input('ipaddress'); // Corrected to use input, not query

    // Check if the MikroTik IP is valid in the database
    $mikrotik = Mikrotik::where('ipmikrotik', $ipmikrotik)->first();

    if (!$mikrotik) {
        return response()->json(['success' => false, 'message' => 'Mikrotik dengan IP tersebut tidak ditemukan.']);
    }

    // Get VPN data based on the MikroTik IP and unique user ID
    $datavpn = VPN::where('ipaddress', $mikrotik->ipmikrotik)
        ->where('unique_id', auth()->user()->unique_id)
        ->first();

    if (!$datavpn) {
        return response()->json(['success' => false, 'message' => 'Data VPN tidak ditemukan untuk IP ini.']);
    }

    // Retrieve username and password from the database
    $username = $mikrotik->username;
    $password = $mikrotik->password;

    try {
        // Create connection to MikroTik with RouterOS PHP Client
        $client = new \RouterOS\Client([
            'host' => 'id-1.aqtnetwork.my.id:' . $datavpn->portapi,
            'user' => $username,
            'pass' => $password,
            'port' => $datavpn->portapi,
        ]);
    
        // Query to find the active session by MAC address
        $query = new \RouterOS\Query('/ip/hotspot/active/print');
        $query->where('mac-address', $macAddress);
    
        // Get active sessions
        $activeSessions = $client->query($query)->read();
    
        if (!empty($activeSessions)) {
            // Extract the ID of the active session
            $activeId = $activeSessions[0]['.id'];
    
            // Now create a query to remove the session using its ID
            $removeQuery = new \RouterOS\Query('/ip/hotspot/active/remove');
            $removeQuery->equal('.id', $activeId);
    
            // Send the query to remove the active session
            $client->query($removeQuery)->read();
    
            return response()->json(['success' => true, 'message' => 'User disconnected successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'No active session found for the provided MAC address.']);
        }
    
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
    
}



    
    
}   