<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistem Manajemen Blog (CMS)</title>
<style>
  /* ===== RESET & BASE ===== */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --danger:  #ef4444;
    --danger-dark: #dc2626;
    --success: #22c55e;
    --bg:      #f1f5f9;
    --sidebar: #1e293b;
    --sidebar-hover: #334155;
    --sidebar-active: #2563eb;
    --white:   #ffffff;
    --border:  #e2e8f0;
    --text:    #1e293b;
    --muted:   #64748b;
    --header-h: 60px;
    --sidebar-w: 220px;
    --radius: 8px;
    --shadow: 0 1px 3px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.06);
    --shadow-md: 0 4px 6px rgba(0,0,0,.07), 0 2px 4px rgba(0,0,0,.06);
  }
  body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

  /* ===== HEADER ===== */
  header {
    position: fixed; top: 0; left: 0; right: 0; height: var(--header-h);
    background: var(--white); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; padding: 0 24px; gap: 12px;
    box-shadow: var(--shadow); z-index: 100;
  }
  header .logo { 
    font-size: 20px; font-weight: 700; color: var(--primary); 
}
  header .subtitle { font-size: 13px; color: var(--muted); }

  /* ===== SIDEBAR ===== */
  aside {
    position: fixed; top: var(--header-h); left: 0; bottom: 0;
    width: var(--sidebar-w); background: var(--sidebar);
    padding: 20px 0; overflow-y: auto;
  }
  .nav-label {
    font-size: 10px; font-weight: 600; letter-spacing: .1em;
    color: #94a3b8; padding: 0 20px 8px; text-transform: uppercase;
  }
  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 20px; cursor: pointer; color: #cbd5e1;
    font-size: 14px; transition: background .15s, color .15s;
    border-left: 3px solid transparent;
  }
  .nav-item:hover  { background: var(--sidebar-hover); color: var(--white); }
  .nav-item.active { background: var(--sidebar-hover); color: var(--white); border-left-color: var(--primary); }
  .nav-item .icon  { font-size: 16px; flex-shrink: 0; }

  /* ===== MAIN ===== */
  main {
    margin-top: var(--header-h); margin-left: var(--sidebar-w);
    padding: 28px; min-height: calc(100vh - var(--header-h));
  }
  .page { display: none; }
  .page.active { display: block; }

  /* ===== CARD ===== */
  .card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow-md); overflow: hidden;
  }
  .card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px; border-bottom: 1px solid var(--border);
  }
  .card-header h2 { font-size: 16px; font-weight: 600; }

  /* ===== TABLE ===== */
  .table-wrap { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  thead { background: #f8fafc; }
  th { padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); border-bottom: 1px solid var(--border); }
  td { padding: 12px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: #f8fafc; }
  .thumb { width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); background: #f1f5f9; }
  .badge {
    display: inline-block; padding: 3px 10px; border-radius: 999px;
    font-size: 12px; font-weight: 600; background: #dbeafe; color: #1d4ed8;
  }
  .pw-mask { font-family: monospace; color: var(--muted); }

  /* ===== BUTTONS ===== */
  .btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: var(--radius); font-size: 13px;
    font-weight: 500; border: none; cursor: pointer; transition: background .15s, transform .1s;
  }
  .btn:active { transform: scale(.97); }
  .btn-primary { background: var(--primary); color: #fff; }
  .btn-primary:hover { background: var(--primary-dark); }
  .btn-edit { background: #f1f5f9; color: var(--text); border: 1px solid var(--border); padding: 6px 12px; font-size: 12px; }
  .btn-edit:hover { background: #e2e8f0; }
  .btn-danger { background: var(--danger); color: #fff; padding: 6px 12px; font-size: 12px; }
  .btn-danger:hover { background: var(--danger-dark); }
  .btn-secondary { background: #f1f5f9; color: var(--text); border: 1px solid var(--border); }
  .btn-secondary:hover { background: #e2e8f0; }

  /* ===== MODAL ===== */
  .overlay {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45);
    z-index: 200; align-items: center; justify-content: center; padding: 20px;
  }
  .overlay.open { display: flex; }
  .modal {
    background: var(--white); border-radius: 12px; width: 100%; max-width: 520px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideUp .2s ease;
  }
  @keyframes slideUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
  .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); }
  .modal-header h3 { font-size: 16px; font-weight: 600; }
  .modal-close { background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted); line-height: 1; }
  .modal-body { padding: 24px; max-height: 70vh; overflow-y: auto; }
  .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border); }

  /* ===== FORM ===== */
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-group { margin-bottom: 16px; }
  .form-group:last-child { margin-bottom: 0; }
  label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--text); }
  input[type=text], input[type=password], select, textarea {
    width: 100%; padding: 9px 12px; border: 1px solid var(--border);
    border-radius: var(--radius); font-size: 14px; color: var(--text);
    transition: border-color .15s, box-shadow .15s; background: var(--white);
  }
  input[type=text]:focus, input[type=password]:focus, select:focus, textarea:focus {
    outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.12);
  }
  textarea { resize: vertical; min-height: 90px; }
  input[type=file] { font-size: 13px; }
  .hint { font-size: 12px; color: var(--muted); margin-top: 4px; }

  /* ===== KONFIRMASI HAPUS ===== */
  .confirm-modal .modal { max-width: 360px; text-align: center; }
  .confirm-icon { font-size: 40px; margin-bottom: 12px; }
  .confirm-title { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
  .confirm-desc { font-size: 13px; color: var(--muted); margin-bottom: 4px; }

  /* ===== TOAST ===== */
  #toast {
    position: fixed; bottom: 28px; right: 28px; z-index: 999;
    padding: 12px 20px; border-radius: var(--radius); font-size: 14px;
    color: #fff; box-shadow: var(--shadow-md);
    opacity: 0; transform: translateY(10px);
    transition: opacity .25s, transform .25s; pointer-events: none;
  }
  #toast.show { opacity: 1; transform: translateY(0); }
  #toast.success { background: #16a34a; }
  #toast.error   { background: var(--danger); }

  /* ===== EMPTY STATE ===== */
  .empty { text-align: center; padding: 48px 24px; color: var(--muted); }
  .empty .empty-icon { font-size: 40px; margin-bottom: 12px; }
  .empty p { font-size: 14px; }

  /* ===== LOADING ===== */
  .loading { text-align: center; padding: 36px; color: var(--muted); font-size: 14px; }
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <span style="font-size:22px;">📝</span>
  <div>
    <div class="logo">Sistem Manajemen Blog (CMS)</div>
    <div class="subtitle">Blog Keren</div>
  </div>
