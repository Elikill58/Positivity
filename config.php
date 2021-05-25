<?php
if(isset($_POST["host"]) && isset($_POST["port"]) && isset($_POST["database"]) && isset($_POST["link"])
        && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["lang"]) && isset($_POST["servername"])
        && isset($_POST["webusername"]) && isset($_POST["webpassword"])) {
    $host = $_POST["host"];
    $port = $_POST["port"];
    $database = $_POST["database"];
    $link = $_POST["link"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $lang = $_POST["lang"];
    $serverName = $_POST["servername"];

    $webUsername = $_POST["webusername"];
    $webPassword = $_POST["webpassword"];

    $limit_per_page = $_POST["limit_per_page"];

    $content = json_encode(array("init" => true, "host" => $host, "port" => $port, "database" => $database,
                "link" => $link, "username" => $username, "password" => $password, "lang" => $lang, "server_name" => $serverName,
                "limit_per_page" => $limit_per_page));
    file_put_contents("./include/settings.txt", $content);
    file_put_contents("./include/user.txt", json_encode(array($webUsername => json_encode(array("password" => hash("sha256", $webPassword), "admin" => true, "special" => "un_removable")))));
    header("Location: ./index.php");
    exit();
}

if(file_exists("./include/settings.txt")) {
    $tempSettings = json_decode(file_get_contents("./include/settings.txt"), true);
    if(isset($tempSettings["init"]) && $tempSettings["init"] == "true"){
        header("Location: ./error/already-config.php");
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Negativity - Configuration</title>
    <link href="./include/css/main.css" rel="stylesheet">
</head>
<body>
    <div class="container solo">
        <h2>Configuration</h2>
        <p>Welcome to the Negativity web interface!</p><br>
        <form id="config" method="POST" action="./config.php">
            <div class="table-config">
                <div class="table-left">
                    <div class="row">
                        <label for="host">The database IP: </label>
                        <input type="text" name="host">
                    </div>
                    <div class="row">
                        <label for="port">The database port: </label>
                        <input type="text" name="port" value="3306">
                    </div>
                    <div class="row">
                        <label for="database">Database name: </label>
                        <input type="text" name="database">
                    </div>
                    <div class="row">
                        <label for="username">User name: </label>
                        <input type="text" name="username" placeholder="root">
                    </div>
                    <div class="row">
                        <label for="password">User password: </label>
                        <input type="password" name="password" placeholder="myPassword">
                    </div>
                    <div class="row">
                        <label for="link">Link (on header): </label>
                        <input type="text" name="link" value="/">
                    </div>
                    <div class="row">
                        <label for="servername">Server name (show on header): </label>
                        <input type="text" name="servername" placeholder="MyServer">
                    </div>
                    <div class="row select-container">
                        <label for="lang">Choose lang: </label>
                        <select name="lang">
                            <option value="en_US" selected="selected">Lang</option>
                            <?php
                            foreach (scandir("./lang/") as $value) {
                                $langName = explode(".", $value)[0];
                                if($langName != "")
                                    echo '<option value="' . str_replace(".php", "", $value) . '">' . $langName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="table-right">
                    <div class="row">
                        <label for="web-username">Web admin user name: </label>
                        <input type="text" name="webusername">
                    </div>
                    <div class="row">
                        <label for="web-password">Web admin password: </label>
                        <input type="password" name="webpassword">
                    </div>
                    <div class="row number-wrapper">
                        <label for="limit_per_page">Limit per page: </label>
                        <input class="number-input" type="number" name="limit_per_page" value="15">
                    </div>
                </div>
            </div>
            <br/>
            <button style="width:40%;" class="btn-outline" type="submit" form="config" value="Submit"><div class="text">Save config</div></button>
        </form>
    </div>
</body>
</html>
