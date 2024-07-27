<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

function generateId($conn) {
  $sql = "SELECT id_rujukan FROM rujukan ORDER BY id_rujukan DESC LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $lastId = $row['id_rujukan'];
      $lastNumber = intval(substr($lastId, 3)); // Get numbers after 'RSP'
      $newNumber = $lastNumber + 1;
      return 'RJK' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
  } else {
      return 'RJK01'; // First ID if the table is empty
  }
}

$message = '';

// Proses untuk menampilkan detail rekam medis setelah form dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $no_rekam_medis = $_POST['no_rekam_medis'];
    $newId = generateId($conn);
    $no_rekam_medis_clean = preg_replace('/^RM00/', '', $no_rekam_medis);

    // Query untuk mendapatkan detail rekam medis
    $sql = "SELECT rm.*, p.nama_pasien, p.email_pasien, p.nomorhp_pasien, dp.jenis_kelamin, dp.tanggal_lahir, dp.alamat_pasien, rm.keluhan, rm.diagnosa
            FROM rekam_medis rm
            INNER JOIN antrian a ON rm.id_antrian = a.id_antrian
            INNER JOIN pasien p ON a.id_pasien = p.id_pasien
            INNER JOIN detail_pasien dp ON p.id_pasien = dp.id_pasien
            WHERE rm.id_rekam_medis = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $no_rekam_medis_clean);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Tampilkan data rekam medis
        $message = "Detail Rekam Medis ditemukan untuk No Rekam Medis: " . $no_rekam_medis;
    } else {
        $message = "Detail Rekam Medis tidak ditemukan untuk No Rekam Medis: " . $no_rekam_medis;
    }
    $stmt->close();
}

// Fungsi untuk menghitung umur berdasarkan tanggal lahir
function hitungUmur($tanggal_lahir) {
    $tanggal_lahir = new DateTime($tanggal_lahir);
    $today = new DateTime();
    $umur = $today->diff($tanggal_lahir);
    $umur_tahun = $umur->y;
    $umur_bulan = $umur->m;
    $umur_hari = $umur->d;
    
    // Format umur
    $umur_formatted = "";
    if ($umur_tahun > 0) {
        $umur_formatted .= $umur_tahun . " tahun ";
    }
    if ($umur_bulan > 0) {
        $umur_formatted .= $umur_bulan . " bulan ";
    }
    if ($umur_hari > 0) {
        $umur_formatted .= $umur_hari . " hari";
    }
  
    return $umur_formatted;
}

