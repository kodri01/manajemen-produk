<?php

namespace App\Http\Controllers;

use App\Models\OrderStok;
use App\Models\Product;
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
        // $produks = Product::with('stokMasuk', 'stokKeluar')->get();
        $getBarangs = Product::with('stokMasuk', 'stokKeluar')->get();
        $produks = $getBarangs->unique('nama_barang');

        $suppliers = Supplier::get();
        return view('pages.product.order', compact('title', 'judul', 'orders', 'produks', 'suppliers'));
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
        $harga = $request->harga;

        for ($i = 0; $i < count($nama_barang); $i++) {
            $sub_total = $qty[$i] * $harga[$i];

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
        $total = OrderStok::select(
            DB::raw('SUM(qty) as qty'),
            DB::raw('SUM(harga) as harga'),
            DB::raw('SUM(sub_total) as total_harga'),
        )->where('no_order', $no)->first();
        $orders = OrderStok::where('no_order', '=', $no)->get();
        $order = OrderStok::where('no_order', '=', $no)->first();
        return view('pages.product.order_details', compact('title', 'judul', 'order', 'orders', 'total'));
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
        OrderStok::where('no_order', $no_order)->delete();
        return redirect()->back()->with('error', 'Data Order Berhasil dihapus');
    }
}
