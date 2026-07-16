<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Manajemen Pemilihan Sinode</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: #f4f6f9;
      padding: 20px;
      color: #333;
    }

    .row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .col {
      flex: 1;
      min-width: 300px;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }

    .btn {
      background-color: #3498db;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      margin-top: 5px;
    }

    .btn-danger {
      background-color: #e74c3c;
      width: auto;
      padding: 5px 10px;
      font-size: 0.8rem;
    }

    .alert {
      background-color: #2ecc71;
      color: white;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-weight: bold;
    }

    table {
      width: 100%;
      margin-top: 15px;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>

  <h2>Pusat Kendali Data Pemilihan Sinode</h2>

  @if (session('success'))
    <div class="alert">{{ session('success') }}</div>
  @endif

  <div class="row">
    <!-- KOLOM 1: CRUD JABATAN -->
    <div class="col">
      <h3>1. Pengaturan Jabatan / Kategori</h3>
      <form action="{{ route('position.store') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Nama Jabatan Baru</label>
          <input type="text" name="name" placeholder="Misal: Sekretaris Umum" required>
        </div>
        <button type="submit" class="btn" style="background-color: #1abc9c;">Tambah Jabatan</button>
      </form>

      <table>
        <thead>
          <tr>
            <th>Nama Jabatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($positions as $pos)
            <tr>
              <td>{{ $pos->name }}</td>
              <td>
                <form action="{{ route('position.destroy', $pos->id) }}" method="POST"
                  onsubmit="return confirm('Menghapus jabatan akan menghapus kandidat di dalamnya!')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- KOLOM 2: INPUT KANDIDAT -->
    <div class="col">
      <h3>2. Tambah Kandidat</h3>
      <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Pilih Jabatan</label>
          <select name="position_id" required>
            <option value="">-- Pilih Jabatan --</option>
            @foreach ($positions as $pos)
              <option value="{{ $pos->id }}">{{ $pos->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Foto</label>
          <input type="file" name="image" accept="image/*" required>
        </div>
        <button type="submit" class="btn">Simpan Kandidat</button>
      </form>
    </div>
  </div>

  <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

  <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <h3>3. Daftar Semua Kandidat</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
      <thead>
        <tr style="background-color: #f2f2f2;">
          <th style="padding: 12px; border: 1px solid #ddd; text-align: center; width: 80px;">Foto</th>
          <th style="padding: 12px; border: 1px solid #ddd;">Nama Kandidat</th>
          <th style="padding: 12px; border: 1px solid #ddd;">Jabatan / Kategori</th>
          <th style="padding: 12px; border: 1px solid #ddd; text-align: center; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($candidates as $c)
          <tr>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
              <img src="{{ asset($c->image) }}" alt="Foto"
                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background-color: #ddd;">
            </td>
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #2c3e50;">
              {{ $c->name }}
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;">
              <span
                style="background: #3498db; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;">
                {{ $c->position->name ?? 'Tanpa Jabatan' }}
              </span>
            </td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
              <form action="{{ route('candidate.destroy', $c->id) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kandidat {{ $c->name }}?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                  style="background-color: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.85rem;">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="padding: 20px; text-align: center; color: #7f8c8d; font-style: italic;">
              Belum ada kandidat yang ditambahkan. Silakan isi form di atas.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:20px;"><a href="{{ route('vote.admin') }}">← Ke Panel Hitung Suara</a></div>

</body>

</html>
