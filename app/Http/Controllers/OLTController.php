<?php

namespace App\Http\Controllers;

use App\Models\OLT;
use Illuminate\Http\Request;

class OLTController extends Controller
{
    public function index(){
        $olts = OLT::where('unique_id', auth()->user()->unique_id)->get();
        return view('Dashboard.OLT.index', compact('olts'));
    }
    public function tambaholt(Request $req){
        $ipolt = $req->input('ipolt');
        $site = $req->input('site');
       
        $unique_id = auth()->user()->unique_id;
        if($ipolt == null && $site == null ){
            
            session()->flash('error', "Harap Isikan Data OLT");
            return redirect()->back();
        }
        try {
            // Assuming Mikrotik::create() method exists
            $data = OLT::create([
                'ipolt' => $ipolt,
                'site' => $site,
               
                'unique_id' => $unique_id
            ]);

            session()->flash('success', "OLT Site ".$site." Berhasil Di Tambahkan");
            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data: ' . $e->getMessage(),
            ]);
        }
    }
    public function aksesOLT(Request $request)
    {
        $ipolt = $request->query('ipolt');
        return view('Dashboard.OLT.olt-akses', compact('ipolt'));
    }
    public function hapusolt($id){
        $data = OLT::find($id)->delete();
        session()->flash('success', "Berhasil Menghapus Data OLT");
            return redirect()->back();
    }
}
