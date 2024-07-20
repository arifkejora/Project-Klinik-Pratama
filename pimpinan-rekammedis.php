<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_pimpinan'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$sql = "SELECT COUNT(*) AS total_medicine FROM obat";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$total_medicine = $row['total_medicine'];

$sql = "SELECT COUNT(*) AS total_pharmachy FROM farmasi";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$total_pharmachy = $row['total_pharmachy'];

$sql = "SELECT COUNT(*) AS total_doctor FROM dokter";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$total_doctor = $row['total_doctor'];

$sql = "SELECT COUNT(*) AS total_patient FROM pasien";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$total_patient = $row['total_patient'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard Pimpinan</title>
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

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Pratama</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_pimpinan']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6><?php echo $_SESSION['login_pimpinan']; ?></h6>
              <span>Pimpinan Klinik</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout-doctor.php">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
              </a>
            </li>
          
          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-doctor.php">
          <i class="bi bi-person-badge"></i>
          <span>Laporan Dokter</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-farmasi.php">
          <i class="bi bi-hospital"></i>
          <span>Laporan Farmasi</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-pasien.php">
          <i class="bi bi-file-medical"></i>
          <span>Laporan Pasien</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-obat.php">
          <i class="bi bi-capsule"></i>
          <span>Laporan Obat</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="pimpinan-rekammedis.php">
          <i class="bi bi-clipboard-data"></i>
          <span>Laporan Rekam Medis</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pimpinan-rating.php">
          <i class="bi bi-star"></i>
          <span>Laporan Rating</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Beranda</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Laporan Rekam Medis</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">ID Rekam Medis</th>
                <th scope="col">ID Antrian</th>
                <th scope="col">Keluhan</th>
                <th scope="col">Diagnosa</th>
                <th scope="col">Tekanan Darah S</th>
                <th scope="col">Tekanan Darah D</th>
                <th scope="col">Berat Badan</th>
                <th scope="col">Suhu Badan</th>
                <th scope="col">Hasil Pemeriksaan</th>
                <th scope="col">Pembayaran</th>
                <th scope="col">Status Pembayaran</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT id_rekam_medis, id_antrian, keluhan, diagnosa, tekanan_darah_s, tekanan_darah_d, berat_badan, suhu_badan, hasil_pemeriksaan, pembayaran, status_pembayaran FROM rekam_medis";
              $result = $conn->query($sql);
              if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $row['id_rekam_medis']; ?></td>
                    <td><?php echo $row['id_antrian']; ?></td>
                    <td><?php echo $row['keluhan']; ?></td>
                    <td><?php echo $row['diagnosa']; ?></td>
                    <td><?php echo $row['tekanan_darah_s']; ?></td>
                    <td><?php echo $row['tekanan_darah_d']; ?></td>
                    <td><?php echo $row['berat_badan']; ?></td>
                    <td><?php echo $row['suhu_badan']; ?></td>
                    <td><?php echo $row['hasil_pemeriksaan']; ?></td>
                    <td><?php echo $row['pembayaran']; ?></td>
                    <td><?php echo $row['status_pembayaran']; ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="10" class="text-center">Tidak ada data rekam medis</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          <a href="generatepdf_rekammedis.php?type=rekammedis" target="_blank" class="btn btn-primary">Cetak PDF Rekam Medis</a>
        </div>
      </div>
    </div>
  </div>
</section>


  </main><!-- End #main -->

  <!-- ======= Footer ======= -->

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Klinik Pratama Anugrah Hexa</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Artadevnymous</a>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>