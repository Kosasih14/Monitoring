<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;


class StudentController extends Controller
{
    protected $database;
    protected $table;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path('firebase.json'))
            ->withDatabaseUri('https://celengan-7c473-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
        $this->table = 'students';
    }

    public function index()
    {
        $students = $this->database->getReference($this->table)->getValue() ?? [];
        $formatted = [];

        foreach ($students as $id => $data) {
            $data['id'] = $id;
            $data['saldo'] = $this->calculateStudentBalance($id);
            $formatted[] = $data;
        }

        return view('student.index', ['students' => $formatted]);
    }

    public function create()
    {
        return view('student.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:10'
        ]);

        $data = $request->only('nama', 'kelas');
        $this->database->getReference($this->table)->push($data);

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function show($id)
    {
        $student = $this->database->getReference("$this->table/$id")->getValue();

        if (!$student) {
            return redirect()->route('students.index')->withErrors('Siswa tidak ditemukan.');
        }

        $student['id'] = $id;
        $student['saldo'] = $this->calculateStudentBalance($id);
        $student['transactions'] = $this->getStudentTransactions($id);

        return view('student.show', ['student' => $student]);
    }

    public function edit($id)
    {
        $student = $this->database->getReference("$this->table/$id")->getValue();

        if (!$student) {
            return redirect()->route('students.index')->withErrors('Siswa tidak ditemukan.');
        }

        $student['id'] = $id;
        $student['saldo'] = $this->calculateStudentBalance($id);

        return view('student.edit', ['student' => $student]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:10'
        ]);

        $data = $request->only('nama', 'kelas');
        $this->database->getReference("$this->table/$id")->update($data);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        // Hapus transaksi dan data siswa
        $this->database->getReference("$this->table/$id/transaksi")->remove();
        $this->database->getReference("$this->table/$id")->remove();

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus');
    }

    /**
     * Hitung total saldo siswa berdasarkan transaksi
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
     * Ambil transaksi siswa
     */
    public function getStudentTransactions($studentId)
    {
        $transactions = $this->database
            ->getReference("$this->table/$studentId/transaksi")
            ->getValue() ?? [];

        $formatted = [];

        foreach ($transactions as $id => $data) {
            $data['id'] = $id;
            $formatted[] = $data;
        }

        // Urutkan berdasarkan tanggal terbaru
        usort($formatted, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return $formatted;
    }

    /**
     * API saldo real-time
     */
    public function getBalance($id)
    {
        $student = $this->database->getReference("$this->table/$id")->getValue();

        if (!$student) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        $saldo = $this->calculateStudentBalance($id);

        return response()->json([
            'student_id' => $id,
            'nama' => $student['nama'],
            'kelas' => $student['kelas'],
            'saldo' => $saldo,
            'formatted_saldo' => 'Rp ' . number_format($saldo, 0, ',', '.')
        ]);
    }

    /**
     * Hitung ulang semua saldo siswa (opsional)
     */
    public function recalculateAllBalances()
    {
        $students = $this->database->getReference($this->table)->getValue() ?? [];
        $results = [];

        foreach ($students as $id => $data) {
            $saldo = $this->calculateStudentBalance($id);
            $results[] = [
                'id' => $id,
                'nama' => $data['nama'],
                'saldo' => $saldo
            ];
        }

        return response()->json([
            'message' => 'Saldo semua siswa telah dihitung ulang.',
            'data' => $results
        ]);
    }
}
