<?php
class Lang {
    public function __construct() {
        $array = array();
        $this->array = &$array;
        
        $array["yes"] = "Oui";
        $array["no"] = "Non";

        $array["index.main"] = "Bienvenue sur l'interface web de Negativity pour %server% !";
        $array["index.sub"] = "Ici, tout les bans, comptes Negativity et verifications sont listés.";
        
        $array["connection.name"] = "Connexion";
        $array["connection.form.login"] = "Identifiant";
        $array["connection.form.password"] = "Mot de passe";
        $array["connection.form.confirm"] = "Se connecter";
        $array["connection.wrong_name"] = "Mauvais identifiant !";
        $array["connection.wrong_pass"] = "Mauvais identifiant ou  mot de passe !";
        $array["connection.well"] = "Vous êtes connecté !";
        $array["connection.back"] = "Cliquez ici pour aller sur la page principale";
        $array["connection.disconnect"] = "Déconnexion";
        $array["connection.login_as"] = "Connecté en tant que %name%";

        $array["title.index"] = "Home";
        $array["title.bans"] = "Bans";
        $array["title.accounts"] = "Comptes";
        $array["title.admin"] = "Administration";
        $array["title.verifications"] = "Vérifications";
        $array["title.search"] = "Rechercher";

        $array["generic.cheat"] = "Cheat";
        $array["generic.amount"] = "Quantité";
        $array["generic.name"] = "Nom: %name%";
        $array["generic.lang"] = "Langue: %lang%";
        $array["generic.creation_time"] = "Première connection: %time%";
        $array["generic.uuid"] = "UUID: %uuid%";
        $array["generic.see_more"] = "Plus d'infos";
        $array["generic.see_all"] = "Voir tout";

        $array["column.id"] = "UUID";
        $array["column.name"] = "Nom du joueur";
        $array["column.lang"] = "Langue";
        $array["column.verifications"] = "Vérifications";
        $array["column.minerate_full"] = "Tout les blocks minés";
        $array["column.most_clicks"] = "Meilleur click/s";
        $array["column.violations"] = "Violations totales";
        $array["column.reason"] = "Raison";
        $array["column.banned_by"] = "Banni par";
        $array["column.expiration_time"] = "Expire le";
        $array["column.creation_time"] = "Date de création";
        $array["column.cheat_name"] = "Cheat(s)";
        $array["column.started_by"] = "Démarré par";
        $array["column.player_version"] = "Version du joueur";
        $array["column.amount"] = "Quantité";
        $array["column.more_info"] = "Plus d'infos";

        $array["table.pager.number"] = "Page";

        $array["check.violations"] = "Violations";
        $array["check.minerate"] = "Minerate";
        $array["check.bans"] = "Bans (%nb%)";
        $array["check.verifications"] = "Vérifications (%nb%)";

        $array["minerate.name"] = "Minerate";
        $array["minerate.diamond_ore"] = "Minerais de diamant";
        $array["minerate.gold_ore"] = "Minerais d'or";
        $array["minerate.iron_ore"] = "Minerais de fer";
        $array["minerate.coal_ore"] = "Minerais de charbon";
        $array["minerate.all"] = "Tout les blocks";

        $array["verif.infos"] = "Informations principales";
        $array["verif.result"] = "Résultat de la vérification";

        $array["admin.create_user"] = "Créer un utilisateur";
        $array["admin.button.create"] = "Créer l'utilisateur";
        $array["admin.column.name"] = "Nom d'utilisateur";
        $array["admin.column.password"] = "Mot de passe";
        $array["admin.column.is_admin"] = "Est admin";
        $array["admin.column.special"] = "Special";
        $array["admin.column.option"] = "Option";
        $array["admin.special.nothing"] = "Basique";
        $array["admin.special.un_removable"] = "Insupprimable";

        $array["error.not_found.player"] = "Joueur introuvable.";
        $array["error.not_found.verifications"] = "Vérifications introuvable.";

        $array["server_crash"] = "Server Crash";
    }
}
