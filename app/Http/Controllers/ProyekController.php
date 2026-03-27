<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Departemen;
use App\Http\Resources\ProyekResource;
use Illuminate\Support\Facades\Validator;

class ProyekController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    
    public function index()
    {
        $proyek = Proyek::leftJoin('departemens','proyeks.departemen_id','=','departemens.id')->select('proyeks.id','departemens.nama_departemen','proyeks.nama_proyek','proyeks.waktu_mulai','proyeks.waktu_selesai','proyeks.status')->paginate(5);
        return new ProyekResource(true, 'List Data Departemen', $proyek);
    }

    public function create()
    {
        $departemen =  Departemen::all();
        return view('proyek.create', compact('departemen'));
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'nama_proyek' => 'required',
            // 'departemen_id' => 'required',
            // 'waktu_mulai' => 'required|date',
            // 'waktu_selesai' => 'required|date|after:waktu_mulai',
            // 'status' => 'required'

            'nama_proyek' => 'required',
            'departemen_id' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $proyek = Proyek::create([
            'nama_proyek' => $request->nama_proyek,
            'departemen_id' => $request->departemen_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => $request->status,
        ]);

        return new ProyekResource(true, 'Data Proyek Berhasil Ditambahkan!', $proyek);
    }

    // public function destroy($id)
    // {
    //     Proyek::find($id)->delete();

    //     return redirect()->route('proyek.index')->with(['success'=> 'Item Berhasil Dihapus']);
    // }

    // public function edit($id)
    // {
    //     $proyek=Proyek::join('departemens','proyeks.departemen_id','=','departemens.id')->find($id);
    //     $departemen =  Departemen::all();
    //     return view('proyek.edit',compact('proyek','departemen'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_proyek' => 'required',
    //         'departemen_id' => 'required',
    //         'waktu_mulai' => 'required|date',
    //         'waktu_selesai' => 'required|date|after:waktu_mulai',
    //         'status' => 'required'
    //     ]);

    //     Proyek::find($id)->update($request->all());
    //     return redirect()->route('proyek.index')->with(['success'=> 'Item Berhasil Diubah']);
    // }
}
