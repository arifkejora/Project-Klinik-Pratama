<?php
session_start();
include('db_connection.php'); 

// Cek jika user belum login, redirect ke halaman login jika belum.
if (!isset($_SESSION['login_idfarmaddoc'])) {
    header("location: pages-login-farmasi.php");
    exit;
}

// Inisialisasi pesan sukses dan error
$success_message = '';
$error_message = '';

// Proses form saat submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $jenis_obat = mysqli_real_escape_string($conn, $_POST['jenis_obat']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok_obat']);
    $harga_obat = mysqli_real_escape_string($conn, $_POST['harga_obat']);

    // Query untuk insert ke tabel obat
    $sql = "INSERT INTO obat (nama_obat, jenis_obat, stok, harga_obat) VALUES ('$nama_obat', '$jenis_obat', '$stok', '$harga_obat')";

    if (mysqli_query($conn, $sql)) {
        // Jika insert berhasil, set pesan sukses
        $success_message = "Obat berhasil ditambahkan.";
    } else {
        // Jika terjadi error, set pesan error
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Ambil data jenis obat
$jenis_obat_options = [
  "Tablet", "Kapsul", "Sirup", "Suntik", "Salep", "Gel", "Krim", "Patch", "Inhaler",
  "Suppositoria", "Suspensi", "Larutan", "Tetes Mata", "Tetes Hidung", "Tetes Telinga",
  "Emulsi", "Bubuk", "Granul", "Effervescent", "Lozenges", "Strip", "Plester", "Lainnya"
];


$sql = "SELECT * FROM obat";
$result = mysqli_query($conn, $sql);
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

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="farmasi-dashboard.php" class="logo d-flex align-items-center">
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
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_farmasi']; ?></span>
          </a><!-- End Profile Iamge Icon -->

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
          
          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="farmasi-dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="farmasi-obat.php">
          <i class="bi bi-person"></i>
          <span>Obat</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="farmasi-riwayat.php">
          <i class="bi bi-person"></i>
          <span>Riwayat Obat</span>
        </a>
      </li>
    
    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Obat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="farmasi-dashboard.php">Beranda</a></li>
          <li class="breadcrumb-item active">Obat</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Tambah Data Obat</h5>
              <?php if (isset($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
              <?php endif; ?>
              <form class="row g-3" action="farmasi-obat.php" method="POST">
                    <div class="col-md-3">
                        <label for="inputNamaObat" class="form-label">Nama Obat</label>
                        <input type="text" class="form-control" id="inputNamaObat" name="nama_obat" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inputJenisObat" class="form-label">Jenis Obat</label>
                        <select class="form-control" id="inputJenisObat" name="jenis_obat" required>
                            <option value="">Pilih Jenis Obat</option>
                            <?php foreach ($jenis_obat_options as $jenis): ?>
                                <option value="<?php echo $jenis; ?>"><?php echo $jenis; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="inputStokObat" class="form-label">Stok Obat</label>
                        <input type="number" class="form-control" id="inputStokObat" name="stok_obat" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inputHargaObat" class="form-label">Harga Obat</label>
                        <input type="text" class="form-control" id="inputHargaObat" name="harga_obat" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>

            </div>
          </div>

          <div class="card mt-5">
            <div class="card-body">
                <h5 class="card-title">Daftar Persediaan Obat</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Nama Obat</th>
                            <th scope="col">Jenis Obat</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nama_obat']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['jenis_obat']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['stok']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['harga_obat']) . "</td>";
                                echo "<td>
                                        <a href='edit_obat.php?id_obat=" . $row['id_obat'] . "' class='btn btn-primary'><i class='bi bi-pencil me-1'></i> Edit</a>
                                        <a href='delete_obat.php?id_obat=" . $row['id_obat'] . "' class='btn btn-danger'><i class='bi bi-trash me-1'></i> Hapus</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No data found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
      </div>
    </section>

 

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