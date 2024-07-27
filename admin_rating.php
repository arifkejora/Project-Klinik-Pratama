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

// Calculate average rating and distribution
$sql_avg = "SELECT AVG(rate_admin) as avg_rating FROM rekam_medis";
$result_avg = mysqli_query($conn, $sql_avg);
$avg_rating = 0;
if ($result_avg && mysqli_num_rows($result_avg) > 0) {
    $row_avg = mysqli_fetch_assoc($result_avg);
    $avg_rating = round($row_avg['avg_rating'], 1);
}

$sql_dist = "SELECT rate_admin, COUNT(rate_admin) as count FROM rekam_medis GROUP BY rate_admin ORDER BY rate_admin DESC";
$result_dist = mysqli_query($conn, $sql_dist);
$rating_distribution = [];
while ($row_dist = mysqli_fetch_assoc($result_dist)) {
    $rating_distribution[$row_dist['rate_admin']] = $row_dist['count'];
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
        <a class="nav-link collapsed" href="admin_bayar.php">
          <i class="bi bi-journal-text"></i>
          <span>Pembayaran</span>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="admin_rating.php">
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
      <h1>Rating</h1>
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
                    <h5 class="card-title">Rating Pelayanan Admin</h5>

                    <div class="rating-box mb-4">
                <div class="average-rating">
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
                
              </div>


                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Rekam Medis</th>
                                <th scope="col">Rating Admin</th>
                                <th scope="col">Rating Dokter</th>
                                <th scope="col">Rating Farmasi</th>
                                <th scope="col">Ulasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('db_connection.php');

                            $sql = "SELECT r.id_rating, r.id_rekam_medis, r.rate_admin, r.rate_dokter, r.rate_farmasi, r.ulasan 
                                    FROM rating r";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($rating = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>RM00" . $rating['id_rekam_medis'] . "</td>";

                                    echo "<td>" . $rating['rate_admin'] . " Bintang</td>";
                                    echo "<td>" . $rating['rate_dokter'] . " Bintang</td>";
                                    echo "<td>" . $rating['rate_farmasi'] . " Bintang</td>";
                                    echo "<td>" . $rating['ulasan'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data rating</td></tr>";
                            }

                            $conn->close();
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