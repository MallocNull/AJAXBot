<?php include("conn.php");

// ALTER TABLE `autonomous` CHANGE `starttime` `starttime` VARCHAR( 6 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '-1,-1'

if($_GET['del']) {
    mysql_query("DELETE FROM `autonomous` WHERE `id`=".$_GET['del']);
    header("Location: auto.php");
}

if($_POST["editId"]) {
    $stime = ($_POST['starttimehour'] == -1 || $_POST['starttimemin'] == -1 || $_POST['startday'] == -2) ? "-1,-1" : $_POST['starttimehour'] .",". $_POST['starttimemin'];
    mysql_query("UPDATE `autonomous` SET `name`='". mysql_real_escape_string($_POST['arname']) ."', `startday`=". $_POST['startday'] .", `starttime`='". $stime ."', `periodicity`=". $_POST['period'] .", `randomness`=". $_POST['randomness'] .", `respid`=". $_POST['resptype'] .", `parameters`='". mysql_real_escape_string($_POST['parameters']) ."', `autolink`=". $_POST['autolink'] .", `linkrespond`=". (($_POST["respond"]?"1":"0")) .", `timeout`=". $_POST['timeout'] .", `torandomness`=". $_POST['torandom'] ." WHERE `id`=". $_POST['editId']) or die(mysql_error());
    mysql_query("UPDATE `updater` SET `autonomous`=1 WHERE `id`=1");
    header("Location: auto.php");
}

if($_POST["resptype"] && !$_POST["editId"]) {
    $stime = ($_POST['starttimehour'] == -1 || $_POST['starttimemin'] == -1 || $_POST['startday'] == -2) ? "-1,-1" : $_POST['starttimehour'] .",". $_POST['starttimemin'];
    if($_POST["autolink"] == -1)
        mysql_query("INSERT INTO `autonomous` (`name`,`startday`,`starttime`,`periodicity`,`randomness`,`respid`,`parameters`) VALUES ('". mysql_real_escape_string($_POST['arname']) ."',". $_POST['startday'] .",'". $stime ."',". $_POST['period'] .",". $_POST['randomness'] .",". $_POST['resptype'] .",'". mysql_real_escape_string($_POST['parameters']) ."')") or die(mysql_error());
    else
        mysql_query("INSERT INTO `autonomous` (`name`,`startday`,`starttime`,`periodicity`,`randomness`,`respid`,`parameters`,`autolink`,`linkrespond`,`timeout`,`torandomness`) VALUES ('". mysql_real_escape_string($_POST['arname']) ."',". $_POST['startday'] .",'". $stime ."',". $_POST['period'] .",". $_POST['randomness'] .",". $_POST['resptype'] .",'". mysql_real_escape_string($_POST['parameters']) ."',". $_POST['autolink'] .",". (($_POST["respond"]?"1":"0")) .",". $_POST['timeout'] .",". $_POST['torandom'] .")") or die(mysql_error());
    mysql_query("UPDATE `updater` SET `autonomous`=1 WHERE `id`=1");
    header("Location: auto.php");
}

