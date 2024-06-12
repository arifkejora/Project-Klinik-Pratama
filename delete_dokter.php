<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_dokter'])) {
    $id = $_GET['id_dokter'];
    $sql = "DELETE FROM dokter WHERE id_dokter = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_dokter.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_dokter.php");
}
?>
