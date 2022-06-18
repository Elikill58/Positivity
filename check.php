<?php
require_once './include/page.php';
$page = new Page("check");

$search = (isset($_GET["search"]) ? $_GET["search"] : null);

$uuid = (isset($_GET["uuid"]) ? $_GET["uuid"] : null);
$name = (isset($_GET["name"]) ? $_GET["name"] : null);

if($search != null AND ($name == null AND $uuid == null)){ // if search
    if(preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $search)) { // search with UUID
        $uuid = $search;
        $name = $page->get_name($uuid);
    } else if(preg_match("/^[0-9A-F]{8}[0-9A-F]{4}[0-9A-F]{3}[89AB][0-9A-F]{3}[0-9A-F]{12}$/i", $search)) { // search with UUID that don't have '-'
        $search = "";
        $i = 1;
        foreach (str_split($search) as $char) {
            $search .= $char;
            if($i == 8 || $i == 12 || $i == 16 || $i == 20)
                $search .= "-";
            $i++;
        }
        $name = $page->get_name($uuid);
    } else { // give name
        $name = $search;
        $uuid = $page->get_uuid($name);
        if($uuid == null) { // failed to find UUID
            $name = null;
        }
    }
} else { // directly give a value
    if($uuid == null && $name != null)
        $uuid = $page->get_uuid($name);

    if($uuid != null && $name == null){
        $uuid = $page->parse_uuid($uuid);

        $name = $page->get_name($uuid);
    }
}

if($page->hasPermission("accounts", "EDIT")) {
    if(isset($_POST['uuid']) && isset($_POST['action'])) {
        $action = $_POST['action'];
        $uuid = $_POST['uuid'];
        if($action == "alerts") {
            $st = $page->conn->prepare("UPDATE negativity_accounts SET violations_by_cheat = '' WHERE id = ?");
            $st->execute(array($uuid));
            $st->closeCursor();
        } else if($action == "minerate") {
            $st = $page->conn->prepare("UPDATE negativity_accounts SET minerate = '', minerate_full_mined = 0 WHERE id = ?");
            $st->execute(array($uuid));
            $st->closeCursor();
        } else if($action == "delete") {
            if($page->hasPermission("accounts", "MANAGE")) {
                $unban = $page->conn->prepare("DELETE FROM negativity_accounts WHERE id = ?;");
                $unban->execute(array($uuid));
                $unban->closeCursor();
                header("Location: ./accounts.php");
                exit();
            }
        } else if($action == "ban") {
            if($page->hasPermission("bans", "EDIT") && isset($_POST["reason"])) {
                $st = $page->conn->prepare("SELECT * FROM negativity_bans_active WHERE id = ?");
                $st->execute(array($uuid));
                $rows = $st->fetchAll(PDO::FETCH_ASSOC);
                if(count($rows) == 0) { // if not already banned
                    $unban = $page->conn->prepare("INSERT INTO negativity_bans_active (id, reason, banned_by, expiration_time, execution_time) VALUES(?,?,?,?,CURRENT_TIMESTAMP);");
                    $unban->execute(array($uuid, $_POST['reason'], "Console", -1));
                    $unban->closeCursor();
                }
                $st->closeCursor();
            }
        } else if($action == "unban") {
            if($page->hasPermission("bans", "EDIT")) {
                $st = $page->conn->prepare("SELECT * FROM negativity_bans_active WHERE id = ?");
                $st->execute(array($uuid));
                $rows = $st->fetchAll(PDO::FETCH_ASSOC);
                if(count($rows) == 1) { // found line to log
                    $row = $rows[0];
                    $logBan = $page->conn->prepare("INSERT INTO negativity_bans_log (id, reason, banned_by, expiration_time, cheat_name, revoked, execution_time, revocation_time, ip) VALUES (?,?,?,?,?,?,?,CURRENT_TIMESTAMP,?);");
                    $logBan->execute(array($uuid, $row["reason"], $row["banned_by"], $row["expiration_time"], $row["cheat_name"], 1, $row["execution_time"], $row["ip"]));
                    $logBan->closeCursor();
                    // now delete from actual db
                    $unban = $page->conn->prepare("DELETE FROM negativity_bans_active WHERE id = ?");
                    $unban->execute(array($uuid));
                    $unban->closeCursor();
                }
                $st->closeCursor();
            }
        }
    }
}

