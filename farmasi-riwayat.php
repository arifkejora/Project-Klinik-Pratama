<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['login_idfarmaddoc'])) {
    header("location: pages-login-farmasi.php");
    exit;
}

// Jika tombol "Terima" ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'lihat') {
    // Ambil id_rekam_medis dari form
    $id_rekam_medis = mysqli_real_escape_string($conn, $_POST['id_rekam_medis']);

    // Redirect ke halaman edit_statusobat.php dengan membawa id_rekam_medis
    header("Location: edit_statusobat.php?id_rekam_medis=$id_rekam_medis");
    exit;
}

$sql = "SELECT jd.tanggal, p.nama_pasien, a.id_antrian, rm.id_rekam_medis, MIN(ro.status) AS status_obat
        FROM rekam_medis rm
        INNER JOIN antrian a ON rm.id_antrian = a.id_antrian
        INNER JOIN jadwal_dokter jd ON a.id_jadwal = jd.id_jadwal
        INNER JOIN pasien p ON a.id_pasien = p.id_pasien
        LEFT JOIN resep_obat ro ON rm.id_rekam_medis = ro.id_rekammedis
        GROUP BY rm.id_rekam_medis";

$result = mysqli_query($conn, $sql);
$queueData = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Query untuk mengambil data rekam medis
$avg_sql = "SELECT AVG(rate_farmasi) AS avg_rating FROM rekam_medis";
$avg_result = mysqli_query($conn, $avg_sql);
$avg_row = mysqli_fetch_assoc($avg_result);
$avg_rating = round($avg_row['avg_rating'], 1);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard Farmasi</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="farmasi-dashboard.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Pratama</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_farmasi']; ?></span>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6><?php echo $_SESSION['login_farmasi']; ?></h6>
              <span>Admin</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout-farmasi.php">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="farmasi-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="farmasi-obat.php">
          <i class="bi bi-person"></i>
          <span>Obat</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="farmasi-riwayat.php">
          <i class="bi bi-person"></i>
          <span>Riwayat Obat</span>
        </a>
      </li>
    
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Riwayat Obat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="farmasi-dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item active">Riwayat Obat</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">Antrian Obat Pasien Hari Ini</h5>

            <div class="average-rating">
                <h3>Rating</h3>
                <h1><?php echo $avg_rating; ?></h1>
                <p>
                  <?php
                  for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $avg_rating) {
                      echo '<i class="bi bi-star-fill text-warning"></i>';
                    } elseif ($i - 0.5 <= $avg_rating) {
                      echo '<i class="bi bi-star-half text-warning"></i>';
                    } else {
                      echo '<i class="bi bi-star-fill text-secondary"></i>';
                    }
                  }
                  ?>
                </p>
              </div>
              
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Tanggal</th>       
                    <th scope="col">Nama Pasien</th>
                    <th scope="col">Nomor Rekam Medis</th>
                    <th scope="col">Status Obat</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php if (!empty($queueData)): ?>
        <?php foreach ($queueData as $row): ?>
            <tr>
                <td><?php echo $row['tanggal']; ?></td>
                <td><?php echo $row['nama_pasien']; ?></td>
                <td><?php echo $row['id_rekam_medis']; ?></td>
                <td><?php echo $row['status_obat']; ?></td>
                <td>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <input type="hidden" name="id_rekam_medis" value="<?php echo htmlspecialchars($row['id_rekam_medis']); ?>">
                        <?php if ($row['status_obat'] == 'Antrian'): ?>
                            <button type="submit" name="action" value="lihat" class="btn btn-primary">Terima</button>
                        <?php elseif ($row['status_obat'] == 'Sedang Diracik'): ?>
                            <button type="submit" name="action" value="lihat" class="btn btn-warning">Lanjutkan Peracikan</button>
                        <?php elseif ($row['status_obat'] == 'Selesai'): ?>
                            <button type="submit" name="action" value="lihat" class="btn btn-info">Lihat Resep</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">Tidak ada data riwayat obat</td>
        </tr>
    <?php endif; ?>
</tbody>

        </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Klinik Pratama Anugrah Hexa</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Artadevnymous</a>
    </div>
  </footer>  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>

<?php

mysqli_close($conn);
?>