</header>

<!-- SIDEBAR -->
<aside>
  <div class="nav-label">Menu Utama</div>
  <div class="nav-item active" onclick="showPage('penulis')">
    <span class="icon">👤</span> Kelola Penulis
  </div>
  <div class="nav-item" onclick="showPage('artikel')">
    <span class="icon">📄</span> Kelola Artikel
  </div>
  <div class="nav-item" onclick="showPage('kategori')">
    <span class="icon">🗂️</span> Kelola Kategori
  </div>
</aside>

<!-- MAIN -->
<main>

  <!-- ========== PAGE: PENULIS ========== -->
  <div id="page-penulis" class="page active">
    <div class="card">
      <div class="card-header">
        <h2>Data Penulis</h2>
        <button class="btn btn-primary" onclick="openModal('modal-tambah-penulis')">
          ＋ Tambah Penulis
        </button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Foto</th><th>Nama</th><th>Username</th><th>Password</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody-penulis"><tr><td colspan="5" class="loading">Memuat data…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========== PAGE: ARTIKEL ========== -->
  <div id="page-artikel" class="page">
    <div class="card">
      <div class="card-header">
        <h2>Data Artikel</h2>
        <button class="btn btn-primary" onclick="openTambahArtikel()">
          ＋ Tambah Artikel
        </button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Gambar</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tanggal</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody-artikel"><tr><td colspan="6" class="loading">Memuat data…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ========== PAGE: KATEGORI ========== -->
  <div id="page-kategori" class="page">
    <div class="card">
      <div class="card-header">
        <h2>Data Kategori Artikel</h2>
        <button class="btn btn-primary" onclick="openModal('modal-tambah-kategori')">
          ＋ Tambah Kategori
        </button>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nama Kategori</th><th>Keterangan</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody-kategori"><tr><td colspan="3" class="loading">Memuat data…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

