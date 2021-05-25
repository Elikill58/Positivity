
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Negativity - Connection</title>
    <link href="./include/css/main.css" rel="stylesheet">
</head>
<body>
    <?php

    $isConnect = false;
    require_once('./include/page.php');

    $page = new Page("connect");

    $message = "";
    $wrongResult = false;

    if(isset($_POST["pseudo"]) AND isset($_POST["password"])){
        $pseudo = $_POST["pseudo"];
        $password = $_POST["password"];
        $allUser = json_decode(file_get_contents("./include/user.txt"), true);
        if (!isset($allUser[$pseudo])) {
            $message = $page->msg("connection.wrong_name");
            $wrongResult = true;
        } else {
            $userContent = json_decode($allUser[$pseudo], true);
            if (hash('sha256', $password) == $userContent["password"]) {
                if(session_status() == PHP_SESSION_NONE)
                    session_start();
                
                $_SESSION = array();
                $_SESSION["name"] = $pseudo;
                $_SESSION["is_admin"] = $userContent["admin"];
                $isConnect = true;
                $message = $page->msg("connection.well");
            } else {
                $message = $page->msg("connection.wrong_pass");
            }
        }
    }

    // $page->show_header();
    ?>
    <div class="container solo">
        <?php
        if($message != ""){
            ?>
            <div class="negativity-index negativity-index-sub" <?php if($wrongResult) echo 'style="color: red;"'; ?>><h2><?php echo $message; ?></h2></div>
            <?php
        }
        if($isConnect){
            ?>
            <a href="./">
                <div class="negativity-index negativity-index-sub">
                    <p><?php echo $page->msg("connection.back"); ?></p>
                </div>
            </a>
            <?php
        } else {
            ?>
            <div class="negativity-index negativity-index-main">
                <h1><?php echo $page->msg("connection.name"); ?></h1>
            </div>
            <form action="./connection.php" method="post" id="connection" class="text-center">
                
                <div class="table-center">
                    <div class="row">
                        <input class="" type="text" name="pseudo" id="pseudo" placeholder="<?php echo $page->msg("connection.form.login"); ?>" required></input>
                    </div>
                    <div class="row">
                        <input class="" type="text" name="password" id="password" placeholder="<?php echo $page->msg("connection.form.password"); ?>" required></input>
                    </div>
                </div>
                <br/>
                <button type="Submit" value="Submit" name="" id="formsend" class="btn-outline"><?php echo $page->msg("connection.form.confirm"); ?></button>
            </form>
            <?php
        }
        ?>
    </div>
    <?php $page->show_footer(); ?>

</body>
</html>