<?php
include 'db_connect.php';

if(isset($_GET["userid"]))
{
    $sql = "Insert into users (fullname, phone, email, password_hash) 
    values ('".$_GET["name"]."','".$_GET["phone"]."','".$_GET["email"]."','".$_GET["password_hash"]."')";

if (mysqli_query($conn, $sql)) {
    echo "New account created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
}
mysqli_close($conn);
?>