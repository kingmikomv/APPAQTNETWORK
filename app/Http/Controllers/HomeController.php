<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use App\Models\Mikrotik;
use Xendit\Configuration;
use Illuminate\Http\Request;
use App\Models\CoinTransaction;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        
       
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dataCoin = CoinTransaction::all();

        // Menghitung total transaksi berdasarkan nilai coin
        $chartTotalTransaksi = [
            '5' => $dataCoin->where('coin_amount', 5)->count(),
            '10' => $dataCoin->where('coin_amount', 10)->count(),
            '20' => $dataCoin->where('coin_amount', 20)->count(),
            '50' => $dataCoin->where('coin_amount', 50)->count(),
            '100' => $dataCoin->where('coin_amount', 100)->count(),
            '200' => $dataCoin->where('coin_amount', 200)->count(),
        ];
        $totalMikrotik = Mikrotik::all()->count();
        $totalVPN = VPN::all()->count();
        //dd($chartTotalTransaksi);
        return view('Dashboard/index', compact('chartTotalTransaksi', 'totalMikrotik', 'totalVPN'));
    }
    public function myakun()
    {
       
        return view('Dashboard/myakun');
    }
    public function updateAccount(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'telefon' => 'nullable|string|max:15',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    // Ambil user yang sedang login
    $user = Auth::user();

    // Update data pengguna
    $user->update([
        'name' => $validated['name'],
        'telefon' => $validated['telefon'],
        // Jika password diisi, update password
        'password' => !empty($validated['password']) ? bcrypt($validated['password']) : $user->password,
    ]);

    // Redirect dengan pesan sukses
    return redirect()->back()->with('success', 'Berhasil Mengupdate Akun');
}

}
