<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

$id_antrian = isset($_GET['id_antrian']) ? $_GET['id_antrian'] : null;
if (!$id_antrian) {
    die("ID Antrian tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tekanan_darah_s = $_POST['tekanan_darah_s'];
    $tekanan_darah_d = $_POST['tekanan_darah_d'];
    $berat_badan = $_POST['berat_badan'];
    $suhu_badan = $_POST['suhu_badan'];

    $rekamMedisQuery = "
        INSERT INTO rekam_medis (id_antrian, tekanan_darah_s, tekanan_darah_d, berat_badan, suhu_badan) 
        VALUES (?, ?, ?, ?, ?)";
    $rekamMedisStmt = $conn->prepare($rekamMedisQuery);
    $rekamMedisStmt->bind_param("issss", $id_antrian, $tekanan_darah_s, $tekanan_darah_d, $berat_badan, $suhu_badan);
    $rekamMedisStmt->execute();
    $id_rekammedis = $rekamMedisStmt->insert_id;
    $rekamMedisStmt->close();

    header("Location: admin_pasien.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Periksa Pasien - Dokter</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Periksa Pasien</h4>
            </div>
            <div class="card-body">
                <form class="row g-3" action="" method="POST">
                    
                    <div class="col-md-2">
                        <label for="inputTekananDarahS" class="form-label">Tekanan Darah S</label>
                        <input type="text" class="form-control" id="inputTekananDarahS" name="tekanan_darah_s" required>
                    </div>
                    <div class="col-md-2">
                        <label for="inputTekananDarahD" class="form-label">Tekanan Darah D</label>
                        <input type="text" class="form-control" id="inputTekananDarahD" name="tekanan_darah_d" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputBeratBadan" class="form-label">Berat Badan</label>
                        <input type="text" class="form-control" id="inputBeratBadan" name="berat_badan" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputSuhuBadan" class="form-label">Suhu Badan</label>
                        <input type="text" class="form-control" id="inputSuhuBadan" name="suhu_badan" required>
                    </div>
                    
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="admin_pasien.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
$(function() {
    function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    $("#inputResep").on("keydown", function(event) {
        if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
            event.preventDefault();
        }
    }).autocomplete({
        source: function(request, response) {
            $.getJSON("get-obat.php", {
                term: extractLast(request.term)
            }, response);
        },
        search: function() {
            var term = extractLast(this.value);
            if (term.length < 2) {
                return false;
            }
        },
        focus: function() {
            return false;
        },
        select: function(event, ui) {
            var terms = split(this.value);
            var termsIds = $("#inputResepIds").val().split(/,\s*/);

            terms.pop();
            terms.push(ui.item.value); 
            terms.push("");

            termsIds.pop();
            termsIds.push(ui.item.id);
            termsIds.push("");

            this.value = terms.join(", ");
            $("#inputResepIds").val(termsIds.join(", "));

            return false;
        }
    });

    $("form").submit(function(event) {
        var termsIds = $("#inputResepIds").val().split(/,\s*/);
        $("#inputResepIds").val(termsIds.filter(id => id !== ""));
        return true;
    });
});

    </script>
</body>
</html>
