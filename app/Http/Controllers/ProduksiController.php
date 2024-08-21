<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Laporan;
use App\Models\Product;
use App\Models\ProductSell;
use App\Models\Resep;
use App\Models\Setting;
use App\Models\StokKeluar;
use App\Models\StokMasuk;
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
        $setting = Setting::first();

        $resep = Resep::first();
        $reseps = Resep::with(['baku'])
            ->select(
                'no_resep',
                'nama_resep',
                'keterangan',
            )->orderBy('created_at', 'desc')
            ->groupBy('no_resep', 'nama_resep', 'keterangan')
            ->get();
        $getBarangs = BahanBaku::get();
        // $getBarangs = Product::with('stokMasuk', 'stokKeluar')->get();
        // $produks = $getBarangs->unique('nama_barang');
        $produks = $getBarangs->unique('name'); 
        // dd($produks);
        return view('pages.produksi.index', compact('setting', 'title', 'judul', 'reseps', 'produks', 'resep'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function persediaan()
    {
        $title = "Produksi - Persediaan Produksi";
        $judul = "Persediaan Produksi";
        $setting = Setting::first();

        $products = ProductSell::orderBy('created_at', 'desc')->get();
        // $product = ProductSell::with(['stokMasuk', 'stokKeluar'])->first();
        return view('pages.produksi.persediaan', compact('setting', 'title', 'judul', 'products'));
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
            // $baku = BahanBaku::where('id', $nama_barang[$i])->first();
            
            Resep::create([
                'no_resep' => $no_resep,
                'baku_id' => $nama_barang[$i],   
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
        $setting = Setting::first();

        $reseps = Resep::where('no_resep', '=', $no)->get();
        $resep = Resep::where('no_resep', '=', $no)->first();
        return view('pages.produksi.resep_details', compact('setting', 'title', 'judul', 'resep', 'reseps'));
    }

    public function produksi_store(Request $request, string $no)
    {
        $noResep = $no; // Ambil no dari parameter URL
        $reseps = Resep::with('baku')->where('no_resep', '=', $noResep)->get();
        $totalHargaBaku = 0;
        $namaProduk = $request->nama_produk;
        $qtyIn = $request->qty_in;
        $margin = $request->margin / 100;
        $biayaTenagaKerja = $request->biaya_pekerja;
        $biayaOverhead = $request->biaya_overhead;

        // Mengambil semua produk terkait resep dan menghitung harga rata-rata
        $produkIds = $reseps->pluck('baku_id')->unique();
        $produkHargaRataRata = BahanBaku::whereIn('id', $produkIds)->get()->groupBy('name')->map(function ($group) {
            return $group->avg('harga');
        });

        foreach ($reseps as $resep) {
            if ($resep->baku) {
                // $produk_id = $resep->produk_id;  
                $baku_id = $resep->baku_id;
                $qty = $resep->qty;
                $hargaBaku = $produkHargaRataRata[$resep->baku->name];
                $hargaProduksi = $qty * $hargaBaku;
                $totalHargaBaku += $hargaProduksi;
            }
        }

        $hpp = $totalHargaBaku + $biayaTenagaKerja + $biayaOverhead;
        $marginLaba = $hpp * $margin;
        $hargaJual = $hpp + $marginLaba;

        foreach ($reseps as $resep) {
            StokKeluar::create([
                'baku_id' => $resep->baku_id,
                // 'produk_id' => $resep->produk_id,
                'stok_keluar' => $resep->qty,
                'no_dokumen' => $resep->no_resep,
                'keterangan' => 'Bahan Baku Produksi Resep',
            ]);
        }

        $kdProduct = "SLL" . rand(1000, 9999) . date('dm');
        ProductSell::create([
            'no_resep' => $resep->no_resep,
            'kode_product' => $kdProduct,
            'nama_product' => $namaProduk,  
            'hpp' => $hpp,
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

        $reseps = Resep::where('no_resep', $no_resep)->get();

        foreach ($reseps as $resep) {
            if ($resep->produk) {
                $produk_id = $resep->produk_id;
                $qty = $resep->qty;

                // Kurangi stok pada tabel StokKeluar
                $stokKeluar = StokKeluar::where('produk_id', $produk_id)
                    ->where('no_dokumen', $resep->no_resep)
                    ->first();
                if ($stokKeluar) {
                    $stokKeluar->stok_keluar -= $qty;
                    if ($stokKeluar->stok_keluar <= 0) {
                        $stokKeluar->delete();
                    } else {
                        $stokKeluar->save();
                    }
                }
            }
        }

        ProductSell::where('no_resep', $no_resep)->delete();
        // Hapus resep
        Resep::where('no_resep', $no_resep)->delete();

        return redirect()->back()->with('error', 'Data Resep Berhasil dihapus');
    }
}