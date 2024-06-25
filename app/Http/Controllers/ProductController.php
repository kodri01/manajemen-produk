<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StokKeluar;
use App\Models\StokMasuk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Product - Stok Barang";
        $judul = "Stok Barang";
        $setting = Setting::first();

        $products = Product::with(['stokMasuk', 'stokKeluar'])->orderBy('created_at', 'asc')->get();
        $product = Product::with(['stokMasuk', 'stokKeluar'])->first();
        return view('pages.product.index', compact('setting', 'title', 'judul', 'products', 'product'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'nama_barang' => 'required|min:3',
            'satuan'  => 'required',
            // 'harga'  => 'required',
        ];

        $messages = [
            'nama_barang.required'  => 'Nama Barang wajib diisi',
            'nama_barang.min'       => 'Nama Barang minimal 3 karakter',
            'satuan.required'  => 'Satuan wajib dipilih',
            // 'harga.required'  => 'Harga wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kdBarang = "PRD" . $request->satuan . rand(1000, 9999);

        Product::create([
            'kode_barang' => $kdBarang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
        ]);

        return redirect()->route('product')
            ->with('success', 'Data Barang Berhasil ditambahkan');
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
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $rules = [
            'nama_barang' => 'required|min:3',
            'satuan'  => 'required',
            // 'harga'  => 'required',

        ];

        $messages = [
            'nama_barang.required'  => 'Nama Barang wajib diisi',
            'nama_barang.min'       => 'Nama Barang minimal 3 karakter',
            'satuan.required'  => 'Satuan wajib dipilih',
            // 'harga.required'  => 'Harga wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $produk = Product::findOrFail($request->id);
        $produk->nama_barang = $request->nama_barang;
        $produk->satuan = $request->satuan;
        // $produk->harga = $request->harga;

        if ($request->satuan) {
            $kdBarang = "PRD" . $request->satuan . rand(1000, 9999);
            $produk->kode_barang = $kdBarang;
        }
        $produk->save();

        return redirect()->back()->with('success', 'Data Barang Berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::find($id)->delete();
        return redirect()->back()->with('error', 'Data Berhasil dihapus');
    }

    public function masuk()
    {
        $stok_masuk = StokMasuk::with('produk', 'supplier')->orderBy('created_at', 'asc')->get();
        $suppliers = Supplier::get();
        $produks = Product::get();
        $setting = Setting::first();

        $title = "Product - Barang Masuk";
        $judul = "Barang Masuk";
        return view('pages.product.masuk', compact('setting', 'title', 'judul', 'stok_masuk', 'suppliers', 'produks'));
    }

    public function masuk_store(Request $request)
    {
        $rules = [
            'supplier' => 'required',
            'invoice' => 'required',
            'jml_masuk'     => 'required',
            'harga'      => 'required',
            'keterangan'      => 'required',
        ];

        $messages = [
            'supplier.required'  => 'Supplier wajib dipilih',
            'invoice.required'  => 'Nomor Invoice Wajib diisi',
            'jml_masuk.required' => 'Quantity Barang wajib diisi',
            'keterangan.required'  => 'Keterangan wajib diisi',
            'harga.required'  => 'Harga wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kdBarang = "PRD" . $request->satuan . rand(1000, 9999) . date('dm');

        $produk = Product::create([
            'kode_barang' => $kdBarang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
        ]);

        StokMasuk::create([
            'produk_id' => $produk->id,
            'supplier_id' => $request->supplier,
            'invoice' => $request->invoice,
            'stok_masuk' => $request->jml_masuk,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('product.masuk')
            ->with('success', 'Data Berhasil ditambahkan');
    }

    public function edit_masuk($id)
    {
        $product = StokMasuk::findOrFail($id);
        return response()->json($product);
    }

    public function update_masuk(Request $request)
    {
        $rules = [
            'produk' => 'required',
            'supplier' => 'required',
            'invoice' => 'required',
            'jml_masuk'     => 'required',
            'keterangan'      => 'required',
        ];

        $messages = [
            'produk.required'  => 'Supplier wajib dipilih',
            'supplier.required'  => 'Supplier wajib dipilih',
            'invoice.required'  => 'Nomor Invoice Wajib diisi',
            'jml_masuk.required' => 'Quantity Barang wajib diisi',
            'keterangan.required'  => 'Keterangan wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $masuk = StokMasuk::findOrFail($request->id);
        $masuk->produk_id = $request->produk;
        $masuk->supplier_id = $request->supplier;
        $masuk->invoice = $request->invoice;
        $masuk->stok_masuk = $request->jml_masuk;
        $masuk->keterangan = $request->keterangan;

        $masuk->save();

        return redirect()->back()->with('success', 'Data Berhasil diubah');
    }

    public function destroy_masuk($id)
    {
        StokMasuk::find($id)->delete();
        return redirect()->back()->with('error', 'Data Berhasil dihapus');
    }

    public function keluar()
    {
        $stok_keluar = StokKeluar::with('produk')->orderBy('created_at', 'desc')->get();
        $produks = Product::get();
        $setting = Setting::first();

        $title = "Product - Barang Keluar";
        $judul = "Barang Keluar";
        return view('pages.product.keluar', compact('setting', 'title', 'judul', 'stok_keluar', 'produks'));
    }

    public function keluar_store(Request $request)
    {
        $rules = [
            'no_dokumen' => 'required',
            'jml_keluar'     => 'required',
            'keterangan'      => 'required',
        ];

        $messages = [
            'no_dokumen.required' => 'Nomor Dokumen wajib diisi',
            'jml_keluar.required' => 'Quantity Barang wajib diisi',
            'keterangan.required'  => 'Keterangan wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        StokKeluar::create([
            'produk_id' => $request->produk,
            'stok_keluar' => $request->jml_keluar,
            'no_dokumen' => $request->no_dokumen,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('product.keluar')
            ->with('success', 'Data Berhasil ditambahkan');
    }

    public function edit_keluar($id)
    {
        $product = StokKeluar::findOrFail($id);
        return response()->json($product);
    }

    public function update_keluar(Request $request)
    {
        $rules = [
            'produk' => 'required',
            'no_dokumen' => 'required',
            'jml_keluar'     => 'required',
            'keterangan'      => 'required',
        ];

        $messages = [
            'produk.required'  => 'Nama Barang wajib dipilih',
            'no_dokumen.required'  => 'Nomor Dokumen wajib diisi',
            'jml_keluar.required' => 'Quantity Barang wajib diisi',
            'keterangan.required'  => 'Keterangan wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $keluar = StokKeluar::findOrFail($request->id);
        $keluar->produk_id = $request->produk;
        $keluar->no_dokumen = $request->no_dokumen;
        $keluar->stok_keluar = $request->jml_keluar;
        $keluar->keterangan = $request->keterangan;

        $keluar->save();

        return redirect()->back()->with('success', 'Data Berhasil diubah');
    }

    public function destroy_keluar($id)
    {
        StokKeluar::find($id)->delete();
        return redirect()->back()->with('error', 'Data Berhasil dihapus');
    }

    /**
     * Show the form for creating a new resource.
     */
}