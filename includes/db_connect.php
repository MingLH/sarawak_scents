<?php
// Set Timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

$servername = "sql100.infinityfree.com";
$username = "if0_40768673";
$password = "50xFdc9L7y4e8P";
$dbname = "if0_40768673_sarawak_scents_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
