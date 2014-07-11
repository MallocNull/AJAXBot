<?php include("conn.php");
include("header.php"); ?>
<script type="text/javascript">
    function handleRespChange() {
        document.getElementById("respDesc").innerHTML = document.getElementById(""+document.getElementById("resptype").selectedIndex).innerHTML;
    }

    function redrawList() {
        var selectedValues = Array();
        var tmpr = document.getElementById("ifholder").children;
        var tmp = Array();
        for(i = 0; i < tmpr.length; i++) {
            selectedValues[i*4] = tmpr[i].children[0].selectedIndex;
            selectedValues[i*4+1] = tmpr[i].children[1].selectedIndex;
            selectedValues[i*4+2] = tmpr[i].children[2].value;
            if(tmpr[i].children.length == 7)
                selectedValues[i*4+3] = tmpr[i].children[3].selectedIndex;
            else
                selectedValues[i*4+3] = -1;
            tmp[i] = tmpr[i].cloneNode(true);
        }
        document.getElementById("ifholder").innerHTML = "";

        for(i = 0; i < tmp.length; i++) {
            tmp[i].setAttribute("id","if"+(i+1));
            var tmpc = tmp[i].children;
            tmpc[0].name = "if"+ (i+1) +"group";
            tmpc[0].selectedIndex = selectedValues[0];
            tmpc[1].name = "if1"
            document.getElementById("navContainer").appendChild(tmp[i]);
        }
    }

    function addCondition() {
        var cond = document.createElement("span");
        cond.setAttribute("id","if1");
        cond.setAttribute("class","block")
        cond.innerHTML = '<select name="if1group">' +
            '<?php for($i = 1; $i < 10; $i++) echo "<option value=\"$i\">$i</option>"; ?>' +
            '</select>' +
            ' <select name="if1cond">' +
            '<?php $q = mysql_query("SELECT * FROM `conditions`"); while($cond = mysql_fetch_object($q)) { echo "<option value=\"". $cond->id ."\">". $cond->friendlyname ."</option>"; } ?>' +
            '</select>' +
            ' <input type="text" name="if1param" />' +
            ' <img src="img/arrow_up.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowUp(1);" />' +
            ' <img src="img/arrow_down.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowDown(1);" />' +
            ' <img src="img/delete.png" class="fakelink" style="vertical-align: text-bottom;" onclick="handleRowDelete(1);" />'
        document.getElementById("ifholder").appendChild(cond);
        //redrawList();
    }
</script>
    <center>
        <fieldset class="wide" style="padding-bottom: 0;">
            <?php if(!$_GET["do"]) { ?>
                <legend>Response List</legend>
                <p style="margin-top: 0;"><a href="resp.php?do=new">New Response</a></p>

            <?php } else if($_GET["do"]=="new") { ?>
                <legend>Create New Response</legend>
                <p>
                    If
                    <span id="ifholder">
                        <span id="if1" class="block">
                            <select name="if1group">
                                <?php
                                    for($i = 1; $i < 10; $i++)
                                        echo "<option value=\"$i\">$i</option>";
                                ?>
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
                    <input type="submit" name="addResponse" value="Add Response" />
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Cancel" onclick="window.location.href = 'resp.php';" />
                </p>
            <?php } else if($_GET["do"]=="edit") { ?>
                <legend>Edit Response</legend>
            <?php } ?>
        </fieldset>
    </center>
<?php include("footer.php"); ?>