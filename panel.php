<?php
session_start();
include 'config.php';

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$message = '';
$message_type = '';

// Handle Upload/Update Foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Update Nama
        if ($action == 'update_nama') {
            $id = intval($_POST['id']);
            $nama = $conn->real_escape_string($_POST['nama']);
            $jabatan = $conn->real_escape_string($_POST['jabatan']);
            
            $sql = "UPDATE tenaga_kependidikan SET nama = '$nama', jabatan = '$jabatan' WHERE id = $id";
            if ($conn->query($sql)) {
                $message = "Data berhasil diupdate!";
                $message_type = "success";
            } else {
                $message = "Gagal update data: " . $conn->error;
                $message_type = "error";
            }
        }
        
        // Upload/Update Foto
        if ($action == 'update_foto') {
            $id = intval($_POST['id']);
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['foto']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'staff_' . $id . '_' . time() . '.' . $ext;
                    $upload_path = 'img/' . $new_filename;
                    
                    // Hapus foto lama jika ada
                    $old_foto = $conn->query("SELECT foto FROM tenaga_kependidikan WHERE id = $id")->fetch_assoc()['foto'];
                    if ($old_foto && file_exists($old_foto)) {
                        unlink($old_foto);
                    }
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                        $sql = "UPDATE tenaga_kependidikan SET foto = '$upload_path' WHERE id = $id";
                        if ($conn->query($sql)) {
                            $message = "Foto berhasil diupload!";
                            $message_type = "success";
                        }
                    } else {
                        $message = "Gagal upload file!";
                        $message_type = "error";
                    }
                } else {
                    $message = "Format file tidak diizinkan! Gunakan: jpg, jpeg, png, gif, webp";
                    $message_type = "error";
                }
            }
        }
        
        // Hapus Foto
        if ($action == 'hapus_foto') {
            $id = intval($_POST['id']);
            $old_foto = $conn->query("SELECT foto FROM tenaga_kependidikan WHERE id = $id")->fetch_assoc()['foto'];
            
            if ($old_foto && file_exists($old_foto)) {
                unlink($old_foto);
            }
            
            $sql = "UPDATE tenaga_kependidikan SET foto = NULL WHERE id = $id";
            if ($conn->query($sql)) {
                $message = "Foto berhasil dihapus!";
                $message_type = "success";
            }
        }
        
        // Tambah Tenaga Baru
        if ($action == 'tambah') {
            $nama = $conn->real_escape_string($_POST['nama']);
            $jabatan = $conn->real_escape_string($_POST['jabatan']);
            $nik = $conn->real_escape_string($_POST['nik']);
            $email = $conn->real_escape_string($_POST['email']);
            
            $sql = "INSERT INTO tenaga_kependidikan (nama, jabatan, nik, email) VALUES ('$nama', '$jabatan', '$nik', '$email')";
            if ($conn->query($sql)) {
                $message = "Tenaga kependidikan berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambah data: " . $conn->error;
                $message_type = "error";
            }
        }
        
        // Hapus Tenaga
        if ($action == 'hapus') {
            $id = intval($_POST['id']);
            $old_foto = $conn->query("SELECT foto FROM tenaga_kependidikan WHERE id = $id")->fetch_assoc()['foto'];
            
            if ($old_foto && file_exists($old_foto)) {
                unlink($old_foto);
            }
            
            $sql = "DELETE FROM tenaga_kependidikan WHERE id = $id";
            if ($conn->query($sql)) {
                $message = "Data berhasil dihapus!";
                $message_type = "success";
            }
        // Hapus Ulasan
        if ($action == 'hapus_ulasan') {
            $id = intval($_POST['id']);
            if ($conn->query("DELETE FROM ulasan WHERE id = $id")) {
                $message = "Ulasan berhasil dihapus!";
                $message_type = "success";
            }
        }

        // Hapus Ulasan SDM Rendah
        if ($action == 'hapus_ulasan_rendah') {
            $id = intval($_POST['id']);
            if ($conn->query("DELETE FROM ulasan_sdm_rendah WHERE id = $id")) {
                $message = "Ulasan SDM rendah berhasil dihapus!";
                $message_type = "success";
            }
        }
    }
}