</main>

<!-- ====================================================
     MODAL: TAMBAH PENULIS
==================================================== -->
<div class="overlay" id="modal-tambah-penulis">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Penulis</h3>
      <button class="modal-close" onclick="closeModal('modal-tambah-penulis')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Nama Depan</label>
          <input type="text" id="tp-nama-depan" placeholder="Ahmad">
        </div>
        <div class="form-group">
          <label>Nama Belakang</label>
          <input type="text" id="tp-nama-belakang" placeholder="Fauzi">
        </div>
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" id="tp-username" placeholder="ahmad_f">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" id="tp-password" placeholder="••••••••">
      </div>
      <div class="form-group">
        <label>Foto Profil</label>
        <input type="file" id="tp-foto" accept="image/*">
        <div class="hint">Opsional. Maks. 2 MB. Format: JPG, PNG, GIF, WEBP.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-tambah-penulis')">Batal</button>
      <button class="btn btn-primary" onclick="simpanPenulis()">Simpan Data</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: EDIT PENULIS
==================================================== -->
<div class="overlay" id="modal-edit-penulis">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Penulis</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-penulis')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="ep-id">
      <div class="form-row">
        <div class="form-group">
          <label>Nama Depan</label>
          <input type="text" id="ep-nama-depan">
        </div>
        <div class="form-group">
          <label>Nama Belakang</label>
          <input type="text" id="ep-nama-belakang">
        </div>
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" id="ep-username">
      </div>
      <div class="form-group">
        <label>Password Baru <span style="color:var(--muted);font-weight:400;">(kosongkan jika tidak diganti)</span></label>
        <input type="password" id="ep-password" placeholder="••••••••">
      </div>
      <div class="form-group">
        <label>Foto Profil <span style="color:var(--muted);font-weight:400;">(kosongkan jika tidak diganti)</span></label>
        <input type="file" id="ep-foto" accept="image/*">
        <div class="hint">Maks. 2 MB. Format: JPG, PNG, GIF, WEBP.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-penulis')">Batal</button>
      <button class="btn btn-primary" onclick="updatePenulis()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: TAMBAH ARTIKEL
==================================================== -->
<div class="overlay" id="modal-tambah-artikel">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Artikel</h3>
      <button class="modal-close" onclick="closeModal('modal-tambah-artikel')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Judul</label>
        <input type="text" id="ta-judul" placeholder="Judul artikel...">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Penulis</label>
          <select id="ta-penulis"></select>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select id="ta-kategori"></select>
        </div>
      </div>
      <div class="form-group">
        <label>Isi Artikel</label>
        <textarea id="ta-isi" placeholder="Tulis isi artikel di sini..."></textarea>
      </div>
      <div class="form-group">
        <label>Gambar</label>
        <input type="file" id="ta-gambar" accept="image/*">
        <div class="hint">Wajib diunggah. Maks. 2 MB.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-tambah-artikel')">Batal</button>
      <button class="btn btn-primary" onclick="simpanArtikel()">Simpan Data</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: EDIT ARTIKEL
==================================================== -->
<div class="overlay" id="modal-edit-artikel">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Artikel</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-artikel')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="ea-id">
      <div class="form-group">
        <label>Judul</label>
        <input type="text" id="ea-judul">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Penulis</label>
          <select id="ea-penulis"></select>
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select id="ea-kategori"></select>
        </div>
      </div>
      <div class="form-group">
        <label>Isi Artikel</label>
        <textarea id="ea-isi"></textarea>
      </div>
      <div class="form-group">
        <label>Gambar <span style="color:var(--muted);font-weight:400;">(kosongkan jika tidak diganti)</span></label>
        <input type="file" id="ea-gambar" accept="image/*">
        <div class="hint">Maks. 2 MB.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-artikel')">Batal</button>
      <button class="btn btn-primary" onclick="updateArtikel()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: TAMBAH KATEGORI
==================================================== -->
<div class="overlay" id="modal-tambah-kategori">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Kategori</h3>
      <button class="modal-close" onclick="closeModal('modal-tambah-kategori')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" id="tk-nama" placeholder="Nama kategori...">
      </div>
      <div class="form-group">
        <label>Keterangan</label>
        <textarea id="tk-keterangan" placeholder="Deskripsi kategori..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-tambah-kategori')">Batal</button>
      <button class="btn btn-primary" onclick="simpanKategori()">Simpan Data</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: EDIT KATEGORI
