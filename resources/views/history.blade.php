@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .page-title {
        font-size: 24px;
        margin-bottom: 10px;
        text-align: center;
    }

    .history-info {
        margin-bottom: 20px;
        text-align: center;
    }

    .success-message {
        background: #d4edda;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        color: #155724;
        border: 1px solid #c3e6cb;
        text-align: center;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .history-table th, .history-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .history-table th {
        background-color: #f2f2f2;
        text-align: left;
    }

    .history-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .history-table td:nth-child(3),
    .history-table tfoot td {
        text-align: right;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .reset-button,
    .delete-transactions-button {
        padding: 10px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: white;
        min-width: 140px;
    }

    .reset-button {
        background-color: #007bff;
    }

    .reset-button:hover {
        background-color: #0056b3;
    }

    .delete-transactions-button {
        background-color: red;
    }

    .delete-transactions-button:hover {
        background-color: darkred;
    }

    .total-row th, .total-row td {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    .reset-info {
        background-color: #fff3cd;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        color: #856404;
        border: 1px solid #ffeaa7;
        font-style: italic;
        text-align: center;
    }

    .zero-balance {
        color: #dc3545;
        font-weight: bold;
    }

    main.content {
        max-width: 900px;
        margin: 30px auto 30px auto;
        background: #fafbfc;
        border-radius: 10px;
        padding: 24px 32px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        min-height: 80vh;
        transition: margin-left 0.3s;
    }

    /* Sidebar desktop */
    @media (min-width: 769px) {
        main.content {
            margin-left: 260px;
        }
    }

    /* Tablet */
    @media (max-width: 900px) {
        main.content {
            padding: 16px 6vw;
            margin-left: 0;
        }
        .history-table th, .history-table td {
            padding: 6px;
        }
    }

    /* HP */
    @media (max-width: 600px) {
        main.content {
            padding: 8px 2vw;
            margin: 12px 0;
        }
        .page-title {
            font-size: 18px;
        }
        .history-table {
            font-size: 12px;
        }
        .reset-info, .success-message {
            font-size: 13px;
            padding: 7px;
        }
        .button-group {
            flex-direction: column;
            gap: 8px;
        }
        .reset-button, .delete-transactions-button {
            width: 100%;
            min-width: unset;
            font-size: 13px;
            padding: 9px 0;
        }
        .history-table th, .history-table td {
            padding: 5px;
        }
    }

    /* Sidebar responsive terbuka, konten terdorong */
    @media (max-width: 768px) {
        body.sidebar-open main.content {
            margin-left: 70vw !important;
        }
    }
    @media (max-width: 425px) {
        body.sidebar-open main.content {
            margin-left: 75vw !important;
        }
        main.content {
            padding: 4px 1vw;
        }
        .page-title {
            font-size: 16px;
        }
        .history-table {
            font-size: 11px;
        }
    }
</style>

<main class="content">
    <h1 class="page-title">Histori Transaksi {{ $student['nama'] ?? '-' }}</h1>
    <p class="history-info"><strong>Kelas:</strong> {{ $student['kelas'] ?? '-' }}</p>

    @if(session('success'))
        <div class="success-message" id="success-message">
            {{ session('success') }}
        </div>
    @endif

    {{-- Transaksi Lama (Sebelum Reset) --}}
    @if(count($transactionsBeforeReset) > 0)
        <h3>Transaksi Lama</h3>

        @php
            $no = 1;
            $totalOld = 0;
        @endphp
        <table class="history-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactionsBeforeReset as $trx)
                    @php
                        $jumlah = $trx['jumlah'] ?? 0;
                        $totalOld += $jumlah;
                        $formattedDate = isset($trx['tanggal']) ? Carbon::parse($trx['tanggal'])->format('d M Y, H:i:s') : '-';
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $formattedDate }}</td>
                        <td>{{ 'Rp ' . number_format($jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">Total </td>
                    <td class="zero-balance">{{ 'Rp ' . number_format(0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
                {{-- Tampilkan informasi waktu reset jika ada --}}
        @if(isset($student['last_reset_at']) && $student['last_reset_at'])
            <div class="reset-info">
                <strong>Tabungan Diambil Pada:</strong> {{ Carbon::parse($student['last_reset_at'])->format('d M Y, H:i:s') }}
            </div>
        @endif
    @endif

    <h3>Transaksi Baru</h3>
    @php
        $no = 1;
        $total = 0;
    @endphp
    <table class="history-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactionsAfterReset as $trx)
                @php
                    $jumlah = $trx['jumlah'] ?? 0;
                    $total += $jumlah;
                    $formattedDate = isset($trx['tanggal']) ? Carbon::parse($trx['tanggal'])->format('d M Y, H:i:s') : '-';
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $formattedDate }}</td>
                    <td>{{ 'Rp ' . number_format($jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">Total Saldo </td>
                <td>{{ 'Rp ' . number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="button-group">
        <!-- Reset Saldo Saja -->
        <form action="{{ route('history.resetOnlySaldo', $student['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin mereset saldo ke 0? Transaksi tidak akan dihapus.')">
            @csrf
            <button type="submit" class="reset-button">Reset Saldo</button>
        </form>

        <!-- Hapus Transaksi + Reset Saldo -->
        <form action="{{ route('history.deleteAllTransactions', $student['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua transaksi dan mereset saldo siswa ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-transactions-button">Hapus Semua Transaksi</button>
        </form>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
</main>
@endsection
