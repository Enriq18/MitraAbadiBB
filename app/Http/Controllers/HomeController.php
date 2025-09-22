<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function cetakLaporan(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');

        $query = Transaksi::query();

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($day) {
            $query->whereDay('created_at', $day);
        }

        $semuaTransaksi = $query->where('status', 'selesai')->get();

        return view('cetakLaporan')->with([
            'semuaTransaksi' => $semuaTransaksi
        ]);
    }

    public function cetakStruk()
    {
        $semuaTransaksi = Transaksi::where('status', 'selesai')->get();
        return view('cetakStruk')->with([
            'semuaTransaksi' => $semuaTransaksi
        ]);
    }
}
