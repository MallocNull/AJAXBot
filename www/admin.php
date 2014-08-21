<?php include("conn.php");

if(mysql_fetch_object(mysql_query("SELECT * FROM `admin` WHERE `username`='". mysql_real_escape_string($_SESSION['user']) ."'"))->accountaccess == false)
    header("Location: index.php?err=You do not have permission to access account data!");

if($_GET['del']) {
    mysql_query("DELETE FROM `admin` WHERE `id`=".$_GET['del']);
    header("Location: admin.php");
}

if($_POST["editId"]) {
    // TODO update this
    if(trim($_POST["password"]))
        mysql_query("UPDATE `admin` SET `username`='". mysql_real_escape_string($_POST['username']) ."', `password`='". hash('sha256',$_POST['password']) ."', `accountaccess`=". (($_POST['access'])?"1":"0") ." WHERE `id`=". $_POST['editId']) or die(mysql_error());
    else
        mysql_query("UPDATE `admin` SET `username`='". mysql_real_escape_string($_POST['username']) ."', `accountaccess`=". (($_POST['access'])?"1":"0") ." WHERE `id`=". $_POST['editId']) or die(mysql_error());
    header("Location: admin.php");
}

if($_POST["username"] && !$_POST["editId"]) {
    mysql_query("INSERT INTO `admin` (`username`,`password`,`accountaccess`) VALUES ('". mysql_real_escape_string($_POST['username']) ."','". hash('sha256',$_POST['password']) ."',". (($_POST['access'])?"1":"0") .")") or die(mysql_error());
    header("Location: admin.php");
}

include("header.php");
?>
    <script type="text/javascript">
        function confirmDeletion(id) {
            var q = confirm("Are you sure you want to delete this account?");
            if(q) window.location.href = "admin.php?del="+id;
        }
    </script>
    <center>
    <fieldset class="wide" style="padding-bottom: 0;">
    <?php if(!$_GET["do"]) { ?>
        <legend>Admin Account List</legend>
        <p style="margin-top: 0;"><a href="admin.php?do=new">New Admin Account</a></p>
        <center>
            <?php
            $q = mysql_query("SELECT * FROM `admin`");
            while($acc = mysql_fetch_object($q)) {
                echo "
                        <table border='0' style='width:790px;border:1px solid black;margin:5px;'>
                            <tr>
                                <td style='width:50px;text-align:center;verticle-align:middle;'>
                                    <a href='admin.php?do=edit&id=". $acc->id ."' style='verticle-align: middle;'><img src='img/edit.png' border='0' /></a>
                                    &nbsp;<img src='img/delete.png' border='0' class='fakelink' onclick='confirmDeletion(". $acc->id .");' />
                                </td>
                                <td style='width:150px;'>
                                    ". $acc->username ."
                                </td>
                                <td>
                                    ". (($acc->accountaccess)?"Has access to admin accounts":"Does not have access to admin accounts") ."
                                </td>
                            </tr>
                        </table>";
            }
            ?>
        </center>
    <?php } else if($_GET["do"]=="new") { ?>
        <legend>Create New Account</legend>
        <form method="post" action="">
            <p>
                Username:
                <input type="textbox" name="username" />
            </p>
            <p>
                Password:
                <input type="password" name="password" />
            </p>
            <p>
                Has access to admin accounts?
                <input type="checkbox" name="access" />
            </p>
            <p>
                <input type="submit" name="addAccount" value="Add Account" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="window.location.href = 'admin.php';" />
            </p>
        </form>
    <?php } else if($_GET["do"]=="edit") {
    $acc = mysql_fetch_object(mysql_query("SELECT * FROM `admin` WHERE `id`=".$_GET['id']));
    ?>
        <legend>Edit Account</legend>
        <form method="post" action="">
            <p>
                Username:
                <input type="textbox" name="username" value="<?php echo $acc->username ?>" />
            </p>
            <p>
                Password:
                <input type="password" name="password" />
                (leave blank if not changing)
            </p>
            <p>
                Has access to admin accounts?
                <input type="checkbox" name="access"<?php if($acc->accountaccess) { ?> checked="checked"<?php } ?> />
            </p>
            <p>
                <input type="submit" name="editAccount" value="Edit Account" />
                <input type="hidden" name="editId" value="<?php echo $_GET['id']; ?>" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="window.location.href = 'admin.php';" />
            </p>
        </form>
    <?php } ?>
    </fieldset>
    </center>
<?php include("footer.php"); ?>