<?php
include "../init.php";
session_start();

$fs_username = trim($_SERVER["PHP_AUTH_USER"]);
$fs_displayname = preg_replace("/[^a-zA-Z0-9 _.-]/", "", trim($_SERVER["AUTHENTICATE_DISPLAYNAME"]));
$email = "$fs_username@d120.de";

$user = get_login_user( "$fs_username@d120.de" );
if ($user) {
  $_SESSION["user"] = $user;
} else {
  sql("INSERT INTO users (email, fullname, debt_limit, password_hash) VALUES (?,?,?,?)",
    [ $email, $fs_displayname, DEFAULT_DEBT_LIMIT, "" ], true);
  $_SESSION["user"] = sql("SELECT * FROM users WHERE email = ?", [ "$fs_username@d120.de" ], 1);
}
if (isset($_GET["iframe"]))
    echo "Eile mit Weile<script>top.location='".BASE_URL."'</script>";
else {
    header("Location: ".BASE_URL);
    setcookie("autologin","1",time()+9001, "/kdv/");
}