==================================================== -->
<div class="overlay" id="modal-edit-kategori">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Kategori</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-kategori')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="ek-id">
      <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" id="ek-nama">
      </div>
      <div class="form-group">
        <label>Keterangan</label>
        <textarea id="ek-keterangan"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-kategori')">Batal</button>
      <button class="btn btn-primary" onclick="updateKategori()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<!-- ====================================================
     MODAL: KONFIRMASI HAPUS
==================================================== -->
<div class="overlay confirm-modal" id="modal-hapus">
  <div class="modal">
    <div class="modal-body" style="padding:32px 24px;">
      <div style="text-align:center;">
        <div class="confirm-icon">🗑️</div>
        <div class="confirm-title">Hapus data ini?</div>
        <div class="confirm-desc">Data yang dihapus tidak dapat dikembalikan.</div>
      </div>
    </div>
    <div class="modal-footer" style="justify-content:center; gap:12px;">
      <button class="btn btn-secondary" onclick="closeModal('modal-hapus')">Batal</button>
      <button class="btn btn-danger" id="btn-konfirmasi-hapus">Ya, Hapus</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- ====================================================
     JAVASCRIPT
==================================================== -->
<script>
// ---- NAVIGASI ----
function showPage(name) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  event.currentTarget.classList.add('active');

  if (name === 'penulis')  loadPenulis();
  if (name === 'artikel')  loadArtikel();
  if (name === 'kategori') loadKategori();
}

// ---- MODAL ----
function openModal(id) {
  document.getElementById(id).classList.add('open');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}
// Tutup modal dengan klik overlay
document.querySelectorAll('.overlay').forEach(ov => {
  ov.addEventListener('click', function(e) {
    if (e.target === ov) ov.classList.remove('open');
  });
});

// ---- TOAST ----
let toastTimer;
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { t.className = ''; }, 3000);
}

// ---- ESCAPE ----
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.querySelectorAll('.overlay.open').forEach(o => o.classList.remove('open'));
});

// ==================================================
//  PENULIS
// ==================================================
function loadPenulis() {
  fetch('ambil_penulis.php')
    .then(r => r.json())
    .then(res => {
      const tbody = document.getElementById('tbody-penulis');
      if (!res.data || res.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty"><div class="empty-icon">👤</div><p>Belum ada data penulis.</p></div></td></tr>';
        return;
      }
      tbody.innerHTML = res.data.map(p => `
        <tr>
          <td><img src="uploads_penulis/${escHtml(p.foto)}" class="thumb" onerror="this.src='uploads_penulis/default.png'"></td>
          <td><strong>${escHtml(p.nama_depan + ' ' + p.nama_belakang)}</strong></td>
          <td>${escHtml(p.user_name)}</td>
          <td><span class="pw-mask">${escHtml(p.password.substring(0,18))}…</span></td>
          <td>
            <button class="btn btn-edit" onclick="openEditPenulis(${p.id})">Edit</button>
            <button class="btn btn-danger" onclick="konfirmasiHapus('penulis', ${p.id})">Hapus</button>
          </td>
        </tr>`).join('');
    })
    .catch(() => showToast('Gagal memuat data penulis', 'error'));
}

function simpanPenulis() {
  const fd = new FormData();
  fd.append('nama_depan',    document.getElementById('tp-nama-depan').value.trim());
  fd.append('nama_belakang', document.getElementById('tp-nama-belakang').value.trim());
  fd.append('user_name',     document.getElementById('tp-username').value.trim());
  fd.append('password',      document.getElementById('tp-password').value);
  const foto = document.getElementById('tp-foto').files[0];
  if (foto) fd.append('foto', foto);

  fetch('simpan_penulis.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-tambah-penulis');
        resetForm(['tp-nama-depan','tp-nama-belakang','tp-username','tp-password','tp-foto']);
        loadPenulis();
      } else {
        showToast(res.message, 'error');
      }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function openEditPenulis(id) {
  fetch('ambil_satu_penulis.php?id=' + id)
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        const d = res.data;
        document.getElementById('ep-id').value          = d.id;
        document.getElementById('ep-nama-depan').value  = d.nama_depan;
        document.getElementById('ep-nama-belakang').value = d.nama_belakang;
        document.getElementById('ep-username').value    = d.user_name;
        document.getElementById('ep-password').value    = '';
        document.getElementById('ep-foto').value        = '';
        openModal('modal-edit-penulis');
      } else {
        showToast(res.message, 'error');
      }
    });
}

