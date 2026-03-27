<?php

namespace App\Http\Controllers;

/* Import Model */
use Mail;
use App\Mail\DepartemenMail;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Http\Resources\DepartemenResource;
use Illuminate\Support\Facades\Validator;

class DepartemenController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $departemen = Departemen::latest()->get();

        return new DepartemenResource(true, 'List Data Departemen', $departemen);
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('departemen.create');
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
            'nama_departemen' => 'required',
            'nama_manager' => 'required',
            'jumlah_pegawai' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $departemen = Departemen::create([
            'nama_departemen'   => $request->nama_departemen,
            'nama_manager'      => $request->nama_manager,
            'jumlah_pegawai'    => $request->jumlah_pegawai
        ]);

        return new DepartemenResource(true, 'Data Departemen Berhasil Ditambahkan!', $departemen);
    }

    public function destroy($id)
    {
        $departemen = Departemen::findOrfail($id);
        if($departemen) {
            $departemen->delete();
            return new DepartemenResource(true, 'Data Departemen Berhasil Dihapus!', $departemen);
        }
    }

    public function edit($id)
    {
        $departemen=Departemen::find($id);

        return view('departemen.edit',compact('departemen'));
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validator = Validator::make($request->all(), [
            'nama_departemen' => 'required',
            'nama_manager' => 'required',
            'jumlah_pegawai' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json($request, 422);
        }

        $departemen = Departemen::findOrFail($departemen->id);

        if($departemen) {

            $departemen->update([
                'nama_departemen'   => $request->nama_departemen,
                'nama_manager'      => $request->nama_manager,
                'jumlah_pegawai'    => $request->jumlah_pegawai
            ]);

            return new DepartemenResource(true, 'Data Departemen Berhasil Diubah!', $departemen);
        }
    }

    public function show($id){
        $departemen = Departemen::findOrfail($id);
        return new DepartemenResource(true, 'Data Departemen Berhasil Ditampilkan!', $departemen);
    }

}