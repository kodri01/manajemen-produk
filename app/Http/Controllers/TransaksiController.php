<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Master;
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
        $no_jurnal = "JU" . rand(001, 999);
        $harga = $request->harga;
        $sub_total = $request->subtotal;
        $totalSub = 0;
        $total = 0;


        for ($i = 0; $i < count($nama_barang); $i++) {
            $transaksi = Transaksi::create([
                'user_id' => Auth::user()->id,
                'produk_sell_id' => $nama_barang[$i],
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
            $totalSub = $sub_total[$i];
            $total += $totalSub;
        }

        $hpp = $produk->hpp;

        Laporan::create([
            'no_jurnal' => $no_jurnal,
            'ket' => $no_transaksi,
            'akun_debet' => 'Kas',
            'debit' => $total,
            'akun_hpp' => 'HPP',
            'hpp' => $hpp,
            'akun_kredit' => 'Penjualan',
            'kredit' => $total,
            'akun_persediaan' => 'Persediaan',
            'persediaan' => $hpp,
        ]);


        return redirect()->route('invoice.transaksi', $transaksi->no_transaksi)
            ->with('success', 'Transaksi Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $no_transaksi)
    {
        $transaksis = Transaksi::where('no_transaksi', $no_transaksi)->get();

        foreach ($transaksis as $transaksi) {
            $produk_sell_id = $transaksi->produk_sell_id;
            $qty = $transaksi->qty;

            // Update ProductSell untuk mengembalikan qty_in dan qty_out
            $productSell = ProductSell::where('id', $produk_sell_id)->first();
            if ($productSell) {
                $productSell->qty_out -= $qty;

                // Pastikan nilai qty_out tidak negatif
                if ($productSell->qty_out < 0) {
                    $productSell->qty_out = 0;
                }
                $productSell->save();
            }
        }

        Transaksi::where('no_transaksi', $no_transaksi)->delete();
        return redirect()->back()->with('error', 'Data Transaksi Berhasil dihapus');
    }


    public function kas_keluar()
    {
        $title = "Transaksi - Kas Keluar";
        $judul = "Kas Keluar";
        $setting = Setting::first();
        $masters = Master::get();
        $masterName = $masters->pluck('name')->toArray();

        $kass = Laporan::whereIn('akun_debet', $masterName)
            ->select('akun_debet', 'debit', 'no_jurnal', 'ket', 'created_at', 'id')
            ->get();

        // dd($kass);
        return view('pages.transaksi.kas_keluar', compact('setting', 'title', 'judul', 'kass', 'masters'));
    }

    public function store_kas(Request $request)
    {
        $no_jurnal = "JU" . rand(001, 999);

        Laporan::create([
            'no_jurnal' => $no_jurnal,
            'ket' => $request->ket,
            'akun_debet' => $request->akun,
            'debit' => $request->nominal,
            'akun_kredit' => 'Kas',
            'kredit' => $request->nominal,
        ]);

        return redirect()->route('kas')
            ->with('success', 'Pencatatan Kas Keluar Berhasil');
    }

    public function edit_kas($id)
    {
        $kas = Laporan::findOrFail($id);
        return response()->json($kas);
    }

    public function update_kas(Request $request)
    {
        $kas = Laporan::findOrFail($request->id);
        $kas->akun_debet = $request->akun;
        $kas->debit = $request->nominal;
        $kas->ket = $request->ket;

        $kas->save();

        return redirect()->back()->with('success', 'Data Kas Keluar Berhasil diubah');
    }

    public function destroy_kas($id)
    {
        Laporan::findOrFail($id)->delete();
        return redirect()->back()->with('error', 'Data Kas Keluar Berhasil dihapus');
    }
}