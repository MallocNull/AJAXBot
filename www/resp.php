<?php include("conn.php");

function parseConditionString($str, $resptype) {
    $condtypes = array();
    $q = mysql_query("SELECT * FROM `conditions`");
    while($cond = mysql_fetch_object($q)) {
        $condtypes[$cond->id] = $cond->friendlyname;
    }

    $ret = "IF ";
    $conds = explode(";",$str);
    $conds = array_slice($conds, 0, count($conds)-1);
    foreach($conds as $cond) {
        $tk = explode(",",$cond);
        if(count($tk) > 3) {
            for($i = 0; $i < intval($tk[0]); $i++)
                $ret .= "(";
            if($tk[1] == "1")
                $ret .= "not ";
            $ret .= "<b>". $condtypes[intval($tk[2])] ."</b> ". $tk[3];
            for($i = 0; $i < intval($tk[4]); $i++)
                $ret .= ")";
            $ret .= " ";
        } else {
            if($tk[0] == 0) $ret .= "AND ";
            else if($tk[0] == 1) $ret .= "OR ";
        }
    }
    $ret .= "THEN <i>". strtolower($resptype) ."</i>";
    return $ret;
}

if($_GET['del']) {
    mysql_query("DELETE FROM `responses` WHERE `id`=".$_GET['del']);
    header("Location: resp.php");
}

if($_POST["editId"]) {
    $c = "";
    for($i=1;;$i++) {
        if(!isset($_POST["if". $i ."param"])) break;
        $c .= $_POST['if'.$i.'lpar'].",".$_POST['if'.$i.'not'].",".$_POST['if'.$i.'cond'].",".$_POST['if'.$i.'param'].",".$_POST['if'.$i.'rpar'].";";
        if(isset($_POST["op".$i])) $c .= $_POST["op".$i] .";";
    }

    mysql_query("UPDATE `responses` SET `conditions`='". mysql_real_escape_string($c) ."', `respid`=". $_POST['resptype'] .", `parameters`='". mysql_real_escape_string($_POST['parameters']) ."', `cooldown`=". (($_POST['cdd']==0)?-1:$_POST['cooldown']) ." WHERE `id`=". $_POST['editId']) or die(mysql_error());
    mysql_query("UPDATE `updater` SET `responses`=1 WHERE `id`=1");
    header("Location: resp.php");
}

if($_POST["resptype"] && !$_POST["editId"]) {
    $c = "";
    for($i=1;;$i++) {
        if(!isset($_POST["if". $i ."param"])) break;
        $c .= $_POST['if'.$i.'lpar'].",".$_POST['if'.$i.'not'].",".$_POST['if'.$i.'cond'].",".$_POST['if'.$i.'param'].",".$_POST['if'.$i.'rpar'].";";
        if(isset($_POST["op".$i])) $c .= $_POST["op".$i] .";";
    }

    mysql_query("INSERT INTO `responses` (`conditions`,`respid`,`parameters`,`cooldown`) VALUES ('". mysql_real_escape_string($c) ."',". $_POST['resptype'] .",'". mysql_real_escape_string($_POST['parameters']) ."',". (($_POST['ccd']==0)?-1:$_POST['cooldown']) .")") or die(mysql_error());
    mysql_query("UPDATE `updater` SET `responses`=1 WHERE `id`=1");
    header("Location: resp.php");
}

