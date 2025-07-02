<h2>Daftar Siswa</h2>
<ul>
@foreach ($students as $id => $student)
    <li>
        {{ $student['nama'] }} -
        <a href="{{ url('/firebase/transaksi/' . $id) }}">Lihat Transaksi</a>
    </li>
@endforeach
</ul>
