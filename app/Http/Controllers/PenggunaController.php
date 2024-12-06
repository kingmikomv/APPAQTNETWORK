<?php

namespace App\Http\Controllers;

use App\Models\VPN;
use App\Models\User;
use App\Models\Mikrotik;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(){
        $members = User::get();

        foreach ($members as $member) {
            $member->vpn = VPN::where('unique_id', $member->unique_id)->count();
            $member->mikrotik = Mikrotik::where('unique_id', $member->unique_id)->count();
        }
        //dd($member->vpn);
        return view('Dashboard/PENGGUNA/MEMBER/index', compact('members'));
    }
    public function daftarvpn(Request $request){
        $unique_uid = $request->unique_id;
        $dataVPN = VPN::where('unique_id', $unique_uid)->get();
        return view('Dashboard/PENGGUNA/MEMBER/VPN/daftarvpn', compact('dataVPN'));

    }
    public function daftarmikrotik(Request $request){
        $unique_uid = $request->unique_id;
        $dataMikrotik = Mikrotik::where('unique_id', $unique_uid)->get();
        return view('Dashboard/PENGGUNA/MEMBER/MIKROTIK/daftarmikrotik', compact('dataMikrotik'));

    }
}
