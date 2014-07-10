<?php include("conn.php");
include("header.php"); ?>
<center>
    <fieldset class="normal" style="padding-bottom: 0;">
        <legend>General Configuration</legend>
        <center>
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
                    <tr><td style="text-align: right;">Bot Name:</td><td><input type="text" name="username" value="<?php echo $config->name; ?>" /></td></tr>
                    <tr><td></td><td><input type="submit" name="changeConfig" value="Modify" /></td></tr>
                </table>
            </form>
        </center>
    </fieldset>
</center>
<?php include("footer.php"); ?>