<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Setting;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Master - Master Akun";
        $judul = "Data Akun";
        $setting = Setting::first();

        $masters = Master::get();
        return view('pages.setting.master', compact('setting', 'title', 'judul', 'masters'));
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
        Master::create([
            'name' => $request->name,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('master')->with('success', 'Data Master Akun berhasil ditambahkan');
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
        $master = Master::findOrFail($id);
        return response()->json($master);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $master = Master::findOrFail($request->id);
        $master->name = $request->name;
        $master->kategori = $request->kategori;
        $master->save();

        return redirect()->back()->with('success', 'Data Master Berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
