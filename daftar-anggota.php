<?php
// Memulai session dan menyertakan koneksi database
session_start();
include '../koneksi.php'; // Naik satu folder untuk mengambil koneksi.php

// Ambil data pencarian dan filter status jika ada
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';

// Menyusun Query SQL untuk menggabungkan tabel anggota dengan tabel users (untuk mendapatkan status & username)
$query = "SELECT anggota.*, users.status FROM anggota 
          JOIN users ON anggota.id_user = users.id_user WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (anggota.nama LIKE '%$search%' OR anggota.nim LIKE '%$search%' OR anggota.email LIKE '%$search%')";
}

if (!empty($status)) {
    $query .= " AND users.status = '$status'";
}

$query .= " ORDER BY anggota.id_anggota DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/book-management.css">
</head>

<body>

    <div class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i> Perpustakaan
        </div>
        <hr>
        <a href="dashboard-admin.php">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <div class="menu-group">
            <div class="menu-toggle" onclick="toggleMenu(this,'bukuMenu')">
                <span><i class="fa-solid fa-book"></i> Buku</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </div>
            <div class="submenu" id=\"bukuMenu\">
                <a href="daftar-buku.php"><i class="fa-solid fa-list"></i> Daftar Buku</a>
                <a href="tambah-buku.php"><i class="fa-solid fa-plus"></i> Tambah Buku</a>
            </div>
        </div>
        <div class="menu-group">
            <div class="menu-toggle" onclick="toggleMenu(this,'anggotaMenu')">
                <span><i class="fa-solid fa-users"></i> Anggota</span>
                <i class="fa-solid fa-chevron-down arrow rotate"></i>
            </div>
            <div class="submenu show" id="anggotaMenu">
                <a href="daftar-anggota.php" class="active"><i class="fa-solid fa-list"></i> Daftar Anggota</a>
                <a href="tambah-anggota.php"><i class="fa-solid fa-user-plus"></i> Tambah Anggota</a>
            </div>
        </div>
        <a href="../logout.php" class="mt-5 text-danger"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="page-header">
                <div>
                    <p class="page-kicker">Manajemen Pengguna</p>
                    <h2>Daftar Anggota</h2>
                    <p class="page-description">Kelola data seluruh anggota perpustakaan yang terdaftar dalam sistem.</p>
                </div>
                <a href="tambah-anggota.php" class="btn btn-primary" style="background-color: var(--primary); border-radius:12px;">
                    <i class="fa-solid fa-user-plus me-2"></i> Tambah Anggota Baru
                </a>
            </div>

            <form method="GET" action="" class="book-toolbar mb-4">
                <div class="input-group book-search">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, NIM, atau email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" <?php if($status == 'aktif') echo 'selected'; ?>>Aktif</option>
                    <option value="tidak aktif" <?php if($status == 'tidak aktif') echo 'selected'; ?>>Tidak Aktif</option>
                </select>
                <button type="submit" class="btn btn-secondary" style="border-radius: 12px;">Filter</button>
            </form>

            <div class="card section-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table book-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Profil Anggota</th>
                                <th>Kontak</th>
                                <th>Pekerjaan</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Set badge warna status
                                    $statusBadge = ($row['status'] == 'aktif') ? 'bg-success' : 'bg-danger';
                                    // Foto default jika kosong
                                    $foto = !empty($row['foto_profil']) ? '../assets/profil/'.$row['foto_profil'] : 'https://via.placeholder.com/150';
                            ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo $foto; ?>" alt="Foto" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($row['nama']); ?></h6>
                                                    <small class="text-muted">NIM: <?php echo htmlspecialchars($row['nim']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small"><i class="fa-regular fa-envelope me-1 text-muted"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                            <div class="small mt-1"><i class="fa-solid fa-phone me-1 text-muted"></i> <?php echo htmlspecialchars($row['no_telp']); ?></div>
                                        </td>
                                        <td><span class="badge bg-light text-dark py-2 px-3 border"><?php echo htmlspecialchars($row['pekerjaan']); ?></span></td>
                                        <td class="text-wrap" style="max-width: 200px; font-size: 0.9rem;"><?php echo htmlspecialchars($row['alamat']); ?></td>
                                        <td><span class="badge <?php echo $statusBadge; ?> rounded-pill px-3 py-2"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2 px-3">
                                                <a href="edit-anggota.php?id=<?php echo $row['id_anggota']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="hapus-anggota.php?id=<?php echo $row['id_user']; ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini beserta akun loginnya?')"><i class="fa-solid fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Tidak ada data anggota ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>

</html>
