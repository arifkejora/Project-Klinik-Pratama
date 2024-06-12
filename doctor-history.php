<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_doctor'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$id_dokter = $_SESSION['login_iddoc'];

$query = "
    SELECT 
        a.id_antrian, 
        j.tanggal, 
        CONCAT('RM00', rm.id_rekam_medis) as nomor_rekam_medis,
        p.nama_pasien, 
        a.status_antrian 
    FROM antrian a
    JOIN jadwal_dokter j ON a.id_jadwal = j.id_jadwal
    JOIN pasien p ON a.id_pasien = p.id_pasien
    LEFT JOIN rekam_medis rm ON a.id_antrian = rm.id_antrian
    WHERE j.id_dokter = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_dokter); // Corrected bind_param call
$stmt->execute();
$result = $stmt->get_result();

$queueData = array();
while ($row = $result->fetch_assoc()) {
    $queueData[] = $row;
}
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pasien Dokter</title>
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
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Pratama</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_doctor']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6><?php echo $_SESSION['login_doctor']; ?></h6>
              <span>Admin</span>
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
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="doctor-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="doctor-praktik.php">
          <i class="bi bi-person"></i>
          <span>Praktik</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="doctor-patient.php">
          <i class="bi bi-person"></i>
          <span>Pasien</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link " href="doctor-history.php">
          <i class="bi bi-journal-text"></i>
          <span>Riwayat Praktik</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Riwayat Pemeriksaan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Dokter</a></li>
          <li class="breadcrumb-item active">Riwayat Pemeriksaan</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">Riwayat Pemeriksaan Pasien</h5>

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">Tanggal</th>
            <th scope="col">Nomor Rekam Medis</th>
            <th scope="col">Nama Pasien</th>
            <th scope="col">Status Antrian</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($queueData)): ?>
            <?php foreach ($queueData as $row): ?>
                <tr>
                    <td><?php echo $row['tanggal']; ?></td>
                    <td><?php echo $row['nomor_rekam_medis']; ?></td>
                    <td><?php echo $row['nama_pasien']; ?></td>
                    <td><?php echo $row['status_antrian']; ?></td>
                    <td>
                        <a href="doctor-detail-history.php?id_rekam_medis=<?php echo htmlspecialchars(substr($row['nomor_rekam_medis'], 4)); ?>" class="btn btn-success">Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">Tidak ada data antrian</td></tr>
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
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('inputDate').value = today;

        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const currentTime = `${hours}:${minutes}`;
        document.getElementById('inputTime').value = currentTime;
    });
</script>
</body>

</html>