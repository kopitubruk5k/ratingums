<?php
include 'config.php';

// Query untuk mengambil semua tenaga kependidikan beserta rata-rata rating
$check_col = $conn->query("SHOW COLUMNS FROM tenaga_kependidikan LIKE 'urutan'");
$order_by = ($check_col && $check_col->num_rows > 0) ? "tk.urutan ASC, tk.id ASC" : "tk.id ASC";

$query = "
    SELECT tk.*,
           COALESCE(AVG(u.rating), 0) as avg_rating,
           COUNT(u.id) as total_ulasan
    FROM tenaga_kependidikan tk
    LEFT JOIN ulasan u ON u.tenaga_id = tk.id
    GROUP BY tk.id
    ORDER BY $order_by
";
$result = $conn->query($query);

function displayStars($rating) {
    $rounded = round($rating);
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= $rounded)
            ? '<span class="star-filled">★</span>'
            : '<span class="star-empty">☆</span>';
    }
    return $stars;
}
?>
<!DOCTYPE html>
<html lang="id-ID">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Tenaga Kependidikan – Fakultas Keguruan dan Ilmu Pendidikan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    font-size:15px;
    line-height:1.6;
    color:#3a3a3a;
    background:#fff;
    min-height:100vh;
}

/* Animated Headline - Elementor Style */
.header-section{
    text-align:center;
    padding:50px 20px 30px;
    background:#fff;
}
.elementor-headline{
    font-family:'Poppins', Sans-serif;
    font-size:3rem;
    font-weight:700;
    color:#0A1AFF;
    margin:0;
    line-height:1.4em;
    letter-spacing:0.02em;
}
.elementor-headline-plain-text{
    color:#073c64;
}
.elementor-headline-dynamic-wrapper{
    position:relative;
    display:inline-block;
    vertical-align:baseline;
}
.elementor-headline-dynamic-text{
    color:#073c64;
}
.elementor-headline-dynamic-wrapper svg{
    position:absolute;
    bottom:-8px;
    left:0;
    width:100%;
    height:10px;
    overflow:visible;
    pointer-events:none;
}
.elementor-headline-dynamic-wrapper svg line{
    stroke:#0170B9;
    stroke-width:4;
    stroke-linecap:round;
    stroke-dasharray:1000;
    stroke-dashoffset:1000;
    animation:headline-draw 1.2s ease-out forwards;
}
@keyframes headline-draw{
    to{stroke-dashoffset:0;}
}
/* Loop animation setiap 8 detik */
.elementor-headline.e-animated .elementor-headline-dynamic-wrapper svg line{
    animation:headline-draw 1.2s ease-out forwards, headline-loop 8s ease-in-out 1.2s infinite;
}
@keyframes headline-loop{
    0%,90%{stroke-dashoffset:0;}
    95%{stroke-dashoffset:3000;}
    100%{stroke-dashoffset:0;}
}

/* Container */
.container{
    max-width:1100px;
    margin:0 auto;
    padding:0 20px 40px;
}

/* Staff Grid */
.staff-grid{
    display:grid;
    grid-template-columns:repeat(2, 1fr);
    gap:20px;
}

