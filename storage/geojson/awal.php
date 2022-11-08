<?php
//$conn = new PDO('mysql:host=localhost;dbname=bplhkotabogor','root','');
// $conn = new PDO('mysql:host=localhost;dbname=devbplhk_db','root','');
$servername = "103.214.113.175";
$database = "dlh_sil";
$username = "root";
$password = "mardawaG0!!";
$dsn = "mysql:host=$servername;dbname=$database";
// Create connection
// $conn = mysqli_connect($servername, $username, $password, $database);
// $conn = new PDO($dns, $username, $password);
$conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";
// mysqli_close($conn);

$path = 'http://localhost/developbplhkotabogor/uploads/ragamdata/';

?>