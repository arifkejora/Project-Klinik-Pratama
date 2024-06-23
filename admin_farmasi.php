<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

// Insert Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nip = mysqli_real_escape_string($conn, $_POST['nip']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $startdate = mysqli_real_escape_string($conn, $_POST['startdate']);

  // Hashing the password
  $password = md5($password);

  $sql = "INSERT INTO farmasi (NIP, nama_farmasi, mulai_bekerja, email_farmasi, katasandi_farmasi, alamat_farmasi) 
          VALUES ('$nip', '$name', '$startdate', '$email', '$password', '$address')";

  if (mysqli_query($conn, $sql)) {
      $message = "Data berhasil disimpan!";
  } else {
      $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
}

// View Data
$sql = "SELECT id_farmasi, NIP, nama_farmasi, email_farmasi, mulai_bekerja FROM farmasi";
$result = mysqli_query($conn, $sql);

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
        <a class="nav-link" href="admin_farmasi.php">
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
        <a class="nav-link collapsed" href="admin_bayar.php">
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
              <h5 class="card-title">Tambah Anggota Farmasi</h5>
              <?php if (isset($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
              <?php endif; ?>
              <form class="row g-3" action="admin_farmasi.php" method="POST">
              <div class="col-md-12">
                        <label for="inputNip5" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="inputNip5" name="nip" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="inputName5" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputEmail5" class="form-label">Email</label>
                        <input type="email" class="form-control" id="inputEmail5" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputPassword5" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="inputPassword5" name="password" required>
                    </div>
                    <div class="col-12">
                        <label for="inputAddress5" class="form-label">Alamat Lengkap</label>
                        <input type="text" class="form-control" id="inputAddress5" name="address" placeholder="Jl. Kencana" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputStartDate" class="form-label">Tanggal Mulai Bekerja</label>
                        <input type="date" class="form-control" id="inputStartDate" name="startdate" required>
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
                <h5 class="card-title">Daftar Anggota Farmasi</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">NIP</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Tanggal Mulai Kerja</th>
                            <th scope="col">Lama Bekerja</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                // Calculate the length of service
                                $startdate = new DateTime($row['mulai_bekerja']);
                                $today = new DateTime();
                                $interval = $today->diff($startdate);
                                $lama_bekerja = $interval->y . " tahun, " . $interval->m . " bulan, " . $interval->d . " hari";

                                echo "<tr>";
                                echo "<td>" . $row['NIP'] . "</td>";
                                echo "<td>" . $row['nama_farmasi'] . "</td>";
                                echo "<td>" . $row['email_farmasi'] . "</td>";
                                echo "<td>" . $row['mulai_bekerja'] . "</td>";
                                echo "<td>" . $lama_bekerja . "</td>";
                                echo "<td>
                                <a href='edit_farmasi.php?id_farmasi=" . $row['id_farmasi'] . "' class='btn btn-primary'><i class='bi bi-pencil me-1'></i> Edit</a>
                                <a href='delete_farmasi.php?id_farmasi=" . $row['id_farmasi'] . "' class='btn btn-danger'><i class='bi bi-trash me-1'></i> Hapus</a>
                                  </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No data found</td></tr>";
                        }
                        ?>
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
</body>

</html>