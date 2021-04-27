<?php

function show($page) {
    try {
        $st = $page->conn->query("SELECT (SELECT COUNT(*) FROM negativity_bans_active)");
        ($row = $st->fetch(PDO::FETCH_NUM)) or die('Failed to fetch row counts.');
        $st->closeCursor();
        $count = array(
            'bans.php'     => $row[0],
        );
    } catch (PDOException $ex) {
        die ('Erreur : ' . $ex->getMessage());
    }

    $settings = $page->settings;
    ?>
    <header role="banner">
        <div class="navbar navbar-expand-lg fixed-top" style="background-color: #272B30;">
            <a class="navbar-brand" href="<?php echo $settings['link']; ?>">
                <?php echo $settings["server_name"]; ?>
            </a>
            <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#negativity-navbar"
                    aria-controls="negativity-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="negativity-navbar">
                <ul class="navbar-nav mr-auto">
                <?php
                if(isset($_SESSION["name"])){
                    foreach ($page->getNavbar() as $key => $value) {
                        echo '<li class="nav-item' . ($page->info == $value ? ' active' : '') . '">
                                <a class="nav-link" href="' . ($value->getLink()) . '">' . $page->msg("title." . $key) . '
                                    <span class="badge badge-secondary">' . ($value->getNumber()) . '</span>
                                </a>
                            </li>';
                    }
                }
                if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]){
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link' . ($page->info->getLink() == "admin" ? " active" : "") . '" href="./admin">' . $page->msg("title.admin") . '</a>';
                    echo '</li>';
                }
                ?>
                </ul>
                <ul class="nav navbar-nav my-2 my-lg-0">
                    <?php
                    if(isset($_SESSION["name"])){
                        ?>
                        <li class="nav-item">
                            <div class="nav-link">
                                <form action="./check" method="GET">
                                <div class="input-group input-group-sm">
                                    <input class="form-control" type="text" name="name">
                                    <div class="input-group-append">
                                        <button class="btn btn-light"><?php echo $page->msg("title.search"); ?></button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </li>
                        <li class="nav-item" style="margin-right: 5px;">
                            <div class="nav-link">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-dark text-white border-0"><?php echo str_replace("%name%", $_SESSION["name"], $page->msg("connection.login_as")); ?></span>
                                    </div>
                                    <a href="./deconnection">
                                        <button class="btn btn-light btn-sm"><?php echo $page->msg("connection.disconnect"); ?></button>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
    </header>
<?php
}
?>