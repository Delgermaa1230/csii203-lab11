<?php
$servername = "localhost";
$username = "root";
$password = "Delgermaa1";
$dbname = "mystuffs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.stuffId, s.stuffName, s.stuffBdate, s.stuffEdate, s.stuffPrice, 
c.categoryFunction, c.categoryId, c.categoryName, 
u.Location, u.stuffusage
from stuffs s, categories c, stuffsusage u
where s.stuffid=u.stuffid and u.categoryid=c.categoryid";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>


