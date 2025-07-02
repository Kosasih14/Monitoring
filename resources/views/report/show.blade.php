<style>
    @media print {
        header, .sidebar, nav, #sidebar, .navbar, .print-button {
            display: none !important;
        }
        .content, body, html {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        @page {
            margin: 1cm;
        }
        table {
            page-break-inside: avoid;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .btn-kembali {
            display: none !important;
        }
    }

    .report-container {
        padding: 20px;
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
    }

    .report-header {
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .report-header h2 {
        margin-bottom: 8px;
    }

    .report-table, .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .report-table th, .report-table td,
    .summary-table th, .summary-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .report-table th, .summary-table th {
        background-color: #f2f2f2;
        text-align: left;
    }

    .report-table td:nth-child(3),
    .report-table tfoot td,
    .summary-table td {
        text-align: right;
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        margin: 30px 0 15px 0;
        color: #333;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 5px;
    }

    .reset-info {
        background-color: #fff3cd;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        color: #856404;
        border: 1px solid #ffeaa7;
        font-style: italic;
    }

    .total-row {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    .grand-total-row {
        font-weight: bold;
        background-color: #e8f5e8;
        color: #2e7d32;
    }

    .zero-balance {
        color: #dc3545;
        font-weight: bold;
    }

    .print-button {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }

    .print-button:hover {
        background-color: #45a049;
    }

    .btn-kembali {
        padding: 8px 16px;
        background-color: #fa4444;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        margin-left: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .btn-kembali:hover, .btn-kembali:focus {
        background-color: #fe1616;
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }

    /* Responsive styles */
    @media (max-width: 900px) {
        .report-container {
            padding: 12px 2vw;
            margin: 16px 0;
        }
        .report-table, .summary-table {
            font-size: 13px;
        }
        .report-header {
            flex-direction: column;
            gap: 10px;
        }
    }

    @media (max-width: 600px) {
        .report-container {
            padding: 6px 0;
            border-radius: 0;
            box-shadow: none;
        }
        .report-header {
            flex-direction: column;
            gap: 8px;
        }
        .report-header img {
            width: 60px !important;
            height: 60px !important;
        }
        .report-header h1 {
            font-size: 18px !important;
        }
        .report-header p, .report-header h2 {
            font-size: 13px !important;
        }
        .section-title {
            font-size: 15px;
            margin: 18px 0 10px 0;
        }
        .report-table, .summary-table {
            font-size: 11px;
        }
        .report-table th, .report-table td,
        .summary-table th, .summary-table td {
            padding: 5px;
        }
        .print-button, .btn-kembali {
            font-size: 13px;
            padding: 7px 10px;
        }
        .reset-info {
            font-size: 12px;
            padding: 7px;
        }
    }

    @media (max-width: 425px) {
        .report-container {
            padding: 2px 0;
        }
        .report-header img {
            width: 45px !important;
            height: 45px !important;
        }
        .report-header h1 {
            font-size: 15px !important;
        }
        .report-header p, .report-header h2 {
            font-size: 11px !important;
        }
        .section-title {
            font-size: 13px;
        }
        .print-button, .btn-kembali {
            font-size: 12px;
            padding: 6px 7px;
        }
        .report-table, .summary-table {
            font-size: 10px;
        }
        .report-table th, .report-table td,
        .summary-table th, .summary-table td {
            padding: 3px;
        }
        .reset-info {
            font-size: 11px;
            padding: 5px;
        }
        /* Table scroll for very small screens */
        .report-table, .summary-table {
            display: block;
            overflow-x: auto;
            width: 100%;
        }
    }
</style>

@php
    use Carbon\Carbon;
    $saldo = $student['saldo'] ?? 0;

    // Hitung total transaksi lama
    $totalOld = 0;
    foreach ($transactionsBeforeReset as $trx) {
        $totalOld += $trx['jumlah'] ?? 0;
    }

    // Hitung total transaksi baru
    $totalNew = 0;
    foreach ($transactionsAfterReset as $trx) {
        $totalNew += $trx['jumlah'] ?? 0;
    }

    // Total keseluruhan
    $grandTotal = $totalOld + $totalNew;
@endphp

<div class="report-container">
    <div class="report-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
        <img src="/images/Screenshot 2025-05-20 002803.png" alt="Logo Sekolah" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid #4CAF50; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div>
            <h1 style="margin: 0; font-size: 24px;">SPS Mandiri Al-Ikhlas Triananda</h1>
            <p style="margin: 0; font-size: 16px; font-weight: bold;">
                Jl. Kp. Ciketing, RT.001/RW.001, Sumur Batu, Kec. Bantar Gebang, Kota Bks, Jawa Barat 17152
            </p>
        </div>
    </div>

    <div class="report-header">
        <h2>Laporan Tabungan Siswa</h2>
        <p><strong>Nama:</strong> {{ $student['nama'] ?? '-' }}</p>
        <p><strong>Kelas:</strong> {{ $student['kelas'] ?? '-' }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ Carbon::now()->format('d M Y, H:i:s') }}</p>
    </div>

    {{-- Transaksi Lama (Sebelum Reset) --}}
    @if(count($transactionsBeforeReset) > 0)
        <h3 class="section-title">Transaksi Lama </h3>

        {{-- Tampilkan informasi waktu reset jika ada --}}
        @if(isset($student['last_reset_at']) && $student['last_reset_at'])
            <div class="reset-info">
                <strong>Tabungan Diambil Pada:</strong> {{ Carbon::parse($student['last_reset_at'])->format('d M Y, H:i:s') }}
            </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($transactionsBeforeReset as $trx)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ isset($trx['tanggal']) ? Carbon::parse($trx['tanggal'])->format('d M Y, H:i:s') : '-' }}</td>
                        <td style="text-align: right;">{{ number_format($trx['jumlah'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="2" style="text-align: right;">Total Saldo:</th>
                    <th style="text-align: right;" class="zero-balance">{{ number_format(0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Transaksi Baru (Setelah Reset) --}}
    <h3 class="section-title">Transaksi Baru </h3>

    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse ($transactionsAfterReset as $trx)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ isset($trx['tanggal']) ? Carbon::parse($trx['tanggal'])->format('d M Y, H:i:s') : '-' }}</td>
                    <td style="text-align: right;">{{ number_format($trx['jumlah'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada transaksi baru</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="2" style="text-align: right;">Total Saldo Tabungan:</th>
                <th style="text-align: right;">{{ number_format($totalNew, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- Ringkasan Total --}}
    <h3 class="section-title">Ringkasan Total</h3>
    <table class="summary-table">
        <tbody>
            @if(count($transactionsBeforeReset) > 0)
                <tr>
                    <th>Total Saldo Tabungan yang telah diambil:</th>
                    <td>Rp {{ number_format($totalOld, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <th>Total Saldo Tabungan Saat Ini:</th>
                <td>Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
            @if(count($transactionsBeforeReset) > 0)
                <tr class="grand-total-row">
                    <th>Total Keseluruhan Transaksi:</th>
                    <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button class="print-button" onclick="window.print()">Print Laporan</button>
        <a href="{{ route('dashboard') }}" class="btn-kembali">Kembali</a>
    </div>
</div>
