<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin Pemilihan Sinode</title>
  <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-database-compat.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #2c3e50;
      color: white;
      margin: 0;
      padding: 20px;
      text-align: center;
    }

    h1 {
      color: #f1c40f;
      margin-bottom: 30px;
    }

    .section-title {
      background: #16a085;
      padding: 10px;
      border-radius: 6px;
      margin-top: 40px;
      text-align: left;
      padding-left: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .grid {
      display: flex;
      gap: 20px;
      margin-top: 20px;
      flex-wrap: wrap;
      justify-content: flex-start;
    }

    .card {
      background: #34495e;
      border-radius: 12px;
      padding: 20px;
      width: 220px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
    }

    h4 {
      margin: 10px 0;
      min-height: 40px;
      font-size: 1.1rem;
    }

    .votes {
      font-size: 3.5rem;
      margin: 15px 0;
      font-weight: bold;
      color: #f1c40f;
    }

    .btn {
      padding: 15px;
      font-size: 1.3rem;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin: 5px 0;
      width: 90%;
    }

    .btn-add {
      background-color: #2ecc71;
      color: white;
    }

    .btn-add:active {
      background-color: #27ae60;
    }

    .btn-sub {
      background-color: #e74c3c;
      color: white;
      width: 50%;
      font-size: 0.9rem;
      padding: 8px;
    }

    .btn-sub:active {
      background-color: #c0392b;
    }

    .btn-proj {
      background: #f1c40f;
      color: #2c3e50;
      border: none;
      padding: 6px 12px;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.85rem;
    }

    .btn-proj:hover {
      background: #d35400;
      color: white;
    }
  </style>
</head>

<body>

  <h1>PANEL INPUT COUNTER SUARA</h1>
  {{-- <p style="color: #bdc3c7;">Klik "📺 Tampilkan di Proyektor" untuk memunculkan kategori tersebut di layar proyektor
    kanan jemaat.</p> --}}

  <!-- TOMBOL STOP BARU -->
  <div style="margin-bottom: 30px;">
    <button onclick="stopBroadcast()"
      style="background: #e74c3c; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
      🛑 Stop Tampilan Proyektor (Kembali ke Layar Tunggu)
    </button>
  </div>
  <p style="color: #bdc3c7;">Klik "📺 Tampilkan di Proyektor" untuk memunculkan kategori tersebut di layar proyektor
    kanan jemaat.</p>

  @foreach ($positions as $pos)
    <h2 class="section-title">
      <span>{{ $pos->name }}</span>
      <button class="btn-proj" onclick="broadcastPosition({{ $pos->id }})">📺 Tampilkan di Proyektor</button>
    </h2>
    <div class="grid">
      @forelse($pos->candidates as $c)
        <div class="card">
          <h4>{{ $c->name }}</h4>
          <div class="votes" id="vote-{{ $c->id }}">0</div>
          <button class="btn btn-add" onclick="changeVote({{ $c->id }}, 1)">+1 SUARA</button>
          <br>
          <button class="btn btn-sub" onclick="changeVote({{ $c->id }}, -1)">Koreksi (-1)</button>
        </div>
      @empty
        <p style="font-style:italic; color:#bdc3c7; padding-left:10px;">Belum ada kandidat untuk jabatan ini.</p>
      @endforelse
    </div>
  @endforeach

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
    let currentVotes = {};

    // Sinkronisasi data awal dari Firebase
    database.ref('votes').on('value', (snapshot) => {
      currentVotes = snapshot.val() || {};
      for (let id in currentVotes) {
        const element = document.getElementById(`vote-${id}`);
        if (element) element.innerText = currentVotes[id];
      }
    });

    // Push data perubahan ke Firebase
    function changeVote(id, amount) {
      let newVote = (currentVotes[id] || 0) + amount;
      if (newVote < 0) newVote = 0;
      database.ref('votes/' + id).set(newVote);
    }

    // Fungsi mengalihkan proyektor secara remote
    function broadcastPosition(id) {
      database.ref('active_position_id').set(id);
    }
    // Fungsi untuk menghentikan siaran proyektor
    function stopBroadcast() {
      database.ref('active_position_id').set(null)
        .then(() => {
          console.log("Tampilan proyektor dihentikan.");
        });
    }
  </script>
</body>

</html>
