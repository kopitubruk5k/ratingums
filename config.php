<?php
// Konfigurasi koneksi database untuk RatingUMS

// Pengaturan database
$host = 'localhost'; // Host database (biasanya localhost untuk XAMPP)
$user = 'root'; // Username database (default root untuk XAMPP)
$password = ''; // Password database (kosong untuk XAMPP default)
$database = 'ratingums'; // Nama database

// Matikan pelemparan error otomatis (Exception) dari MySQLi di versi PHP terbaru (agar tidak memunculkan Error 500 yang membingungkan)
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("<h3>🚨 Masalah Database!</h3>Terdapat kesalahan pada pengaturan Host, Username, Password, atau Nama Database Anda.<br><b>Detail Error:</b> " . $conn->connect_error . "<br><br><i>Silakan edit kembali file config.php Anda dengan data yang benar dari InfinityFree.</i>");
}

$conn->set_charset("utf8");


function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}
?>