// Menghitung umur berdasarkan tanggal lahir, jika data tersedia
$umur_pasien = isset($row['tanggal_lahir']) ? hitungUmur($row['tanggal_lahir']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_rujukan'])) {
  $no_rekam_medis = $_POST['id_rekam_medis'];
  $nama_rumah_sakit = $_POST['nama_rumah_sakit_rujukan'];
  $nama_dokter = $_POST['nama_dokter'];
  $poli = $_POST['poli'];
  $tanggal_rujukan = $_POST['tanggal_rujukan'];
  $newId = generateId($conn);

  // Query untuk menyimpan data rujukan ke dalam tabel
  $sql_insert = "INSERT INTO rujukan (id_rujukan, id_rekammedis, nama_rumahsakit, nama_dokter, poli, tanggal_rujukan) 
  VALUES (?, ?, ?, ?, ?, ?)";

  $stmt_insert = $conn->prepare($sql_insert);
  $stmt_insert->bind_param("ssssss", $newId, $no_rekam_medis, $nama_rumah_sakit, $nama_dokter, $poli, $tanggal_rujukan);

  if ($stmt_insert->execute()) {
      $stmt_insert->close();
      $_SESSION['success_message'] = "Data rujukan berhasil ditambahkan.";
      header("location: admin_rujukan.php");
      exit;
  } else {
      $stmt_insert->close();
      $_SESSION['error_message'] = "Terjadi kesalahan. Data rujukan gagal ditambahkan.";
      header("location: admin_rujukan.php");
      exit;
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
        <a class="nav-link" href="admin_rujukan.php">
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
      <h1>Rujukan</h1>
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
              <h5 class="card-title">Tambah Data Rujukan</h5>
              <?php if (!empty($message)): ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $message; ?>
                </div>
              <?php endif; ?>
              <form class="row g-3" action="admin_rujukan.php" method="POST">
                <div class="col-md-12">
                  <label for="inputEmail5" class="form-label">No Rekam Medis</label>
                  <input type="text" class="form-control" id="inputEmail5" name="no_rekam_medis" required>
                </div>
                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary">Cek Nomor Rekam Medis</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form>
            </div>
          </div>

          <?php if (isset($result) && $result->num_rows > 0): ?>
<div class="card mt-5">
    <div class="card-body">
        <h5 class="card-title">Detail Rekam Medis</h5>
        <form action="admin_rujukan.php" method="POST">
            <input type="hidden" name="id_rekam_medis" value="<?php echo $row['id_rekam_medis']; ?>">
            <div class="mb-3">
                <label for="nama_pasien" class="form-label">Nama Pasien</label>
                <input type="text" class="form-control" id="nama_pasien" value="<?php echo $row['nama_pasien']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="text" class="form-control" id="tanggal_lahir" value="<?php echo $row['tanggal_lahir']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="umur" class="form-label">Umur</label>
                <input type="text" class="form-control" id="umur" value="<?php echo $umur_pasien; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <input type="text" class="form-control" id="jenis_kelamin" value="<?php echo $row['jenis_kelamin']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="alamat_pasien" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat_pasien" rows="3" readonly><?php echo $row['alamat_pasien']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="keluhan" class="form-label">Keluhan</label>
                <textarea class="form-control" id="keluhan" rows="3" readonly><?php echo $row['keluhan']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="diagnosa" class="form-label">Diagnosa</label>
                <textarea class="form-control" id="diagnosa" rows="3" readonly><?php echo $row['diagnosa']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="email_pasien" class="form-label">Email</label>
                <input type="email" class="form-control" id="email_pasien" value="<?php echo $row['email_pasien']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="nomorhp_pasien" class="form-label">Nomor HP</label>
                <input type="text" class="form-control" id="nomorhp_pasien" value="<?php echo $row['nomorhp_pasien']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="nama_rumah_sakit_rujukan" class="form-label">Nama Rumah Sakit Rujukan</label>
                <input type="text" class="form-control" id="nama_rumah_sakit_rujukan" name="nama_rumah_sakit_rujukan" required>
            </div>
            <div class="mb-3">
                <label for="nama_dokter" class="form-label">Nama Dokter</label>
                <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" required>
            </div>

            <div class="mb-3">
                <label for="poli" class="form-label">Poli</label>
                <input type="text" class="form-control" id="poli" name="poli" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_rujukan" class="form-label">Tanggal Rujukan</label>
                <input type="date" class="form-control" id="tanggal_rujukan" name="tanggal_rujukan" required>
            </div>
            <button type="submit" name="tambah_rujukan" class="btn btn-primary">Tambah Data Rujukan</button>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card mt-5">
    <div class="card-body">
        <h5 class="card-title">Daftar Rujukan</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">No Rekam Medis</th>
                    <th scope="col">Nama Pasien</th>
                    <th scope="col">Rumah Sakit Tujuan</th>
                    <th scope="col">Tanggal Tujuan</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.*, rm.id_rekam_medis, p.nama_pasien 
                        FROM rujukan r
                        INNER JOIN rekam_medis rm ON r.id_rekammedis = rm.id_rekam_medis
                        INNER JOIN antrian a ON rm.id_antrian = a.id_antrian
                        INNER JOIN pasien p ON a.id_pasien = p.id_pasien
                        ORDER BY r.id_rujukan DESC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                  while($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $row['id_rekam_medis'] . "</td>";
                      echo "<td>" . $row['nama_pasien'] . "</td>";
                      echo "<td>" . $row['nama_rumahsakit'] . "</td>";
                      echo "<td>" . $row['tanggal_rujukan'] . "</td>";
                      echo "<td>
                              <a href='print_rujukan.php?id_rujukan=" . $row['id_rujukan'] . "&id_rekam_medis=" . $row['id_rekam_medis'] . "' target='_blank' class='btn btn-success'><i class='bi bi-printer me-1'></i> Print</a>
                              <a href='delete_rujukan.php?id_rujukan=" . $row['id_rujukan'] . "' class='btn btn-danger'><i class='bi bi-trash me-1'></i> Hapus</a>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' class='text-center'>No data found</td></tr>";
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

