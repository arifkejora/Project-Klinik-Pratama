<?php
include('db_connection.php');

$term = $_GET['term'];
$query = $conn->prepare("SELECT id_obat, nama_obat FROM obat WHERE nama_obat LIKE ?");
$query->bind_param("s", $searchTerm);
$searchTerm = '%' . $term . '%';
$query->execute();
$result = $query->get_result();

$obat = array();
while ($row = $result->fetch_assoc()) {
    $obat[] = array(
        'id' => $row['id_obat'],
        'value' => $row['nama_obat']
    );
}
echo json_encode($obat);
?>
