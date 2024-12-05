<?php

namespace App\Http\Controllers;

use App\Models\Undian;
use Illuminate\Http\Request;

class DepanController extends Controller
{
    public function index(){
        return view('Depan/index');
    }
    public function undian(){
        $daftarundian = Undian::orderBy('id', 'DESC')->get();
        return view('Depan/INFORMASI/undian', compact('daftarundian'));
    }
}
