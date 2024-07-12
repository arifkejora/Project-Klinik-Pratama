<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_obat'])) {
    $id = $_GET['id_obat'];
    $sql = "DELETE FROM obat WHERE id_obat = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: farmasi-obat.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: farmasi-obat.php");
}
?>
