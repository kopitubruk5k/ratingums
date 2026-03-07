<?php
include 'config.php';

$query = "
    SELECT tk.*, 
           COALESCE(AVG(u.rating), 0) as avg_rating, 
           COUNT(u.id) as total_reviews
    FROM tenaga_kependidikan tk
    LEFT JOIN ulasan u ON tk.id = u.tenaga_id
    GROUP BY tk.id
    ORDER BY tk.id ASC
";
$result = $conn->query($query);

function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= round($rating)) ? '<span class="star filled">★</span>' : '<span class="star empty">☆</span>';
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Rating Tenaga Kependidikan - FKIP UMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;font-size:15px;line-height:1.6;color:#3a3a3a;background:#f5f5f5;}
        h3{font-weight:600;font-size:1rem;margin:0 0 5px 0;color:#0170B9;}
        p{margin:0 0 5px 0;}
        a{color:#0170B9;text-decoration:none;}
        a:hover{color:#014a7d;}
        .container{max-width:900px;margin:0 auto;padding:20px;}
        .page-header{background:linear-gradient(135deg,#0170B9 0%,#014a7d 100%);color:#fff;padding:40px 20px;text-align:center;margin-bottom:30px;}
        .page-header h1{font-size:1.8rem;margin:0 0 10px 0;font-weight:600;}
        .page-header p{opacity:0.9;margin:0;}
        .back-link{display:inline-block;padding:10px 20px;background:#0170B9;color:#fff;border-radius:5px;margin-bottom:20px;transition:background 0.2s;}
        .back-link:hover{background:#014a7d;color:#fff;}
        .staff-table{background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.08);overflow:hidden;}
        .staff-table table{width:100%;border-collapse:collapse;}
        .staff-table td{padding:15px;vertical-align:middle;border-bottom:1px solid #eee;}
        .staff-table tr:last-child td{border-bottom:none;}
        .staff-table tr:hover{background:#f9f9f9;}
        .staff-photo{width:110px;height:110px;border-radius:0;object-fit:cover;border:none;}
        .stars{font-size:1.2em;color:#ffa600;}
        .star.empty{color:#ddd;}
        .rating-text{font-size:0.85em;color:#666;margin-top:3px;}
        .btn-ulasan{display:inline-block;padding:6px 12px;background-color:#ffa600;color:white;text-decoration:none;border-radius:3px;font-size:0.85em;margin-top:8px;transition:background 0.2s;}
        .btn-ulasan:hover{background-color:#e69500;color:white;}
    </style>
</head>
<body>

<div class="page-header">
    <h1>DAFTAR RATING TENAGA KEPENDIDIKAN</h1>
    <p>Fakultas Keguruan dan Ilmu Pendidikan - Universitas Muhammadiyah Surakarta</p>
</div>

<div class="container">
    <a href="index.php" class="back-link">← Kembali ke Beranda</a>

    <div class="staff-table">
        <table border="0" width="100%">
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($staff = $result->fetch_assoc()): ?>
                    <?php $foto_src = !empty($staff['foto']) ? htmlspecialchars($staff['foto']) : 'img/' . $staff['id'] . '.jpg'; ?>
                    <tr>
                        <td style="width:80px;">
                            <img src="<?php echo $foto_src; ?>" alt="Foto" class="staff-photo" onerror="this.src='https://via.placeholder.com/60x60?text=Foto'">
                        </td>
                        <td>
                            <h3><?php echo htmlspecialchars($staff['nama']); ?></h3>
                            <p><?php echo htmlspecialchars($staff['jabatan']); ?></p>
                            <p>NIK: <?php echo htmlspecialchars($staff['nik']); ?></p>
                        </td>
                        <td style="text-align:right;width:180px;">
                            <div class="stars"><?php echo displayStars($staff['avg_rating']); ?></div>
                            <div class="rating-text"><?php echo number_format($staff['avg_rating'], 1); ?>/5 (<?php echo $staff['total_reviews']; ?> ulasan)</div>
                            <a href="ulasanratingtenaga.php?id=<?php echo $staff['id']; ?>" class="btn-ulasan">Lihat & Beri Ulasan</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align:center;padding:30px;">Tidak ada data.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php closeConnection(); ?>
</body>
</html>
