<?php
class Page {

    public $migrationsUsers = array();
    public $migrationsRoles = array(0 => "CREATE TABLE IF NOT EXISTS positivity_roles (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(16) NOT NULL
    );");

    public function __construct($name, $header = true) {
        $settings = json_decode(file_get_contents("./include/settings.txt"), true);
        $this->settings = $settings;
        if(!(isset($settings["init"]) && $settings["init"] == "true")){
            header("Location: ./error/no-config.php");
            die();
        }
        include("./include/connect.php");
        if(!$isConnect && $name != "connect"){
            header("Location: ./connection.php");
            die();
        }

        require_once './lang/' . $settings["lang"] . '.php';
        $this->lang = new Lang();

        $this->info = Info::create($this, $name);
        if($this->info == null){
            header("Location: ./error/404.php");
            die();
        }

        $this->page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page']; // user input
            if (filter_var($page, FILTER_VALIDATE_INT)) {
                $this->page = max(0, (int)$page);
            }
        }
        $this->te = $this;
        $this->conn = new PDO('mysql:host=' . $settings["host"] . ':' . $settings["port"] . ';dbname=' . $settings["database"], $settings["username"], $settings["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Load history before everything
        try {
            foreach (array("positivity_user" => $this->migrationsUsers, "positivity_roles" => $this->migrationsRoles) as $subsystem => $migrations) {
                $this->checkMigrations($subsystem, $migrations);
            }
            // here we have to add all change according to the result
            $checkBanSt = $this->conn->prepare("SELECT version FROM negativity_migrations_history WHERE subsystem LIKE 'bans/%' ORDER BY version DESC");
            $checkBanSt->execute();
            $checkBansRows = $checkBanSt->fetchAll(PDO::FETCH_ASSOC);
            $this->has_bans = count($checkBansRows) > 0;
        } catch (PDOException $ex) {
            return header("Location: ./error/no-negativity.php");
            //die ('Erreur : ' . $ex->getMessage());
        }

        if($name == "ban" && !$this->has_bans) {
            header("Location: ./error/feature-disabled.php");
            exit();
        }
        // Now load table informations
        if($this->info->getTableName() != ""){
            $sh = $this->conn->prepare("DESCRIBE `" . $this->info->getTableName() . "`");
            $this->valid_table = $sh->execute();
            $sh->closeCursor();
        }
        $this->uuid_name_cache = array();

    }

    function checkMigrations($subsystem, $migrations) {
        try {
            $st = $this->conn->prepare("SELECT version FROM negativity_migrations_history WHERE subsystem = ? ORDER BY version DESC LIMIT 1");
            $st->execute(array($subsystem));
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $has = count($rows) > 0;
            // now manage migrations
            $version = $has ? $rows[0]["version"] : -1; // -1 is to be sure to be lower that migrations versions
            $nextVersion = $version;
            foreach ($migrations as $migrationVersion => $migrationRequest) {
                if($version >= $migrationVersion)
                    continue;
                try {
                    $this->conn->prepare($migrationRequest)->execute();
                } catch (PDOException $ex) {
                    // ignoring, this should be because of already migrated things
                    print_r($ex); // print to be sure
                }
                if($migrationVersion > $nextVersion)
                    $nextVersion = $migrationVersion;
            }
            if($version != $nextVersion) { // change version into DB
                if($has)
                    $this->conn->prepare("UPDATE negativity_migrations_history SET version = ? WHERE subsystem = ?")->execute(array($nextVersion, $subsystem));
                else
                    $this->conn->prepare("INSERT INTO negativity_migrations_history(subsystem, version) VALUES(?,?)")->execute(array($subsystem, $nextVersion));
            }
            $st->closeCursor();
        } catch (PDOException $ex) {
            header("Location: ./error/no-negativity.php");
            //die ('Erreur : ' . $ex->getMessage());
        }
    }

    function print_common_head() {
        ?>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <link href="./include/css/main.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="./include/img/favicon.png"/>
        <?php
    }

    function run_query() {
        if($this->info->getTableName() == ""){
            return null;
        }
        try {
            $table = $this->info->getTableName();
            $limit = $this->settings["limit_per_page"];
            $offset = 0;
            $page = $this->page - 1;
            $offset = ($limit * $page);
            $st = $this->conn->prepare("SELECT * FROM $table LIMIT $limit OFFSET $offset"); // TODO add ordering
            $st->execute();
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $st->closeCursor();
            return $rows;
        } catch (PDOException $ex) {
            return header("Location: ./error/no-negativity.php");
            // die ('Erreur : ' . $ex->getMessage());
        }
    }

    function clean($text) {
        if ($text === null) return null;
        if (strstr($text, "\xa7") || strstr($text, "&")) {
            $text = preg_replace("/(?i)(\x{00a7}|&)[0-9A-FK-OR]/u", "", $text);
        }
        $text = htmlspecialchars($text, ENT_QUOTES, "UTF-8");
        if (strstr($text, "\n")) {
            $text = preg_replace("/\n/", "<br>", $text);
        }
        return $text;
    }

    function print_no_row() {
        echo "<br>";
        echo "<h2>" . $this->msg($this->info->getLink() . ".empty") . "</h2>";
        echo "<p>" . $this->msg("index.empty") . "</p>";
        echo "<br>";
    }

    function print_row($row){
        if(!isset($this->isFirstRow)){
            $this->isFirstRow = true;
        }
        $array = $this->info->getInfos($row);
        if($this->isFirstRow){
            echo "<thead><tr>";
            foreach ($array as $key => $value) {
                if($this->endsWith($key, "_double")){
                    if($this->endsWith($key, "_1_double")){
                        echo "<th colspan=2>" . $this->msg("column." . str_replace("_1_double", "", $key)) . "</th>";
                    } else {
                        // ignore because it's empty column
                    }
                } else
                    echo "<th>" . $this->msg("column." . $key) . "</th>";
            }
            echo "</th></thead><tbody>\n";
        }

        echo "<tr>\n";
        foreach ($array as $key => $value) {
            echo "<td>$value</td>\n";
        }

        echo "</tr>\n";
        $this->isFirstRow = false;
    }

    function getNavbar(){
        return array("bans" => new BanInfo($this), "accounts" => new AccountInfo($this), "verifications" => new VerificationInfo($this));
    }

    function show_header(){
        require_once("./include/header.php");
        show($this);
    }

    function show_footer(){
        require_once("./include/footer.php");
    }

    function show_page_mover(){
        echo "</table>";
        $page = $this->info->getLink();
        $pages = (int) ($this->info->getNumber() / $this->settings["limit_per_page"]) + 1;

        $prev_active = $this->page > 1;
        $next_active = $this->page < $pages;

        $cur = $this->page;
        $prev = $cur - 1;
        $next = $cur + 1;

        $pager_prev = ($prev_active ? "<a href=\"$page.php?page={$prev}\" class=\"pager-active\" style=\"font-size: 20px;\">«</a>" : "");
        $pager_next = ($next_active ? "<a href=\"$page.php?page={$next}\" class=\"pager-active\" style=\"font-size: 20px;\">»</a>" : "");

        echo '<div class="pager-full">
                ' . $pager_prev . '
                <a class="pager-number">
                    ' . $this->msg("table.pager.number") . ' ' . $cur . '/' . $pages . '
                </a>
                ' . $pager_next . '
            </div>';

        /*
        $prev_class = "negativity-" . ($prev_active ? "pager-active" : "pager-inactive");
        $next_class = "negativity-" . ($next_active ? "pager-active" : "pager-inactive");
        $pager_prev = "<div class=\"negativity-pager negativity-pager-left $prev_class\">«</div>";
        if ($prev_active) {
            $pager_prev = "<a href=\"$page?page={$prev}\">$pager_prev</a>";
        }

        $pager_next = "<div class=\"negativity-pager negativity-pager-right $next_class\">»</div>";
        if ($next_active) {
            $pager_next = "<a href=\"$page?page={$next}\">$pager_next</a>";
        }
        $pager_count = '<div><div class="negativity-pager-number">' . $this->msg("table.pager.number") . ' ' . $cur . '/' . $pages . '</div></div>';
        echo "$pager_prev $pager_next $pager_count";*/
    }

    function getDateFromMillis($millis) {
        $ts = $millis / 1000;
        $result = strftime("%B %d, %Y, %H:%M", $ts);
        return $result;
    }

    function msg($key, $defaultMsg = null){
        if(isset($this->lang->array[$key]))
            return $this->lang->array[$key];
        else {
            if($defaultMsg == null)
                return $key . " (not set)";
            else
                return $defaultMsg;
        }
    }

    function get_uuid($playerName){
        if($playerName == "Negativity" || $playerName == "Console")
            return $uuid;
        if ($playerName === null || $playerName === "")
            return null;

        foreach ($this->uuid_name_cache as $uuid => $name) {
            if($name == $playerName){
                return $uuid;
            }
        }

        $result = null;

        $st = $this->conn->prepare("SELECT * FROM negativity_accounts WHERE playername = ?;");
        if ($st->execute(array($playerName)) && $row = $st->fetch()) {
            $result = $row['id'];
        }
        $st->closeCursor();

        $this->uuid_name_cache[$result] = $playerName;
        return $result;
    }

    function get_name($uuid, $default_name = null) {
        if($uuid == "Negativity" || $uuid == "Console")
            return $uuid;
        if ($uuid === null || $uuid === "" || strrpos($uuid, "#", -strlen($uuid)) !== false) {
            return $default_name;
        }
        if (array_key_exists($uuid, $this->uuid_name_cache)) return $this->uuid_name_cache[$uuid];

        $result = null;

        $st = $this->conn->prepare("SELECT * FROM negativity_accounts WHERE id = ?;");
        if ($st->execute(array($uuid)) && $row = $st->fetch()) {
            $result = $row['playername'];
        }
        $st->closeCursor();

        $this->uuid_name_cache[$uuid] = $result;
        return $result;
    }

    function get_avatar($name, $uuid) {
        if (strcasecmp($name, "Negativity") == 0 || strcasecmp($uuid, "Negativity") == 0){
            return "<p align='center'><img class='avatar noselect' src='./include/img/negativity.png'/><br class='noselect'>$name</p>";
        } else if (strcasecmp($name, "Console") == 0 || strcasecmp($uuid, "Console") == 0){
            return "<p align='center'><img class='avatar noselect' src='./include/img/console.png'/><br class='noselect'>$name</p>";
        } else {
            $avatar_source = "https://crafatar.com/avatars/{uuid}?size=25";

            if (strlen($uuid) === 36 && $uuid[14] === '3') {
                $avatar_source = "https://minotar.net/avatar/{name}/25";
            }

            $uuid = str_replace("-", "", $uuid);
            $src = str_replace('{name}', $name, str_replace('{uuid}', $uuid, $avatar_source));

            $img = "<img class='avatar noselect' src='$src'/>";
            return "<a href='./check.php?uuid=$uuid'><p align='center'>{$img}<br class='noselect'>$name</p></a>";
        }
    }

    function parse_version_name($version){
        return str_replace("_", ".", str_replace("V", "", $version));
    }

    function endsWith($haystack, $needle) {
        return str_replace($needle, "", $haystack) != $haystack;
    }

    function countAllViolation($viol_by_cheat){
        $nb = 0;
        foreach (explode(";", $viol_by_cheat) as $allCheat) {
            $tab = explode("=", $allCheat, 2);
            foreach ($tab as $cheat) {
                if(isset($tab[1]) && is_numeric($tab[1])) {
                    $nb = $nb + $tab[1];
                }
            }
        }
        return $nb;
    }

    function addColorFromResult($str) {
        $str = "<span>" . $str;
        $codes = array(
            "§0" => "#000000",
            "§1" => "#0000AA",
            "§2" => "#00AA00",
            "§3" => "#00AAAA",
            "§4" => "#AA0000",
            "§5" => "#AA00AA",
            "§6" => "#FFAA00",
            "§7" => "#AAAAAA",
            "§8" => "#555555",
            "§9" => "#5555FF",
            "§a" => "#55FF55",
            "§b" => "#55FFFF",
            "§c" => "#FF5555",
            "§d" => "#FF55FF",
            "§e" => "#FFFF55",
            "§f" => "#FFFFFF",

            "&0" => "#000000",
            "&1" => "#0000AA",
            "&2" => "#00AA00",
            "&3" => "#00AAAA",
            "&4" => "#AA0000",
            "&5" => "#AA00AA",
            "&6" => "#FFAA00",
            "&7" => "#AAAAAA",
            "&8" => "#555555",
            "&9" => "#5555FF",
            "&a" => "#55FF55",
            "&b" => "#55FFFF",
            "&c" => "#FF5555",
            "&d" => "#FF55FF",
            "&e" => "#FFFF55",
            "&f" => "#FFFFFF"
        );
        foreach ($codes as $key => $value){
            $str = str_replace($key, '</span><span style="color: ' . $value . '">', $str);
        }
        return $str . "</span>";
    }
}

abstract class Info {

