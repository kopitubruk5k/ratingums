<?php
include 'config.php';

// Function to display stars
function displayStarsText($rating) {
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

// Query data tenaga kependidikan dengan rata-rata rating
$query = "
    SELECT tk.*, AVG(u.rating) as avg_rating, COUNT(u.id) as total_reviews
    FROM tenaga_kependidikan tk
    LEFT JOIN ulasan u ON tk.id = u.tenaga_id
    GROUP BY tk.id
    ORDER BY tk.id ASC
";

$result = $conn->query($query);

// Set headers for CSV download
$filename = 'Data_Tenaga_Kependidikan_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row
fputcsv($output, ['No', 'Nama', 'Jabatan', 'NIK', 'Email', 'Rating', 'Bintang', 'Total Ulasan']);

// Write data rows
$no = 1;
while ($row = $result->fetch_assoc()) {
    $avgRating = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
    $roundedRating = round($avgRating);
    $stars = displayStarsText($roundedRating);
    $ratingText = $avgRating . '/5';

    fputcsv($output, [
        $no,
        $row['nama'],
        $row['jabatan'],
        $row['nik'],
        $row['email'] ?? '-',
        $ratingText,
        $stars . ' (' . $ratingText . ')',
        $row['total_reviews']
    ]);
    $no++;
}

fclose($output);
closeConnection();
exit();
?>
