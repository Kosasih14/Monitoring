<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));


        $this->database = $factory->createDatabase();
    }

    public function getStudents()
    {
        return $this->database->getReference('students')->getValue();
    }

    public function getStudentTransactions($studentId)
    {
        return $this->database->getReference("students/{$studentId}/transaksi")->getValue();
    }

    public function addTransaction($studentId, $data)
    {
        return $this->database
            ->getReference("students/{$studentId}/transaksi")
            ->push($data);
    }
}
