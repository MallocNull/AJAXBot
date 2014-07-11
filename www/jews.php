<?php
include("conn.php");

if(!$_GET["do"]) {
    session_start();
    session_destroy();
    header("Location: index.php");
} else {
    if($_GET["do"] = "cerrs") {
        mysql_query("TRUNCATE `error`");
        mysql_query("ALTER TABLE `error` AUTO_INCREMENT=1");
        header("Location: index.php");
    }
}
?>