/* Staff Card */
.staff-card{
    background:#fff;
    border-radius:15px;
    padding:20px;
    box-shadow:0 8px 32px rgba(0,0,0,0.1);
    transition:transform 0.3s ease,box-shadow 0.3s ease;
    display:flex;
    flex-direction:column;
}
.staff-card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 40px rgba(0,0,0,0.15);
}
.staff-header{
    border-bottom:2px solid #667eea;
    margin-bottom:12px;
    padding-bottom:8px;
}
.staff-jabatan{
    font-size:1.15em;
    font-weight:700;
    color:#2c3e50;
}
.staff-body{
    display:flex;
    gap:15px;
    flex:1;
}
.staff-avatar{
    width:110px;
    height:110px;
    border-radius:0;
    background:#e0e0e0;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-weight:600;
    font-size:28px;
    overflow:hidden;
    flex-shrink:0;
    align-self:flex-start;
    border:none;
}
.staff-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.staff-right{
    flex:1;
    display:flex;
    flex-direction:column;
}
.staff-info{
    flex:1;
    color:#555;
    line-height:1.7;
}
.staff-name-container{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
    margin-bottom:5px;
}
.staff-info strong{
    color:#2c3e50;
    font-size:1.1em;
}
.btn-ulasan{
    display:inline-block;
    padding:6px 12px;
    background:#073c64;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-size:0.85em;
    font-weight:600;
    white-space:nowrap;
    transition:transform 0.2s ease,box-shadow 0.2s ease;
}
.btn-ulasan:hover{
    background:#0a4f85;
    transform:translateY(-2px);
    box-shadow:0 5px 15px rgba(7,60,100,0.3);
}
.staff-rating{
    margin-top:8px;
    display:flex;
    align-items:center;
    gap:6px;
}
.star-filled{
    color:#ffa600;
    font-size:1.1em;
}
.star-empty{
    color:#ddd;
    font-size:1.1em;
}
.rating-text{
    font-size:0.82em;
    color:#888;
}

@media(max-width:768px){
    .staff-grid{grid-template-columns:1fr;}
}
@media(max-width:600px){
    .elementor-headline{font-size:2rem;font-weight:700;}
    .header-section{padding:30px 15px 20px;}
    .container{padding:0 15px 30px;}
    .staff-card{padding:20px;}
    .staff-name{font-size:1.1em;}
    .staff-avatar{width:50px;height:50px;font-size:16px;}
}
</style>
</head>
<body>

<section class="header-section">
    <h3 class="elementor-headline e-animated">
        <span class="elementor-headline-dynamic-wrapper">
            <span class="elementor-headline-plain-text">DAFTAR </span><span class="elementor-headline-dynamic-text">TENAGA KEPENDIDIKAN</span><span class="elementor-headline-plain-text"> FKIP</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 10" preserveAspectRatio="none" aria-hidden="true">
                <line x1="0" y1="5" x2="1000" y2="5"></line>
            </svg>
        </span>
    </h3>
</section>

<div class="container">
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="staff-grid">
        <?php while ($staff = $result->fetch_assoc()): ?>
            <?php 
            $foto_src = !empty($staff['foto']) ? htmlspecialchars($staff['foto']) : 'img/' . $staff['id'] . '.jpg';
            $initials = strtoupper(substr($staff['nama'], 0, 1));
            ?>
            <div class="staff-card">
                <div class="staff-header">
                    <div class="staff-jabatan"><?php echo htmlspecialchars($staff['jabatan']); ?></div>
                </div>
                <div class="staff-body">
                    <div class="staff-avatar" data-initials="<?php echo $initials; ?>">
                        <img src="<?php echo $foto_src; ?>" alt="Foto <?php echo htmlspecialchars($staff['nama']); ?>" loading="lazy" onerror="this.style.display='none';this.parentElement.innerHTML='<?php echo $initials; ?>';">
                    </div>
                    <div class="staff-right">
                        <div class="staff-info">
                            <div class="staff-name-container">
                                <strong><?php echo htmlspecialchars($staff['nama']); ?></strong>
                                <a href="ulasanratingtenaga.php?id=<?php echo $staff['id']; ?>" class="btn-ulasan">⭐ Ulasan</a>
                            </div>
                            NIK: <?php echo htmlspecialchars($staff['nik']); ?><br>
                            <?php if (!empty($staff['email'])): ?>
                                <?php echo htmlspecialchars($staff['email']); ?>
                            <?php endif; ?>
                            <div class="staff-rating">
                                <?php echo displayStars($staff['avg_rating']); ?>
                                <span class="rating-text">
                                    <?php if ($staff['total_ulasan'] > 0): ?>
                                        <?php echo number_format($staff['avg_rating'], 1); ?>/5
                                        (<?php echo $staff['total_ulasan']; ?> ulasan)
                                    <?php else: ?>
                                        Belum ada ulasan
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="staff-card">
            <p style="text-align:center;">Tidak ada data tenaga kependidikan.</p>
        </div>
    <?php endif; ?>
</div>

<?php closeConnection(); ?>

<?php include 'footer.php'; ?>
</body>
</html>