include("header.php");
?>
<script type="text/javascript">
    var defaultCool = <?php echo $config->cooldown; ?>;

    function confirmDeletion(id) {
        var q = confirm("Are you sure you want to delete this response?");
        if(q) window.location.href = "resp.php?del="+id;
    }

    function handleRespChange() {
        document.getElementById("respDesc").innerHTML = document.getElementById(""+document.getElementById("resptype").selectedIndex).innerHTML;
    }

    function coolChange() {
        if(document.getElementById("cdd").selectedIndex == 0) {
            document.getElementById("cooldown").disabled = true;
            document.getElementById("cooldown").value = defaultCool;
        } else
            document.getElementById("cooldown").disabled = false;
    }

    function redrawList() {
        var selectedValues = Array();
        var tmpr = document.getElementById("ifholder").children;
        if(tmpr[0].children.length < 3) {
            document.getElementById("ifholder").removeChild(tmpr[0]);
            tmpr = document.getElementById("ifholder").children;
        }
        var tmp = Array();
        for(i = 0; i < tmpr.length; i++) {
            if(tmpr[i].children.length > 3) {
                selectedValues[i*5] = tmpr[i].children[0].selectedIndex;
                selectedValues[i*5+1] = tmpr[i].children[1].selectedIndex;
                selectedValues[i*5+2] = tmpr[i].children[2].selectedIndex;
                selectedValues[i*5+3] = tmpr[i].children[3].value;
                selectedValues[i*5+4] = tmpr[i].children[4].selectedIndex;
            } else {
                selectedValues[i*5] = tmpr[i].children[0].selectedIndex;
                selectedValues[i*5+1] = "operator";
            }
            tmp[i] = tmpr[i].cloneNode(true);
        }
        document.getElementById("ifholder").innerHTML = "";

        var j = 1;
        for(i = 0; i < tmp.length; i++) {
            if(selectedValues[i*5+1] != "operator") {
                tmp[i].setAttribute("id","if"+j);
                var tmpc = tmp[i].children;
                tmpc[0].name = "if"+ j +"lpar";
                tmpc[0].selectedIndex = selectedValues[i*5];
                tmpc[1].name = "if"+ j +"not";
                tmpc[1].selectedIndex = selectedValues[i*5+1];
                tmpc[2].name = "if"+ j +"cond";
                tmpc[2].selectedIndex = selectedValues[i*5+2];
                tmpc[3].name = "if"+ j +"param";
                tmpc[3].value = selectedValues[i*5+3];
                tmpc[4].name = "if"+ j +"rpar";
                tmpc[4].selectedIndex = selectedValues[i*5+4];
                tmpc[5].setAttribute("onclick","handleRowUp("+ j +");");
                tmpc[6].setAttribute("onclick","handleRowDown("+ j +");");
                tmpc[7].setAttribute("onclick","handleRowDelete("+ j +");");
                if(i%2==1) {
                    if(document.getElementById("op"+ (j-1)) == null) {
                        var op = document.createElement("span");
                        op.setAttribute("id","op"+(j-1));
                        op.setAttribute("class","block");
                        op.innerHTML = "<select name='op"+ (j-1) +"'><option value='0'>and</option><option value='1'>or</option></select>";
                        document.getElementById("ifholder").appendChild(op);
                    }
                }
                j++;

                document.getElementById("ifholder").appendChild(tmp[i]);
                /*if(i != tmp.length)
                    tmp.innerHTML += "<br />";*/
            } else {
                if(i != tmp.length-1 && i != 0) {
                    if(selectedValues[(i-1)*5+1] != "operator") {
                        tmp[i].setAttribute("id","op"+(j-1));
                        tmp[i].children[0].name = "op"+(j-1);
                        tmp[i].children[0].selectedIndex = selectedValues[i*5];
                        document.getElementById("ifholder").appendChild(tmp[i]);
                    }
                }
            }
        }

        var childs = document.getElementById("ifholder").children;
        for(i = 0; i < childs.length; i++) {
            if((i%2==0 && childs[i].children.length < 3) || (i%2==1 && childs[i].children.length > 3)) {
                redrawList();
                break;
            }
        }
    }

    function addCondition() {
        var cond = document.createElement("span");
        cond.setAttribute("id","if1");
        cond.setAttribute("class","block");
        cond.innerHTML = '<select name="if1lpar">' +
            '<?php for($i = 0; $i < 6; $i++) { echo "<option value=\"$i\">"; for($j = 0; $j < $i; $j++) echo "("; echo "</option>"; } ?>' +
            '</select>' +
            ' <select name="if1not"><option value="0"></option><option value="1">not</option></select>' +
            ' <select name="if1cond">' +
            '<?php $q = mysql_query("SELECT * FROM `conditions`"); while($cond = mysql_fetch_object($q)) { echo "<option value=\"". $cond->id ."\">". $cond->friendlyname ."</option>"; } ?>' +
            '</select>' +
            ' <input type="text" name="if1param" />' +
            ' <select name="if1rpar">' +
            '<?php for($i = 0; $i < 6; $i++) { echo "<option value=\"$i\">"; for($j = 0; $j < $i; $j++) echo ")"; echo "</option>"; } ?>' +
            '</select>' +
            ' <img src="img/arrow_up.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowUp(1);" />' +
            ' <img src="img/arrow_down.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowDown(1);" />' +
            ' <img src="img/delete.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowDelete(1);" />'
        document.getElementById("ifholder").appendChild(cond);
        redrawList();
    }

    function handleRowUp(r) {
        if(r != 1) {
            var child = document.getElementById("if"+r);
            var clone = child.cloneNode(true);
            clone.children[0].selectedIndex = child.children[0].selectedIndex;
            clone.children[1].value = child.children[1].value;
            clone.children[2].selectedIndex = child.children[2].selectedIndex;
            clone.children[3].value = child.children[3].value;
            clone.children[4].selectedIndex = child.children[4].selectedIndex;
            document.getElementById("ifholder").removeChild(child);
            document.getElementById("ifholder").insertBefore(clone, document.getElementById("if"+(r-1)));
            redrawList();
        }
    }

    function handleRowDown(r) {
        if(r != (document.getElementById("ifholder").children.length+1)/2) {
            var child = document.getElementById("if"+r);
            var clone = child.cloneNode(true);
            clone.children[0].selectedIndex = child.children[0].selectedIndex;
            clone.children[1].value = child.children[1].value;
            clone.children[2].selectedIndex = child.children[2].selectedIndex;
            clone.children[3].value = child.children[3].value;
            clone.children[4].selectedIndex = child.children[4].selectedIndex;
            document.getElementById("ifholder").removeChild(child);
            document.getElementById("ifholder").insertBefore(clone, document.getElementById("if"+(r+2)));

            /*if(r+2 != (document.getElementById("ifholder").children.length+1)/2) {
             var opchild = document.getElementById("op"+r);
             var opclone = opchild.cloneNode(true);
             opclone.children[0].selectedIndex = opchild.children[0].selectedIndex;
             document.getElementById("ifholder").removeChild(opchild);
             document.getElementById("ifholder").insertBefore(opclone, document.getElementById("if"+(r+2)));
             }*/

            redrawList();
        }
    }

    function evaluateCondition() {
        var childs = document.getElementById("ifholder").children;
        var parens = 0;
        for(i = 0; i < childs.length; i+=2) {
            if(childs[i].children[3].value.trim() == "") {
                alert("Condition parameters cannot be empty!");
                return;
            }
            parens += childs[i].children[0].selectedIndex - childs[i].children[4].selectedIndex;
            if(parens < 0) {
                alert("There are mismatched parentheses!");
                return;
            }
        }
        if(parens != 0) {
            alert("There are mismatched parentheses!");
            return;
        }
        if(document.getElementById("cdd").selectedIndex == 1 && (document.getElementById("cooldown").value.trim() == "" || isNaN(document.getElementById("cooldown").value))) {
            alert("Custom cooldown must not be empty and must be a number!");
            return;
        }
        document.getElementById("resp").submit();
    }

    function handleRowDelete(r) {
        if(document.getElementById("ifholder").children.length > 1) {
            document.getElementById("ifholder").removeChild(document.getElementById("if"+r));
            var oper = document.getElementById("op"+r);
            if(oper != null) document.getElementById("ifholder").removeChild(oper);
            redrawList();
        } else {
            alert("You need at least one condition!");
        }
    }
