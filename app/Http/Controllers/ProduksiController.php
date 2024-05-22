<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Resep;
use App\Models\StokKeluar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Produksi - Data Resep";
        $judul = "Data Resep";
        $reseps = Resep::with(['produk'])
            ->select(
                'no_resep',
                'nama_resep',
                'keterangan',
            )
            ->groupBy('no_resep', 'nama_resep', 'keterangan')
            ->get();
        $produks = Product::get();
        return view('pages.produksi.index', compact('title', 'judul', 'reseps', 'produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function persediaan()
    {
        $title = "Produksi - Persediaan Barang";
        $judul = "Persediaan Barang";
        $products = Product::with(['stokMasuk', 'stokKeluar'])->orderBy('updated_at', 'desc')->get();
        $product = Product::with(['stokMasuk', 'stokKeluar'])->first();
        return view('pages.produksi.persediaan', compact('title', 'judul', 'products', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $date = Carbon::now()->format('mY');
        $nama_resep = $request->nama_resep;
        $keterangan = $request->keterangan;
        $nama_barang = $request->nama_barang;
        $qty = $request->qty;
        $instruksi = $request->instruksi;
        $no_resep = "RES" . $date . rand(100000, 999999);

        for ($i = 0; $i < count($nama_barang); $i++) {
            Resep::create([
                'no_resep' => $no_resep,
                'produk_id' => $nama_barang[$i],
                'qty' => $qty[$i],
                'nama_resep' => $nama_resep,
                'keterangan' => $keterangan,
                'instruksi' => $instruksi,
            ]);
        }

        return redirect()->route('resep')
            ->with('success', 'Resep Berhasil Berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $no)
    {

        $title = "Produksi - Details Resep";
        $judul = "Details Resep";
        $reseps = Resep::where('no_resep', '=', $no)->get();
        $resep = Resep::where('no_resep', '=', $no)->first();
        return view('pages.produksi.resep_details', compact('title', 'judul', 'resep', 'reseps'));
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
    public function destroy(string $no_resep)
    {
        Resep::where('no_resep', $no_resep)->delete();
        return redirect()->back()->with('error', 'Data Resep Berhasil dihapus');
    }
}