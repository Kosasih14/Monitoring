<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $firebase;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path('firebase.json'))
            ->withDatabaseUri('https://celengan-7c473-default-rtdb.firebaseio.com');

        $this->firebase = $factory->createDatabase();
    }

    public function index()
    {
        // Ambil semua data siswa dari Firebase
        $studentsRef = $this->firebase->getReference('students');
        $studentsData = $studentsRef->getValue() ?? [];

        $today = Carbon::today()->format('Y-m-d');
        $students = [];

        foreach ($studentsData as $studentId => $student) {
            $transactions = $student['transaksi'] ?? [];
            $tabunganHariIni = 0;
            $saldoBaru = 0;

            // Ambil waktu reset terakhir (jika ada)
            $lastReset = isset($student['last_reset']) ? Carbon::parse($student['last_reset']) : null;

            foreach ($transactions as $trx) {
                $trxTanggal = isset($trx['tanggal']) ? Carbon::parse($trx['tanggal']) : null;
                $jumlah = $trx['jumlah'] ?? 0;

                // Hitung saldo hanya dari transaksi setelah reset
                if ($trxTanggal && (!$lastReset || $trxTanggal->greaterThan($lastReset))) {
                    $saldoBaru += $jumlah;
                }

                // Hitung tabungan hari ini hanya jika dilakukan setelah reset
                if (
                    $trxTanggal &&
                    $trxTanggal->format('Y-m-d') === $today &&
                    (!$lastReset || $trxTanggal->greaterThan($lastReset))
                ) {
                    $tabunganHariIni += $jumlah;
                }
            }

            $students[] = [
                'id' => $studentId,
                'nama' => $student['nama'] ?? '-',
                'kelas' => $student['kelas'] ?? '-',
                'saldo' => $saldoBaru,
                'tabunganHariIni' => $tabunganHariIni,
            ];
        }

        return view('dashboard', compact('students'));
    }
}
