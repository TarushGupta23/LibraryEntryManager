<?php
session_start();
include './partials/connections.php';

$contact = $_POST['visitorContact'];
$name = $_POST['visitorName'];
$email = $_POST['visitorEmail'];
$currentDateTime = date("Y-m-d H:i:s");

$query = "insert into visitor values ('$contact', '$name', '$email', '$currentDateTime')";
$conn->query($query);

header( 'Location: ./' ) ;
?>