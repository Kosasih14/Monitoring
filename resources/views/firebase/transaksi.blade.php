<h2>Data Transaksi</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('firebase.transaksi.store', $id) }}">
    @csrf
    <input type="number" name="jumlah" placeholder="Jumlah" required min="0">
    <input type="datetime-local" name="tanggal" required>
    <button type="submit">Simpan</button>
</form>

<hr>

<h3>Riwayat Transaksi</h3>
<ul>
    @if($transaksi)
        @foreach ($transaksi as $trx)
            {{-- Tampilkan tanggal dan waktu lengkap --}}
            <li>{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y, H:i:s') }} - Rp {{ number_format($trx['jumlah'], 0, ',', '.') }}</li>
        @endforeach
    @else
        <li>Belum ada transaksi</li>
    @endif
</ul>
