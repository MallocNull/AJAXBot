<?php
mysql_connect("HOSTADDRESS", "USERNAME", "PASSWORD");
mysql_select_db("DATABASE_NAME");
session_start();

$config = mysql_fetch_object(mysql_query("SELECT * FROM `config` WHERE `id`=1"));

function checkIfLoggedIn() {
    if(mysql_num_rows(mysql_query("SELECT * FROM `admin` WHERE `username`='". mysql_real_escape_string($_SESSION['user']) ."' AND `password`='". $_SESSION['pwd'] ."'")) > 0)
        return true;
    else
        return false;
}
?>