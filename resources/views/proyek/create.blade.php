@extends('dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tambah Proyek</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Proyek</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
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
                        <form action="{{ route('proyek.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Nama Proyek</label>
                                    <input type="text" class="form-control @error('nama_proyek') is-invalid @enderror" name="nama_proyek" value="{{ old('nama_proyek') }}" placeholder="Masukkan Nama Proyek">
                                    @error('nama_proyek')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Departemen</label>
                                    <select class="form-control @error('departemen_id') is-invalid @enderror" name="departemen_id">
                                        <option value="" disabled selected style="display: none;">Pilih Departemen</option>
                                        @foreach ($departemen as $item)
                                            <option value="{{$item->id}}">{{$item->nama_departemen}}</option>
                                        @endforeach
                                    </select>
                                    @error('departemen_id')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Waktu Mulai</label>
                                    <input type="date" class="form-control @error('waktu_mulai') is-invalid @enderror" name="waktu_mulai" value="{{ old('waktu_mulai', date('j F Y')) }}">
                                    @error('waktu_mulai')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Waktu Selesai</label>
                                    <input type="date" class="form-control @error('waktu_selesai') is-invalid @enderror" name="waktu_selesai" value="{{ old('waktu_selesai') }}">
                                    @error('waktu_selesai')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="font-weight-bold">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" name="status">
                                        <option value="" disabled selected style="display: none;">Pilih Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
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