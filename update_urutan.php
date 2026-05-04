<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    $order = $_POST['order'];
    
    // Check if urutan column exists
    $check_col = $conn->query("SHOW COLUMNS FROM tenaga_kependidikan LIKE 'urutan'");
    if ($check_col->num_rows == 0) {
        $conn->query("ALTER TABLE tenaga_kependidikan ADD urutan INT DEFAULT 0;");
    }

    $success = true;
    foreach ($order as $index => $id) {
        $id = intval($id);
        $urutan = intval($index) + 1;
        $sql = "UPDATE tenaga_kependidikan SET urutan = $urutan WHERE id = $id";
        if (!$conn->query($sql)) {
            $success = false;
        }
    }

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Urutan berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan urutan']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
closeConnection();
?>
