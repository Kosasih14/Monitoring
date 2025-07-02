@extends('layouts.app')
<head>
    <style>
        .content {
            padding: 20px;
            background-color: white;
        }

        .title {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .btn-add {
            background-color: #7CFC00;
            color: black;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-add:hover, .btn-edit:hover {
            background-color: #5fdc00;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8e8c0;
            font-size: 16px;
        }

        .student-table th,
        .student-table td {
            padding: 12px;
            border: 1px solid #d6a85f;
            text-align: left;
        }

        .student-table thead {
            background-color: #fffab5;
        }

        .btn-edit {
            background-color: #b2f7b5;
            border: none;
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 8px;
            text-decoration: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-delete {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-delete:hover {
            background-color: #b30000;
        }

        /* Responsive: Tablet */
        @media (max-width: 900px) {
            .content {
                padding: 12px 2vw;
            }
            .student-table {
                font-size: 15px;
            }
            .title {
                font-size: 22px;
            }
        }

        /* Responsive: HP */
        @media (max-width: 600px) {
            .content {
                padding: 6px 0;
            }
            .title {
                font-size: 18px;
            }
            .btn-add {
                font-size: 14px;
                padding: 7px 10px;
            }
            .student-table {
                font-size: 13px;
            }
            .student-table th, .student-table td {
                padding: 7px 4px;
            }
            .btn-edit, .btn-delete {
                font-size: 12px;
                padding: 5px 7px;
                margin-bottom: 3px;
            }
            .student-table td {
                vertical-align: middle;
            }
        }

        /* Scrollable table on very small screens */
        @media (max-width: 425px) {
            .content {
                padding: 2px 0;
            }
            .student-table {
                display: block;
                width: 100%;
                overflow-x: auto;
                font-size: 11px;
            }
            .student-table thead, .student-table tbody, .student-table tr {
                display: table;
                width: 100%;
                table-layout: fixed;
            }
            .btn-edit, .btn-delete {
                width: 100%;
                margin-bottom: 4px;
            }
        }

        @media (min-width: 769px) {
            .content {
                margin-left: 260px; /* Lebar sidebar desktop */
            }
        }

        @media (max-width: 768px) {
            body.sidebar-open .content {
                margin-left: 70vw !important; /* Lebar sidebar responsive */
            }
        }

        @media (max-width: 425px) {
            body.sidebar-open .content {
                margin-left: 75vw !important;
            }
        }
    </style>
</head>
@section('content')
<div class="content">
    <h2 class="title">Managament Siswa</h2>
    <a href="{{ route('students.create') }}" class="btn-add">Tambah Siswa</a>

    <table class="student-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas Siswa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['nama'] }}</td>
                    <td>{{ $student['kelas'] }}</td>
                    <td>
                        <a href="{{ route('students.edit', $student['id']) }}" class="btn-edit">Edit</a>
                        <form action="{{ route('students.destroy', $student['id']) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda ingin menghapus siswa ini?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
