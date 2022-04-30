<?php
$isConnect = false;
require_once('./include/page.php');

$page = new Page("connect");
    
$message = "";
$wrongResult = false;

if(isset($_POST["pseudo"]) AND isset($_POST["password"])){
    $pseudo = $_POST["pseudo"];
    $password = $_POST["password"];
    $allUser = $page->run_query();
    foreach ($allUser as $userContent) {
        if ($userContent["username"] == $pseudo) {
            if (hash('sha256', $password) == $userContent["password"]) {
                if(session_status() == PHP_SESSION_NONE)
                    session_start();
                
                $_SESSION = array();
                $_SESSION["name"] = $pseudo;
                $_SESSION["is_admin"] = $userContent["admin"];
                $isConnect = true;
                header('Location: ./');
                exit();
                //$message = $page->msg("connection.well");
            } else {
                $message = $page->msg("connection.wrong_pass");
            }
        }
    }
    if($message == "") {
        $message = $page->msg("connection.wrong_name");
        $wrongResult = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Negativity - Connection</title>
    <link href="./include/css/main.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script>
    function togglePasswordVisibility() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
    </script>
</head>
<body>
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
                        <div class="input" style="display: flex; width: 100%;">
                            <i class="material-icons">person</i>
                            <input style="border: none;" type="text" name="pseudo" id="pseudo" placeholder="<?php echo $page->msg("connection.form.login"); ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="input" style="display: flex; width: 100%;">
                            <i class="material-icons">lock</i>
                            <input style="border: none;" type="password" name="password" id="password" placeholder="<?php echo $page->msg("connection.form.password"); ?>" required />
                            <i class="material-icons" onclick="togglePasswordVisibility()" style="cursor: pointer;">visibility</i>
                        </div>
                    </div>
                </div>
                <br/>
                <button type="Submit" value="Submit" name="" id="formsend" class="btn-outline"><div class="text"><?php echo $page->msg("connection.form.confirm"); ?></div></button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>