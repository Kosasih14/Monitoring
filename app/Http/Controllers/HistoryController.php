<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Carbon\Carbon;

class HistoryController extends Controller
{
    protected $database;
    protected $studentTable = 'students';

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path('firebase.json'))
            ->withDatabaseUri('https://celengan-7c473-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    // Tampilkan histori transaksi siswa
    public function index($studentId)
    {
        $studentRef = $this->database->getReference("{$this->studentTable}/{$studentId}");
        $studentData = $studentRef->getValue();

        if (!$studentData) {
            abort(404, 'Siswa tidak ditemukan');
        }

        $studentData['id'] = $studentId;
        $transactions = $studentData['transaksi'] ?? [];

        // Ambil waktu reset terakhir
        $lastReset = null;
        if (isset($studentData['last_reset'])) {
            $lastReset = Carbon::parse($studentData['last_reset']);
            // Tambahkan ke student data untuk dikirim ke view
            $studentData['last_reset_at'] = $studentData['last_reset'];
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
        

        return view('history', [
            'student' => $studentData,
            'transactionsBeforeReset' => $transactionsBeforeReset,
            'transactionsAfterReset' => $transactionsAfterReset,
            'lastReset' => $lastReset,
        ]);
    }

    /**
     * Reset saldo ke 0 tanpa menghapus transaksi
     */
    public function resetOnlySaldo($studentId)
    {
        try {
            $student = $this->database->getReference("{$this->studentTable}/{$studentId}")->getValue();

            if (!$student) {
                return redirect()->route('history', $studentId)->withErrors('Siswa tidak ditemukan.');
            }

            // Reset saldo ke 0
            $this->database->getReference("{$this->studentTable}/{$studentId}/saldo")->set(0);

            // Simpan waktu reset
            $resetTime = Carbon::now()->toDateTimeString();
            $this->database->getReference("{$this->studentTable}/{$studentId}/last_reset")->set($resetTime);

            return redirect()->back()->with('success', 'Saldo berhasil direset ke 0. Transaksi lama tetap tersimpan.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan saat mereset saldo: ' . $e->getMessage());
        }
    }

    /**
     * Hapus semua transaksi & reset saldo
     */
    public function deleteAllTransactions($studentId)
    {
        try {
            $student = $this->database->getReference("{$this->studentTable}/{$studentId}")->getValue();

            if (!$student) {
                return redirect()->route('history', $studentId)->withErrors('Siswa tidak ditemukan.');
            }

            // Hapus semua transaksi
            $this->database->getReference("{$this->studentTable}/{$studentId}/transaksi")->remove();

            // Reset saldo ke 0
            $this->database->getReference("{$this->studentTable}/{$studentId}/saldo")->set(0);

            // Hapus waktu reset karena semua transaksi sudah dihapus
            $this->database->getReference("{$this->studentTable}/{$studentId}/last_reset")->remove();

            return redirect()->back()->with('success', 'Semua transaksi berhasil dihapus dan saldo direset ke 0.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan saat menghapus transaksi: ' . $e->getMessage());
        }
    }
}
