<?php

$db = array(
    "host" => "localhost",
    "user" => "root",
    "pass" => "",
    "dbname" => "mrg-360",
    "charset" => "utf8"
);
$conn = @new mysqli($db["host"], $db["user"], $db["pass"], $db["dbname"]);
if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    exit;
}
if (!$conn->set_charset($db["charset"])) {
} else {
}