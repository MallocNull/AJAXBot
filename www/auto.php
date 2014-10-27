<?php include("conn.php");

if($_GET['del']) {
    mysql_query("DELETE FROM `autonomous` WHERE `id`=".$_GET['del']);
    header("Location: auto.php");
}

if($_POST["editId"]) {
    // TODO update this
    mysql_query("UPDATE `autonomous` SET `conditions`='". mysql_real_escape_string($c) ."', `respid`=". $_POST['resptype'] .", `parameters`='". mysql_real_escape_string($_POST['parameters']) ."', `cooldown`=". (($_POST['cdd']==0)?-1:$_POST['cooldown']) ." WHERE `id`=". $_POST['editId']) or die(mysql_error());
    mysql_query("UPDATE `updater` SET `autonomous`=1 WHERE `id`=1");
    header("Location: auto.php");
}

if($_POST["resptype"] && !$_POST["editId"]) {
    if($_POST["autolink"] == -1)
        mysql_query("INSERT INTO `autonomous` (`conditions`,`respid`,`parameters`,`cooldown`) VALUES ('". mysql_real_escape_string($c) ."',". $_POST['resptype'] .",'". mysql_real_escape_string($_POST['parameters']) ."',". (($_POST['ccd']==0)?-1:$_POST['cooldown']) .")") or die(mysql_error());
    else
        // do query
    mysql_query("UPDATE `updater` SET `autonomous`=1 WHERE `id`=1");
    header("Location: auto.php");
}

