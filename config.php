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

    $conn = new PDO('mysql:host=' . $host . ':' . $port . ';dbname=' . $database, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $content = json_encode(array("init" => true, "host" => $host, "port" => $port, "database" => $database,
                "link" => $link, "username" => $username, "password" => $password, "lang" => $lang, "server_name" => $serverName,
                "limit_per_page" => $limit_per_page));
    exec("chmod -R 0777 ./include");
    file_put_contents("./include/settings.txt", $content);
    chmod("./include/settings.txt", 0755);

    $hisCreate = $conn->prepare("INSERT INTO negativity_migrations_history (version, subsystem) VALUES (0, 'positivity_user')");
    $hisCreate->execute();

    $hisCreate = $conn->prepare("CREATE TABLE IF NOT EXISTS positivity_user (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(16) NOT NULL,
        password TEXT NOT NULL,
        admin BOOLEAN NOT NULL,
        special VARCHAR(256) NOT NULL
    );");
    $hisCreate->execute();

    $userDel = $conn->prepare("INSERT INTO positivity_user (username, password, admin, special) VALUES (?,?,?,?);");
    $userDel->execute(array($webUsername, hash("sha256", $webPassword), true, "un_removable"));
    $userDel->closeCursor();

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
    <?php $page->print_common_head(); ?>
    <script type="text/javascript">
        function checkDbConnection(argument) {
            var host = document.getElementById("host").value;
            var port = document.getElementById("port").value;
            var database = document.getElementById("database").value;
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            if(host == "" || database == "" || username == "") {
                document.getElementById("db-result").innerHTML = "Database field not filled";
            } else {
                document.getElementById("db-result").innerHTML = "Checking ...";
                var xhr = new XMLHttpRequest();
                // we defined the xhr
                xhr.onreadystatechange = function () {
                    if (this.readyState != 4) return;

                    if (this.status == 200) {
                        document.getElementById("db-result").innerHTML = this.responseText;
                    }

                    // end of state change: it can be after some time (async)
                };

                xhr.open("POST", "./include/tester.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("request=bdd&host=" + host + "&port=" + port + "&database=" + database + "&username=" + username + "&password=" + password);
            }
        }


    </script>
    <style type="text/css">
        label {
            padding-top: 10px;
        }
    </style>
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
                        <input type="text" name="host" id="host" required>
                    </div>
                    <div class="row">
                        <label for="port">The database port: </label>
                        <input type="text" name="port" id="port" value="3306" required>
                    </div>
                    <div class="row">
                        <label for="database">Database name: </label>
                        <input type="text" name="database" id="database" required>
                    </div>
                    <div class="row">
                        <label for="username">User name: </label>
                        <input type="text" name="username" id="username" placeholder="root" required>
                    </div>
                    <div class="row">
                        <label for="password">User password: </label>
                        <input type="password" name="password" id="password" placeholder="myPassword">
                    </div>
                    <div class="row">
                        <div></div>
                        <button class="btn-outline" type="button" onclick="checkDbConnection()" style="padding: 5px 15px;"><div class="text">Check database connection</div></button>
                    </div>
                    <div class="row">
                        <div></div>
                        <div id="db-result"></div>
                    </div>
                </div>
                <div class="table-right">
                    <div class="row">
                        <label for="link">Link (on header): </label>
                        <input type="text" name="link" value="/">
                    </div>
                    <div class="row">
                        <label for="servername">Server name (on header): </label>
                        <input type="text" name="servername" placeholder="MyServer" required>
                    </div>
                    <div class="row">
                        <label for="web-username">Web admin user name: </label>
                        <input type="text" name="webusername" required>
                    </div>
                    <div class="row">
                        <label for="web-password">Web admin password: </label>
                        <input type="password" name="webpassword" required>
                    </div>
                    <div class="row number-wrapper">
                        <label for="limit_per_page">Limit per page: </label>
                        <input class="number-input" type="number" name="limit_per_page" value="15">
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
            </div>
            <br/>
            <button style="width:40%;" class="btn-outline" type="submit" form="config" value="Submit"><div class="text">Save config</div></button>
        </form>
    </div>
</body>
</html>
