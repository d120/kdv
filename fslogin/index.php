<?php
include "../init.php";
session_start();

$fs_username = $_SERVER["PHP_AUTH_USER"];
$email = "$fs_username@d120.de";

$user = sql("SELECT * FROM users WHERE email = ?", [ "$fs_username@d120.de" ], 1);
if ($user) {
  $_SESSION["user"] = $user;
} else {
  sql("INSERT INTO users (email, fullname, debt_limit, password_hash) VALUES (?,?,?,?)",
    [ $email, $fs_username, 10000, "" ], true);
  $_SESSION["user"] = sql("SELECT * FROM users WHERE email = ?", [ "$fs_username@d120.de" ], 1);

}

header("Location: ".BASE_URL);


