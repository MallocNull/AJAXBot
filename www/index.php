<?php
include("conn.php");

var_dump($config);

$err = $_GET["err"];

if($_POST["loginAttempt"]) {
    if(mysql_num_rows(mysql_query("SELECT * FROM `admin` WHERE `username`='". mysql_real_escape_string($_POST['name']) ."' AND `password`='". hash('sha256',$_POST['pwd']) ."'")) > 0) {
        $_SESSION["user"] = $_POST["name"];
        $_SESSION["pwd"] = hash('sha256',$_POST['pwd']);
    } else $err = "Failed to log in.";
}

if($_GET["jew"] == "true")
    session_destroy();
?>
<?php if(!checkIfLoggedIn()) { ?>
<html>
<head>
    <title>AJAX Bot Administration</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <center>
        <h1>Admin Login</h1>
        <?php if($err) { ?><h3 style="color: red;"><?php echo $err; ?></h3><?php } ?>
        <p>Javascript must be enabled for proper functionality.</p>
        <form method="post" action="index.php">
            <table border="0">
            <tr><td style="text-align: right;">Username:</td><td><input type="text" name="name" /></td></tr>
            <tr><td style="text-align: right;">Password:</td><td><input type="password" name="pwd" /></td></tr>
            <tr><td></td><td><input type="submit" value="Login" name="loginAttempt" /></td></tr>
            </table>
        </form>
    </center>
</body>
</html>
<?php } else { ?>
    <?php include("header.php"); ?>
    <center>
        <fieldset class="narrow">
            <legend>Pulse</legend>
            Last hearbeat sent
            <?php
            echo mysql_fetch_object(mysql_query("SELECT `heartbeat` FROM `updater` WHERE `id`=1"))->heartbeat;
            echo " UTC". $config->timezone ."". (($config->dst)?" in accordance to daylight savings.":"disregarding daylight savings.");
            ?>
        </fieldset>
        <br />
        <fieldset class="wide">
            <legend>Error Log</legend>
            <a href="jews.php?do=cerrs">Clear Error List</a>
            <?php
            $q = mysql_query("SELECT * FROM `error` ORDER BY `id` DESC");
            while($err = mysql_fetch_object($q)) {
                echo "<p class='error'>". $err->time ." - ". $err->msg ."</p>";
            }
            ?>
        </fieldset>
    </center>
    <?php include("footer.php"); ?>
<?php } ?>