function updatePenulis() {
  const fd = new FormData();
  fd.append('id',            document.getElementById('ep-id').value);
  fd.append('nama_depan',    document.getElementById('ep-nama-depan').value.trim());
  fd.append('nama_belakang', document.getElementById('ep-nama-belakang').value.trim());
  fd.append('user_name',     document.getElementById('ep-username').value.trim());
  fd.append('password',      document.getElementById('ep-password').value);
  const foto = document.getElementById('ep-foto').files[0];
  if (foto) fd.append('foto', foto);

  fetch('update_penulis.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-edit-penulis');
        loadPenulis();
      } else {
        showToast(res.message, 'error');
      }
    });
}

// ==================================================
//  ARTIKEL
// ==================================================
function loadArtikel() {
  fetch('ambil_artikel.php')
    .then(r => r.json())
    .then(res => {
      const tbody = document.getElementById('tbody-artikel');
      if (!res.data || res.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6"><div class="empty"><div class="empty-icon">📄</div><p>Belum ada data artikel.</p></div></td></tr>';
        return;
      }
      tbody.innerHTML = res.data.map(a => `
        <tr>
          <td><img src="uploads_artikel/${escHtml(a.gambar)}" class="thumb" onerror="this.style.background='#e2e8f0'"></td>
          <td><strong>${escHtml(a.judul)}</strong></td>
          <td><span class="badge">${escHtml(a.nama_kategori)}</span></td>
          <td>${escHtml(a.nama_penulis)}</td>
          <td style="font-size:12px;color:var(--muted);">${escHtml(a.hari_tanggal)}</td>
          <td>
            <button class="btn btn-edit" onclick="openEditArtikel(${a.id})">Edit</button>
            <button class="btn btn-danger" onclick="konfirmasiHapus('artikel', ${a.id})">Hapus</button>
          </td>
        </tr>`).join('');
    })
    .catch(() => showToast('Gagal memuat data artikel', 'error'));
}

function loadDropdownsArtikel(selectPenulis, selectKategori, idPenulis = '', idKategori = '') {
  Promise.all([
    fetch('ambil_penulis.php').then(r => r.json()),
    fetch('ambil_kategori.php').then(r => r.json())
  ]).then(([rp, rk]) => {
    selectPenulis.innerHTML = rp.data.map(p =>
      `<option value="${p.id}" ${p.id == idPenulis ? 'selected' : ''}>${escHtml(p.nama_depan + ' ' + p.nama_belakang)}</option>`
    ).join('');
    selectKategori.innerHTML = rk.data.map(k =>
      `<option value="${k.id}" ${k.id == idKategori ? 'selected' : ''}>${escHtml(k.nama_kategori)}</option>`
    ).join('');
  });
}

function openTambahArtikel() {
  resetForm(['ta-judul','ta-isi','ta-gambar']);
  loadDropdownsArtikel(
    document.getElementById('ta-penulis'),
    document.getElementById('ta-kategori')
  );
  openModal('modal-tambah-artikel');
}

function simpanArtikel() {
  const fd = new FormData();
  fd.append('judul',       document.getElementById('ta-judul').value.trim());
  fd.append('id_penulis',  document.getElementById('ta-penulis').value);
  fd.append('id_kategori', document.getElementById('ta-kategori').value);
  fd.append('isi',         document.getElementById('ta-isi').value.trim());
  const gambar = document.getElementById('ta-gambar').files[0];
  if (gambar) fd.append('gambar', gambar);

  fetch('simpan_artikel.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-tambah-artikel');
        loadArtikel();
      } else {
        showToast(res.message, 'error');
      }
    });
}