</script>
    <center>
        <fieldset class="wide" style="padding-bottom: 0;">
            <?php if(!$_GET["do"]) { ?>
                <legend>Response List</legend>
                <p style="margin-top: 0;"><a href="resp.php?do=new">New Response</a></p>
                <center>
                <?php
                $q = mysql_query("SELECT * FROM `responses`");
                while($resp = mysql_fetch_object($q)) {
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
                }
                ?>
                </center>
            <?php } else if($_GET["do"]=="new") { ?>
                <legend>Create New Response</legend>
                <form method="post" action="" id="resp">
                    <p>
                        If
                        <span id="ifholder">
                            <span id="if1" class="block">
                                <select name="if1lpar">
                                    <?php
                                        for($i = 0; $i < 6; $i++) {
                                            echo "<option value=\"$i\">";
                                            for($j = 0; $j < $i; $j++)
                                                echo "(";
                                            echo "</option>";
                                        }
                                    ?>
                                </select>
                                <select name="if1not">
                                    <option value="0"></option>
                                    <option value="1">not</option>
                                </select>
                                <select name="if1cond">
                                    <?php
                                    $q = mysql_query("SELECT * FROM `conditions`");
                                    while($cond = mysql_fetch_object($q)) {
                                        echo "<option value='". $cond->id ."'>". $cond->friendlyname ."</option>";
                                    }
                                    ?>
                                </select>
                                <input type="text" name="if1param" />
                                <select name="if1rpar">
                                    <?php
                                    for($i = 0; $i < 6; $i++) {
                                        echo "<option value=\"$i\">";
                                        for($j = 0; $j < $i; $j++)
                                            echo ")";
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                                <img src='img/arrow_up.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowUp(1);' />
                                <img src='img/arrow_down.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowDown(1);' />
                                <img src='img/delete.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowDelete(1);' />
                            </span>
                        </span>
                        <span class="block">
                            <a href="javascript:addCondition();">Add Condition</a>
                        </span>
                    </p>
                    <p>
                        then
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
                        Cooldown:
                        <select name="cdd" id="cdd" onchange="coolChange();">
                            <option value="0">Default</option>
                            <option value="1">Custom</option>
                        </select>
                        <input type="textbox" name="cooldown" id="cooldown" size="6" value="<?php echo $config->cooldown; ?>" disabled="disabled" /> seconds
                    </p>
                    <p>
                        <input type="button" name="addResponse" value="Add Response" onclick="evaluateCondition();" />
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" value="Cancel" onclick="window.location.href = 'resp.php';" />
                    </p>
                </form>
            <?php } else if($_GET["do"]=="edit") {
                $response = mysql_fetch_object(mysql_query("SELECT * FROM `responses` WHERE `id`=".$_GET['id']));
                ?>
                <legend>Edit Response</legend>
                <form method="post" action="" id="resp">
                    <p>
                        If
                        <span id="ifholder">
                            <?php
                            $conds = mysql_fetch_object(mysql_query("SELECT * FROM `responses` WHERE `id`=". $_GET['id']))->conditions;
                            $conds = explode(";",$conds);
                            $conds = array_slice($conds, 0, count($conds)-1);
                            $on = 1;
                            foreach($conds as $cond) {
                                $tk = explode(",",$cond);
                                if(count($tk) > 3) { ?>
                                    <span id="if<?php echo $on; ?>" class="block">
                                        <select name="if<?php echo $on; ?>lpar">
                                            <?php
                                            for($i = 0; $i < 6; $i++) {
                                                echo "<option value=\"$i\"";
                                                if($i==intval($tk[0]))
                                                    echo " selected='selected'";
                                                echo ">";
                                                for($j = 0; $j < $i; $j++)
                                                    echo "(";
                                                echo "</option>";
                                            }
                                            ?>
                                        </select>
                                        <select name="if<?php echo $on; ?>not">
                                            <option value="0"></option>
                                            <option value="1"<?php if(intval($tk[1])==1) { ?> selected="selected"<?php } ?>>not</option>
                                        </select>
                                        <select name="if<?php echo $on; ?>cond">
                                            <?php
                                            $q = mysql_query("SELECT * FROM `conditions`");
                                            while($cond = mysql_fetch_object($q)) {
                                                echo "<option value='". $cond->id ."'";
                                                if($cond->id == intval($tk[2]))
                                                    echo " selected='selected'";
                                                echo ">". $cond->friendlyname ."</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="text" name="if<?php echo $on; ?>param" value="<?php echo escapeDoubleQuotes($tk[3]); ?>" />
                                        <select name="if<?php echo $on; ?>rpar">
                                            <?php
                                            for($i = 0; $i < 6; $i++) {
                                                echo "<option value=\"$i\"";
                                                if($i==intval($tk[4]))
                                                    echo " selected='selected'";
                                                echo ">";
                                                for($j = 0; $j < $i; $j++)
                                                    echo ")";
                                                echo "</option>";
                                            }
                                            ?>
                                        </select>
                                        <img src='img/arrow_up.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowUp(<?php echo $on; ?>);' />
                                        <img src='img/arrow_down.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowDown(<?php echo $on; ?>);' />
                                        <img src='img/delete.png' class='fakelink' style='vertical-align: text-bottom;' onclick='handleRowDelete(<?php echo $on; ?>);' />
                                    </span>
                                <?php for($i = 0; $i < intval($tk[0]); $i++)
                                        $ret .= "(";
                                    if($tk[1] == "1")
                                        $ret .= "not ";
                                    $ret .= $condtypes[intval($tk[2])] ." ". $tk[3];
                                    for($i = 0; $i < intval($tk[4]); $i++)
                                        $ret .= ")";
                                    $ret .= " ";
                                    $i++;
                                } else { ?>
                                    <span id="op<?php echo $i; ?>">
                                        <select name='op<?php echo $i; ?>'>
                                            <option value='0'>and</option>
                                            <option value='1'<?php if(intval($tk[0]==1)) { ?> selected="selected" <?php } ?>>or</option>
                                        </select>
                                    </span>
                                <?php }
                            }
                            ?>
                        </span>
                        <span class="block">
                            <a href="javascript:addCondition();">Add Condition</a>
                        </span>
                    </p>
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
                        </center></span>
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