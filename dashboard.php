<?php
include 'config.php';

// Daftar kata-kata tidak pantas
$badWords = [
    // Bahasa Indonesia
    'fuck', 'shit', 'anjing', 'asu', 'babi', 'kontol', 'bangsat', 'badjingan', 'nyenuk', 'ngentod', 'memek', 'tolol', 'goblok', 'nigger', 'pussy',
    // English
    'fuck', 'shit', 'asshole', 'bitch', 'bastard', 'cunt', 'damn', 'hell', 'piss', 'dick', 'cock', 'pussy', 'nigger', 'faggot', 'slut', 'whore'
];

function containsBadWords($text, $badWords) {
    $text = strtolower($text);
    foreach ($badWords as $word) {
        if (strpos($text, strtolower($word)) !== false) {
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $staff_id = intval($_POST['staff_id']);
    $reviewer_name = trim($_POST['reviewer_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);


    if (!empty($reviewer_name) && $rating >= 1 && $rating <= 5 && !empty($comment)) {
        $table = containsBadWords($comment, $badWords) ? 'ulasan_sdm_rendah' : 'ulasan';
        $stmt = $conn->prepare("INSERT INTO $table (tenaga_id, nama_reviewer, rating, komentar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $staff_id, $reviewer_name, $rating, $comment);

        if ($stmt->execute()) {
            $success_message = "Ulasan berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan ulasan. Silakan coba lagi.";
        }
        $stmt->close();
    } else {
        $error_message = "Harap isi semua field dengan benar.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '★'; 
        } else {
            $stars .= '☆'; 
        }
    }
    return $stars;
}

$query = "
    SELECT tk.*, AVG(u.rating) as avg_rating, COUNT(u.id) as total_reviews
    FROM tenaga_kependidikan tk
    LEFT JOIN ulasan u ON tk.id = u.tenaga_id
    GROUP BY tk.id
    ORDER BY tk.id ASC
";

$result = $conn->query($query);

// Simpan data ke array untuk digunakan di HTML dan export
$staffData = [];
while ($row = $result->fetch_assoc()) {
    $staffData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMS - Daftar Tenaga Kependidikan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>DAFTAR TENAGA KEPENDIDIKAN FKIP</h1>
    </div>

    <!-- Export Section -->
    <div class="export-section">
        <div class="export-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export Data
        </div>
        <div class="export-buttons">
            <a href="export_csv.php" class="export-btn export-btn-csv">
                <span class="export-icon">📄</span>
                <span class="export-label">Export CSV</span>
            </a>
            <button onclick="exportToExcel()" class="export-btn export-btn-excel">
                <span class="export-icon">📊</span>
                <span class="export-label">Export Excel</span>
            </button>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (count($staffData) > 0): ?>
        <?php foreach ($staffData as $staff): ?>
            <div class="staff-card">
                <div class="staff-name"><?php echo htmlspecialchars($staff['jabatan']); ?></div>
                <div class="staff-header">
                    <div class="staff-avatar">
                        <img src="<?php echo htmlspecialchars($staff['foto'] ?? 'https://via.placeholder.com/60x60?text=Avatar'); ?>" alt="Staff Avatar">
                    </div>
                    <div class="staff-info">
                        <?php echo htmlspecialchars($staff['nama']); ?><br>
                        <strong>NIK:</strong> <?php echo htmlspecialchars($staff['nik']); ?><br>
                        <?php if ($staff['email']): ?>
                            <?php echo htmlspecialchars($staff['email']); ?><br>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="rating">
                    <a href="ulasan.php" style="text-decoration: none; color: inherit;">
                         <?php echo displayStars(round($staff['avg_rating'])); ?>
                        (<?php echo number_format($staff['avg_rating'], 1); ?>/5 dari <?php echo $staff['total_reviews']; ?> ulasan)
                    </a>
                </div>

                <!-- Form untuk menambah ulasan -->
                <div class="add-review">
                    <h4>Tambah Ulasan:</h4>
                    <form method="POST" action="">
                        <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                        <div class="form-group">
                            <label for="reviewer_name_<?php echo $staff['id']; ?>">Nama Anda:</label>
                            <input type="text" id="reviewer_name_<?php echo $staff['id']; ?>" name="reviewer_name" required>
                        </div>
                        <div class="form-group">
                            <label>Rating:</label>
                            <div class="star-rating">
                                <input type="radio" id="star1_<?php echo $staff['id']; ?>" name="rating" value="1" required>
                                <label for="star1_<?php echo $staff['id']; ?>">★</label>
                                <input type="radio" id="star2_<?php echo $staff['id']; ?>" name="rating" value="2">
                                <label for="star2_<?php echo $staff['id']; ?>">★</label>
                                <input type="radio" id="star3_<?php echo $staff['id']; ?>" name="rating" value="3">
                                <label for="star3_<?php echo $staff['id']; ?>">★</label>
                                <input type="radio" id="star4_<?php echo $staff['id']; ?>" name="rating" value="4">
                                <label for="star4_<?php echo $staff['id']; ?>">★</label>
                                <input type="radio" id="star5_<?php echo $staff['id']; ?>" name="rating" value="5">
                                <label for="star5_<?php echo $staff['id']; ?>">★</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment_<?php echo $staff['id']; ?>">Komentar:</label>
                            <textarea id="comment_<?php echo $staff['id']; ?>" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="submit-btn">Kirim Ulasan</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada data tenaga kependidikan.</p>
    <?php endif; ?>

    <!-- Hidden table for Excel export -->
    <table id="exportTable" style="display:none;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>NIK</th>
                <th>Email</th>
                <th>Rating</th>
                <th>Bintang</th>
                <th>Total Ulasan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($staffData as $staff): ?>
            <?php
                $avgRating = $staff['avg_rating'] ? round($staff['avg_rating'], 1) : 0;
                $roundedRating = round($avgRating);
                $starsExport = displayStars($roundedRating);
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo htmlspecialchars($staff['nama']); ?></td>
                <td><?php echo htmlspecialchars($staff['jabatan']); ?></td>
                <td><?php echo htmlspecialchars($staff['nik']); ?></td>
                <td><?php echo htmlspecialchars($staff['email'] ?? '-'); ?></td>
                <td><?php echo $avgRating; ?>/5</td>
                <td style="color: #ffa600; font-size: 16px;"><?php echo $starsExport; ?> (<?php echo $avgRating; ?>/5)</td>
                <td><?php echo $staff['total_reviews']; ?></td>
            </tr>
            <?php $no++; endforeach; ?>
        </tbody>
    </table>

    <?php
    // Tutup koneksi database
    closeConnection();
    ?>

    <script>
        // Daftar kata-kata tidak pantas
        const badWords = [
            // Bahasa Indonesia
            'fuck', 'shit', 'anjing', 'asu', 'babi', 'kontol', 'bangsat', 'badjingan', 'nyenuk', 'ngentod', 'memek', 'tolol', 'goblok', 'nigger', 'pussy',
            // English
            'fuck', 'shit', 'asshole', 'bitch', 'bastard', 'cunt', 'damn', 'hell', 'piss', 'dick', 'cock', 'pussy', 'nigger', 'faggot', 'slut', 'whore'
        ];

        // Function to check if comment contains bad words
        function containsBadWords(text) {
            const lowerText = text.toLowerCase();
            return badWords.some(word => lowerText.includes(word.toLowerCase()));
        }

        // Export to Excel function
        function exportToExcel() {
            const table = document.getElementById('exportTable');
            const html = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="utf-8">
                    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
                    <x:Name>Data Tenaga Kependidikan</x:Name>
                    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
                    <style>
                        table { border-collapse: collapse; width: 100%; }
                        th { background-color: #073c64; color: white; font-weight: bold; padding: 10px; text-align: center; border: 1px solid #ddd; }
                        td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
                        tr:nth-child(even) { background-color: #f9f9f9; }
                        .star-cell { color: #ffa600; font-size: 14px; }
                    </style>
                </head>
                <body>${table.outerHTML.replace(/style="display:none;"/g, '').replace(/style="display: none;"/g, '')}</body>
                </html>
            `;

            const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const now = new Date();
            const dateStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
            a.href = url;
            a.download = 'Data_Tenaga_Kependidikan_' + dateStr + '.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Star rating functionality
        function initStarRatings() {
            const starContainers = document.querySelectorAll('.star-rating');

            starContainers.forEach(container => {
                const labels = container.querySelectorAll('label');
                const inputs = container.querySelectorAll('input[type="radio"]');

                labels.forEach((label, index) => {
                    label.addEventListener('click', function() {
                        // Remove active class from all labels in this container
                        labels.forEach(l => l.classList.remove('active'));
                        // Add active class to clicked label and previous ones
                        for (let i = 0; i <= index; i++) {
                            labels[i].classList.add('active');
                        }
                        // Set the corresponding radio button as checked
                        inputs[index].checked = true;
                    });

                    // Add hover effect
                    label.addEventListener('mouseenter', function() {
                        // Highlight stars up to this one on hover
                        for (let i = 0; i <= index; i++) {
                            labels[i].classList.add('hover');
                        }
                    });

                    label.addEventListener('mouseleave', function() {
                        // Remove hover effect
                        labels.forEach(l => l.classList.remove('hover'));
                    });
                });

                // Set initial state based on checked radio button
                inputs.forEach((input, index) => {
                    if (input.checked) {
                        for (let i = 0; i <= index; i++) {
                            labels[i].classList.add('active');
                        }
                    }
                });
            });
        }

        // Add event listener to forms
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize star ratings
            initStarRatings();

            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const commentField = form.querySelector('textarea[name="comment"]');
                    if (commentField && containsBadWords(commentField.value)) {
                        alert('Komentar Anda mengandung kata-kata tidak pantas. Ulasan akan dikategorikan sebagai SDM rendah.');
                    }
                });
            });
        });
    </script>
</body>
</html>