include("header.php");
?>
    <script type="text/javascript">
        /*var defaultCool = <?php echo $config->cooldown; ?>;*/

        function confirmDeletion(id) {
            var q = confirm("Are you sure you want to delete this response?");
            if(q) window.location.href = "auto.php?del="+id;
        }

        function handleRespChange() {
            document.getElementById("respDesc").innerHTML = document.getElementById(""+document.getElementById("resptype").selectedIndex).innerHTML;
        }

        function doTimeTest(fieldname, fieldtext, canbezero) {
            if(!isNaN(parseInt(fieldtext.trim())) && isFinite(parseInt(fieldtext.trim()))) {
                if(parseInt(fieldtext) > ((canbezero)?-1:0)) {
                    return true;
                } else {
                    alert(fieldname +" must be positive and nonzero integer!");
                    return false;
                }
            } else {
                alert(fieldname +" must be a finite integer!");
                return false;
            }
        }

        function evaluateCondition() {
            if(document.getElementById("arname").value.trim() != "") {
                if(doTimeTest("Periodicity",document.getElementById("period").value)) {
                    if(doTimeTest("Randomness",document.getElementById("randomness").value)) {
                        if(document.getElementById("autolink").selectedIndex != 0) {
                            if(doTimeTest("Link timeout",document.getElementById("timeout").value)) {
                                if(doTimeTest("Link randomness",document.getElementById("torandom").value)) {
                                    document.getElementById("auto").submit();
                                }
                            }
                        } else {
                            document.getElementById("auto").submit();
                        }
                    }
                }
            } else {
                alert("Friendly name cannot be blank!");
            }
        }

        function handleAutolinkChange() {
            if(document.getElementById("autolink").selectedIndex != 0) {
                for(i = 1; i <= 3; i++)
                    document.getElementById("linktable").getElementsByTagName("tr")[i].style.display = "table-row";
            } else {
                for(i = 1; i <= 3; i++)
                    document.getElementById("linktable").getElementsByTagName("tr")[i].style.display = "none";
            }
        }

        /*function coolChange() {
            if(document.getElementById("cdd").selectedIndex == 0) {
                document.getElementById("cooldown").disabled = true;
                document.getElementById("cooldown").value = defaultCool;
            } else
                document.getElementById("cooldown").disabled = false;
        }*/
    </script>
    <center>
    <fieldset class="wide" style="padding-bottom: 0;">
    <?php if(!$_GET["do"]) { ?>
        <legend>Autonomy List</legend>
        <p style="margin-top: 0;"><a href="auto.php?do=new">New Autonomous Routine</a></p>
        <center>
            <?php
            $q = mysql_query("SELECT * FROM `auto`");
            // TODO update this
            /*while($resp = mysql_fetch_object($q)) {
                echo "
                        <table border='0' style='width:790px;border:1px solid black;margin:5px;'>
                            <tr>
                                <td style='width:50px;text-align:center;verticle-align:middle;'>
                                    <a href='resp.php?do=edit&id=". $resp->id ."' style='verticle-align: middle;'><img src='img/edit.png' border='0' /></a>
                                    &nbsp;<img src='img/delete.png' border='0' class='fakelink' onclick='confirmDeletion(". $resp->id .");' />
                                </td>
                                <td>
                                    ". parseConditionString($resp->conditions, mysql_fetch_object(mysql_query("SELECT * FROM `resptypes` WHERE `id`=". $resp->respid))->friendlyname) ."
                                </td>
                            </tr>
                        </table>";
            }*/
            ?>
        </center>
    <?php } else if($_GET["do"]=="new") { ?>
        <legend>Create New Autonomous Routine</legend>
        <form method="post" action="" id="auto">
            <p>
                Friendly name: <input type="text" name="arname" id="arname" /> (for future reference)
            </p>
            <p>
                Trigger first routine on
                <select name="startday">
                    <option value="-1">program start</option>
                    <option value="1">Sunday</option>
                    <option value="2">Monday</option>
                    <option value="3">Tuesday</option>
                    <option value="4">Wednesday</option>
                    <option value="5">Thursday</option>
                    <option value="6">Friday</option>
                    <option value="7">Saturday</option>
                </select>
                at
                <select name="starttimehour">
                    <?php
                    echo "<option value='-1'></option>";
                    for($i = 1; $i <= 24; $i++) {
                        echo "<option value='$i'>". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
                :
                <select name="starttimehour">
                    <?php
                    echo "<option value='-1'></option>";
                    for($i = 0; $i <= 59; $i++) {
                        echo "<option value='$i'>". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
            </p>
            <p>
                After first trigger,
                <table border="0">
                <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>continue triggering every</td><td></td><td><input type="text" name="period" id="period" /> seconds</td></tr>
                <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>with a randomness of<td>&plusmn;</td></td><td><input type="text" name="randomness" id="randomness" /> seconds.</td></tr>
                </table>

            </p>
            <p>
                On trigger,
                <select name="resptype" id="resptype" onchange="handleRespChange();">
                    <?php
                    $q = mysql_query("SELECT * FROM `resptypes`");
                    $descarr = array();
                    for($i = 0;;$i++) {
                        $type = mysql_fetch_object($q);
                        if(!$type) break;
                        echo "<option value='". $type->id ."'>". $type->friendlyname ."</option>";
                        $descarr[$i] = $type->description;
                    }
                    ?>
                </select>
                <?php
                $i = 0;
                foreach($descarr as $desc) {
                    echo "<p style='display:none;' id='$i'>". $desc ."</p>";
                    $i++;
                }
                ?>
            </p>
            <p>
                <span class="block" id="respDesc">
                <?php echo $descarr[0]; ?>
                </span>
                <span class="block">Parameters:
                <center>
                    <textarea name="parameters" rows="8" style="width:95%;"></textarea>
                </center></span>
            </p>
            <p>
                After triggering,
                <table border="0" id="linktable">
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Force routine to trigger</td>
                    <td></td>
                    <td><select name="autolink" id="autolink" onchange="handleAutolinkChange();">
                            <option value="-1">&nbsp;&nbsp;&nbsp;&nbsp;</option>
                            <?php
                            $q = mysql_query("SELECT * FROM `autonomous`");
                            while($auto = mysql_fetch_object($q)) {
                                echo "<option value='". $auto->id ."'>". $auto->name ."</option>";
                            }
                            ?>
                    </select></td>
                </tr>
                <tr style="display: none;">
                    <td></td>
                    <td>after</td>
                    <td></td>
                    <td>
                        <input type="text" name="timeout" id="timeout" /> seconds
                    </td>
                </tr>
                <tr style="display: none;">
                    <td></td>
                    <td>with a randomness of</td>
                    <td>&plusmn;</td>
                    <td>
                        <input type="text" name="torandom" id="torandom" /> seconds.
                    </td>
                </tr>
                <tr style="display: none;">
                    <td></td>
                    <td>While waiting to trigger, </td>
                    <td></td>
                    <td>
                        <select name="respond" id="respond">
                            <option value="0">do not respond to messages</option>
                            <option value="1">do respond to messages</option>
                        </select>.
                    </td>
                </tr>
                </table>
            </p>
            <p>
                <input type="button" name="addResponse" value="Add Autonomous Routine" onclick="evaluateCondition();" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="window.location.href = 'auto.php';" />
            </p>
        </form>
    <?php } else if($_GET["do"]=="edit") {
    $response = mysql_fetch_object(mysql_query("SELECT * FROM `responses` WHERE `id`=".$_GET['id']));
    ?>
        <legend>Edit Response</legend>
        <form method="post" action="" id="resp">
            <p>
                then
                <select name="resptype" id="resptype" onchange="handleRespChange();">
                    <?php
                    $q = mysql_query("SELECT * FROM `resptypes`");
                    $descarr = array();
                    for($i = 0;;$i++) {
                        $type = mysql_fetch_object($q);
                        if(!$type) break;
                        echo "<option value='". $type->id ."'";
                        if($type->id==$response->respid)
                            echo " selected='selected'";
                        echo ">". $type->friendlyname ."</option>";
                        $descarr[$i] = $type->description;
                    }
                    ?>
                </select>
                <?php
                $i = 0;
                foreach($descarr as $desc) {
                    echo "<p style='display:none;' id='$i'>". $desc ."</p>";
                    $i++;
                }
                ?>
            </p>
            <p>
                <span class="block" id="respDesc">
                    <?php
                    echo $d = mysql_fetch_object(mysql_query("SELECT * FROM `resptypes` WHERE `id`=". $response->respid))->description;
                    ?>
                    </span>
                    <span class="block">Parameters:
                    <center>
                        <textarea name="parameters" rows="8" style="width:95%;"><?php echo $response->parameters; ?></textarea>
                    </center>
                </span>
            </p>
            <p>
                Cooldown:
                <select name="cdd" id="cdd" onchange="coolChange();">
                    <option value="0">Default</option>
                    <option value="1"<?php if($response->cooldown != -1) { ?> selected="selected"<?php } ?>>Custom</option>
                </select>
                <input type="textbox" name="cooldown" id="cooldown" size="6" value="<?php if($response->cooldown == -1) echo $config->cooldown; else echo $response->cooldown; ?>"<?php if($response->cooldown == -1) { ?> disabled="disabled"<?php } ?> /> seconds
            </p>
            <p>
                <input type="button" name="editResponse" value="Edit Response" onclick="evaluateCondition();" />
                <input type="hidden" name="editId" value="<?php echo $_GET['id']; ?>" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="window.location.href = 'resp.php';" />
            </p>
        </form>
        <script type="text/javascript">
            redrawList();
        </script>
    <?php } ?>
    </fieldset>
    </center>
<?php include("footer.php"); ?>