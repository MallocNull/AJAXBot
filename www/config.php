<?php include("conn.php");

if($_POST["changeConfig"]) {
    if(is_numeric($_POST["cooldown"]) && $_POST["cooldown"] > 0 && $_POST["cooldown"]) {
        if(strlen($_POST["tzHour"]) == 2 && is_numeric($_POST["tzHour"]) && strlen($_POST["tzMins"]) == 2 && is_numeric($_POST["tzMins"]) && $_POST["tzHour"] && $_POST["tzMins"]) {
            if(trim($_POST["name"])) {
                if(trim($_POST["username"])) {
                    mysql_query("UPDATE `config` SET
                                `cooldown`='". $_POST['cooldown'] ."',
                                `name`='". mysql_real_escape_string(trim($_POST['name'])) ."',
                                `username`='". mysql_real_escape_string(trim($_POST['username'])) ."',
                                `dst`=". (($_POST['dst'])?"1":"0") .",
                                `timezone`='". $_POST['tzSign'] . $_POST['tzHour'] .":". $_POST['tzMins'] ."',
                                `parsechatbot`=". (($_POST['chatbot'])?"1":"0") ."
                                WHERE `id`=1") or die(mysql_error());
                    mysql_query("UPDATE `updater` SET `config`=1 WHERE `id`=1");
                    $config = mysql_fetch_object(mysql_query("SELECT * FROM `config` WHERE `id`=1"));
                } else {
                    $configerr = "Chat username is empty!";
                }
            } else {
                $configerr = "Bot name is empty!";
            }
        } else {
            $configerr = "Timezone formatted incorrectly!";
        }
    } else {
        $configerr = "Cooldown is not a positive integer!";
    }
}

$rowCount = mysql_num_rows(mysql_query("SELECT * FROM `navigate`"));

include("header.php"); ?>
<script type="text/javascript">
    var rowCount = <?php echo $rowCount; ?>;

    function handleColumnChange(r, c) {
        if(c == 1) {
            if(document.getElementById("r"+r+"c1").selectedIndex > 0) {
                document.getElementById("r"+r+"c3").style.display = "inline";
                if(document.getElementById("r"+r+"c3").selectedIndex > 0)
                    document.getElementById("r"+r+"c4").style.display = "inline";
            } else {
                document.getElementById("r"+r+"c3").style.display = "none";
                document.getElementById("r"+r+"c4").style.display = "none";
            }
        } else {
            if(document.getElementById("r"+r+"c3").selectedIndex > 0)
                document.getElementById("r"+r+"c4").style.display = "inline";
            else
                document.getElementById("r"+r+"c4").style.display = "none";
        }
    }

    function redrawTable() {
        var selectedValues = Array();
        var tmpr = document.getElementById("navContainer").children;
        var tmp = Array();
        for(i = 0; i < tmpr.length; i++) {
            selectedValues[i*4] = tmpr[i].children[0].selectedIndex;
            selectedValues[i*4+1] = tmpr[i].children[1].value;
            selectedValues[i*4+2] = tmpr[i].children[2].selectedIndex;
            selectedValues[i*4+3] = tmpr[i].children[3].value;
            tmp[i] = tmpr[i].cloneNode(true);
        }
        document.getElementById("navContainer").innerHTML = "";

        for(i = 0; i < tmp.length; i++) {
            if(tmp[i].getAttribute("id") != "r"+(i+1)) {
                tmp[i].setAttribute("id","r"+(i+1));
                tmp[i].innerHTML = (i+1) + tmp[i].innerHTML.substr(tmp[i].innerHTML.indexOf("."));
                var tmpc = tmp[i].children;
                for(j = 0; j < 4; j++) {
                    tmpc[j].setAttribute("id","r"+(i+1)+"c"+(j+1));
                    tmpc[j].setAttribute("name","r"+(i+1)+"c"+(j+1));
                    if(j%2==0 && j < 4) {
                        tmpc[j].selectedIndex = (j==0)?selectedValues[i*4]:selectedValues[i*4+2];
                        tmpc[j].setAttribute("onchange","handleColumnChange("+(i+1)+","+(j+1)+");");
                    } else if(j < 4) {
                        tmpc[j].value = (j==1)?selectedValues[i*4+1]:selectedValues[i*4+3];
                    } else if(j == 4)
                        tmpc[j].setAttribute("onchange","handleRowUp("+(i+1)+");");
                    else if(j == 4)
                        tmpc[j].setAttribute("onchange","handleRowDown("+(i+1)+");");
                    else if(j == 4)
                        tmpc[j].setAttribute("onchange","handleRowDelete("+(i+1)+");");
                }
            }
            document.getElementById("navContainer").appendChild(tmp[i]);
        }
    }

    function handleRowUp(r) {
        if(r != 1) {
            var data = Array();
            var child = document.getElementById("r"+r);
            data[0] = child.children[0].selectedIndex;
            data[0] = child.children[0].selectedIndex;
            data[0] = child.children[0].selectedIndex;
            data[0] = child.children[0].selectedIndex;
            var clone = child.cloneNode();
            document.getElementById("navContainer").removeChild();
        }
    }

    function handleAddRow() {
        if(rowCount == 0) {
            var element = document.createElement("option");
            element.text = "after";
            document.getElementById("addSelect").add(element);

            element = document.createElement("option");
            element.text = "before";
            document.getElementById("addSelect").add(element);
        }
        rowCount++;
        var placementHint = document.getElementById("addSelect").selectedIndex;
        var placementPos = document.getElementById("addPosition").selectedIndex;

        var holder = document.createElement("p");
        holder.setAttribute("id","r1");
        holder.innerHTML = "1. <select name='r1c1' id='r1c1' onchange='handleColumnChange(1,1);'>" +
            "<option value='0'>Go to URL</option>" +
            "<option value='1'>Find element by link text</option>" +
            "<option value='2'>Find element by ID</option>" +
            "<option value='3'>Find element by name</option>" +
            "<option value='4'>Find element by class</option>" +
            "<option value='5'>Find element by tag name</option>" +
            "</select>" +
            " <input type='text' name='r1c2' id='r1c2' />" +
            " <select name='r1c3' id='r1c3' onchange='handleColumnChange(1,3);' style='display: none;'>" +
            "<option value='0'>and click it.</option>" +
            "<option value='1'>and type</option>" +
            "<option value='2'>and select the value</option>" +
            "<option value='3'>and select the index</option>" +
            "</select>" +
            " <input type='text' name='r1c4' id='r1c4' style='display: none;' />" +
            " <img src='img/arrow_up.png' class='fakelink' style='vertical-align: text-bottom;' onclick='' />" +
            " <img src='img/arrow_down.png' class='fakelink' style='vertical-align: text-bottom;' onclick='' />" +
            " <img src='img/delete.png' class='fakelink' style='vertical-align: text-bottom;' onclick='' />";

        switch(placementHint) {
            case 0:
                document.getElementById("navContainer").appendChild(holder);
                break;
            case 1:
                if(rowCount == 0)
                    document.getElementById("navContainer").appendChild(holder);
                else
                    document.getElementById("navContainer").insertBefore(holder, document.getElementById("r1"));
                break;
            case 2:
                if(rowCount == placementPos+1)
                    document.getElementById("navContainer").appendChild(holder);
                else
                    document.getElementById("navContainer").insertBefore(holder, document.getElementById("r"+(placementPos+2)));
                break;
            case 3:
                document.getElementById("navContainer").insertBefore(holder, document.getElementById("r"+(placementPos+1)));
                break;
            default:
                alert("wat");
                break;
        }
        redrawTable();
        populateRowDropdown();
    }

    function handleAddSelectChange() {
        var selected = document.getElementById("addSelect").selectedIndex;
        if(selected > 1)
            document.getElementById("addPosition").style.display = "inline";
        else
            document.getElementById("addPosition").style.display = "none";
    }
</script>
<center>
    <fieldset class="normal" style="padding-bottom: 0;">
        <legend>General Configuration</legend>
        <center>
            <?php if($configerr) { echo "<h4 style='color: red;margin-top:0;'>$configerr</h4>"; } ?>
            <form method="post" action="">
                <table border="0">
                    <tr><td style="text-align: right;">Default Cooldown:</td><td><input type="text" name="cooldown" size="6" value="<?php echo $config->cooldown; ?>" /> seconds</td></tr>
                    <tr><td style="text-align: right;">Parse ChatBot Messages:</td><td><select name="chatbot"><option value="1">Yes</option><option value="0"<?php if(!$config->parsechatbot) { ?> selected="true"<?php } ?>>No</option></select></td></tr>
                    <tr><td style="text-align: right;vertical-align: top;">Timezone:</td><td>
                            UTC
                            <select name="tzSign">
                                <option value="+">+</option>
                                <option value="-"<?php if(substr($config->timezone, 0, 1) == "-") { ?> selected="true"<?php } ?>>-</option>
                            </select>
                            <input type="text" name="tzHour" maxlength="2" value="<?php echo substr($config->timezone, 1, 2); ?>" style="width:20px;" />
                            :
                            <input type="text" name="tzMins" maxlength="2" value="<?php echo substr($config->timezone, 4); ?>" style="width:20px;" />
                            <br /><abbr title="Daylight Savings Time?">DST?</abbr> <input type="checkbox" name="dst"<?php if($config->dst) { ?> checked="true"<?php } ?> />
                        </td></tr>
                    <tr><td style="text-align: right;">Chat Username:</td><td><input type="text" name="username" value="<?php echo $config->username; ?>" /></td></tr>
                    <tr><td style="text-align: right;">Bot Name:</td><td><input type="text" name="name" value="<?php echo $config->name; ?>" /></td></tr>
                    <tr><td></td><td><input type="submit" name="changeConfig" value="Modify" /></td></tr>
                </table>
            </form>
        </center>
    </fieldset>
    <br />
    <fieldset class="wide">
        <legend>Navigation Instructions</legend>
        <p style="margin-top: 0;">In order for the bot to work, it needs to know how to both log into the website and get into the chat. The following is a user-defined method for the bot to perform this task.</p>
        <span id="navContainer">

        </span>
        <p style="margin-bottom: 0">Add instruction
            <select id="addSelect" onchange="handleAddSelectChange();">
                <option selected="selected">at the end</option>
                <option>at the beginning</option>
                <?php if($rowCount > 0) { ?>
                <option>after</option>
                <option>before</option>
                <?php } ?>
            </select>
            <select id="addPosition" style="display: none;">
            </select>
            <input type="button" value="Go" onclick="handleAddRow();" />
        </p>
    </fieldset>
</center>
<script type="text/javascript">
    function populateRowDropdown() {
        document.getElementById("addPosition").innerHTML = "";
        for(i = 0; i < rowCount; i++) {
            var opt = document.createElement("option");
            opt.text = "instruction "+ (i+1);
            document.getElementById("addPosition").add(opt);
        }
    }
    populateRowDropdown();
</script>
<?php include("footer.php"); ?>