<?php
session_start();
include 'config.php';
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Blokir akses export jika bukan admin
if (isset($_GET['export']) && !$is_admin) {
    header("Location: index.php");
    exit();
}

// Helper: display star characters
function exportStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rating) ? '★' : '☆';
    }
    return $stars;
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = 'Ulasan_Tenaga_Kependidikan_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, ['No', 'Tenaga Kependidikan', 'Jabatan', 'Reviewer', 'Rating', 'Bintang', 'Komentar', 'Tanggal']);

    $export_query = "
        SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
        FROM ulasan u
        JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
        ORDER BY u.tanggal DESC
    ";
    $export_result = $conn->query($export_query);
    $no = 1;
    while ($row = $export_result->fetch_assoc()) {
        $stars = exportStars($row['rating']);
        fputcsv($output, [
            $no++,
            $row['staff_name'],
            $row['staff_position'],
            $row['nama_reviewer'],
            $row['rating'] . '/5',
            $stars . ' (' . $row['rating'] . '/5)',
            $row['komentar'],
            date('d/m/Y H:i', strtotime($row['tanggal']))
        ]);
    }
    fclose($output);
    closeConnection();
    exit();
}

// Handle Excel export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $filename = 'Ulasan_Tenaga_Kependidikan_' . date('Y-m-d_H-i-s') . '.xls';
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // UTF-8 BOM
    echo chr(0xEF).chr(0xBB).chr(0xBF);

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="utf-8">';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
    echo '<x:Name>Ulasan</x:Name>';
    echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
    echo '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; }';
    echo 'th { background-color: #073c64; color: white; font-weight: bold; padding: 10px; text-align: center; border: 1px solid #ddd; font-size: 12pt; }';
    echo 'td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; font-size: 11pt; }';
    echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
    echo '.star { color: #ffa600; font-size: 13pt; }';
    echo '</style></head><body>';

    echo '<h2 style="text-align:center; font-family: Times New Roman;">LAPORAN ULASAN TENAGA KEPENDIDIKAN FKIP UMS</h2>';
    echo '<p style="text-align:center;">Tanggal Export: ' . date('d/m/Y H:i') . '</p>';

    echo '<table>';
    echo '<tr><th>No</th><th>Tenaga Kependidikan</th><th>Jabatan</th><th>Reviewer</th><th>Rating</th><th>Bintang</th><th>Komentar</th><th>Tanggal</th></tr>';

    $export_query = "
        SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
        FROM ulasan u
        JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
        ORDER BY u.tanggal DESC
    ";
    $export_result = $conn->query($export_query);
    $no = 1;
    while ($row = $export_result->fetch_assoc()) {
        $stars = exportStars($row['rating']);
        echo '<tr>';
        echo '<td style="text-align:center;">' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['staff_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['staff_position']) . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_reviewer']) . '</td>';
        echo '<td style="text-align:center;">' . $row['rating'] . '/5</td>';
        echo '<td class="star">' . $stars . ' (' . $row['rating'] . '/5)</td>';
        echo '<td>' . htmlspecialchars($row['komentar']) . '</td>';
        echo '<td>' . date('d/m/Y H:i', strtotime($row['tanggal'])) . '</td>';
        echo '</tr>';
    }
    echo '</table></body></html>';
    closeConnection();
    exit();
}

// Handle PDF export
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    $export_query = "
        SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position
        FROM ulasan u
        JOIN tenaga_kependidikan tk ON u.tenaga_id = tk.id
        ORDER BY u.tanggal DESC
    ";
    $export_result = $conn->query($export_query);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Export PDF - Ulasan Tenaga Kependidikan</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            h1 { text-align: center; font-size: 18px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #333; padding: 8px; text-align: left; }
            th { background-color: #667eea; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .print-btn { background: #667eea; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-bottom: 20px; }
            @media print { .no-print { display: none; } }
        </style>
    </head>
    <body>
        <div class="no-print">
            <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
            <a href="ulasan.php" style="margin-left:10px;">← Kembali</a>
        </div>
        <h1>LAPORAN ULASAN TENAGA KEPENDIDIKAN FKIP UMS</h1>
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
    SELECT u.*, tk.nama as staff_name, tk.jabatan as staff_position, tk.foto
    FROM ulasan u
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
    <title>Semua Ulasan - UMS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif !important; }
        .header h1 { font-family: 'Times New Roman', Times, serif !important; }
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
        .export-csv { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEMUA ULASAN TENAGA KEPENDIDIKAN FKIP</h1>
    </div>

    <div class="filters">
        <form method="GET" action="ulasan.php">
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

        <?php if ($is_admin): ?>
        <div class="export-buttons">
            <a href="ulasan.php?export=csv" class="export-btn export-csv">📄 Export CSV</a>
            <a href="ulasan.php?export=excel" class="export-btn export-excel">📊 Export Excel</a>
            <a href="ulasan.php?export=pdf" class="export-btn export-pdf">🖨️ Export PDF</a>
        </div>
        <?php endif; ?>
    </div>

    <div class="reviews-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="staff-avatar">
                            <img src="<?php echo htmlspecialchars($review['foto'] ?? 'https://via.placeholder.com/60x60?text=Avatar'); ?>" alt="Staff Avatar">
                        </div>
                        <div class="staff-info">
                            <strong><?php echo htmlspecialchars($review['staff_position']); ?>:</strong> <?php echo htmlspecialchars($review['staff_name']); ?>
                        </div>
                        <div class="review-rating"><?php echo displayStars($review['rating']); ?> (<?php echo $review['rating']; ?>/5)</div>
                    </div>
                    <div class="review-author" style="display:flex;align-items:center;gap:6px;">
                        🙍 Pengguna Anonim
                        <span style="background:#f0f4ff;border:1px solid #c7d4ff;color:#667eea;font-size:0.75em;padding:2px 8px;border-radius:20px;font-weight:500;cursor:help;" title="Nama reviewer disembunyikan untuk menjaga privasi">🔒 Privasi</span>
                    </div>
                    <div class="review-comment"><?php echo htmlspecialchars($review['komentar']); ?></div>
                    <small><?php echo date('d M Y H:i', strtotime($review['tanggal'])); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada ulasan yang sesuai dengan filter.</p>
        <?php endif; ?>
    </div>

    <div class="back-link-container">
        <a href="dashboard.php" class="back-link">Kembali ke Daftar</a>
    </div>

    <?php if ($is_admin): ?>
    <div class="bottom-links">
        <div class="link-box">
            <a href="sdmrendah.php" class="sdm-link">Ulasan SDM Rendah</a>
        </div>
        <div class="link-box">
            <a href="panel.php" class="sdm-link" style="background-color: #073c64;">⚙️ Panel Admin</a>
        </div>
    </div>
    <?php endif; ?>

    <?php closeConnection(); ?>
<?php include 'footer.php'; ?>
</body>
</html>
