<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function show($studentId)
    {
        $student = $this->studentService->getStudent($studentId);

        if (!$student) {
            abort(404, 'Siswa tidak ditemukan');
        }

        $student['id'] = $studentId;
        $transactions = $this->studentService->getTransactions($studentId);

        // Ambil waktu reset terakhir
        $lastReset = null;
        if (isset($student['last_reset'])) {
            $lastReset = Carbon::parse($student['last_reset']);
            // Tambahkan ke student data untuk dikirim ke view
            $student['last_reset_at'] = $student['last_reset'];
        }

        // Pisahkan transaksi berdasarkan waktu reset
        $transactionsBeforeReset = [];
        $transactionsAfterReset = [];

        foreach ($transactions as $trx) {
            if (!isset($trx['tanggal'])) {
                // Jika tidak ada tanggal, masukkan ke transaksi baru
                $transactionsAfterReset[] = $trx;
                continue;
            }

            $trxDate = Carbon::parse($trx['tanggal']);

            if ($lastReset && $trxDate->lessThan($lastReset)) {
                $transactionsBeforeReset[] = $trx;
            } else {
                $transactionsAfterReset[] = $trx;
            }
        }

        // Urutkan transaksi berdasarkan tanggal (terlama dulu)
        usort($transactionsBeforeReset, function($a, $b) {
            $dateA = isset($a['tanggal']) ? Carbon::parse($a['tanggal']) : Carbon::now();
            $dateB = isset($b['tanggal']) ? Carbon::parse($b['tanggal']) : Carbon::now();
            return $dateA->timestamp - $dateB->timestamp;
        });

        usort($transactionsAfterReset, function($a, $b) {
            $dateA = isset($a['tanggal']) ? Carbon::parse($a['tanggal']) : Carbon::now();
            $dateB = isset($b['tanggal']) ? Carbon::parse($b['tanggal']) : Carbon::now();
            return $dateA->timestamp - $dateB->timestamp;
        });

        // Hitung total untuk setiap kategori
        $totalOld = 0;
        foreach ($transactionsBeforeReset as $trx) {
            $totalOld += $trx['jumlah'] ?? 0;
        }

        $totalNew = 0;
        foreach ($transactionsAfterReset as $trx) {
            $totalNew += $trx['jumlah'] ?? 0;
        }

        // Saldo saat ini adalah total transaksi baru (setelah reset)
        $student['saldo'] = $totalNew;

        // Total keseluruhan untuk keperluan statistik
        $grandTotal = $totalOld + $totalNew;

        return view('report.show', compact(
            'student',
            'transactionsBeforeReset',
            'transactionsAfterReset',
            'totalOld',
            'totalNew',
            'grandTotal',
            'lastReset'
        ));
    }
}
