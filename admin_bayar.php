<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

$message = '';
$total_obat = 0;
$harga_perkunjungan = 0;
$id_rekam_medis = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['generate_payment'])) {
        if (isset($_POST['id_rekam_medis'])) {
            $id_rekam_medis = $_POST['id_rekam_medis'];

            $sql = "SELECT SUM(obat.harga_obat) AS total_obat, dokter.harga_perkunjungan
                    FROM resep_obat
                    INNER JOIN obat ON resep_obat.id_obat = obat.id_obat
                    INNER JOIN rekam_medis ON resep_obat.id_rekammedis = rekam_medis.id_rekam_medis
                    INNER JOIN antrian ON rekam_medis.id_antrian = antrian.id_antrian
                    INNER JOIN jadwal_dokter ON antrian.id_jadwal = jadwal_dokter.id_jadwal
                    INNER JOIN dokter ON jadwal_dokter.id_dokter = dokter.id_dokter
                    WHERE resep_obat.id_rekammedis = $id_rekam_medis";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_obat = $row['total_obat'];
                $harga_perkunjungan = $row['harga_perkunjungan'];
                $total_biaya = $total_obat + $harga_perkunjungan;
            } else {
                $message = "Error: Tidak dapat menghitung total biaya.";
            }
        } else {
            $message = "Error: Data tidak lengkap.";
        }
    }

    if (isset($_POST['process_payment'])) {
        if (isset($_POST['id_rekam_medis'])) {
            $id_rekam_medis = $_POST['id_rekam_medis'];
            $total_biaya = $_POST['total_biaya'];

            // Update status_pembayaran to 'Lunas' and set pembayaran to total_biaya
            $update_sql = "UPDATE rekam_medis SET pembayaran = $total_biaya, status_pembayaran = 'Lunas' WHERE id_rekam_medis = $id_rekam_medis";

            if ($conn->query($update_sql) === TRUE) {
                $message = "Pembayaran berhasil.";
            } else {
                $message = "Error: " . $conn->error;
            }

            $sql = "SELECT SUM(obat.harga_obat) AS total_obat, dokter.harga_perkunjungan
                    FROM resep_obat
                    INNER JOIN obat ON resep_obat.id_obat = obat.id_obat
                    INNER JOIN rekam_medis ON resep_obat.id_rekammedis = rekam_medis.id_rekam_medis
                    INNER JOIN antrian ON rekam_medis.id_antrian = antrian.id_antrian
                    INNER JOIN jadwal_dokter ON antrian.id_jadwal = jadwal_dokter.id_jadwal
                    INNER JOIN dokter ON jadwal_dokter.id_dokter = dokter.id_dokter
                    WHERE resep_obat.id_rekammedis = $id_rekam_medis";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_obat = $row['total_obat'];
                $harga_perkunjungan = $row['harga_perkunjungan'];
                $total_biaya = $total_obat + $harga_perkunjungan;
            } else {
                $message = "Error: Tidak dapat mengambil rincian pembayaran setelah pembayaran diproses.";
            }
        } else {
            $message = "Error: Data tidak lengkap.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard Admin</title>
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
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_user']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['login_user']; ?></h6>
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
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_farmasi.php">
          <i class="bi bi-person"></i>
          <span>Farmasi</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_dokter.php">
          <i class="bi bi-person"></i>
          <span>Dokter</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_rujukan.php">
          <i class="bi bi-journal-text"></i>
          <span>Rujukan</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="admin_bayar.php">
          <i class="bi bi-journal-text"></i>
          <span>Pembayaran</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_rating.php">
          <i class="bi bi-bar-chart"></i>
          <span>Rating</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_pasien.php">
          <i class="bi bi-bar-chart"></i>
          <span>Pasien</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="admin_crm.php">
          <i class="bi bi-bar-chart"></i>
          <span>Broadcast</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Farmasi</h1>
      <nav>
        <ol class="breadcrumb">
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Pembayaran</h5>
              <?php if (!empty($message)): ?>
              <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
              </div>
              <?php endif; ?>
              <form class="row g-3" action="admin_bayar.php" method="POST">
                <div class="col-md-12">
                  <label for="inputNoRekamMedis" class="form-label">No Rekam Medis</label>
                  <select class="form-select" id="inputNoRekamMedis" name="id_rekam_medis" required>
                    <option selected disabled>-- Pilih No Rekam Medis --</option>
                    <?php
                    $sql_rekam_medis = "SELECT id_rekam_medis FROM rekam_medis WHERE status_pembayaran = 'Belum Lunas' ";
                    $result_rekam_medis = $conn->query($sql_rekam_medis);
                    if ($result_rekam_medis->num_rows > 0) {
                      while ($row_rekam_medis = $result_rekam_medis->fetch_assoc()) {
                        echo "<option value='" . $row_rekam_medis['id_rekam_medis'] . "'>RM00" . $row_rekam_medis['id_rekam_medis'] . "</option>";                      }
                    } else {
                      echo "<option value='' disabled>Tidak ada data</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary" name="generate_payment">Generate Pembayaran</button>
                </div>
              </form>
              <?php if (isset($total_obat, $harga_perkunjungan, $total_biaya)): ?>
              <div class="mt-4">
                <h5>Rincian Biaya:</h5>
                <p>Biaya Dokter: Rp. <?php echo number_format($harga_perkunjungan, 0, ',', '.'); ?></p>
                <p>Total Harga Obat: Rp. <?php echo number_format($total_obat, 0, ',', '.'); ?></p>
                <p>Total Biaya: Rp. <?php echo number_format($total_biaya, 0, ',', '.'); ?></p>
<form action="generate_receipt.php" method="POST" target="_blank">
                                    <input type="hidden" name="id_rekam_medis" value="<?php echo $id_rekam_medis; ?>">
                                    <input type="hidden" name="total_biaya" value="<?php echo $total_biaya; ?>">
                                    <button type="submit" class="btn btn-success" name="process_payment">Bayar</button>
                                    <a href="generate_receipt.php?id_rekam_medis=<?php echo $id_rekam_medis; ?>" class="btn btn-info" target="_blank">Cetak Pembayaran</a>
                                </form>
              </div>
              <?php endif; ?>
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
</body>

</html>

