<?php
include 'config.php';

// Daftar kata-kata tidak pantas
$badWords = ['fuck', 'shit', 'anjing', 'asu', 'babi', 'kontol', 'bangsat', 'badjingan', 'nyenuk', 'ngentod', 'memek', 'tolol', 'goblok', 'nigger', 'pussy', 'asshole', 'bitch', 'bastard', 'cunt', 'damn', 'hell', 'piss', 'dick', 'cock', 'faggot', 'slut', 'whore'];

function containsBadWords($text, $badWords) {
    $text = strtolower($text);
    foreach ($badWords as $word) {
        if (strpos($text, strtolower($word)) !== false) return true;
    }
    return false;
}

// Ambil ID tenaga kependidikan
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: tberitarating.php");
    exit();
}

// Proses submit ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = trim($_POST['reviewer_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if (!empty($reviewer_name) && $rating >= 1 && $rating <= 5 && !empty($comment)) {
        $table = containsBadWords($comment, $badWords) ? 'ulasan_sdm_rendah' : 'ulasan';
        $stmt = $conn->prepare("INSERT INTO $table (tenaga_id, nama_reviewer, rating, komentar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $id, $reviewer_name, $rating, $comment);
        if ($stmt->execute()) {
            $success_message = "Ulasan berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan ulasan.";
        }
        $stmt->close();
    } else {
        $error_message = "Harap isi semua field dengan benar.";
    }
}

// Ambil data tenaga kependidikan
$stmt = $conn->prepare("SELECT * FROM tenaga_kependidikan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$staff) {
    header("Location: tberitarating.php");
    exit();
}

// Ambil rata-rata rating dan total ulasan
$query = "SELECT AVG(rating) as avg_rating, COUNT(id) as total FROM ulasan WHERE tenaga_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$rating_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil semua ulasan
$query = "SELECT * FROM ulasan WHERE tenaga_id = ? ORDER BY tanggal DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan <?php echo htmlspecialchars($staff['nama']); ?> - FKIP UMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .staff-profile { background: rgba(255,255,255,0.95); border-radius: 15px; padding: 25px; margin-bottom: 25px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .staff-profile h2 { margin: 0 0 10px 0; color: #2c3e50; }
        .staff-profile .jabatan { color: #667eea; font-weight: 600; margin-bottom: 10px; }
        .staff-profile .info { color: #666; margin-bottom: 5px; }
        .rating-summary { font-size: 1.3em; margin-top: 15px; }
        .stars { color: #ffa600; font-size: 1.5em; }
        .star.empty { color: #ddd; }
        .reviews-section { margin-top: 30px; }
        .reviews-section h3 { color: #2c3e50; margin-bottom: 20px; }
        .review-item { background: rgba(255,255,255,0.95); border-radius: 10px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .review-item .reviewer { font-weight: 600; color: #667eea; }
        .review-item .review-stars { color: #ffa600; margin: 8px 0; }
        .review-item .comment { color: #555; line-height: 1.6; }
        .review-item .date { color: #999; font-size: 0.85em; margin-top: 10px; }
        .back-link-container { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ULASAN TENAGA KEPENDIDIKAN</h1>
    </div>

    <div class="back-link-container">
        <a href="tberitarating.php" class="back-link">← Kembali ke Daftar Rating</a>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php 
    // Tentukan sumber foto: dari database atau default
    $foto_src = !empty($staff['foto']) ? htmlspecialchars($staff['foto']) : 'img/' . $staff['id'] . '.jpg';
    ?>
    <div class="staff-profile">
        <div style="display:flex; align-items:center; gap:20px; margin-bottom:15px;">
            <img src="<?php echo $foto_src; ?>" alt="Foto <?php echo htmlspecialchars($staff['nama']); ?>" style="width:110px;height:110px;border-radius:0;object-fit:cover;" onerror="this.src='https://via.placeholder.com/110x110?text=No+Photo'">
            <div>
                <h2><?php echo htmlspecialchars($staff['nama']); ?></h2>
                <div class="jabatan"><?php echo htmlspecialchars($staff['jabatan']); ?></div>
            </div>
        </div>
        <div class="info">NIK: <?php echo htmlspecialchars($staff['nik']); ?></div>
        <?php if ($staff['email']): ?>
            <div class="info">Email: <?php echo htmlspecialchars($staff['email']); ?></div>
        <?php endif; ?>
        <div class="rating-summary">
            <div class="stars"><?php echo displayStars($rating_data['avg_rating'] ?? 0); ?></div>
            <span><?php echo number_format($rating_data['avg_rating'] ?? 0, 1); ?>/5 dari <?php echo $rating_data['total'] ?? 0; ?> ulasan</span>
        </div>
    </div>

    <!-- Form Ulasan -->
    <div class="staff-card">
        <div class="add-review">
            <h4>Beri Ulasan:</h4>
            <form method="POST">
                <div class="form-group">
                    <label for="reviewer_name">Nama Anda:</label>
                    <input type="text" id="reviewer_name" name="reviewer_name" required>
                </div>
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="star-rating" id="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>" data-value="<?php echo $i; ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment">Komentar:</label>
                    <textarea id="comment" name="comment" rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="submit-btn">Posting Ulasan</button>
            </form>
        </div>
    </div>

    <!-- Daftar Ulasan -->
    <div class="reviews-section">
        <h3>Ulasan Terbaru</h3>
        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review-item">
                    <div class="reviewer"><?php echo htmlspecialchars($review['nama_reviewer']); ?></div>
                    <div class="review-stars"><?php echo displayStars($review['rating']); ?></div>
                    <div class="comment"><?php echo nl2br(htmlspecialchars($review['komentar'])); ?></div>
                    <div class="date"><?php echo date('d M Y, H:i', strtotime($review['tanggal'])); ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-reviews">Belum ada ulasan untuk tenaga kependidikan ini.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('.star-rating label');
            const inputs = document.querySelectorAll('.star-rating input');
            
            labels.forEach((label, index) => {
                label.addEventListener('click', function() {
                    labels.forEach(l => l.classList.remove('active'));
                    for (let i = 0; i <= index; i++) {
                        labels[i].classList.add('active');
                    }
                });
                label.addEventListener('mouseenter', function() {
                    for (let i = 0; i <= index; i++) labels[i].classList.add('hover');
                });
                label.addEventListener('mouseleave', function() {
                    labels.forEach(l => l.classList.remove('hover'));
                });
            });
        });
    </script>

    <?php closeConnection(); ?>
</body>
</html>
