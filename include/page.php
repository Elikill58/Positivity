<?php
class Page {

    public $migrationsUsers = array(1 => "ALTER TABLE positivity_user ADD COLUMN role INT(11) AFTER admin");
    public $migrationsRoles = array(0 => "CREATE TABLE IF NOT EXISTS positivity_roles (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(16) NOT NULL,
        perm_bans VARCHAR(16) NOT NULL DEFAULT 'none',
        perm_bans_logs VARCHAR(16) NOT NULL DEFAULT 'none',
        perm_accounts VARCHAR(16) NOT NULL DEFAULT 'none',
        perm_verifications VARCHAR(16) NOT NULL DEFAULT 'none',
        perm_admin_users VARCHAR(16) NOT NULL DEFAULT 'none',
        perm_admin_roles VARCHAR(16) NOT NULL DEFAULT 'none'
    );");

    public function __construct($pageName, $header = true) {
        $settings = json_decode(file_get_contents("./include/settings.txt"), true);
        $this->settings = $settings;
        if(!(isset($settings["init"]) && $settings["init"] == "true")){
            header("Location: ./error/no-config.php");
            die();
        }
        include("./include/connect.php");
        if(!$isConnect && $pageName != "connect"){
            header("Location: ./connection.php");
            die();
        }

        require_once './lang/' . $settings["lang"] . '.php';
        $this->lang = new Lang();

        $this->info = Info::create($this, $pageName);
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

        // now check for permissions
        $this->role = array();
        if(isset($_SESSION['role'])) {
            $roleSt = $this->conn->prepare("SELECT * FROM positivity_roles WHERE id = ?");
            $roleSt->execute(array($_SESSION["role"]));
            $roleRows = $roleSt->fetchAll(PDO::FETCH_ASSOC);
            if(count($roleRows) > 0) {
                $this->role = $roleRows[0];
            }
            $roleSt->closeCursor();
        }
        $perm = $this->info->getPermissionPrefix();
        if($perm != null) {
            if(!$this->hasPermission($perm, "SEE")) { // can't see this page
                header("Location: ./error/access-denied.php");
                exit();
            }
        }

        if(($pageName == "bans" || $pageName == "bans_logs") && !$this->has_bans) {
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

    function hasPermission($perm, $searching) {
        return (isset($this->role["perm_" . $perm]) && strcasecmp($this->role["perm_" . $perm], $searching) == 0) || (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1);
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
            die('Erreur : ' . $ex->getMessage());
        }
    }

    function print_common_head() {
        ?>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <link href="./include/css/main.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="./include/img/favicon.png"/>
        <title>Positivity - <?php echo $this->msg("title." . $this->info->getLink()) ?></title>
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

    function print_row($row, $info = null){
        if($info == null)
            $info = $this->info;
        if(!isset($this->isFirstRow)){
            $this->isFirstRow = true;
        }
        $array = $info->getInfos($row);
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
        return array("bans" => new BanInfo($this), "bans_logs" => new BanLogsInfo($this), "accounts" => new AccountInfo($this), "verifications" => new VerificationInfo($this), "admin_users" => new AdminUsersInfo($this), "admin_roles" => new AdminRolesInfo($this));
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

    function is_special_name($name) {
        return strcasecmp($name, "Negativity") == 0 || strcasecmp($name, "Console") == 0;
    }

    function get_uuid($playerName){
        if($this->is_special_name($playerName))
            return $playerName;
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

    function is_uuid($uuid) {
        return strlen($uuid) == 36 && preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $uuid);
    }

    function parse_uuid($uuid) {
        if($this->is_uuid($uuid)) // if valid UUID
            return $uuid;
        else if ($this->is_special_name($uuid))
            return $uuid;
        $nextUUID = "";
        $i = 1;
        foreach (str_split($uuid) as $char) {
            $nextUUID .= $char;
            if($i == 8 || $i == 12 || $i == 16 || $i == 20)
                $nextUUID .= "-";
            $i++;
        }
        return $nextUUID;
    }

    function get_name($uuid, $default_name = null) {
        if($this->is_special_name($uuid))
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
            if($uuid != null && strlen($uuid) == 32) // UUID without -
                $uuid = $this->parse_uuid($uuid);
            $avatar_source = $this->is_uuid($uuid) ? "https://crafatar.com/avatars/" . $uuid . "?size=25" : "https://minotar.net/avatar/" . $name . "/25";

            return "<a href='./check.php?uuid=" . str_replace("-", "", $uuid) . "'><p align='center'><img class='avatar noselect' src='" . $avatar_source . "'/><br class='noselect'>" . $name . "</p></a>";
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
            case "admin_users":
                return new AdminUsersInfo($page);
            case "admin_roles":
                return new AdminRolesInfo($page);
            case "bans":
                return new BanInfo($page);
            case "bans_logs":
                return new BanLogsInfo($page);
            case "check":
                return new CheckInfo($page);
            case "connect":
                return new ConnectInfo($page);
            case "index":
                return new IndexInfo($page);
        }
        return null;
    }

    function getPermissionPrefix() {
        return $this->getLink();
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

class AdminUsersInfo extends Info {

    function getTableName(){
        return "positivity_user";
    }

    function getLink(){
        return "admin_users";
    }

    function getInfos($row) {
        $roleId = $row["role"];
        $roleName = "-";
        if(isset($roleId) && $roleId > 0) {
            $roleSt = $this->page->conn->prepare("SELECT * FROM positivity_roles WHERE id = ?");
            $roleSt->execute(array($row["role"]));
            $roleRow = $roleSt->fetchAll(PDO::FETCH_ASSOC);
            if(count($roleRow) > 0)
                $roleName = $roleRow[0]["name"];
        }
        $infos = array("user_name" => $row["username"],
            "is_admin" => ($this->page->msg($row["admin"] ? "yes" : "no")),
            "role_name" => $roleName,
            "special" => $this->page->msg("admin.special." . (isset($row["special"]) ? $row["special"] : "nothing")));
        if($this->page->hasPermission("admin_users", "EDIT")) {
            $btn = '<button class="btn-outline">';
            $infos = array_merge($infos, array("options" => $row["special"] != "un_removable" ? '<form action="./admin_users.php" method="POST"><input type="hidden" name="id" value="' . $row["id"] . '">' . $btn . $this->page->msg("generic.delete") . '</button></form>' : "-"));
        }
        return $infos;
    }
}

class AdminRolesInfo extends Info {

    public $rolePermGeneral = array("none", "see", "edit");
    public $rolePermAccounts = array("none", "see", "edit", "clear");

    function getTableName(){
        return "positivity_roles";
    }

    function getLink(){
        return "admin_roles";
    }

    function getInfos($row) {
        $infos = array("role_name" => $row["name"],
            "bans" => $this->getValue("bans", $row, $this->rolePermGeneral),
            "bans_logs" => $this->getValue("bans_logs", $row, $this->rolePermGeneral),
            "accounts" => $this->getValue("accounts", $row, $this->rolePermAccounts),
            "verifications" => $this->getValue("verifications", $row, $this->rolePermGeneral),
            "admin_users" => $this->getValue("admin_users", $row, $this->rolePermGeneral),
            "admin_roles" => $this->getValue("admin_roles", $row, $this->rolePermGeneral)
        );
        if($this->page->hasPermission("admin_roles", "EDIT")) {
            $btn = '<button class="btn-outline" name="action"';
            $infos = array_merge($infos, array("options" => '<input type="hidden" name="id" value="' . $row["id"] . '">' . $btn . ' value="save">' . $this->page->msg("generic.save") . '</button>
                        <input type="hidden" name="id" value="' . $row["id"] . '">' . $btn . ' value="delete">' . $this->page->msg("generic.delete") . '</button>'));
        }
        return $infos;
    }

    function getValue($name, $row, $rolePerm) {
        $content = '<select name="' . $name . '" class="custom-select custom-select-sm" style="width:150px;">';
        foreach ($rolePerm as $perm) {
            $content .= '<option value="' . $perm . '" ' . (strcasecmp($row["perm_" . $name], $perm) == 0 ? 'selected="selected"' : '') . '>' . $this->page->msg("role." . $perm) . '</option>';
        }
        $content .= "</select>";
        return $content;
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
        $infos = array("name" => $page->get_avatar($page->get_name($row["id"]), $row["id"]),
                    "reason" => $row["reason"],
                    "banned_by" => $page->get_avatar($page->get_name($page->parse_uuid($row["banned_by"])), $row["banned_by"]),
                    "expiration_time" => $page->getDateFromMillis($row["expiration_time"]),
                    "cheat_name" => $page->msg($row["cheat_name"], $row["cheat_name"]),
        );
        if($this->page->hasPermission("admin_roles", "EDIT")) {
            $infos = array_merge($infos, array("options" => '<form action="./bans.php" method="POST"><input type="hidden" name="id" value="' . $row["id"] . '"><button class="btn-outline">' . $this->page->msg("generic.delete") . '</button></form>'));
        }
        return $infos;
    }
}

class BanLogsInfo extends Info {

    function getTableName(){
        return $this->page->has_bans ? "negativity_bans_log" : "";
    }

    function getLink(){
        return "bans_logs";
    }

    function getInfos($row) {
        $page = $this->page;
        return array("name" => $page->get_avatar($page->get_name($row["id"]), $row["id"]),
                    "reason" => $row["reason"],
                    "banned_by" => $page->get_avatar($page->get_name($page->parse_uuid($row["banned_by"])), $row["banned_by"]),
                    "revocation_time" => $row["revocation_time"],
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

    function getPermissionPrefix() {
        return null;
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

    function getPermissionPrefix() {
        return null;
    }

    function getLink(){
        return "index";
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