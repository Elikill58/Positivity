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
        if(!(preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $uuid))){
            $nextUUID = "";
            $i = 1;
            foreach (str_split($uuid) as $char) {
                $nextUUID .= $char;
                if($i == 8 || $i == 12 || $i == 16 || $i == 20)
                    $nextUUID .= "-";
                $i++;
            }
            $uuid = $nextUUID;
        }

        $name = $page->get_name($uuid);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Negativity - <?php echo ($name == null ? "Not found" : $name); ?></title>
    <link href="./include/css/main.css" rel="stylesheet">
</head>
<body>
	<div class="page-wrapper">
    <?php
    $page->show_header();

    if($uuid == null || $name == null){
        ?>
        <div class="container">
            <h1><?php echo $page->msg("error.not_found.player"); ?></h1>
        </div>
        <?php
    } else {
        ?>
        <?php

        $stAccount = $page->conn->prepare("SELECT * FROM negativity_accounts WHERE id = ?;");
        $stAccount->execute(array($uuid));
        $rowAcc = $stAccount->fetch();
        $stAccount->closeCursor();

        $minerateArray = array();
        foreach (explode(";", $rowAcc["minerate"]) as $allMinerate) {
            $tab = explode("=", $allMinerate, 2);
            foreach ($tab as $minerate) {
                if(is_numeric($minerate)) continue;
                $minerateArray = array_merge($minerateArray, array($page->msg("minerate." . strtolower($minerate)) => $tab[1]));
            }
        }
        $minerateArray = array_merge($minerateArray, array($page->msg("minerate.all") => $rowAcc["minerate_full_mined"]));
        ?>

        <div class="content-wrapper">
        <div class="content">
            <div class="container items">
                <div class="item">
                    <?php echo str_replace("%name%", $name, $page->msg("generic.name")); ?>
                </div>
                <div class="item">
                    <?php echo str_replace("%lang%", $rowAcc["language"], $page->msg("generic.lang")); ?>
                </div>
                <div class="item">
                    <?php echo str_replace("%time%", $rowAcc["creation_time"], $page->msg("generic.creation_time")); ?>
                </div>
                <div class="item">
                    <?php echo str_replace("%uuid%", $uuid, $page->msg("generic.uuid")); ?>
                </div>
            </div>
            <h2><?php echo str_replace("%name%", $name, $page->msg("check.violations")); ?></h2>
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
                            foreach (explode(";", $rowAcc["violations_by_cheat"]) as $allCheat) {
                                $tab = explode("=", $allCheat, 2);
                                foreach ($tab as $cheat) {
                                    if(isset($tab[1]) && !is_numeric($cheat)) {
                                        echo "<td>" . $cheat . "</td>";
                                        echo "<td>$tab[1]</td>";
                                        $tempNb++;
                                        if($tempNb == 2){
                                            $tempNb = 0;
                                            echo "</tr><tr>";
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
            <h2><?php echo str_replace("%name%", $name, $page->msg("check.minerate")); ?></h2>
            <div class="container">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo $page->msg("minerate.name"); ?></th>
                            <th><?php echo $page->msg("generic.amount"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach (explode(";", $rowAcc["minerate"]) as $allMinerate) {
                            $tab = explode("=", $allMinerate, 2);
                            foreach ($tab as $minerate) {
                                if(is_numeric($minerate)) continue;
                                echo "<td>" . $page->msg("minerate." . strtolower($minerate)) . "</td>";
                                echo "<td>" . $tab[1] . "</td>";
                                echo "</tr><tr>";
                            }
                        }
                        echo "<td>" . $page->msg("minerate.all") . "</td>";
                        echo "<td>" . $rowAcc["minerate_full_mined"] . "</td>";
                        ?>
                    </tbody>
                </table>
            </div>
            <?php

            $stBan = $page->conn->prepare("SELECT * FROM negativity_bans_active WHERE id = ?;");
            $stBan->execute(array($uuid));
            $allRowBan = $stBan->fetchAll(PDO::FETCH_ASSOC);
            $stBan->closeCursor();

            $nbBan = count($allRowBan);
            if($nbBan > 0){
                echo '<h2>' . str_replace("%nb%", $nbBan, str_replace("%name%", $name, $page->msg("check.bans"))) . '</h2><br>';

                echo '<div class="container"><table>';
                $isLocalFirstRow = true;
                foreach ($allRowBan as $rowBan) {
                    $showedRowBan = (new BanInfo($page))->getInfos($rowBan);
                    if($isLocalFirstRow){
                        echo "<thead><tr>";
                        foreach ($showedRowBan as $key => $value) {
                            echo "<th>" . $page->msg("column." . $key) . "</th>";
                        }
                        echo "</th></thead><tbody>\n";
                    }
                    echo "<tr>";
                    foreach ($showedRowBan as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>\n";
                    $isLocalFirstRow = false;
                }
                echo '</table></div>';
            }
            ?>
            <?php

            $stVerif = $page->conn->prepare("SELECT * FROM negativity_verifications WHERE uuid = ?;");
            $stVerif->execute(array($uuid));
            $allRowVerif = $stVerif->fetchAll(PDO::FETCH_ASSOC);
            $stVerif->closeCursor();

            $nbVerif = count($allRowVerif);
            if($nbVerif > 0){
                echo '<h2>' . str_replace("%nb%", $nbVerif, str_replace("%name%", $name, $page->msg("check.verifications"))) . '</h2><br>';
                
                echo '<div class="container"><table>';
                $isLocalFirstRow = true;
                foreach ($allRowVerif as $rowVerif) {
                    $showedRowVerif = (new VerificationInfo($page))->getInfos($rowVerif);
                    if($isLocalFirstRow){
                        echo "<thead><tr>";
                        foreach ($showedRowVerif as $key => $value) {
                            if($page->endsWith($key, "_double")){
                                if($page->endsWith($key, "_1_double")){
                                    echo "<th colspan=2>" . $page->msg("column." . str_replace("_1_double", "", $key)) . "</th>";
                                } else {
                                    // ignore because it's empty column
                                }
                            } else
                                echo "<th>" . $page->msg("column." . $key) . "</th>";
                        }
                        echo "</th></thead><tbody>\n";
                    }
                    echo "<tr>";
                    foreach ($showedRowVerif as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>\n";
                    $isLocalFirstRow = false;
                }
                echo '</table></div>';
            }
        }
        ?>
        </div>
    <?php $page->show_footer(); ?>
    </div>
    </div>

</body>
</html>