function showContent($info) {
    global $page, $uuid, $name;
    $st = $page->conn->prepare("SELECT * FROM " . $info->getTableName() . " WHERE " . ($info->getTableName() == "negativity_verifications" ? "uuid" : "id") . " = ?;");
    $st->execute(array($uuid));
    $allRows = $st->fetchAll(PDO::FETCH_ASSOC);
    $st->closeCursor();

    $nb = count($allRows);
    if($nb > 0){
        echo '<h2>' . str_replace("%nb%", $nb, str_replace("%name%", $name, $page->msg("check." . $info->getLink()))) . '</h2><br>';

        echo '<div class="container"><table>';
        foreach ($allRows as $row) {
            $page->print_row($row, $info);
        }
        echo '</table></div>';
    }
    unset($page->isFirstRow);
}

$minerateAvailable = array("diamond_ore","gold_ore","iron_ore","coal_ore","ancient_debris");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Positivity - <?php echo ($name == null ? "Not found" : $name); ?></title>
    <?php $page->print_common_head(); ?>
</head>
<body>
	<div class="page-wrapper">
    <?php
    $page->show_header();

    if($uuid == null || $name == null){
        ?>
        <div class="content-wrapper">
            <div class="content">
                <div class="container">
                    <br>
                    <?php
                    echo '<h1>' . $page->msg("error.not_found.player") . '</h1>';
                    echo '<h3>' . $page->msg("check.propositions") . '</h3>';
                    ?>
                    <br>
                </div>
                <?php
                // now let's trying to find possible players
                if($search != null) { // but only if it's search

                    $st = $page->conn->prepare("SELECT * FROM negativity_accounts WHERE UPPER(playername) LIKE UPPER(?);");
                    $st->execute(array('%' . $search . '%'));
                    $allRowSearch = $st->fetchAll(PDO::FETCH_ASSOC);
                    $st->closeCursor();
                    $nbSearch = count($allRowSearch);
                    if($nbSearch > 0) {
                        echo '<div class="container"><table>';
                        foreach ($allRowSearch as $rowSearch) {
                            $page->print_row($rowSearch);
                        }
                        echo '</table></div>';
                    }
                }
                $page->show_footer();
                ?>
            </div>
        </div>
        <?php
    } else {
        $stAccount = $page->conn->prepare("SELECT * FROM negativity_accounts WHERE id = ?;");
        $stAccount->execute(array($uuid));
        $rowAcc = $stAccount->fetch();
        $stAccount->closeCursor();

        $minerateArray = array();
        foreach (explode(";", $rowAcc["minerate"]) as $allMinerate) {
            $tab = explode("=", $allMinerate, 2);
            foreach ($tab as $minerate) {
                if(is_numeric($minerate) || count($tab) <= 1) continue;
                $minerateArray = array_merge($minerateArray, array($minerate => $tab[1]));
            }
        }
        ?>

        <div class="content-wrapper">
            <div class="content">
                <div class="container">
                    <table class="table table-striped table-bordered table-condensed text-white">
                        <thead>
                            <tr>
                                <th><?php echo $page->msg("column.name"); ?></th>
                                <th><?php echo $page->msg("column.lang"); ?></th>
                                <th><?php echo $page->msg("column.creation_time"); ?></th>
                                <th><?php echo $page->msg("column.id"); ?></th>
                                <th><?php echo $page->msg("column.options"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $name; ?></td>
                                <td><?php echo $rowAcc["language"]; ?></td>
                                <td><?php echo $rowAcc["creation_time"]; ?></td>
                                <td><?php echo $uuid; ?></td>
                                <td>
                                    <?php
                                    if($page->has_bans && $page->hasPermission("bans", "EDIT")) {
                                        $banned = $page->is_banned($uuid);
                                        ?>
                                        <form action="./check.php?uuid=<?php echo $uuid . '&name=' . $name; ?>" method="POST" style="display: contents;">
                                            <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
                                            <?php
                                            if($banned) {
                                                echo '<button class="btn-outline" name="action" value="unban">' . $page->msg("generic.unban") . '</button>';
                                            } else {
                                                echo '<input type="text" name="reason" required style="width: auto;" placeholder="' . $page->msg("ask.ban.reason") . '">';
                                                echo '<button class="btn-outline" name="action" value="ban" style="margin-left: 10px;">' . $page->msg("generic.ban") . '</button>';
                                            }
                                        echo '</form>';
                                    }
                                    if($page->hasPermission("accounts", "MANAGE")) {
                                        ?>
                                        <form action="./check.php?uuid=<?php echo $uuid . '&name=' . $name; ?>" method="POST" style="display: contents;">
                                            <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
                                            <button class="btn-outline" name="action" value="delete"><?php echo $page->msg("generic.delete"); ?></button>
                                        </form>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <?php
                    echo '<h2>' . str_replace("%name%", $name, $page->msg("check.violations")) . '</h2>';
                    if($page->hasPermission("accounts", "EDIT")) {
                        ?>
                        <form action="./check.php?uuid=<?php echo $uuid . '&name=' . $name; ?>" method="POST" style="display: contents;">
                            <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
                            <button class="btn-outline" name="action" value="alerts"><?php echo $page->msg("generic.clear"); ?></button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
                <div class="container">
                    <table class="table table-striped table-bordered table-condensed text-white">
                        <?php
                        $allViolationsSplitted = explode(";", $rowAcc["violations_by_cheat"]);
                        $nbAllViolations = $page->countAllViolation($rowAcc["violations_by_cheat"]);
                        ?>
                        <thead>
                            <tr>
                                <?php

                                if($nbAllViolations == 0){
                                    ?>
                                    <th style="width: 50%;"><?php echo $page->msg("generic.cheat"); ?></th>
                                    <th style="width: 50%;"><?php echo $page->msg("generic.amount"); ?></th>
                                    <?php
                                } else {
                                    ?>
                                    <th style="width: 24%;"><?php echo $page->msg("generic.cheat"); ?></th>
                                    <th style="width: 24%;"><?php echo $page->msg("generic.amount"); ?></th>
                                    <th style="width: 4%;"></th>
                                    <th style="width: 24%;"><?php echo $page->msg("generic.cheat"); ?></th>
                                    <th style="width: 24%;"><?php echo $page->msg("generic.amount"); ?></th>
                                    <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tempNb = 0;
                            echo "<tr>";
                            $allViolationsSplitted = explode(";", $rowAcc["violations_by_cheat"]);
                            if($nbAllViolations == 0){
                                ?>
                                <td>-</td>
                                <td>0</td>
                                <?php
                            } else {
                                foreach ($allViolationsSplitted as $allCheat) {
                                    $tab = explode("=", $allCheat, 2);
                                    foreach ($tab as $cheat) {
                                        if(isset($tab[1]) && !is_numeric($cheat)) {
                                            echo "<td>" . $cheat . "</td>";
                                            echo "<td>$tab[1]</td>";
                                            $tempNb++;
                                            if($tempNb == 2){
                                                $tempNb = 0;
                                                echo "</tr>";
                                                if(end($allViolationsSplitted) != $allCheat)
                                                    echo "<tr>";
                                            } else
                                                echo "<td></td>";
                                        }
                                    }
                                }
                            }
                            echo "</tr>";

                            ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <?php
                    echo '<h2>' . str_replace("%name%", $name, $page->msg("check.minerate")) . '</h2>';
                    if($page->hasPermission("accounts", "EDIT")) {
                        ?>
                        <form action="./check.php?uuid=<?php echo $uuid . '&name=' . $name; ?>" method="POST" style="display: contents;">
                            <input type="hidden" name="uuid" value="<?php echo $uuid; ?>">
                            <button class="btn-outline" name="action" value="minerate"><?php echo $page->msg("generic.clear"); ?></button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
                <div class="container">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo $page->msg("minerate.name"); ?></th>
                                <th><?php echo $page->msg("generic.amount"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <?php
                            foreach ($minerateAvailable as $mineKey) {
                                echo "<td>" . $page->msg("minerate." . strtolower($mineKey)) . "</td>";
                                echo "<td>" . (isset($minerateArray[$mineKey]) ? $minerateArray[$mineKey] : 0) . "</td>";
                                echo "</tr><tr>";
                            }
                            echo "<td>" . $page->msg("minerate.all") . "</td>";
                            echo "<td>" . $rowAcc["minerate_full_mined"] . "</td>";
                            ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php

                showContent(new BanInfo($page));
                showContent(new BanLogsInfo($page));
                showContent(new VerificationInfo($page));
            }
            $page->show_footer();
            ?>
            </div>
        </div>
    </div>

</body>
</html>