    public function __construct($page) {
        $this->page = $page;
    }

    static function create($page, $type) {
        switch ($type) {
            case "verification":
                return new VerificationInfo($page);
            case "verification_check":
                return new VerificationCheckInfo($page);
            case "account":
                return new AccountInfo($page);
            case "admin":
                return new AdminInfo($page);
            case "ban":
                return new BanInfo($page);
            case "check":
                return new CheckInfo($page);
            case "connect":
                return new ConnectInfo($page);
            case "index":
                return new IndexInfo($page);
        }
        return null;
    }

    abstract function getTableName();

    function getNumber(){
        try {
            $st = $this->page->conn->prepare("SELECT COUNT(*) as nb FROM " . $this->getTableName());
            $st->execute();
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            $st->closeCursor();
            return $rows[0]["nb"];
        } catch (PDOException $ex) {
            return header("Location: ./error/no-negativity.php");
            // die ('Erreur : ' . $ex->getMessage());
        }
    }

    abstract function getLink();

    abstract function getInfos($row);
}

class AccountInfo extends Info {

    function getTableName(){
        return "negativity_accounts";
    }

    function getLink(){
        return "accounts";
    }

    function getInfos($row) {
        return array("name" => $this->page->get_avatar($row["playername"], $row["id"]),
                    "lang" => $row["language"],
                    "minerate_full" => $row["minerate_full_mined"],
                    //"minerate" => $row["minerate"], CANNOT SHOW ON LIST ACCOUNT INFO
                    "most_clicks" => $row["most_clicks_per_second"],
                    "violations" => $this->page->countAllViolation($row["violations_by_cheat"]),
                    "verifications" => $this->getVerifNumber($row["id"]),
                    "creation_time" => $row["creation_time"]
        );
    }

