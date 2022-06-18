<?php
require_once './include/page.php';

$page = new Page("bans");

if($page->hasPermission("bans", "EDIT")) {
    if(isset($_POST["id"])) {
        $uuid = $_POST['id'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page->print_common_head(); ?>
</head>
<body>
    <?php
    $page->show_topbar();
    ?>
	<div class="page-wrapper">
        <?php
        $page->show_header();
        ?>
		<div class="content-wrapper">
			<div class="container">
                <table>
                <?php
                    $rows = $page->run_query();
                    if(count($rows) == 0) {
                        $page->print_no_row();
                    } else {
                        foreach ($rows as $row) {
                            $player_name = $page->get_name($row["id"]);
                            if ($player_name === null)
                                continue;
                            $page->print_row($row);
                        }
                        $page->show_page_mover();
                    }

                ?>
                </table>
            </div>
            <?php $page->show_footer(); ?>
        </div>
    </div>
</body>
</html>