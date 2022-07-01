<?php
require_once './include/page.php';

$page = new Page("index");

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if(!$length)
        return true;
    return substr($haystack, -$length) === $needle;
}
function execPrint($command) {
    $result = array();
    exec($command, $result);
    print("<pre>");
    foreach ($result as $line) {
        print($line . "\n");
    }
    print("</pre>");
}
if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1) {
	if(isset($_POST["action"])) {
		$action = $_POST["action"];
		if($action == "pull") {
			// Print the exec output inside of a pre element
			execPrint("git pull");
			execPrint("git status");
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page->print_common_head(); ?>
    <title>Positivity - Index</title>
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
			<div class="content">
				<div class="container">
					<br/>
					<h2><?php echo str_replace("%server%", $page->settings["server_name"], $page->msg("index.main")); ?></h2>
					<p><?php echo $page->msg("index.sub"); ?></p>
					<br/>
				</div>
				<?php
				if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1) {
					$context = stream_context_create(array('http' => array(
					        'method' => "GET",
					        'header' => "User-Agent: " . $_SERVER['HTTP_USER_AGENT']
					    )
					));
					$actualVersion = file_get_contents("./include/version.txt");
					$snapshot = endsWith($actualVersion, "-SNAPSHOT");
					$latest = json_decode(file_get_contents("https://api.github.com/repos/Elikill58/Positivity/releases/latest", false, $context));
					$isGit = is_dir("./.git"); // check if using git
					if($isGit) {
						$commitVersion = file_get_contents("https://raw.githubusercontent.com/Elikill58/Positivity/master/include/version.txt", false, $context);
						$commitSnapshot = endsWith($commitVersion, "-SNAPSHOT");
						if($snapshot && $commitSnapshot) { // using snapshot everywhere
							if($actualVersion == $commitVersion) { // exact same version, with snapshot
								?>
								<div class="container">
									<br/>
									<h2><?php echo $page->msg("index.version.snapshot.title"); ?></h2>
									<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.snapshot.snapshot_too"))); ?></p>
									<button class="btn-outline" name="action" value="pull"><?php echo $page->msg("index.version.pull.try"); ?></button>
									<br/>
								</div>
								<?php
							} else { // not exact same version
								?>
								<div class="container">
									<br/>
									<h2><?php echo $page->msg("index.version.snapshot.title"); ?></h2>
									<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.snapshot.upgrade"))); ?></p>
									<button class="btn-outline" name="action" value="pull"><?php echo $page->msg("index.version.pull.try"); ?></button>
									<br/>
								</div>
								<?php
							}
						} else if($snapshot && !$commitSnapshot) { // using snapshot but full release available
							?>
							<div class="container">
								<br/>
								<h2><?php echo $page->msg("index.version.snapshot.title"); ?></h2>
								<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.snapshot.release"))); ?></p>
								<button class="btn-outline" name="action" value="pull"><?php echo $page->msg("index.version.pull.try"); ?></button>
								<br/>
							</div>
							<?php
						} else { // not using snapshot
							?>
							<div class="container">
								<br/>
								<h2><?php echo $page->msg("index.version.snapshot.title"); ?></h2>
								<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.snapshot.upgrade"))); ?></p>
								<button class="btn-outline" name="action" value="pull"><?php echo $page->msg("index.version.pull.try"); ?></button>
								<br/>
							</div>
							<?php
						}
					} else {
						if($latest->{"draft"} == 0 && $latest->{"prerelease"} == 0 && $latest->{"tag_name"} != $actualVersion) { // version different from latest github release
							?>
							<div class="container">
								<br/>
								<h2><?php echo $page->msg("index.version.yes.title"); ?></h2>
								<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.yes.sub"))); ?></p>
								<br/>
							</div>
							<?php
						} else { // same as github release
							?>
							<div class="container">
								<br/>
								<h2><?php echo $page->msg("index.version.no.title"); ?></h2>
								<p><?php echo $page->msg("index.version.no.sub"); ?></p>
								<br/>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
			<?php
			$page->show_footer();
			?>
		</div>
	</div>
</body>
</html>
