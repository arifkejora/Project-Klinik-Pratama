<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_doctor'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_dokter = $_SESSION['login_iddoc'];
    $tanggal = $_POST['date'];
    $waktu_mulai = $_POST['time'];
    $kuota = $_POST['kuota'];
    $status = ($_POST['action'] === 'open') ? 'Aktif' : 'Tidak aktif';

    if ($_POST['action'] === 'open') {
        // Check if the doctor has already opened practice today
        $checkQuery = "SELECT COUNT(*) AS count FROM jadwal_dokter WHERE id_dokter = ? AND tanggal = ?";
        $checkStmt = $conn->prepare($checkQuery);
        if ($checkStmt) {
            $checkStmt->bind_param("is", $id_dokter, $tanggal);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $row = $result->fetch_assoc();
            $checkStmt->close();

            if ($row['count'] > 0) {
                $message = "Anda sudah buka praktik hari ini, silahkan buka praktik kembali besok.";
            } else {
                // Insert into jadwal_dokter
                $query = "INSERT INTO jadwal_dokter (id_dokter, tanggal, waktu_mulai, waktu_selesai, kuota, status) VALUES (?, ?, ?, NULL, ?, ?)";
                $stmt = $conn->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("issss", $id_dokter, $tanggal, $waktu_mulai, $kuota, $status);
                    if ($stmt->execute()) {
                        $message = "Anda berhasil membuka praktik hari ini, semangat dokter.";
                    } else {
                        $message = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $message = "Error: " . $conn->error;
                }
            }
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        // Update jadwal_dokter
        $query = "UPDATE jadwal_dokter SET waktu_selesai = ?, status = ? WHERE id_dokter = ? AND tanggal = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $waktu_selesai = $_POST['time'];
            $stmt->bind_param("ssis", $waktu_selesai, $status, $id_dokter, $tanggal);
            if ($stmt->execute()) {
                $message = "Anda telah menutup praktik hari ini, selamat bekerja kembali besok.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetching the history of practice schedules
$id_dokter = $_SESSION['login_iddoc'];
$historyQuery = "SELECT tanggal, waktu_mulai, waktu_selesai, kuota FROM jadwal_dokter WHERE id_dokter = ? ORDER BY tanggal DESC";
$historyStmt = $conn->prepare($historyQuery);
$historyData = [];

if ($historyStmt) {
    $historyStmt->bind_param("i", $id_dokter);
    $historyStmt->execute();
    $result = $historyStmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $historyData[] = $row;
    }
    $historyStmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Praktik Dokter</title>
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
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_doctor']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6><?php echo $_SESSION['login_doctor']; ?></h6>
              <span>Admin</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
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
        <a class="nav-link collapsed" href="doctor-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="doctor-praktik.php">
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
        <a class="nav-link collapsed" href="doctor-history.php">
          <i class="bi bi-journal-text"></i>
          <span>Riwayat Praktik</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Praktik</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Dokter</a></li>
          <li class="breadcrumb-item active">Praktik</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Buka Praktik Dokter</h5>
              <?php if ($message): ?>
            <div class="alert alert-info mt-3">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form class="row g-3" action="doctor-praktik.php" method="POST">
            <div class="row">
                <div class="col-md-4">
                    <label for="inputDate" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="inputDate" name="date" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputTime" class="form-label">Jam</label>
                    <input type="time" class="form-control" id="inputTime" name="time" readonly>
                </div>
                <div class="col-md-4">
                    <label for="inputKuota" class="form-label">Kuota per Hari</label>
                    <input type="number" class="form-control" id="inputKuota" name="kuota">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" name="action" value="open" class="btn btn-primary">Buka Praktik</button>
                <button type="submit" name="action" value="close" class="btn btn-secondary">Tutup Praktik</button>
            </div>
        </form>
            </div>
          </div>
          
          <div class="card mt-5">
            <div class="card-body">
                <h5 class="card-title">Riwayat Jadwal Praktik</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Waktu Mulai</th>
                            <th scope="col">Waktu Selesai</th>
                            <th scope="col">Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($historyData) > 0): ?>
                            <?php foreach ($historyData as $row): ?>
                                <tr>
                                    <td><?php echo $row['tanggal']; ?></td>
                                    <td><?php echo $row['waktu_mulai']; ?></td>
                                    <td><?php echo $row['waktu_selesai'] ?? 'N/A'; ?></td>
                                    <td><?php echo $row['kuota']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No data found</td></tr>
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