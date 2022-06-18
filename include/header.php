<?php

function showTopbar($page) {
    $settings = $page->settings;
    ?>
    <script>
    function isMobile() {
        return localStorage.mobile || window.navigator.maxTouchPoints > 1;
    }

    function manageSidebar() {
        let sidebar = document.getElementById("sidebar");
        let sidebarIcon = document.getElementById("sidebar-icon");
        if(sidebar.style.display == "none"){
            sidebar.style.display = "block";
            sidebarIcon.innerHTML = "close"; // change icon
        } else {
            sidebar.style.display = "none";
            sidebarIcon.innerHTML = "menu"; // change icon
        }
    }

    if(isMobile()){
        setTimeout(manageSidebar, 1);
    }
    </script>
    <div class="topbar">
        <a class="nav-item" onclick="manageSidebar()">
            <i class="material-icons" id="sidebar-icon">close</i>
        </a>
        <div class="nav-item hide-mobile">
            <a class="logo" href="<?php echo $settings['link']; ?>">
                <?php echo $settings["server_name"]; ?>
            </a>
        </div>
        <?php
        if(isset($_SESSION["name"])){
            ?>
            <span style="flex: 1;"></span>
            <div class="topbar-sub">
                <div class="nav-item">
                    <form action="./check.php" method="GET">
                        <div class="search-input">
                            <input class="form-control" type="text" name="search">
                            <button class="btn-outline btn-small"><div class="text"><?php echo $page->msg("title.search"); ?></div></button>
                        </div>
                    </form>
                </div>
                <div class="nav-item hide-mobile">
                    <span class=""><?php echo str_replace("%name%", $_SESSION["name"], $page->msg("connection.login_as")); ?></span>
                </div>
                <div class="nav-item">
                    <a href="./deconnection.php">
                        <button class="btn-outline btn-small"><div class="text"><?php echo $page->msg("connection.disconnect"); ?></div></button>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
<?php
}

function show($page) {
    $settings = $page->settings;
    ?>
    <div class="sidebar" id="sidebar">
        <?php
        echo '<div class="nav-item' . ($page->info->getLink() == "checks" ? " active" : "") .'">';
        echo '<a class="nav-link" href="./">' . $page->msg("title.index") . '</a>';
        echo '</div>';
        ?>
        <?php
        if(isset($_SESSION["name"])){
            foreach ($page->getNavbar() as $key => $value) {
                if($key == "bans" && !$page->has_bans)
                    continue;
                $perm = $value->getPermissionPrefix();
                if($perm == null || $page->hasPermission($perm, "SEE")){
                    echo '<div class="nav-item' . ($page->info == $value ? ' active' : '') . '">
                        <a class="nav-link" href="' . ($value->getLink()) . '.php">' . $page->msg("title." . $key) . '
                            <span class="number">' . ($value->getNumber()) . '</span>
                        </a>
                    </div>';
                }
            }
        }
        ?>
    </div>
<?php
}
?>