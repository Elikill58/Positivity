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
        $array["index.version.pull.try"] = "Try to update to latest";
        $array["index.version.no.title"] = "You are up-to-date !";
        $array["index.version.no.sub"] = "You are using latest Positivity release :)";
        $array["index.version.snapshot.title"] = "Snapshot available !";
        $array["index.version.snapshot.snapshot_too"] = "You can try to update to latest modifications.";
        $array["index.version.snapshot.upgrade"] = "You are using snapshot. Public version: %version%. Actually, you're using %actual_version%";
        $array["index.version.snapshot.release"] = "You are using snapshot, but a new version is available: %version%.";
        $array["index.version.yes.title"] = "A new update is available";
        $array["index.version.yes.sub"] = "Newer version : %version%. Actually, you're using %actual_version%";

        $array["accounts.empty"] = "No account found yet";
        $array["bans.empty"] = "No bans found yet";
        $array["bans_logs.empty"] = "No old bans found yet";
        $array["admin_roles.empty"] = "No roles found yet";
        $array["proofs.empty"] = "No proofs found yet.";

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
        $array["title.bans_logs"] = "Old bans";
        $array["title.accounts"] = "Accounts";
        $array["title.proofs"] = "Proofs";
        $array["title.admin_users"] = "Admin Users";
        $array["title.admin_roles"] = "Admin Roles";
        $array["title.verifications"] = "Verifications";
        $array["title.search"] = "Search";
        $array["title.connection"] = "Connection";

        $array["generic.cheat"] = "Cheat";
        $array["generic.amount"] = "Amount";
        $array["generic.name"] = "Name: %name%";
        $array["generic.lang"] = "Language: %lang%";
        $array["generic.creation_time"] = "First join: %time%";
        $array["generic.uuid"] = "UUID: %uuid%";
        $array["generic.see_more"] = "See more";
        $array["generic.see_all"] = "See all";
        $array["generic.delete"] = "Delete";
        $array["generic.clear"] = "Clear";
        $array["generic.save"] = "Save";
        $array["generic.ban"] = "Ban";
        $array["generic.unban"] = "Unban";
        $array["generic.never"] = "Never";
        $array["generic.expired"] = "Expired";

        $array["ask.ban.reason"] = "Reason of ban";

        $array["column.id"] = "UUID";
        $array["column.name"] = "Player name";
        $array["column.role_name"] = "Role name";
        $array["column.lang"] = "Language";
        $array["column.verifications"] = "Verifications";
        $array["column.minerate_full"] = "All blocks mined";
        $array["column.most_clicks"] = "Most clicks/s";
        $array["column.violations"] = "Total violations";
        $array["column.reason"] = "Reason";
        $array["column.banned_by"] = "Banned by";
        $array["column.expiration_time"] = "Expiration time";
        $array["column.revocation_time"] = "Revocation time";
        $array["column.creation_time"] = "Creation time";
        $array["column.cheat_name"] = "Cheat(s)";
        $array["column.started_by"] = "Started by";
        $array["column.player_version"] = "Player Version";
        $array["column.amount"] = "Amount";
        $array["column.check_name"] = "Check name";
        $array["column.reliability"] = "Reliability";
        $array["column.ping"] = "Ping";
        $array["column.time"] = "Time";
        $array["column.more_info"] = "More infos";
        $array["column.user_name"] = "User name";
        $array["column.password"] = "Password";
        $array["column.is_admin"] = "Is admin";
        $array["column.special"] = "Special";
        $array["column.options"] = "Option";
        $array["column.bans"] = "Bans";
        $array["column.bans_logs"] = "Old Bans";
        $array["column.accounts"] = "Accounts";
        $array["column.verifications"] = "Verifications";
        $array["column.proofs"] = "Proofs";
        $array["column.admin_users"] = "Admin Users";
        $array["column.admin_roles"] = "Admin Roles";

        $array["table.pager.number"] = "Page";

        $array["check.violations"] = "Violations";
        $array["check.minerate"] = "Minerate";
        $array["check.bans"] = "Bans (%nb%)";
        $array["check.bans_logs"] = "Old bans (%nb%)";
        $array["check.verifications"] = "Verifications (%nb%)";
        $array["check.proofs"] = "Proofs (%nb%)";
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
        $array["admin.create_roles"] = "Create a new role";
        $array["admin.duplicate"] = "This user already exist";
        $array["admin.button.create_user"] = "Create User";
        $array["admin.button.create_roles"] = "Create Role";
        $array["admin.special.nothing"] = "Basic";
        $array["admin.special.un_removable"] = "Cannot be removed";

        $array["role.none"] = "None";
        $array["role.see"] = "See";
        $array["role.edit"] = "Edit";
        $array["role.manage"] = "Manage";

        $array["error.not_found.player"] = "Player not found.";
        $array["error.not_found.verifications"] = "Verifications not found.";

        $array["server_crash"] = "Server Crash";
        $array["world_downloader"] = "World Downloader";
    }
}
