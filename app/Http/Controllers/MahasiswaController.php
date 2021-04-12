<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Models\Mahasiswa_MataKuliah;

class MahasiswaController extends Controller
{
    /**
     * * Display a listing of the resource.
     * *
     * * * @return \Illuminate\Http\Response
     * */
    public function index(Request $request)
    {
        if($request->has('search')){
            $mahasiswas = Mahasiswa::where('Nama', 'like', "%".$request->search."%")
                ->orWhere('Nim', $request->search)
                ->paginate(5);
        } else {
            // fungsi eloquent menampilkan data menggunakan pagination
            $mahasiswas = Mahasiswa::paginate(5);
        }
        return view('mahasiswas.index', compact('mahasiswas'));
    }
    
    public function create()
    {
        $kelas = Kelas::all();
        return view('mahasiswas.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        //melakukan validasi data
        $request->validate([
        'Nim' => 'required',
        'Nama' => 'required',
        'Kelas' => 'required',
        'Jurusan' => 'required',
        'No_Handphone' => 'required',
        'email' => 'required',
        'tanggal_lahir' => 'required',
        ]);
        
        //fungsi eloquent untuk menambah data
        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->kelas_id = $request->get('Kelas');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->no_handphone = $request->get('No_Handphone');
        $mahasiswa->email = $request->get('email');
        $mahasiswa->tanggal_lahir = $request->get('tanggal_lahir');
        $mahasiswa->save();
        // $mahasiswa::create($request->all());

        //$kelas = new Kelas;
        //$kelas->id = $request->get('Kelas');

        //fungsi eloquent untuk menambah data dengan relasi belongsTo
        //$mahasiswa->kelas()->associate($kelas);
        //$mahasiswa->save();
        
        //jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswas.index')
            ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    public function show($Nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa
        $Mahasiswa = Mahasiswa::with('kelas')->where('Nim', $Nim)->first();
        return view('mahasiswas.detail', compact('Mahasiswa'));
    }

    public function edit($Nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk diedit
        $Mahasiswa = Mahasiswa::find($Nim);
        return view('mahasiswas.edit', compact('Mahasiswa'));
    }

    public function update(Request $request, $Nim)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            'No_Handphone' => 'required',
            'email' => 'required',
            'tanggal_lahir' => 'required',
        ]);

        //fungsi eloquent untuk mengupdate data inputan kita
        Mahasiswa::find($Nim)->update($request->all());

        //jika data berhasil diupdate, akan kembali ke halaman utama
        return redirect()->route('mahasiswas.index')
        ->with('success', 'Mahasiswa Berhasil Diupdate');
    }
   
    public function destroy($Nim)
    {
       //fungsi eloquent untuk menghapus data
       Mahasiswa::find($Nim)->delete();
       return redirect()->route('mahasiswas.index')
            -> with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function nilai($Nim)
    {
        $mahasiswa = Mahasiswa_MataKuliah::with('mahasiswa')
            ->where('mahasiswa_id', $Nim)
            ->first();
        $nilai = Mahasiswa_MataKuliah::with('matakuliah')
            ->where('mahasiswa_id', $Nim)
            ->get();
        return view('mahasiswas.nilai', compact('mahasiswa', 'nilai'));
    }
}