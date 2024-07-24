<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

$id_patient = $_SESSION['login_id'];

function generateId($conn) {
    $sql = "SELECT id_antrian FROM antrian ORDER BY id_antrian DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id_antrian'];
        $lastNumber = intval(substr($lastId, 3)); // Extract number after 'ANT'
        $newNumber = $lastNumber + 1;
        return 'ANT' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    } else {
        return 'ANT01'; // First ID if table is empty
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_schedule = $_POST['id_schedule'];

    // Check if patient is already in the queue
    $stmt_check_antrian = $conn->prepare("SELECT * FROM antrian WHERE id_jadwal = ? AND id_pasien = ?");
    $stmt_check_antrian->bind_param("ss", $id_schedule, $id_patient);
    $stmt_check_antrian->execute();
    $result_check_antrian = $stmt_check_antrian->get_result();

    if ($result_check_antrian->num_rows == 0) {
        // Check quota
        $stmt_kuota = $conn->prepare("SELECT kuota FROM jadwal_dokter WHERE id_jadwal = ? AND status = 'Aktif'");
        $stmt_kuota->bind_param("s", $id_schedule);
        $stmt_kuota->execute();
        $result_kuota = $stmt_kuota->get_result();

        if ($result_kuota && $result_kuota->num_rows > 0) {
            $row_kuota = $result_kuota->fetch_assoc();
            $kuota = $row_kuota['kuota'];

            if ($kuota > 0) {
                // Reduce quota
                $newId = generateId($conn);
                $new_kuota = $kuota - 1;
                $stmt_update_kuota = $conn->prepare("UPDATE jadwal_dokter SET kuota = ? WHERE id_jadwal = ?");
                $stmt_update_kuota->bind_param("is", $new_kuota, $id_schedule);
                if ($stmt_update_kuota->execute()) {
                    // Get last queue number
                    $stmt_last_queue = $conn->prepare("SELECT MAX(antrian) AS last_queue FROM antrian WHERE id_jadwal = ?");
                    $stmt_last_queue->bind_param("s", $id_schedule);
                    $stmt_last_queue->execute();
                    $result_last_queue = $stmt_last_queue->get_result();
                    $last_queue = 0;
                    if ($result_last_queue && $result_last_queue->num_rows > 0) {
                        $row_last_queue = $result_last_queue->fetch_assoc();
                        $last_queue = $row_last_queue['last_queue'];
                    }
                    $new_queue = $last_queue + 1;

                    // Insert data into queue table
                    $stmt_insert_antrian = $conn->prepare("INSERT INTO antrian (id_antrian, id_jadwal, id_pasien, antrian, status_antrian, dtmcrt) VALUES (?, ?, ?, ?, 'Menunggu', NOW())");
                    $stmt_insert_antrian->bind_param("sssi", $newId, $id_schedule, $id_patient, $new_queue);

                    if ($stmt_insert_antrian->execute()) {
                        $success_message = "Kamu berhasil mengambil antrian.";
                    } else {
                        $error_message = "Gagal memasukkan data ke tabel antrian.";
                    }
                } else {
                    $error_message = "Gagal memperbarui kuota.";
                }
            } else {
                $error_message = "Kuota pemeriksaan ini sudah habis, silahkan kembali besok.";
            }
        } else {
            $error_message = "Jadwal tidak ditemukan atau tidak aktif.";
        }
    } else {
        $error_message = "Kamu sudah terdaftar dalam antrian untuk jadwal ini.";
    }
}

