<?php
class Lang {
    public function __construct() {
        $array = array();
        $this->array = &$array;
        
        $array["yes"] = "Oui";
        $array["no"] = "Non";

        $array["index.main"] = "Bienvenue sur l'interface web de Negativity pour %server% !";
        $array["index.sub"] = "Ici, tout les bans, comptes Negativity et verifications sont listés.";
        $array["index.empty"] = "Utilisez plus Negativity pour obtenir des informations.";
        $array["index.version.pull.try"] = "Essayer de mettre à jour";
        $array["index.version.no.title"] = "Vous êtes à jour !";
        $array["index.version.no.sub"] = "Vous avez la dernière version de Positivity :)";
        $array["index.version.snapshot.title"] = "Snapshot disponible !";
        $array["index.version.snapshot.snapshot_too"] = "Vous pouvez essayer de mettre à jour les dernière modifications.";
        $array["index.version.snapshot.upgrade"] = "Vous pouvez utiliser une snapshot. Version publique : %version%. Actuellement, vous avez %actual_version%";
        $array["index.version.snapshot.release"] = "Vous utilisez une snapshot, mais une nouvelle version est disponible: %version%.";
        $array["index.version.yes.title"] = "Une nouvelle mise à jour est disponible";
        $array["index.version.yes.sub"] = "Nouvelle version : %version%. Actuellement, vous avez %actual_version%";

        $array["accounts.empty"] = "Aucun compte créé pour le moment.";
        $array["bans.empty"] = "Aucun banissement effectué pour le moment.";
        $array["bans_logs.empty"] = "Aucun ancien banissement pour le moment.";
        $array["admin_roles.empty"] = "Aucun rôle disponible pour le moment.";
        $array["proofs.empty"] = "Aucune preuve n'est disponible pour le moment.";
        
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
        $array["title.bans_logs"] = "Ancien bans";
        $array["title.accounts"] = "Comptes";
        $array["title.proofs"] = "Preuves";
        $array["title.admin_users"] = "Admin Utilisateurs";
        $array["title.admin_roles"] = "Admin Rôles";
        $array["title.verifications"] = "Vérifications";
        $array["title.search"] = "Rechercher";
        $array["title.connection"] = "Connexion";

        $array["generic.cheat"] = "Cheat";
        $array["generic.amount"] = "Quantité";
        $array["generic.name"] = "Nom: %name%";
        $array["generic.lang"] = "Langue: %lang%";
        $array["generic.creation_time"] = "Première connection: %time%";
        $array["generic.uuid"] = "UUID: %uuid%";
        $array["generic.see_more"] = "Plus d'infos";
        $array["generic.see_all"] = "Voir tout";
        $array["generic.delete"] = "Supprimer";
        $array["generic.clear"] = "Vider";
        $array["generic.save"] = "Sauvegarder";
        $array["generic.ban"] = "Bannir";
        $array["generic.unban"] = "Débannir";
        $array["generic.never"] = "Jamais";
        $array["generic.expired"] = "Expiré";

        $array["ask.ban.reason"] = "Raison du banissement";

        $array["column.id"] = "UUID";
        $array["column.name"] = "Nom du joueur";
        $array["column.role_name"] = "Nom du rôle";
        $array["column.lang"] = "Langue";
        $array["column.verifications"] = "Vérifications";
        $array["column.minerate_full"] = "Tout les blocks minés";
        $array["column.most_clicks"] = "Meilleur click/s";
        $array["column.violations"] = "Violations totales";
        $array["column.reason"] = "Raison";
        $array["column.banned_by"] = "Banni par";
        $array["column.expiration_time"] = "Expire le";
        $array["column.creation_time"] = "Date de création";
        $array["column.revocation_time"] = "Date de révocation";
        $array["column.cheat_name"] = "Cheat(s)";
        $array["column.started_by"] = "Démarré par";
        $array["column.player_version"] = "Version du joueur";
        $array["column.amount"] = "Quantité";
        $array["column.check_name"] = "Nom du check";
        $array["column.reliability"] = "Fiabilité";
        $array["column.ping"] = "Latence";
        $array["column.time"] = "Date";
        $array["column.more_info"] = "Plus d'infos";
        $array["column.user_name"] = "Nom d'utilisateur";
        $array["column.password"] = "Mot de passe";
        $array["column.is_admin"] = "Est admin";
        $array["column.special"] = "Special";
        $array["column.options"] = "Option";
        $array["column.bans"] = "Bans";
        $array["column.bans_logs"] = "Anciens Bans";
        $array["column.accounts"] = "Comptes";
        $array["column.verifications"] = "Vérifications";
        $array["column.proofs"] = "Preuves";
        $array["column.admin_users"] = "Admin Utilisateurs";
        $array["column.admin_roles"] = "Admin Rôles";

        $array["table.pager.number"] = "Page";

        $array["check.violations"] = "Violations";
        $array["check.minerate"] = "Minerate";
        $array["check.bans"] = "Bans (%nb%)";
        $array["check.bans_logs"] = "Ancien banissement (%nb%)";
        $array["check.verifications"] = "Vérifications (%nb%)";
        $array["check.proofs"] = "Preuves (%nb%)";
        $array["check.propositions"] = "Voici quelques joueurs qui peuvent répondre à votre demande";

        $array["minerate.name"] = "Minerate";
        $array["minerate.ancient_debris"] = "Débris antique";
        $array["minerate.diamond_ore"] = "Minerais de diamant";
        $array["minerate.gold_ore"] = "Minerais d'or";
        $array["minerate.iron_ore"] = "Minerais de fer";
        $array["minerate.coal_ore"] = "Minerais de charbon";
        $array["minerate.all"] = "Tout les blocks";

        $array["verifications.result"] = "Résultat de la vérification";
        $array["verifications.empty"] = "Aucun vérification effectué pour le moment.";

        $array["admin.create_user"] = "Créer un utilisateur";
        $array["admin.create_roles"] = "Créer un rôle";
        $array["admin.duplicate"] = "Cet utilisateur existe déjà";
        $array["admin.button.create_user"] = "Créer l'utilisateur";
        $array["admin.button.create_roles"] = "Créer le role";
        $array["admin.special.nothing"] = "Basique";
        $array["admin.special.un_removable"] = "Insupprimable";

        $array["role.none"] = "Aucun";
        $array["role.see"] = "Voir";
        $array["role.edit"] = "Modifier";
        $array["role.manage"] = "Gérer";

        $array["error.not_found.player"] = "Joueur introuvable.";
        $array["error.not_found.verifications"] = "Vérifications introuvable.";

        $array["server_crash"] = "Server Crash";
        $array["world_downloader"] = "Téléchargement de monde";
    }
}
