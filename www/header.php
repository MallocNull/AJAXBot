<html>
<head>
    <title>AJAX Bot Administration</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<center>
    <h1>Control Panel</h1>
    <h4>
        <?php
            $request = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/")+1);
            $request = strtolower(substr($request, 0, strrpos($request, ".")));

            if(!checkIfLoggedIn()) header("Location: index.php?err=Session invalid.");
        ?>
        <?php if($request == "" || $request == "index") { ?>Home<?php } else { ?><a href="index.php">Home</a><?php } ?> |
        <?php if($request == "config") { ?>Configuration<?php } else { ?><a href="config.php">Configuration</a><?php } ?> |
        <?php if($request == "resp") { ?>Responses<?php } else { ?><a href="resp.php">Responses</a><?php } ?> |
        <?php if($request == "auto") { ?>Autonomous<?php } else { ?><a href="auto.php">Autonomous</a><?php } ?> |
        <a href="jews.php">Logout</a>
    </h4>
</center>