    function getVerifNumber($uuid){
        $result = 0;
        $st = $this->page->conn->prepare("SELECT COUNT(*) as nb FROM negativity_verifications WHERE uuid = ?;");
        if ($st->execute(array($uuid)) && $row = $st->fetch()) {
            $result = $row['nb'];
        }
        $st->closeCursor();
        return $result;
    }
}

class AdminInfo extends Info {

    function getTableName(){
        return "positivity_user";
    }

    function getLink(){
        return "admin";
    }

    function getInfos($row) {
        return array("username" => $row["username"],
            "admin" => $row["admin"],
            "special" => $row["special"],
            "options" => $row["options"]);
    }
}

class BanInfo extends Info {

    function getTableName(){
        return $this->page->has_bans ? "negativity_bans_active" : "";
    }

    function getLink(){
        return "bans";
    }

    function getInfos($row) {
        $page = $this->page;
        return array("name" => $page->get_avatar($page->get_name($row["id"]), $row["id"]),
                    "reason" => $row["reason"],
                    "banned_by" => $page->get_avatar($page->get_name($row["banned_by"]), $row["banned_by"]),
                    "expiration_time" => $page->getDateFromMillis($row["expiration_time"]),
                    "cheat_name" => $page->msg($row["cheat_name"], $row["cheat_name"]),
        );
    }
}

class CheckInfo extends Info {

