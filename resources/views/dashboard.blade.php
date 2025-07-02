@extends('layouts.app')

    <link rel="stylesheet" href="{{ asset('css/kos.css') }}">

@section('content')
    <main class="content">
        <h1 class="page-title">Dashboard</h1>
    <div class="cards-container">
        @foreach ($students as $student)
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{ $student['nama'] }}</div>
                <div class="card-subtitle">Kelas {{ $student['kelas'] ?? '-' }}</div>
            </div>

            <div class="card-body">
                <p><strong>Tabungan Hari Ini:</strong> Rp {{ number_format($student['tabunganHariIni'], 0, ',', '.') }}</p>
                <p><strong>Total Saldo Tabungan:</strong> Rp {{ number_format($student['saldo'] ?? 0, 0, ',', '.') }}</p>
            </div>

            <div class="card-footer">
                <div class="btn-group">
                    <a href="{{ route('history', $student['id']) }}" class="btn history-btn">Lihat History</a>
                    <a href="{{ route('report.show', $student['id']) }}" class="btn print-btn" target="_blank">Cetak Laporan</a>
                </div>
            </div>
        </div>
            @endforeach
    </div>
</main>
@endsection
