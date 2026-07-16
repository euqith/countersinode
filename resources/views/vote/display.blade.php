<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Count Pemilihan Ketua Sinode</title>
  <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-database-compat.js"></script>
  <style>
    /* Mengoptimalkan agar muat di setengah layar proyektor tanpa scroll */
    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
      width: 100%;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      color: #333;
      overflow: hidden;
    }

    .display-container {
      display: flex;
      flex-direction: column;
      height: 100vh;
      box-sizing: border-box;
      padding: 20px;
    }

    h1 {
      color: #2c3e50;
      margin: 0 0 5px 0;
      font-size: 1.6rem;
      text-align: center;
    }

    .subtitle {
      color: #7f8c8d;
      margin-bottom: 20px;
      font-style: italic;
      text-align: center;
      font-size: 0.9rem;
    }

    /* PERBAIKAN: Kotak putih dan bayangan dipindah ke sini agar ikut hilang saat tidak aktif */
    .jabatan-block {
      display: none;
      background: white;
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      box-sizing: border-box;
      width: 100%;
      height: calc(100vh - 100px);
      /* Membatasi tinggi agar pas di sisa layar proyektor */
    }

    /* Saat aktif, blok akan menjadi flex container dan otomatis naik ke posisi paling atas */
    .jabatan-block.active {
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }

    .jabatan-title {
      color: #2c3e50;
      border-bottom: 3px solid #3498db;
      padding-bottom: 10px;
      margin-top: 0;
      margin-bottom: 25px;
      font-size: 1.6rem;
      text-align: left;
    }

    /* Grid 2 Kolom agar pas di layar split-screen vertikal */
    .grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      justify-content: center;
      align-content: center;
      width: 100%;
    }

    .card {
      background: #fdfdfd;
      border: 1px solid #eef2f5;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
    }

    .avatar {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #3498db;
      background-color: #ddd;
    }

    h3 {
      margin: 12px 0 5px 0;
      color: #2c3e50;
      font-size: 1.1rem;
      min-height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .votes {
      font-size: 3.5rem;
      font-weight: bold;
      color: #2c3e50;
      margin: 5px 0;
      background: #eef2f5;
      border-radius: 8px;
      width: 100%;
      padding: 5px 0;
      box-sizing: border-box;
    }

    .waiting-msg {
      font-style: italic;
      color: #95a5a6;
      font-size: 1.2rem;
      text-align: center;
      margin: auto;
    }
  </style>
</head>

<body>

  <div class="display-container">
    <h1>REAL-TIME LIVE COUNT PEMILIHAN SINODE</h1>
    <p class="subtitle">Pembaruan Angka Berjalan Secara Otomatis</p>

    <div id="waiting-screen" class="jabatan-block active" style="display: flex;">
      <div class="waiting-msg">Menunggu konfirmasi pemilihan dimulai dari Admin...</div>
    </div>

    @foreach ($positions as $pos)
      <div class="jabatan-block" id="position-block-{{ $pos->id }}">
        <h2 class="jabatan-title">{{ $pos->name }}</h2>

        <div class="grid">
          @forelse($pos->candidates as $c)
            <div class="card">
              <img class="avatar" src="{{ asset($c->image) }}" alt="Foto {{ $c->name }}">
              <h3>{{ $c->name }}</h3>
              <div class="votes" id="vote-{{ $c->id }}">0</div>
            </div>
          @empty
            <div style="grid-column: span 2; font-style:italic; color:#95a5a6; text-align:center; padding: 40px 0;">
              Belum ada data kandidat pada kategori ini.
            </div>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>

  <script>
    const firebaseConfig = {
      apiKey: "AIzaSyDs-RGeFBKwgX4ZfYTdxL9wGqci5wODiLk",
      authDomain: "sinode-counter.firebaseapp.com",
      databaseURL: "https://sinode-counter-default-rtdb.firebaseio.com",
      projectId: "sinode-counter",
      storageBucket: "sinode-counter.firebasestorage.app",
      messagingSenderId: "164307962618",
      appId: "1:164307962618:web:f2c7df69717494e69f1743",
      measurementId: "G-Y1WZ7HVMY5"
    };

    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    // 1. Dengarkan Kategori Aktif yang Dipilih oleh Admin
    database.ref('active_position_id').on('value', (snapshot) => {
      const activeId = snapshot.val();

      // Sembunyikan layar tunggu
      document.getElementById('waiting-screen').style.display = 'none';

      // Matikan dan sembunyikan semua blok jabatan
      document.querySelectorAll('.jabatan-block').forEach(block => {
        block.classList.remove('active');
        block.style.display = 'none'; // Sembunyikan total layout fisiknya
      });

      // Cari blok jabatan yang dituju
      const activeBlock = document.getElementById(`position-block-${activeId}`);
      if (activeBlock) {
        activeBlock.classList.add('active');
        activeBlock.style.display = 'flex'; // Nyalakan hanya yang dipilih admin
      } else if (!activeId) {
        // Jika admin klik STOP (null), kembali tampilkan layar tunggu
        document.getElementById('waiting-screen').style.display = 'flex';
      }
    });

    // 2. Dengarkan Firebase secara realtime dan langsung update angka ke HTML
    database.ref('votes').on('value', (snapshot) => {
      const firebaseData = snapshot.val() || {};
      const voteElements = document.querySelectorAll('[id^="vote-"]');
      voteElements.forEach(element => {
        const candidateId = element.id.split('-')[1];
        element.innerText = firebaseData[candidateId] || 0;
      });
    });
  </script>
</body>

</html>