include("header.php");
?>
    <script type="text/javascript">
        /*var defaultCool = <?php echo $config->cooldown; ?>;*/

        function confirmDeletion(id) {
            var q = confirm("Are you sure you want to delete this autonomous routine?");
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
                if(document.getElementById("startday").selectedIndex <= 2 || (document.getElementById("startday").selectedIndex > 2 && document.getElementById("sth").selectedIndex != 0 && document.getElementById("stm").selectedIndex != 0)) {
                    if(doTimeTest("Periodicity",document.getElementById("period").value)) {
                        if(doTimeTest("Randomness",document.getElementById("randomness").value,true)) {
                            if(document.getElementById("autolink").selectedIndex != 0) {
                                if(doTimeTest("Link timeout",document.getElementById("timeout").value)) {
                                    if(doTimeTest("Link randomness",document.getElementById("torandom").value,true)) {
                                        document.getElementById("auto").submit();
                                    }
                                }
                            } else {
                                document.getElementById("auto").submit();
                            }
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

        function handleStartDayChange() {
            if(document.getElementById("startday").selectedIndex > 1)
                document.getElementById("stdisp").style.display = "inline";
            else
                document.getElementById("stdisp").style.display = "none";
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
            $q = mysql_query("SELECT * FROM `autonomous` ORDER BY `name`");
            while($resp = mysql_fetch_object($q)) {
                echo "
                        <table border='0' style='width:790px;border:1px solid black;margin:5px;'>
                            <tr>
                                <td style='width:50px;text-align:center;verticle-align:middle;'>
                                    <a href='auto.php?do=edit&id=". $resp->id ."' style='verticle-align: middle;'><img src='img/edit.png' border='0' /></a>
                                    &nbsp;<img src='img/delete.png' border='0' class='fakelink' onclick='confirmDeletion(". $resp->id .");' />
                                </td>
                                <td>
                                    ". $resp->name ."
                                </td>
                            </tr>
                        </table>";
            }
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
                <select name="startday" id="startday" onchange="handleStartDayChange();">
                    <option value="-2">after first cooldown</option>
                    <option value="-999">never</option>
                    <option value="-1">day of program start</option>
                    <option value="1">Sunday</option>
                    <option value="2">Monday</option>
                    <option value="3">Tuesday</option>
                    <option value="4">Wednesday</option>
                    <option value="5">Thursday</option>
                    <option value="6">Friday</option>
                    <option value="7">Saturday</option>
                </select>
                <span id="stdisp" style="display: none;">
                at
                <select name="starttimehour" id="sth">
                    <?php
                    echo "<option value='-1'></option>";
                    for($i = 0; $i <= 23; $i++) {
                        echo "<option value='$i'>". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
                :
                <select name="starttimemin" id="stm">
                    <?php
                    echo "<option value='-1'></option>";
                    for($i = 0; $i <= 59; $i++) {
                        echo "<option value='$i'>". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
                </span>
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
                            $q = mysql_query("SELECT * FROM `autonomous` ORDER BY `name`");
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
    $autono = mysql_fetch_object(mysql_query("SELECT * FROM `autonomous` WHERE `id`=".$_GET['id']));
    ?>
        <legend>Edit Autonomous Routine</legend>
        <form method="post" action="" id="auto">
            <p>
                Friendly name: <input type="text" name="arname" id="arname" value="<?php echo escapeDoubleQuotes($autono->name); ?>" /> (for future reference)
            </p>
            <p>
                Trigger first routine on
                <select name="startday" id="startday" onchange="handleStartDayChange();">
                    <option value="-2"<?php if($autono->startday == -2) { ?> selected="selected" <?php } ?>>after first cooldown</option>
                    <option value="-999"<?php if($autono->startday == -999) { ?> selected="selected" <?php } ?>>never</option>
                    <option value="-1"<?php if($autono->startday == -1) { ?> selected="selected" <?php } ?>>day of program start</option>
                    <option value="1"<?php if($autono->startday == 1) { ?> selected="selected" <?php } ?>>Sunday</option>
                    <option value="2"<?php if($autono->startday == 2) { ?> selected="selected" <?php } ?>>Monday</option>
                    <option value="3"<?php if($autono->startday == 3) { ?> selected="selected" <?php } ?>>Tuesday</option>
                    <option value="4"<?php if($autono->startday == 4) { ?> selected="selected" <?php } ?>>Wednesday</option>
                    <option value="5"<?php if($autono->startday == 5) { ?> selected="selected" <?php } ?>>Thursday</option>
                    <option value="6"<?php if($autono->startday == 6) { ?> selected="selected" <?php } ?>>Friday</option>
                    <option value="7"<?php if($autono->startday == 7) { ?> selected="selected" <?php } ?>>Saturday</option>
                </select>
                <span id="stdisp"<?php if($autono->startday == -2) { ?> style="display: none;"<?php } ?>>
                at
                <select name="starttimehour" id="sth">
                    <?php
                    $explodeTime = explode(",", $autono->starttime);
                    echo "<option value='-1'". ($explodeTime[0] == "-1"?" selected='selected'":"") ."></option>";
                    for($i = 0; $i <= 23; $i++) {
                        echo "<option value='$i'". ($explodeTime[0] == "".$i?" selected='selected'":"") .">". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
                :
                <select name="starttimemin" id="stm">
                    <?php
                    echo "<option value='-1'". ($explodeTime[1] == "-1"?" selected='selected'":"") ."></option>";
                    for($i = 0; $i <= 59; $i++) {
                        echo "<!-- iteration $i looking for ". $explodeTime[1] ." from string ". $autono->starttime ." -->";
                        echo "<option value='$i'". ($explodeTime[1] == "".$i?" selected='selected'":"") .">". (($i<10)?"0":"") ."$i</option>";
                    }
                    ?>
                </select>
                </span>
            </p>
            <p>
                After first trigger,
            <table border="0">
                <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>continue triggering every</td><td></td><td><input type="text" name="period" id="period" value="<?php echo $autono->periodicity; ?>" /> seconds</td></tr>
                <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>with a randomness of<td>&plusmn;</td></td><td><input type="text" name="randomness" id="randomness" value="<?php echo $autono->randomness; ?>" /> seconds.</td></tr>
            </table>

            </p>
            <p>
                On trigger,
                <select name="resptype" id="resptype" onchange="handleRespChange();">
                    <?php
                    $q = mysql_query("SELECT * FROM `resptypes`");
                    $descarr = array();
                    $descdef = "";
                    for($i = 0;;$i++) {
                        $type = mysql_fetch_object($q);
                        if(!$type) break;
                        if($autono->respid==$type->id) $descdef = $type->description;
                        echo "<option value='". $type->id ."'". ($autono->respid==$type->id?" selected='selected'":"") .">". $type->friendlyname ."</option>";
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
                <?php echo $descdef; ?>
                </span>
                <span class="block">Parameters:
                <center>
                    <textarea name="parameters" rows="8" style="width:95%;"><?php echo $autono->parameters; ?></textarea>
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
                            <option value="-1"<?php if($autono->autolink == -1) { ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;</option>
                            <?php
                            $q = mysql_query("SELECT * FROM `autonomous` ORDER BY `name`");
                            while($auto = mysql_fetch_object($q)) {
                                echo "<option value='". $auto->id ."'". ($autono->autolink == $auto->id?"selected='selected'":"") .">". $auto->name ."</option>";
                            }
                            ?>
                        </select></td>
                </tr>
                <tr<?php if($autono->autolink == -1) { ?> style="display: none;"<?php } ?>>
                    <td></td>
                    <td>after</td>
                    <td></td>
                    <td>
                        <input type="text" name="timeout" id="timeout" value="<?php echo $autono->timeout; ?>" /> seconds
                    </td>
                </tr>
                <tr<?php if($autono->autolink == -1) { ?> style="display: none;"<?php } ?>>
                    <td></td>
                    <td>with a randomness of</td>
                    <td>&plusmn;</td>
                    <td>
                        <input type="text" name="torandom" id="torandom" value="<?php echo $autono->torandomness; ?>" /> seconds.
                    </td>
                </tr>
                <tr<?php if($autono->autolink == -1) { ?> style="display: none;"<?php } ?>>
                    <td></td>
                    <td>While waiting to trigger, </td>
                    <td></td>
                    <td>
                        <select name="respond" id="respond">
                            <option value="0"<?php if($autono->linkrespond == 0) { ?> selected="selected" <?php } ?>>do not respond to messages</option>
                            <option value="1"<?php if($autono->linkrespond == 1) { ?> selected="selected" <?php } ?>>do respond to messages</option>
                        </select>.
                    </td>
                </tr>
            </table>
            </p>
            <p>
                <input type="button" name="editResponse" value="Edit Autonomous Routine" onclick="evaluateCondition();" />
                <input type="hidden" name="editId" value="<?php echo $_GET['id']; ?>" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick="window.location.href = 'auto.php';" />
            </p>
        </form>
    <?php } ?>
    </fieldset>
    </center>
<?php include("footer.php"); ?>