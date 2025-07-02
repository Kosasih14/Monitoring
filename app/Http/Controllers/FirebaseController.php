<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    protected $firebase;
    protected $database;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;

        // Inisialisasi Firebase database
        $factory = (new Factory)
            ->withServiceAccount(base_path('firebase.json'))
            ->withDatabaseUri('https://celengan-7c473-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function index()
    {
        $students = $this->firebase->getStudents();

        // Tambahkan saldo untuk setiap siswa
        foreach ($students as &$student) {
            if (isset($student['id'])) {
                $student['saldo'] = $this->calculateStudentBalance($student['id']);
            }
        }

        return view('firebase.index', compact('students'));
    }

    public function transaksi($id)
    {
        $transaksi = $this->firebase->getStudentTransactions($id);
        $transaksi = $transaksi ?? [];

        // Hitung saldo siswa
        $saldo = $this->calculateStudentBalance($id);

        // Ambil data siswa
        $student = $this->database->getReference("students/$id")->getValue();

        return view('firebase.transaksi', compact('transaksi'))
            ->with('id', $id)
            ->with('saldo', $saldo)
            ->with('student', $student);
    }

    public function storeTransaksi($id, Request $request)
    {
        // Validasi input
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date_format:Y-m-d\TH:i'
        ]);

        // Format tanggal ke bentuk 'Y-m-d H:i:s'
        $formattedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->tanggal)
            ->format('Y-m-d H:i:s');

        $data = [
            'jumlah' => (int) $request->jumlah,
            'tanggal' => $formattedDate
        ];

        // Simpan transaksi
        $this->firebase->addTransaction($id, $data);

        // Hitung ulang saldo
        $saldo = $this->calculateStudentBalance($id);

        // Simpan saldo ke Firebase
        $this->database->getReference("students/$id/saldo")->set($saldo);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan dan saldo diperbarui.');
    }

    /**
     * Hitung saldo siswa berdasarkan total transaksi
     */
   private function calculateStudentBalance($studentId)
{
    $studentRef = $this->database->getReference("students/$studentId");
    $studentData = $studentRef->getValue();

    $transactions = $studentData['transaksi'] ?? [];
    $lastReset = isset($studentData['last_reset']) ? Carbon::parse($studentData['last_reset']) : null;

    $totalSaldo = 0;

    foreach ($transactions as $transaction) {
        if (isset($transaction['jumlah'], $transaction['tanggal'])) {
            $transactionDate = Carbon::parse($transaction['tanggal']);

            // Tambahkan hanya jika transaksi dilakukan setelah waktu reset
            if (!$lastReset || $transactionDate->greaterThan($lastReset)) {
                $totalSaldo += (int)$transaction['jumlah'];
            }
        }
    }

    return $totalSaldo;
}

    /**
     * API endpoint untuk mendapatkan saldo real-time
     */
    public function getStudentBalance($id)
    {
        $student = $this->database->getReference("students/$id")->getValue();

        if (!$student) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        $saldo = $this->calculateStudentBalance($id);
        $transactions = $this->database->getReference("students/$id/transaksi")->getValue() ?? [];

        return response()->json([
            'student_id' => $id,
            'nama' => $student['nama'],
            'kelas' => $student['kelas'],
            'saldo' => $saldo,
            'formatted_saldo' => 'Rp ' . number_format($saldo, 0, ',', '.'),
            'total_transactions' => count($transactions)
        ]);
    }

    /**
     * Recalculate semua saldo siswa (Opsional untuk admin/maintenance)
     */
    public function recalculateAllBalances()
    {
        $students = $this->database->getReference('students')->getValue() ?? [];
        $results = [];

        foreach ($students as $id => $student) {
            $saldo = $this->calculateStudentBalance($id);
            $this->database->getReference("students/$id/saldo")->set($saldo);

            $results[] = [
                'id' => $id,
                'nama' => $student['nama'] ?? '-',
                'saldo' => $saldo
            ];
        }

        return response()->json([
            'message' => 'Saldo semua siswa berhasil diperbarui.',
            'data' => $results
        ]);
    }
}