// Fetch patient queue history
$stmt_history = $conn->prepare("
    SELECT a.antrian, d.nama_dokter, jd.waktu_mulai, jd.waktu_selesai, a.status_antrian, a.dtmcrt, rm.id_rekam_medis, rm.pembayaran, rm.status_pembayaran
    FROM antrian a
    JOIN jadwal_dokter jd ON a.id_jadwal = jd.id_jadwal
    JOIN dokter d ON jd.id_dokter = d.id_dokter
    LEFT JOIN rekam_medis rm ON a.id_antrian = rm.id_antrian
    WHERE a.id_pasien = ?
");
$stmt_history->bind_param("s", $id_patient);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard Pasien</title>
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
              <a class="dropdown-item d-flex align-items-center" href="logout-patient.php">
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
        <a class="nav-link collapsed" href="patient_dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link " href="patient-checkup.php">
          <i class="bi bi-person"></i>
          <span>Periksa</span>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="patient-history.php">
          <i class="bi bi-journal-text"></i>
          <span>Riwayat Periksa</span>
        </a>
      </li> -->

    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Menu Periksa</h1>
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
                            <h5 class="card-title">Pilih Periksamu</h5>
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <form class="row g-3" action="patient-checkup.php" method="POST">
                                <div class="col-md-12">
                                    <label for="inputNip5" class="form-label">Pilih Dokter</label>
                                    <select id="inputNip5" class="form-select" name="id_schedule" required>
                                        <option selected disabled value="">Pilih Dokter...</option>
                                        <?php
                                        $sql = "SELECT jd.id_jadwal, d.nama_dokter, jd.kuota, d.spesialis, jd.kuota
                                                FROM jadwal_dokter jd 
                                                INNER JOIN dokter d ON jd.id_dokter = d.id_dokter
                                                WHERE jd.status = 'Aktif'";
                                        $result = mysqli_query($conn, $sql);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='" . $row['id_jadwal'] . "'>" . $row['nama_dokter'] . " - " . $row['spesialis'] . " - " . $row['kuota'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Pilih Dokter!</div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Ambil Antrian</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
    <div class="card-body">
        <h5 class="card-title">Riwayat Periksa Pasien</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Antrian</th>
                    <th scope="col">Dokter</th>
                    <th scope="col">Waktu Ambil Antrian</th>
                    <th scope="col">Estimasi Waktu</th>
                    <th scope="col">Status</th>
                    <th scope="col">Rekam Medis</th>
                    <th scope="col">Nominal</th>
                    <th scope="col">Status Bayar</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_history && mysqli_num_rows($result_history) > 0) {
                    $index = 1;

                    while ($row = mysqli_fetch_assoc($result_history)) {
                        // Ambil waktu ambil antrian dari kolom dtmupd
                        $waktu_ambil = new DateTime($row['dtmcrt']);
                        
                        // Hitung estimasi waktu berdasarkan nomor antrian
                        $waktu_mulai = clone $waktu_ambil;
                        $waktu_mulai->add(new DateInterval('PT' . ($row['antrian'] - 1) * 30 . 'M')); // Tambah waktu berdasarkan nomor antrian
                        $waktu_selesai = clone $waktu_mulai;
                        $waktu_selesai->add(new DateInterval('PT30M')); // Tambah 30 menit untuk estimasi waktu selesai

                        $estimasi_waktu = $waktu_mulai->format('H:i') . " - " . $waktu_selesai->format('H:i');
                        
                        echo "<tr>
                                <th scope='row'>{$index}</th>
                                <td>{$row['antrian']}</td>
                                <td>{$row['nama_dokter']}</td>
                                <td>{$row['dtmcrt']}</td>
                                <td>{$estimasi_waktu}</td>
                                <td>{$row['status_antrian']}</td>
                                <td>{$row['id_rekam_medis']}</td>
                                <td>{$row['pembayaran']}</td>
                                <td>{$row['status_pembayaran']}</td>
                                <td>
                                    <form action='patient-detail.php' method='GET'>
                                        <input type='hidden' name='id_rekam_medis' value='" . htmlspecialchars($row['id_rekam_medis']) . "'>
                                        <button type='submit' class='btn btn-primary'>Lihat Detail</button>
                                    </form>
                                </td>
                              </tr>";

                        $index++;
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data riwayat periksa.</td></tr>";
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