// Ambil semua data tenaga
$tenaga_list = $conn->query("SELECT * FROM tenaga_kependidikan ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Kelola Tenaga Kependidikan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .panel-container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .add-form { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .add-form h3 { margin-bottom: 15px; color: #333; }
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; }
        .form-row input { flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        
        .staff-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .staff-table th, .staff-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .staff-table th { background-color: #073c64; color: white; }
        .staff-table tr:hover { background: #f8f9fa; }
        
        .staff-foto { width: 110px; height: 110px; border-radius: 0; object-fit: cover; border: none; }
        .no-foto { width: 80px; height: 80px; border-radius: 8px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 12px; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; transition: all 0.3s; }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
        
        .action-btns { display: flex; gap: 5px; flex-wrap: wrap; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%; }
        .modal-content h3 { margin-bottom: 20px; }
        .modal-content input, .modal-content select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .modal-btns { display: flex; gap: 10px; justify-content: flex-end; }
        
        .back-link-container { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h1>PANEL ADMIN - KELOLA TENAGA KEPENDIDIKAN</h1>
        <div style="display:flex; align-items:center; gap:15px;">
            <span style="font-size:0.85em; opacity:0.85;">👤 <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
            <a href="?logout=1" style="background:#dc3545; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:0.9em; font-weight:600;" onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
        </div>
    </div>

    <div class="panel-container">
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form Tambah Tenaga Baru -->
        <div class="add-form">
            <h3>➕ Tambah Tenaga Kependidikan Baru</h3>
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="form-row">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                    <input type="text" name="jabatan" placeholder="Jabatan" required>
                </div>
                <div class="form-row">
                    <input type="text" name="nik" placeholder="NIK (4 digit)" maxlength="4" required>
                    <input type="email" name="email" placeholder="Email">
                </div>
                <button type="submit" class="btn btn-success">Tambah Data</button>
            </form>
        </div>

        <!-- Tabel Data Tenaga -->
        <table class="staff-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>NIK</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tenaga_list->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($row['foto'] && file_exists($row['foto'])): ?>
                            <img src="<?php echo htmlspecialchars($row['foto']); ?>" class="staff-foto" alt="Foto" onclick="openPreviewModal('<?php echo htmlspecialchars($row['foto']); ?>', '<?php echo addslashes($row['nama']); ?>')" style="cursor:pointer;" title="Klik untuk preview">
                        <?php else: ?>
                            <div class="no-foto">No Foto</div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                    <td><?php echo htmlspecialchars($row['nik']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-primary" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nama']); ?>', '<?php echo addslashes($row['jabatan']); ?>')">✏️ Edit</button>
                            <button class="btn btn-warning" onclick="openFotoModal(<?php echo $row['id']; ?>)">📷 Foto</button>
                            <?php if ($row['foto']): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus foto ini?')">
                                <input type="hidden" name="action" value="hapus_foto">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">🗑️ Hapus Foto</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini? Semua ulasan terkait juga akan terhapus!')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">❌ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="back-link-container">
            <a href="ulasan.php" class="back-link">← Kembali ke Ulasan</a>
            <a href="dashboard.php" class="back-link" style="margin-left:15px;">← Dashboard</a>
        </div>
    </div>

    <!-- Modal Edit Nama -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>✏️ Edit Data Tenaga</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_nama">
                <input type="hidden" name="id" id="edit_id">
                <label>Nama:</label>
                <input type="text" name="nama" id="edit_nama" required>
                <label>Jabatan:</label>
                <input type="text" name="jabatan" id="edit_jabatan" required>
                <div class="modal-btns">
                    <button type="button" class="btn btn-danger" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Upload Foto -->
    <div id="fotoModal" class="modal">
        <div class="modal-content">
            <h3>📷 Upload/Ganti Foto</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_foto">
                <input type="hidden" name="id" id="foto_id">
                <label>Pilih Foto (jpg, png, gif, webp):</label>
                <input type="file" name="foto" accept="image/*" required>
                <div class="modal-btns">
                    <button type="button" class="btn btn-danger" onclick="closeModal('fotoModal')">Batal</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Kelola Ulasan -->
    <div class="panel-container" style="margin-top: 30px;">
        <h2 style="color:#073c64; border-bottom: 2px solid #073c64; padding-bottom: 10px; margin-bottom: 20px;">🗑️ Kelola Ulasan</h2>

        <?php
        $ulasan_list = $conn->query("
            SELECT u.id, u.nama_reviewer, u.rating, u.komentar, u.tanggal, tk.nama as staff_nama
            FROM ulasan u
            JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
            ORDER BY u.tanggal DESC
        ");
        ?>

        <?php if ($ulasan_list && $ulasan_list->num_rows > 0): ?>
        <table class="staff-table" style="width:100%; border-collapse:collapse; margin-bottom:30px;">
            <thead>
                <tr>
                    <th style="background:#073c64;color:white;padding:10px;text-align:left;">Tenaga</th>
                    <th style="background:#073c64;color:white;padding:10px;text-align:left;">Reviewer</th>
                    <th style="background:#073c64;color:white;padding:10px;text-align:left;">Rating</th>
                    <th style="background:#073c64;color:white;padding:10px;text-align:left;">Komentar</th>
                    <th style="background:#073c64;color:white;padding:10px;text-align:left;">Tanggal</th>
                    <th style="background:#073c64;color:white;padding:10px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($u = $ulasan_list->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['staff_nama']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['nama_reviewer']); ?></td>
                    <td style="padding:10px;color:#ffa600;">
                        <?php echo str_repeat('★', $u['rating']) . str_repeat('☆', 5 - $u['rating']); ?>
                        (<?php echo $u['rating']; ?>/5)
                    </td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['komentar']); ?></td>
                    <td style="padding:10px; font-size:0.85em; color:#666;"><?php echo date('d M Y H:i', strtotime($u['tanggal'])); ?></td>
                    <td style="padding:10px;text-align:center;">
                        <form method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                            <input type="hidden" name="action" value="hapus_ulasan">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <button type="submit" style="background:#dc3545;color:white;border:none;padding:6px 12px;border-radius:5px;cursor:pointer;font-size:13px;">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color:#999; text-align:center; padding:20px;">Tidak ada ulasan.</p>
        <?php endif; ?>

        <?php
        $sdm_list = $conn->query("
            SELECT u.id, u.nama_reviewer, u.rating, u.komentar, u.tanggal, tk.nama as staff_nama
            FROM ulasan_sdm_rendah u
            JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
            ORDER BY u.tanggal DESC
        ");
        ?>

        <?php if ($sdm_list && $sdm_list->num_rows > 0): ?>
        <h3 style="color:#dc3545; margin-bottom:10px;">⚠️ Ulasan SDM Rendah</h3>
        <table class="staff-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:left;">Tenaga</th>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:left;">Reviewer</th>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:left;">Rating</th>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:left;">Komentar</th>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:left;">Tanggal</th>
                    <th style="background:#dc3545;color:white;padding:10px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($u = $sdm_list->fetch_assoc()): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['staff_nama']); ?></td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['nama_reviewer']); ?></td>
                    <td style="padding:10px;color:#dc3545;">
                        <?php echo str_repeat('★', $u['rating']) . str_repeat('☆', 5 - $u['rating']); ?>
                        (<?php echo $u['rating']; ?>/5)
                    </td>
                    <td style="padding:10px;"><?php echo htmlspecialchars($u['komentar']); ?></td>
                    <td style="padding:10px; font-size:0.85em; color:#666;"><?php echo date('d M Y H:i', strtotime($u['tanggal'])); ?></td>
                    <td style="padding:10px;text-align:center;">
                        <form method="POST" onsubmit="return confirm('Hapus ulasan SDM rendah ini?')">
                            <input type="hidden" name="action" value="hapus_ulasan_rendah">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <button type="submit" style="background:#dc3545;color:white;border:none;padding:6px 12px;border-radius:5px;cursor:pointer;font-size:13px;">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Modal Preview Foto -->
    <div id="previewModal" class="modal">
        <div class="modal-content" style="max-width:600px;text-align:center;">
            <h3 id="preview_nama">Preview Foto</h3>
            <img id="preview_img" src="" alt="Preview" style="max-width:100%;max-height:400px;border-radius:10px;margin:15px 0;">
            <div class="modal-btns" style="justify-content:center;">
                <button type="button" class="btn btn-primary" onclick="closeModal('previewModal')">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, nama, jabatan) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_jabatan').value = jabatan;
            document.getElementById('editModal').classList.add('active');
        }

        function openFotoModal(id) {
            document.getElementById('foto_id').value = id;
            document.getElementById('fotoModal').classList.add('active');
        }

        function openPreviewModal(src, nama) {
            document.getElementById('preview_img').src = src;
            document.getElementById('preview_nama').textContent = '📷 ' + nama;
            document.getElementById('previewModal').classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>

    <?php closeConnection(); ?>
</body>
</html>
