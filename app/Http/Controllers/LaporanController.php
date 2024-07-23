<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Master;
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

        $kasAwal = 6000000;
        $persediaanAwal = 2450000;
        $utangUsaha = 0;
        $utangBank = 0;
        $modalAwal = 6000000;
        $labaRugiAwal = 2450000;
        $debit = 0;
        $kredit = 0;
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

        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('hpp'));

        $debit = Laporan::whereYear('created_at', $selectedYear)->sum(DB::raw('debit'));
        $kredit = Laporan::whereYear('created_at', $selectedYear)->sum(DB::raw('kredit'));
        $masters = Master::get();
        $masterName = $masters->pluck('name')->toArray();
        $bebanUsaha = Laporan::whereIn('akun_kredit', $masterName)
            ->sum(DB::raw('kredit'));

        $labaKotor = ($persediaanAwal + $transaksi->sell) - $produkSell;

        $modal = ($modalAwal + $debit) - $kredit;
        $kas = ($modalAwal + $debit) - $kredit;
        // dd($modalAwal + $debit + $transaksi->sell);
        $labaRugi = $labaKotor - $bebanUsaha - $pajak;
        $persediaan = $labaKotor - $bebanUsaha;


        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = date('Y');
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

        $pendapatanAwal = 2450000;
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

        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('hpp'));


        $hargaProduk = Product::join('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->whereYear('stok_masuks.created_at', $selectedYear) // Filter berdasarkan tahun
            ->sum(DB::raw('products.harga * stok_masuks.stok_masuk'));

        $masters = Master::get();
        $masterName = $masters->pluck('name')->toArray();
        $kass = Laporan::whereIn('akun_kredit', $masterName)
            ->sum(DB::raw('kredit'));
        // dd($kass);
        $bebanUsaha = $kass;
        $labaKotor = ($pendapatanAwal + $transaksi->sell) - $produkSell;
        // }

        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = 2024;
        }

        $pendapatan = $labaKotor;
        $labaRugi = $pendapatan - $bebanUsaha - $pajak;

        return view('pages.laporan.laba_rugi', compact('setting', 'title', 'judul', 'tahun', 'pendapatan', 'bebanUsaha', 'pajak', 'labaRugi', 'selectedYear'));
    }

    public function per_modal(Request $request)
    {
        $title = "Laporan - Perubahan Modal";
        $judul = "Laporan Perubahan Modal";
        $setting = Setting::first();

        $modalAwal = 6000000;
        $persediaan = 2450000;
        $labaBersih = 0;
        $prive = 0;
        $pajak = 0;

        $tahun = Product::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->pluck('year')
            ->sort()
            ->toArray();

        $selectedYear = $request->input('tahun', reset($tahun));

        $debit = Laporan::whereYear('created_at', $selectedYear)->sum(DB::raw('debit'));
        $kredit = Laporan::whereYear('created_at', $selectedYear)->sum(DB::raw('kredit'));

        $transaksi = Transaksi::select(
            DB::raw('(SELECT SUM(transaksis.sub_total)) AS sell')
        )
            ->whereYear('created_at', $selectedYear)
            ->whereNull('transaksis.deleted_at')
            ->first();

        $produkSell = ProductSell::whereYear('created_at', $selectedYear)->sum(DB::raw('hpp'));


        $hargaProduk = Product::join('stok_masuks', 'products.id', '=', 'stok_masuks.produk_id')
            ->whereYear('stok_masuks.created_at', $selectedYear) // Filter berdasarkan tahun
            ->sum(DB::raw('products.harga * stok_masuks.stok_masuk'));

        $masters = Master::get();
        $masterName = $masters->pluck('name')->toArray();
        $kass = Laporan::whereIn('akun_kredit', $masterName)
            ->sum(DB::raw('kredit'));
        // dd($kass);
        $modal = ($modalAwal + $debit) - $kredit;
        $bebanUsaha = $kass;
        $labaKotor = ($persediaan + $transaksi->sell) - $produkSell;

        $labaBersih = $labaKotor - $bebanUsaha - $pajak;

        // $prive = $persediaan;
        // } else {
        //     $labaBersih = 0;
        // }

        // if ($produkModal != null) {
        //     $produkModal = $produkModal;
        // } else {
        //     $modalAwal = 6000000;
        // }

        if ($tahun != null) {
            $tahun;
        } else {
            $tahun[] = 2024;
        }
        $total = $labaBersih - $prive;
        $modalAkhir = $modal + $total;

        return view('pages.laporan.perubahan_modal', compact('setting', 'title', 'judul', 'tahun', 'modalAwal', 'labaBersih', 'prive', 'total', 'modalAkhir', 'selectedYear'));
    }

    public function jurnal_umum(Request $request)
    {
        $title = "Laporan - Jurnal Umum";
        $judul = "Laporan Jurnal Umum";
        $setting = Setting::first();
        $laporans = Laporan::get();

        return view('pages.laporan.jurnal', compact('setting', 'title', 'judul', 'laporans'));
    }

    public function buku_besar(Request $request)
    {
        $title = "Laporan - Buku Besar";
        $judul = "Laporan Buku Besar";
        $setting = Setting::first();
        $masters = Master::get();
        $masterName = $masters->pluck('name')->toArray();

        $laporans = Laporan::where(function ($query) use ($masterName) {
            $query->whereIn('akun_kredit', $masterName)
                ->orWhere('akun_debet', 'Penjualan')
                ->orWhere('akun_kredit', 'Kas');
        })
            ->select('akun_debet', 'debit', 'akun_kredit', 'kredit', 'no_jurnal', 'ket', 'created_at')
            ->get();
        $penjualan = Laporan::where('akun_debet', 'Penjualan')
            ->select('akun_debet', 'debit', 'no_jurnal', 'ket', 'created_at')
            ->get();

        $beban = Laporan::whereIn('akun_kredit', $masterName)
            ->select('akun_kredit', 'kredit', 'no_jurnal', 'ket', 'created_at')
            ->get();
        return view('pages.laporan.buku_besar', compact('setting', 'title', 'judul', 'laporans', 'penjualan', 'beban', 'masters', 'masterName'));
    }
}
