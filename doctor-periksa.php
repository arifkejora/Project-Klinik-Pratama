<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['login_doctor'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$id_antrian = isset($_GET['id_antrian']) ? $_GET['id_antrian'] : null;
if (!$id_antrian) {
    die("ID Antrian tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keluhan = $_POST['keluhan'];
    $diagnosa = $_POST['diagnosa'];
    $tekanan_darah = $_POST['tekanan_darah'];
    $berat_badan = $_POST['berat_badan'];
    $suhu_badan = $_POST['suhu_badan'];
    $hasil_pemeriksaan = $_POST['hasil_pemeriksaan'];
    $resep_obat = explode(',', $_POST['resep_obat_ids']);

    $rekamMedisQuery = "
        INSERT INTO rekam_medis (id_antrian, keluhan, diagnosa, tekanan_darah, berat_badan, suhu_badan, hasil_pemeriksaan, status_pembayaran) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Belum Lunas')";
    $rekamMedisStmt = $conn->prepare($rekamMedisQuery);
    $rekamMedisStmt->bind_param("issssss", $id_antrian, $keluhan, $diagnosa, $tekanan_darah, $berat_badan, $suhu_badan, $hasil_pemeriksaan);
    $rekamMedisStmt->execute();
    $id_rekammedis = $rekamMedisStmt->insert_id;
    $rekamMedisStmt->close();

    $resepObatQuery = "
    INSERT INTO resep_obat (id_rekammedis, id_obat, status) 
    VALUES (?, ?, 'Antrian')";

    $resepObatStmt = $conn->prepare($resepObatQuery);

    foreach ($resep_obat as $id_obat) {
        $id_obat = (int)trim($id_obat); // Ensure the id_obat is an integer
        if ($id_obat > 0) {
            $resepObatStmt->bind_param("ii", $id_rekammedis, $id_obat);
            if (!$resepObatStmt->execute()) {
                echo "Error inserting obat: " . $resepObatStmt->error;
                exit;
            }
        }
    }
    $resepObatStmt->close();

    $updateAntrianQuery = "
        UPDATE antrian 
        SET status_antrian = 'Selesai Diperiksa' 
        WHERE id_antrian = ?";
    $updateAntrianStmt = $conn->prepare($updateAntrianQuery);
    $updateAntrianStmt->bind_param("i", $id_antrian);
    if (!$updateAntrianStmt->execute()) {
        echo "Error updating antrian: " . $updateAntrianStmt->error;
        exit;
    }
    $updateAntrianStmt->close();

    header("Location: doctor-patient.php");
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
                    <div class="col-md-12">
                        <label for="inputKeluhan" class="form-label">Keluhan</label>
                        <textarea class="form-control" id="inputKeluhan" name="keluhan" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputDiagnosa" class="form-label">Diagnosa</label>
                        <textarea class="form-control" id="inputDiagnosa" name="diagnosa" rows="3" required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="inputTekananDarah" class="form-label">Tekanan Darah</label>
                        <input type="text" class="form-control" id="inputTekananDarah" name="tekanan_darah" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputBeratBadan" class="form-label">Berat Badan</label>
                        <input type="text" class="form-control" id="inputBeratBadan" name="berat_badan" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputSuhuBadan" class="form-label">Suhu Badan</label>
                        <input type="text" class="form-control" id="inputSuhuBadan" name="suhu_badan" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputHasilPemeriksaan" class="form-label">Hasil Pemeriksaan</label>
                        <textarea class="form-control" id="inputHasilPemeriksaan" name="hasil_pemeriksaan" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputResep" class="form-label">Resep Obat</label>
                        <textarea class="form-control" id="inputResep" name="resep_obat" rows="3" required></textarea>
                        <input type="hidden" id="inputResepIds" name="resep_obat_ids">
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="admin_dokter.php" class="btn btn-secondary">Batal</a>
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
