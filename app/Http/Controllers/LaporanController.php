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
    public function index(Request $request)
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


        $tahun = Product::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $selectedYear = $request->input('tahun', reset($tahun));

        // Ambil data transaksi berdasarkan tahun yang dipilih
        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereYear('created_at', $selectedYear)
            ->whereNull('transaksis.deleted_at')
            ->first();

        $po = OrderStok::select(
            DB::raw('(SELECT SUM(order_stoks.sub_total)) AS po')
        )
            ->whereNull('order_stoks.deleted_at')
            ->first();

        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual * qty_in '));
        // $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual * (qty_in - qty_out)'));
        $produkModal = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual * qty_in'));
        $produkHarga = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual'));


        $hargaProduk = Product::join('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->whereYear('stok_masuks.created_at', $selectedYear) // Filter berdasarkan tahun
            ->sum(DB::raw('products.harga * stok_masuks.stok_masuk'));

        if ($produkSell != null) {
            $modal = $produkModal;
            $persediaan = $produkSell;
        } else {
            $modal = 0;
            $persediaan = 0;
        }

        if ($transaksi != null) {
            $kas = $transaksi->sell - $produkHarga;
            $labaRugi = $transaksi->sell - $produkHarga;
        } else {
            $kas = 0;
        }


        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = 2024;
        }

        $totalAktiva = $kas + $persediaan;
        $totalUtang = $utangBank + $utangUsaha;
        $totalEkuitas = $modal + $labaRugi;

        $totalKewajiban = $totalUtang + $totalEkuitas;

        return view('pages.laporan.neraca', compact('setting', 'title', 'judul', 'tahun', 'kas', 'persediaan', 'utangUsaha', 'utangBank', 'totalUtang', 'modal', 'labaRugi', 'totalAktiva', 'totalEkuitas', 'totalKewajiban', 'selectedYear'));
    }

    public function laba_rugi(Request $request)
    {
        $title = "Laporan - Laba Rugi";
        $judul = "Laporan Laba Rugi";
        $setting = Setting::first();

        $pendapatan = 0;
        $bebanUsaha = 0;
        $pajak = 0;

        $tahun = Product::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $selectedYear = $request->input('tahun', reset($tahun));

        // Ambil data transaksi berdasarkan tahun yang dipilih
        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereYear('created_at', $selectedYear)
            ->whereNull('transaksis.deleted_at')
            ->first();

        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual'));


        $hargaProduk = Product::join('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->whereYear('stok_masuks.created_at', $selectedYear) // Filter berdasarkan tahun
            ->sum(DB::raw('products.harga * stok_masuks.stok_masuk'));


        if ($produkSell != null) {
            $bebanUsaha = $produkSell;
        } else {
            $bebanUsaha = 0;
        }

        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = 2024;
        }

        $pendapatan = $transaksi->sell;
        $labaRugi = $pendapatan - $bebanUsaha;

        return view('pages.laporan.laba_rugi', compact('setting', 'title', 'judul', 'tahun', 'pendapatan', 'bebanUsaha', 'pajak', 'labaRugi', 'selectedYear'));
    }

    public function per_modal(Request $request)
    {
        $title = "Laporan - Perubahan Modal";
        $judul = "Laporan Perubahan Modal";
        $setting = Setting::first();

        $modalAwal = 0;
        $labaBersih = 0;
        $prive = 0;

        $tahun = Product::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $selectedYear = $request->input('tahun', reset($tahun));

        // Ambil data transaksi berdasarkan tahun yang dipilih
        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereYear('created_at', $selectedYear)
            ->whereNull('transaksis.deleted_at')
            ->first();

        $produkModal = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual * qty_in'));
        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('harga_jual'));


        if ($produkSell != null) {
            $labaBersih = $transaksi->sell - $produkSell;
        } else {
            $labaBersih = 0;
        }

        if ($produkModal != null) {
            $modalAwal = $produkModal;
        } else {
            $modalAwal = 0;
        }

        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = 2024;
        }
        $total = $labaBersih - $prive;
        $modalAkhir = $modalAwal + $total;

        return view('pages.laporan.perubahan_modal', compact('setting', 'title', 'judul', 'tahun', 'modalAwal', 'labaBersih', 'prive', 'total', 'modalAkhir', 'selectedYear'));
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