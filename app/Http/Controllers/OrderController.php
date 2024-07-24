<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\OrderStok;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Product - Purchase Order";
        $judul = "Purchase Order (PO) Barang";
        $setting = Setting::first();

        $subQuery = OrderStok::select('no_order', DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('no_order');

        $orders = OrderStok::with(['produk', 'supplier'])
            ->joinSub($subQuery, 'latest_orders', function ($join) {
                $join->on('order_stoks.no_order', '=', 'latest_orders.no_order');
            })
            ->orderBy('order_stoks.created_at', 'desc')
            ->select(
                'order_stoks.no_order',
                'order_stoks.supplier_id',
                'latest_orders.latest_created_at as tgl_order',
                DB::raw('COUNT(order_stoks.no_order) as jml_produk'),
                DB::raw('SUM(order_stoks.qty) as qty')
            )
            ->groupBy('order_stoks.no_order', 'order_stoks.supplier_id', 'latest_orders.latest_created_at')
            ->get();


        $getBarangs = Product::with('stokMasuk', 'stokKeluar')->get();

        $produks = $getBarangs->groupBy('nama_barang')->map(function ($group) {
            $averagePrice = $group->avg('harga');
            $group[0]->harga = $averagePrice;
            return $group[0];
        });

        // Mengubah hasil menjadi koleksi
        $produks = collect($produks->values());

        $suppliers = Supplier::get();
        return view('pages.product.order', compact('setting', 'title', 'judul', 'orders', 'produks', 'suppliers'));
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
        $satuan = $request->satuan;
        $qty = $request->qty;
        $supplier = $request->supplier;
        $no_order = "PO" . $date . rand(100000, 999999);
        $no_jurnal = "JU" . rand(001, 999);
        $harga = $request->harga;
        $total = 0;

        for ($i = 0; $i < count($nama_barang); $i++) {
            $sub_total = $qty[$i] * $harga[$i];
            $total += $sub_total;
            OrderStok::create([
                'no_order' => $no_order,
                'supplier_id' => $supplier,
                'produk_id' => $nama_barang[$i],
                'satuan' => $satuan[$i],
                'qty' => $qty[$i],
                'harga' => $harga[$i],
                'sub_total' => $sub_total,
            ]);
        }

        Laporan::create([
            'no_jurnal' => $no_jurnal,
            'ket' => $no_order,
            'akun_debet' => 'Pembelian',
            'debit' => $total,
            'akun_kredit' => 'Kas',
            'kredit' => $total,
        ]);

        return redirect()->route('product.order')
            ->with('success', 'PO Barang Berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $no)
    {

        $title = "Product - Details Order";
        $judul = "Details PO Barang";
        $setting = Setting::first();

        $total = OrderStok::select(
            DB::raw('SUM(qty) as qty'),
            DB::raw('SUM(harga) as harga'),
            DB::raw('SUM(sub_total) as total_harga'),
        )->where('no_order', $no)->first();
        $orders = OrderStok::where('no_order', '=', $no)->get();
        $order = OrderStok::where('no_order', '=', $no)->first();
        return view('pages.product.order_details', compact('setting', 'title', 'judul', 'order', 'orders', 'total'));
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
    public function destroy(string $no_order)
    {
        Laporan::where('ket', $no_order)->delete();
        OrderStok::where('no_order', $no_order)->delete();
        return redirect()->back()->with('error', 'Data Order Berhasil dihapus');
    }
}