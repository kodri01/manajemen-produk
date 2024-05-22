<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Resep;
use App\Models\StokKeluar;
use App\Models\StokMasuk;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Dashboard";

        $modelrole = DB::table('model_has_roles')->where('model_id', auth()->user()->id)->first();
        $role = Role::where('id', $modelrole->role_id)->first();

        $currance = Transaksi::select(DB::raw('SUM(transaksis.sub_total) as total'))->first();
        $totalProduk = Product::select(DB::raw('COUNT(id) as totalProduk'))->first();
        $masuk = StokMasuk::select(DB::raw('COUNT(id) as total'))->first();
        $keluar = StokKeluar::select(DB::raw('COUNT(id) as total'))->first();
        $resep = Resep::select(DB::raw('COUNT(id) as total'))->first();


        $supplier = Supplier::select(DB::raw('COUNT(id) as total'))->first();
        $stok = Product::with(['stokMasuk', 'stokKeluar'])->orderBy('created_at', 'desc')->paginate(3);
        $stok_masuk = StokMasuk::with('produk', 'supplier')->orderBy('created_at', 'desc')->paginate(3);
        $stok_keluar = StokKeluar::with('produk')->orderBy('created_at', 'desc')->paginate(3);
        $transaksi = Transaksi::with(['user'])->orderBy('created_at', 'desc')
            ->select(
                'no_transaksi',
                'created_at',
                DB::raw('SUM(transaksis.sub_total) as total')
            )
            ->groupBy('no_transaksi', 'created_at')
            ->paginate(3);
        $usersTransaksi = Transaksi::with('user')->orderBy('no_transaksi')->first();

        if ($role->name == 'administrator') {
            return view('pages.dashboard.index', compact('supplier', 'title', 'stok', 'stok_masuk', 'stok_keluar', 'transaksi', 'usersTransaksi', 'currance', 'totalProduk'));
        } elseif ($role->name == 'gudang') {
            return view('pages.dashboard.gudang', compact('masuk', 'title', 'stok', 'stok_masuk', 'stok_keluar', 'keluar', 'totalProduk'));
        } else {
            return view('pages.dashboard.produksi', compact('title', 'stok', 'resep', 'totalProduk'));
        }
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