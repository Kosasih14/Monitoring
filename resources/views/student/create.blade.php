@extends('layouts.app')
<head>
<link rel="stylesheet" href="{{ asset('css/apa.css') }}">
</head>
@section('content')
<div class="content">
    <h2 class="title">Tambah Siswa</h2>

    <form action="{{ route('students.store') }}" method="POST" class="student-form">
        @csrf
        <div class="form-group">
            <label for="nama">Nama Siswa:</label>
            <input type="text" name="nama" id="nama" required>
        </div>

        <div class="form-group">
            <label for="kelas">Kelas Siswa:</label>
            <input type="text" name="kelas" id="kelas" required>
        </div>

        <button type="submit" class="btn-add">Simpan</button>
        <a href="{{ route('students.index') }}" class="btn-cancel">Batal</a>
    </form>
</div>
@endsection
