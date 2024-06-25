<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSell;
use App\Models\Setting;
use App\Models\StokKeluar;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Transaksi";
        $judul = "Transaksi";
        $setting = Setting::first();

        $produks = ProductSell::orderBy('created_at', 'asc')->get();

        $subQuery = Transaksi::select('no_transaksi', DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('no_transaksi');

        $transaksis = Transaksi::with(['produkSell'])
            ->joinSub($subQuery, 'latest_transaksis', function ($join) {
                $join->on('transaksis.no_transaksi', '=', 'latest_transaksis.no_transaksi');
            })
            ->orderBy('transaksis.created_at', 'desc')
            ->select(
                'transaksis.no_transaksi',
                'latest_transaksis.latest_created_at as tgl_transaksi',
                DB::raw('SUM(transaksis.sub_total) as total')
            )
            ->groupBy('transaksis.no_transaksi',  'latest_transaksis.latest_created_at')
            ->get();

        return view('pages.transaksi.index', compact('setting', 'title', 'judul', 'produks', 'transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $date = Carbon::now()->format('mY');
        $nama_barang = $request->nama_barang;
        $qty = $request->qty;
        $no_transaksi = "TRS" . $date . rand(100000, 999999);
        $harga = $request->harga;
        $sub_total = $request->subtotal;


        for ($i = 0; $i < count($nama_barang); $i++) {
            $transaksi = Transaksi::create([
                'user_id' => Auth::user()->id,
                'produk_id' => $nama_barang[$i],
                'no_transaksi' => $no_transaksi,
                'harga_barang' => $harga[$i],
                'qty' => $qty[$i],
                'sub_total' => $sub_total[$i],
            ]);

            $produk = ProductSell::where('id', $nama_barang[$i])->first();
            if ($produk) {
                $produk->qty_out += $qty[$i]; // Menambahkan qty ke qty_out yang sudah ada
                $produk->save();
            }
        }


        return redirect()->route('invoice.transaksi', $transaksi->no_transaksi)
            ->with('success', 'Transaksi Berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $no_transaksi)
    {
        Transaksi::where('no_transaksi', $no_transaksi)->delete();
        return redirect()->back()->with('error', 'Data Transaksi Berhasil dihapus');
    }
}