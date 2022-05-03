<?php

function show($page) {
    /*try {
        $st = $page->conn->query("SELECT (SELECT COUNT(*) FROM negativity_bans_active)");
        ($row = $st->fetch(PDO::FETCH_NUM)) or die('Failed to fetch row counts.');
        $st->closeCursor();
        $count = array(
            'bans.php'     => $row[0],
        );
    } catch (PDOException $ex) {
        header("Location: ./error/no-negativity.php");
        die();
        // die ('Erreur : ' . $ex->getMessage());
    }*/

    $settings = $page->settings;
    ?>
    <div class="sidebar">
        <div class="nav-item">
            <a class="logo" href="<?php echo $settings['link']; ?>">
                <?php echo $settings["server_name"]; ?>
            </a>
        </div>
        <?php
            echo '<div class="nav-item' . ($page->info->getLink() == "checks" ? " active" : "") .'">';
            echo '<a class="nav-link" href="./">' . $page->msg("title.index") . '</a>';
            echo '</div>';
        ?>
        <!-- <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#negativity-navbar"
                aria-controls="negativity-navbar" aria-expanded="false" aria-label="Toggle navigation">
        </button> -->
        <?php
        if(isset($_SESSION["name"])){
            foreach ($page->getNavbar() as $key => $value) {
                if($key == "bans" && !$page->has_bans)
                    continue;
                echo '<div class="nav-item' . ($page->info == $value ? ' active' : '') . '">
                        <a class="nav-link" href="' . ($value->getLink()) . '.php">' . $page->msg("title." . $key) . '
                            <span class="number">' . ($value->getNumber()) . '</span>
                        </a>
                    </div>';
            }
        }
        if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]){
            echo '<div class="nav-item' . ($page->info->getLink() == "admin" ? " active" : "") .'">';
            echo '<a class="nav-link" href="./admin.php">' . $page->msg("title.admin") . '</a>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="topbar">
        <?php
        if(isset($_SESSION["name"])){
            ?>
            <div class="nav-item">
                <form action="./check.php" method="GET">
                    <div class="search-input">
                        <input class="form-control" type="text" name="search">
                        <button class="btn-outline btn-small"><div class="text"><?php echo $page->msg("title.search"); ?></div></button>
                    </div>
                </form>
            </div>
            <div class="nav-item">
                <span class=""><?php echo str_replace("%name%", $_SESSION["name"], $page->msg("connection.login_as")); ?></span>
            </div>
            <div class="nav-item">
                <a href="./deconnection.php">
                    <button class="btn-outline btn-small"><div class="text"><?php echo $page->msg("connection.disconnect"); ?></div></button>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>