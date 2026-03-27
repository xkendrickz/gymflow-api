@extends('dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pegawai</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Pegawai</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('pegawai.update',$pegawai->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">NIP</label>
                                    <input type="text" class="form-control @error('nomor_induk_pegawai') is-invalid @enderror" name="nomor_induk_pegawai" value="{{ old('nomor_induk_pegawai',$pegawai->nomor_induk_pegawai) }}" placeholder="Masukkan NIP">
                                    @error('nomor_induk_pegawai')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Nama Pegawai</label>
                                    <input type="text" class="form-control @error('nama_pegawai') is-invalid @enderror" name="nama_pegawai" value="{{ old('nama_pegawai',$pegawai->nama_pegawai) }}" placeholder="Masukkan Nama Pegawai" maxlength="15">
                                    @error('nama_pegawai')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Departemen</label>
                                    <select class="form-control @error('id_departemen') is-invalid @enderror" name="id_departemen">
                                        <option value="{{ old('id_departemen', $pegawai->id_departemen) }}" selected style="display: none;">{{ old('nama_departemen',$pegawai->nama_departemen) }}</option>
                                        @foreach ($departemen as $item)
                                            <option value="{{$item->id}}">{{$item->nama_departemen}}</option>
                                        @endforeach
                                    </select>
                                    @error('id_departemen')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Telepon</label>
                                    <input type="number" class="form-control @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon',$pegawai->telepon) }}" placeholder="Masukkan No. Telepon" pattern="[0][8][0-9]{4,5}">
                                    @error('telepon')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email',$pegawai->email) }}" placeholder="Masukkan Email">
                                    @error('email')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Gender</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" name="gender">
                                        <option value="{{ old('gender', $pegawai->gender) }}" selected style="display: none;">{{ $item->gender==0 ? "Wanita" : "Pria" }}</option>
                                        <option value="1">Pria</option>
                                        <option value="0">Wanita</option>
                                    </select>
                                    @error('gender')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" name="status">
                                        <option value="{{ old('status', $pegawai->status) }}" selected style="display: none;">{{ $item->status==0 ? "Inactive" : "Active" }}</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn-primary">UPDATE</button>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
@endsection