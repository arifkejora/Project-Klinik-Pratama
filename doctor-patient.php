<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_doctor'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$id_dokter = $_SESSION['login_iddoc'];

// Fetch the latest id_jadwal for the logged-in doctor
$jadwalQuery = "SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = ? ORDER BY tanggal DESC LIMIT 1";
$jadwalStmt = $conn->prepare($jadwalQuery);
$jadwalStmt->bind_param("i", $id_dokter);
$jadwalStmt->execute();
$jadwalResult = $jadwalStmt->get_result();
$jadwalRow = $jadwalResult->fetch_assoc();
$id_jadwal = $jadwalRow['id_jadwal'];
$jadwalStmt->close();

// Fetch the patient queue data
$queueData = [];
if (!empty($id_jadwal)) {
    $queueQuery = "
        SELECT 
        a.id_antrian, 
        p.nama_pasien, 
        a.antrian, 
        a.status_antrian 
        FROM antrian a 
        JOIN pasien p ON a.id_pasien = p.id_pasien 
        JOIN jadwal_dokter jd ON jd.id_jadwal = a.id_jadwal 
        WHERE jd.id_dokter = ? AND a.status_antrian = 'Menunggu' OR a.status_antrian = 'Sedang Diperiksa'
        ORDER BY a.antrian ASC;";
    $queueStmt = $conn->prepare($queueQuery);
    $queueStmt->bind_param("i", $id_dokter);
    $queueStmt->execute();
    $queueResult = $queueStmt->get_result();

    while ($row = $queueResult->fetch_assoc()) {
        $queueData[] = $row;
    }

    $queueStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id_antrian'])) {
        $action = $_POST['action'];
        $id_antrian = $_POST['id_antrian'];

        if ($action === 'tolak') {
            // Update status antrian menjadi 'Ditolak'
            $updateQuery = "UPDATE antrian SET status_antrian = 'Ditolak' WHERE id_antrian = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $id_antrian);

            if ($updateStmt->execute()) {
                // Redirect back to the queue page after update
                header("location: {$_SERVER['PHP_SELF']}");
                exit;
            } else {
                echo "Gagal melakukan update status antrian.";
            }

            $updateStmt->close();
        } elseif ($action === 'periksa') {
            // Check if the status is 'Sedang Diperiksa'
            $statusQuery = "SELECT status_antrian FROM antrian WHERE id_antrian = ?";
            $statusStmt = $conn->prepare($statusQuery);
            $statusStmt->bind_param("i", $id_antrian);
            $statusStmt->execute();
            $statusResult = $statusStmt->get_result();
            $statusRow = $statusResult->fetch_assoc();
            $status = $statusRow['status_antrian'];

            $statusStmt->close();

            if ($status === 'Sedang Diperiksa') {
                // Redirect to doctor-periksa.php if status is 'Sedang Diperiksa'
                header("location: doctor-periksa.php");
                exit;
            } else {
                // Update status antrian menjadi 'Sedang Diperiksa'
                $updateQuery = "UPDATE antrian SET status_antrian = 'Sedang Diperiksa' WHERE id_antrian = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("i", $id_antrian);

                if ($updateStmt->execute()) {
                    // Redirect to doctor-periksa.php after update
                    header("location: doctor-periksa.php");
                    exit;
                } else {
                    echo "Gagal melakukan update status antrian.";
                }

                $updateStmt->close();
            }
        }
    }
}

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
        <a class="nav-link " href="doctor-patient.php">
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
          <li class="breadcrumb-item active">Pasien</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title">Antrian Pasien Hari Ini</h5>
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Nama Pasien</th>
                    <th scope="col">Nomor Antrian</th>
                    <th scope="col">Status Antrian</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($queueData)): ?>
                    <?php foreach ($queueData as $row): ?>
                        <tr>
                            <td><?php echo $row['nama_pasien']; ?></td>
                            <td><?php echo $row['antrian']; ?></td>
                            <td><?php echo $row['status_antrian']; ?></td>
                            <td>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <input type="hidden" name="id_antrian" value="<?php echo htmlspecialchars($row['id_antrian']); ?>">
                                <?php if ($row['status_antrian'] === 'Menunggu'): ?>
                                    <a href="doctor-periksa.php?id_antrian=<?php echo htmlspecialchars($row['id_antrian']); ?>" class="btn btn-success">Periksa Pasien</a>
                                <?php elseif ($row['status_antrian'] === 'Sedang Diperiksa'): ?>
                                    <a href="doctor-periksa.php?id_antrian=<?php echo htmlspecialchars($row['id_antrian']); ?>" class="btn btn-success">Sedang Diperiksa</a>
                                <?php endif; ?>
                                <button type="submit" name="action" value="tolak" class="btn btn-danger">Tolak</button>
                            </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">Tidak ada data antrian</td></tr>
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