<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Carbon\Carbon;

class StudentService
{
    protected $database;
    protected $table = 'students';

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path('firebase.json'))
            ->withDatabaseUri('https://celengan-7c473-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function calculateStudentBalance($studentId)
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

    public function getStudent($studentId)
    {
        return $this->database->getReference("$this->table/$studentId")->getValue();
    }

    public function getTransactions($studentId)
    {
        return $this->database->getReference("$this->table/$studentId/transaksi")->getValue() ?? [];
    }
}