    function getTableName(){
        return "";
    }

    function getLink(){
        return "checks";
    }

    function getInfos($row) {
        return array("name" => $this->page->get_avatar($row["playername"], $row["id"]),
                    "lang" => $row["language"],
                    "minerate_full" => $row["minerate_full_mined"],
                    //"minerate" => $row["minerate"], CANNOT SHOW ON LIST ACCOUNT INFO
                    "most_clicks" => $row["most_clicks_per_second"],
                    "violations" => $this->page->countAllViolation($row["violations_by_cheat"]),
                    "verifications" => $this->getVerifNumber($row["id"]),
                    "creation_time" => $row["creation_time"]
        );
    }

    function getVerifNumber($uuid){
        $result = 0;
        $st = $this->page->conn->prepare("SELECT COUNT(*) as nb FROM negativity_verifications WHERE uuid = ?;");
        if ($st->execute(array($uuid)) && $row = $st->fetch()) {
            $result = $row['nb'];
        }
        $st->closeCursor();
        return $result;
    }
}

class ConnectInfo extends Info {

    function getTableName(){
        return "positivity_user";
    }

    function getLink(){
        return "connection";
    }

    function getInfos($row) {
        return array("username" => $row["username"],
            "admin" => $row["admin"],
            "special" => $row["special"],
            "options" => $row["options"]);
    }
}

class IndexInfo extends Info {

    function getTableName(){
        return "";
    }

    function getLink(){
        return "checks";
    }

    function getInfos($row) {
        return array();
    }
}

class VerificationInfo extends Info {

    function getTableName(){
        return "negativity_verifications";
    }

    function getLink(){
        return "verifications";
    }

    function getInfos($row) {
        $page = $this->page;
        $uuid = $row["uuid"];
        return array("name" => $page->get_avatar($page->get_name($uuid), $uuid),
                    "started_by" => $page->get_avatar($row["startedBy"], $page->get_uuid($row["startedBy"])),
                    "player_version" => $page->parse_version_name($row["player_version"]),
                    "amount" => $this->getVerifNumber($uuid),
                    "more_info_1_double" => '<a href="./verifications_check.php?id=' . $row["id"] . '">' . $page->msg("generic.see_more") . '</a>',
                    "more_info_2_double" => '<a href="./verifications_check.php?uuid=' . str_replace("-", "", $uuid) . '">' . $page->msg("generic.see_all") . '</a>');
    }

    function getVerifNumber($uuid){
        $result = 0;
        $st = $this->page->conn->prepare("SELECT COUNT(*) as nb FROM negativity_verifications WHERE uuid = ?;");
        if ($st->execute(array($uuid)) && $row = $st->fetch()) {
            $result = $row['nb'];
        }
        $st->closeCursor();
        return $result;
    }
}

class VerificationCheckInfo extends Info {

    function getTableName(){
        return "";
    }

    function getLink(){
        return "verifications";
    }

    function getInfos($row) {
        return array();
    }
}