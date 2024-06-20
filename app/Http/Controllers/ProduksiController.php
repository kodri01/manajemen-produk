<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSell;
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
        $resep = Resep::first();
        $reseps = Resep::with(['produk'])
            ->select(
                'no_resep',
                'nama_resep',
                'keterangan',
            )
            ->groupBy('no_resep', 'nama_resep', 'keterangan')
            ->get();
        $produks = Product::get();
        return view('pages.produksi.index', compact('title', 'judul', 'reseps', 'produks', 'resep'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function persediaan()
    {
        $title = "Produksi - Persediaan Produksi";
        $judul = "Persediaan Produksi";
        $products = ProductSell::orderBy('created_at', 'asc')->get();
        // $product = ProductSell::with(['stokMasuk', 'stokKeluar'])->first();
        return view('pages.produksi.persediaan', compact('title', 'judul', 'products'));
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

    public function produksi_store(Request $request, string $no)
    {
        $reseps = Resep::with('produk')->where('no_resep', '=', $no)->get();
        $totalHargaBaku = 0;
        $namaProduk = $request->nama_produk;
        $qtyIn = $request->qty_in;
        $margin = $request->margin / 100;
        $biayaTenagaKerja = $request->biaya_pekerja;
        $biayaOverhead = $request->biaya_overhead;


        foreach ($reseps as $resep) {
            // Pastikan relasi produk tidak kosong
            if ($resep->produk) {
                $produk_id = $resep->produk_id;
                $qty = $resep->qty;
                $hargaBaku = $resep->produk->harga;
                $hargaProduksi = $qty * $hargaBaku;
                $totalHargaBaku += $hargaProduksi;
                $hpp = $totalHargaBaku + $biayaTenagaKerja + $biayaOverhead;
                $marginLaba = $hpp * $margin;
                $hargaJual = $hpp + $marginLaba;

                // Masukkan data ke dalam tabel StokKeluar
                StokKeluar::create([
                    'produk_id' => $produk_id,
                    'stok_keluar' => $qty,
                    'no_dokumen' => $resep->no_resep,
                    'keterangan' => 'Bahan Baku Produksi Resep',
                ]);
            }
        }

        $kdProduct = "SLL"  . rand(1000, 9999) . date('dm');
        ProductSell::create([
            'no_resep' => $resep->no_resep,
            'kode_product' => $kdProduct,
            'nama_product' => $namaProduk,
            'harga_jual' => $hargaJual,
            'qty_in' => $qtyIn,
            'qty_out' => 0,
        ]);

        return redirect()->route('persediaan')
            ->with('success', 'Produksi Berhasil Ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = ProductSell::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $produk = ProductSell::findOrFail($request->id);
        $produk->nama_product = $request->nama_produk;
        $produk->qty_in = $request->qty;
        $produk->save();

        return redirect()->back()->with('success', 'Update Stok Berhasil');
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
