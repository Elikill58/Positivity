<?php
class Lang {
    public function __construct() {
        $array = array();
        $this->array = &$array;
        
        $array["yes"] = "Yes";
        $array["no"] = "No";

        $array["index.main"] = "Welcome to %server%'s Negativity web interface.";
        $array["index.sub"] = "Here, all bans, negativity account and verifications are listed";
        $array["index.empty"] = "More use Negativity to fill data.";

        $array["account.empty"] = "No account found yet";
        $array["bans.empty"] = "No bans found yet";
        
        $array["connection.name"] = "Connection";
        $array["connection.form.login"] = "Your login";
        $array["connection.form.password"] = "Your password";
        $array["connection.form.confirm"] = "Connect";
        $array["connection.wrong_name"] = "Wrong login !";
        $array["connection.wrong_pass"] = "Wrong login or password !";
        $array["connection.well"] = "You're connected !";
        $array["connection.back"] = "Click to go back to the home page";
        $array["connection.disconnect"] = "Disconnect";
        $array["connection.login_as"] = "Connected as %name%";

        $array["title.index"] = "Home";
        $array["title.bans"] = "Bans";
        $array["title.accounts"] = "Accounts";
        $array["title.admin"] = "Administration";
        $array["title.verifications"] = "Verifications";
        $array["title.search"] = "Search";

        $array["generic.cheat"] = "Cheat";
        $array["generic.amount"] = "Amount";
        $array["generic.name"] = "Name: %name%";
        $array["generic.lang"] = "Language: %lang%";
        $array["generic.creation_time"] = "First join: %time%";
        $array["generic.uuid"] = "UUID: %uuid%";
        $array["generic.see_more"] = "See more";
        $array["generic.see_all"] = "See all";

        $array["column.id"] = "UUID";
        $array["column.name"] = "Player name";
        $array["column.lang"] = "Language";
        $array["column.verifications"] = "Verifications";
        $array["column.minerate_full"] = "All blocks mined";
        $array["column.most_clicks"] = "Most clicks/s";
        $array["column.violations"] = "Total violations";
        $array["column.reason"] = "Reason";
        $array["column.banned_by"] = "Banned by";
        $array["column.expiration_time"] = "Expiration time";
        $array["column.creation_time"] = "Creation time";
        $array["column.cheat_name"] = "Cheat(s)";
        $array["column.started_by"] = "Started by";
        $array["column.player_version"] = "Player Version";
        $array["column.amount"] = "Amount";
        $array["column.more_info"] = "More infos";

        $array["table.pager.number"] = "Page";

        $array["check.violations"] = "Violations";
        $array["check.minerate"] = "Minerate";
        $array["check.bans"] = "Bans (%nb%)";
        $array["check.verifications"] = "Verifications (%nb%)";
        $array["check.propositions"] = "Here are some possible players that can fit to your request";

        $array["minerate.name"] = "Minerate";
        $array["minerate.ancient_debris"] = "Ancient Debris";
        $array["minerate.diamond_ore"] = "Diamond Ore";
        $array["minerate.gold_ore"] = "Gold Ore";
        $array["minerate.iron_ore"] = "Iron Ore";
        $array["minerate.coal_ore"] = "Coal Ore";
        $array["minerate.all"] = "All blocks";

        $array["verifications.result"] = "Verifications result";
        $array["verifications.empty"] = "No verifications made yet";

        $array["admin.create_user"] = "Create a new user";
        $array["admin.duplicate"] = "This user already exist";
        $array["admin.button.create"] = "Create User";
        $array["admin.column.name"] = "User name";
        $array["admin.column.password"] = "Password";
        $array["admin.column.is_admin"] = "Is admin";
        $array["admin.column.special"] = "Special";
        $array["admin.column.option"] = "Option";
        $array["admin.special.nothing"] = "Basic";
        $array["admin.special.un_removable"] = "Cannot be removed";

        $array["error.not_found.player"] = "Player not found.";
        $array["error.not_found.verifications"] = "Verifications not found.";

        $array["server_crash"] = "Server Crash";
        $array["world_downloader"] = "World Downloader";
    }
}
