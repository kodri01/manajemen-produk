<?php

namespace App\Http\Controllers;

use App\Models\OrderStok;
use App\Models\Product;
use App\Models\ProductSell;
use App\Models\Setting;
use App\Models\StokMasuk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Laporan - Neraca";
        $judul = "Laporan Neraca";
        $setting = Setting::first();

        $kas = 0;
        $persediaan = 0;

        $utangUsaha = 0;
        $utangBank = 0;

        $modal = 0;
        $labaRugi = 0;


        $tahun = StokMasuk::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereNull('transaksis.deleted_at')
            ->first();

        $po = OrderStok::select(
            DB::raw('(SELECT SUM(order_stoks.sub_total)) AS po')
        )
            ->whereNull('order_stoks.deleted_at')
            ->first();

        $produk = Product::with(['stokMasuk', 'stokKeluar'])
            ->leftJoin('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->leftJoin('stok_keluars', 'products.id', '=', 'stok_keluars.produk_id')
            ->selectRaw(
                '
        products.harga as harga, 
        stok_masuks.stok_masuk as stokMasuk, 
        SUM(stok_keluars.stok_keluar) as stokKeluar
        '
            )
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'stokMasuk', 'harga')
            ->first();


        $kas = $transaksi->sell - $po->po;
        $persediaan = $po->po;
        $totalAktiva = $kas + $persediaan;

        $totalUtang = $utangBank + $utangUsaha;

        $modal = $produk->stokMasuk * $produk->harga;
        $labaRugi = $transaksi->sell - $modal;
        $totalEkuitas = $modal + $labaRugi;

        $totalKewajiban = $totalUtang + $totalEkuitas;

        return view('pages.laporan.neraca', compact('setting', 'title', 'judul', 'tahun', 'kas', 'persediaan', 'utangUsaha', 'utangBank', 'totalUtang', 'modal', 'labaRugi', 'totalAktiva', 'totalEkuitas', 'totalKewajiban'));
    }

    public function laba_rugi()
    {
        $title = "Laporan - Laba Rugi";
        $judul = "Laporan Laba Rugi";
        $setting = Setting::first();

        $pendapatan = 0;
        $bebanUsaha = 0;
        $pajak = 0;

        $tahun = StokMasuk::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereNull('transaksis.deleted_at')
            ->first();

        $produk = Product::with(['stokMasuk', 'stokKeluar'])
            ->leftJoin('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->leftJoin('stok_keluars', 'products.id', '=', 'stok_keluars.produk_id')
            ->selectRaw(
                '
        products.harga as harga, 
        stok_masuks.stok_masuk as stokMasuk, 
        SUM(stok_keluars.stok_keluar) as stokKeluar
        '
            )
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'stokMasuk', 'harga')
            ->first();

        $pendapatan = $transaksi->sell;
        $bebanUsaha = $produk->stokMasuk * $produk->harga;
        $labaRugi = $pendapatan - $bebanUsaha;

        return view('pages.laporan.laba_rugi', compact('setting', 'title', 'judul', 'tahun', 'pendapatan', 'bebanUsaha', 'pajak', 'labaRugi'));
    }

    public function per_modal()
    {
        $title = "Laporan - Perubahan Modal";
        $judul = "Laporan Perubahan Modal";
        $setting = Setting::first();

        $modalAwal = 0;
        $labaBersih = 0;
        $prive = 0;

        $tahun = StokMasuk::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereNull('transaksis.deleted_at')
            ->first();

        $produk = Product::with(['stokMasuk', 'stokKeluar'])
            ->leftJoin('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->leftJoin('stok_keluars', 'products.id', '=', 'stok_keluars.produk_id')
            ->selectRaw(
                '
            products.harga as harga, 
            stok_masuks.stok_masuk as stokMasuk, 
            SUM(stok_keluars.stok_keluar) as stokKeluar
            '
            )
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'stokMasuk', 'harga')
            ->first();

        $modalAwal = $produk->stokMasuk * $produk->harga;
        $labaBersih = $transaksi->sell - ($produk->stokMasuk * $produk->harga);
        $total = $labaBersih - $prive;
        $modalAkhir = $modalAwal + $total;

        return view('pages.laporan.perubahan_modal', compact('setting', 'title', 'judul', 'tahun', 'modalAwal', 'labaBersih', 'prive', 'total', 'modalAkhir'));
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
        //
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
    public function destroy(string $id)
    {
        //
    }
}