function openEditArtikel(id) {
  fetch('ambil_satu_artikel.php?id=' + id)
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        const d = res.data;
        document.getElementById('ea-id').value    = d.id;
        document.getElementById('ea-judul').value = d.judul;
        document.getElementById('ea-isi').value   = d.isi;
        document.getElementById('ea-gambar').value = '';
        loadDropdownsArtikel(
          document.getElementById('ea-penulis'),
          document.getElementById('ea-kategori'),
          d.id_penulis, d.id_kategori
        );
        openModal('modal-edit-artikel');
      } else {
        showToast(res.message, 'error');
      }
    });
}

function updateArtikel() {
  const fd = new FormData();
  fd.append('id',          document.getElementById('ea-id').value);
  fd.append('judul',       document.getElementById('ea-judul').value.trim());
  fd.append('id_penulis',  document.getElementById('ea-penulis').value);
  fd.append('id_kategori', document.getElementById('ea-kategori').value);
  fd.append('isi',         document.getElementById('ea-isi').value.trim());
  const gambar = document.getElementById('ea-gambar').files[0];
  if (gambar) fd.append('gambar', gambar);

  fetch('update_artikel.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-edit-artikel');
        loadArtikel();
      } else {
        showToast(res.message, 'error');
      }
    });
}

// ==================================================
//  KATEGORI
// ==================================================
function loadKategori() {
  fetch('ambil_kategori.php')
    .then(r => r.json())
    .then(res => {
      const tbody = document.getElementById('tbody-kategori');
      if (!res.data || res.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3"><div class="empty"><div class="empty-icon">🗂️</div><p>Belum ada data kategori.</p></div></td></tr>';
        return;
      }
      tbody.innerHTML = res.data.map(k => `
        <tr>
          <td><span class="badge">${escHtml(k.nama_kategori)}</span></td>
          <td>${escHtml(k.keterangan || '-')}</td>
          <td>
            <button class="btn btn-edit" onclick="openEditKategori(${k.id})">Edit</button>
            <button class="btn btn-danger" onclick="konfirmasiHapus('kategori', ${k.id})">Hapus</button>
          </td>
        </tr>`).join('');
    })
    .catch(() => showToast('Gagal memuat data kategori', 'error'));
}

function simpanKategori() {
  const fd = new FormData();
  fd.append('nama_kategori', document.getElementById('tk-nama').value.trim());
  fd.append('keterangan',    document.getElementById('tk-keterangan').value.trim());

  fetch('simpan_kategori.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-tambah-kategori');
        resetForm(['tk-nama','tk-keterangan']);
        loadKategori();
      } else {
        showToast(res.message, 'error');
      }
    });
}

function openEditKategori(id) {
  fetch('ambil_satu_kategori.php?id=' + id)
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        document.getElementById('ek-id').value          = res.data.id;
        document.getElementById('ek-nama').value        = res.data.nama_kategori;
        document.getElementById('ek-keterangan').value  = res.data.keterangan || '';
        openModal('modal-edit-kategori');
      } else {
        showToast(res.message, 'error');
      }
    });
}

function updateKategori() {
  const fd = new FormData();
  fd.append('id',            document.getElementById('ek-id').value);
  fd.append('nama_kategori', document.getElementById('ek-nama').value.trim());
  fd.append('keterangan',    document.getElementById('ek-keterangan').value.trim());

  fetch('update_kategori.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showToast(res.message);
        closeModal('modal-edit-kategori');
        loadKategori();
      } else {
        showToast(res.message, 'error');
      }
    });
}

// ==================================================
//  HAPUS UMUM
// ==================================================
function konfirmasiHapus(tipe, id) {
  openModal('modal-hapus');
  document.getElementById('btn-konfirmasi-hapus').onclick = function() {
    const urlMap = { penulis: 'hapus_penulis.php', artikel: 'hapus_artikel.php', kategori: 'hapus_kategori.php' };
    const fd = new FormData();
    fd.append('id', id);

    fetch(urlMap[tipe], { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        closeModal('modal-hapus');
        if (res.status === 'success') {
          showToast(res.message);
          if (tipe === 'penulis')  loadPenulis();
          if (tipe === 'artikel')  loadArtikel();
          if (tipe === 'kategori') loadKategori();
        } else {
          showToast(res.message, 'error');
        }
      });
  };
}

// ==================================================
//  HELPERS
// ==================================================
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;');
}

function resetForm(ids) {
  ids.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
}

// Load data awal
loadPenulis();
</script>
</body>
</html>