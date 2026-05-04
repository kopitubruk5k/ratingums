<?php
require_once 'auth_check.php';
include 'config.php';

// Handle Excel export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="ulasan_sdm_rendah_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "<table border='1'>";
    echo "<tr><th>No</th><th>Tenaga Kependidikan</th><th>Jabatan</th><th>Reviewer</th><th>Rating</th><th>Komentar</th><th>Tanggal</th></tr>";

    $export_query = "
        SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
        FROM ulasan_sdm_rendah u
        JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
        ORDER BY u.tanggal DESC
    ";
    $export_result = $conn->query($export_query);
    $no = 1;
    while ($row = $export_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['staff_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['staff_position']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama_reviewer']) . "</td>";
        echo "<td>" . $row['rating'] . "/5</td>";
        echo "<td>" . htmlspecialchars($row['komentar']) . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row['tanggal'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}

// Handle PDF export
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    $export_query = "
        SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
        FROM ulasan_sdm_rendah u
        JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
        ORDER BY u.tanggal DESC
    ";
    $export_result = $conn->query($export_query);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Export PDF - Ulasan SDM Rendah</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            h1 { text-align: center; font-size: 18px; margin-bottom: 20px; color: #dc3545; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #333; padding: 8px; text-align: left; }
            th { background-color: #dc3545; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .print-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-bottom: 20px; }
            @media print { .no-print { display: none; } }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
            <a href="sdmrendah.php" style="margin-left:10px;">← Kembali</a>
        </div>
        <h1>LAPORAN ULASAN SDM RENDAH - FKIP UMS</h1>
        <p>Tanggal Export: <?php echo date('d/m/Y H:i'); ?></p>
        <table>
            <tr>
                <th>No</th>
                <th>Tenaga Kependidikan</th>
                <th>Jabatan</th>
                <th>Reviewer</th>
                <th>Rating</th>
                <th>Komentar</th>
                <th>Tanggal</th>
            </tr>
            <?php $no = 1; while ($row = $export_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                <td><?php echo htmlspecialchars($row['staff_position']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_reviewer']); ?></td>
                <td><?php echo $row['rating']; ?>/5</td>
                <td><?php echo htmlspecialchars($row['komentar']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </body>
    </html>
    <?php
    exit();
}

$rating_filter = isset($_GET['rating']) ? intval($_GET['rating']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$where_clause = "";
if ($rating_filter && in_array($rating_filter, [1,2,3,4,5])) {
    $where_clause = "WHERE u.rating = $rating_filter";
}

$order_clause = "";
switch ($sort) {
    case 'oldest':
        $order_clause = "ORDER BY u.tanggal ASC";
        break;
    case 'relevant':
        $order_clause = "ORDER BY u.rating DESC, u.tanggal DESC";
        break;
    case 'newest':
    default:
        $order_clause = "ORDER BY u.tanggal DESC";
        break;
}

$query = "
    SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
    FROM ulasan_sdm_rendah u
    JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
    $where_clause
    $order_clause
";

$result = $conn->query($query);

function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? '★' : '☆';
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan SDM Rendah - UMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .export-buttons { display: flex; gap: 10px; margin-left: auto; }
        .export-btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9em;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .export-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .export-excel { background: #1d6f42; color: white; }
        .export-pdf { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ULASAN SDM RENDAH</h1>
    </div>

    <div class="filters">
        <form method="GET" action="sdmrendah.php">
            <label for="rating">Filter Bintang:</label>
            <select name="rating" id="rating">
                <option value="">Semua</option>
                <option value="5" <?php if ($rating_filter == 5) echo 'selected'; ?>>5 Bintang</option>
                <option value="4" <?php if ($rating_filter == 4) echo 'selected'; ?>>4 Bintang</option>
                <option value="3" <?php if ($rating_filter == 3) echo 'selected'; ?>>3 Bintang</option>
                <option value="2" <?php if ($rating_filter == 2) echo 'selected'; ?>>2 Bintang</option>
                <option value="1" <?php if ($rating_filter == 1) echo 'selected'; ?>>1 Bintang</option>
            </select>

            <label for="sort">Urutkan:</label>
            <select name="sort" id="sort">
                <option value="newest" <?php if ($sort == 'newest') echo 'selected'; ?>>Terbaru</option>
                <option value="oldest" <?php if ($sort == 'oldest') echo 'selected'; ?>>Terlama</option>
                <option value="relevant" <?php if ($sort == 'relevant') echo 'selected'; ?>>Relevan</option>
            </select>

            <button type="submit">Terapkan</button>
        </form>

        <div class="export-buttons">
            <a href="sdmrendah.php?export=excel" class="export-btn export-excel">📊 Export Excel</a>
            <a href="sdmrendah.php?export=pdf" class="export-btn export-pdf">📄 Export PDF</a>
        </div>
    </div>

    <div class="reviews-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="staff-info">
                            <strong><?php echo htmlspecialchars($review['staff_position']); ?>:</strong> <?php echo htmlspecialchars($review['staff_name']); ?>
                        </div>
                        <div class="review-rating"><?php echo displayStars($review['rating']); ?> (<?php echo $review['rating']; ?>/5)</div>
                    </div>
                    <div class="review-author"><?php echo htmlspecialchars($review['nama_reviewer']); ?></div>
                    <div class="review-comment"><?php echo htmlspecialchars($review['komentar']); ?></div>
                    <small><?php echo date('d M Y H:i', strtotime($review['tanggal'])); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada ulasan SDM rendah yang sesuai dengan filter.</p>
        <?php endif; ?>
    </div>

    <div class="back-link-container">
        <div class="link-box">
            <a href="ulasan.php" class="back-link">Kembali ke Semua Ulasan</a>
        </div>
    </div>

    <?php closeConnection(); ?><?php include 'footer.php'; ?>
</body>
</html>
