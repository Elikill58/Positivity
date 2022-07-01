<?php
require_once './include/page.php';

$page = new Page("index");

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if(!$length)
        return true;
    return substr($haystack, -$length) === $needle;
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
					$actualVersion = file_get_contents("./include/version.txt");
					$context = stream_context_create(array('http' => array(
					        'method' => "GET",
					        'header' => "User-Agent: " . $_SERVER['HTTP_USER_AGENT']
					    )
					));
					$latest = json_decode(file_get_contents("https://api.github.com/repos/Elikill58/Positivity/releases/latest", false, $context));
					if($latest->{"draft"} == 0 && $latest->{"prerelease"} == 0 && $latest->{"tag_name"} != $actualVersion) {
						?>
						<div class="container">
							<br/>
							<h2><?php echo $page->msg("index.version.yes.title"); ?></h2>
							<p><?php echo str_replace("%actual_version%", $actualVersion, str_replace("%version%", $latest->{"tag_name"}, $page->msg("index.version.yes.sub" . (endsWith($actualVersion, "-SNAPSHOT") ? "_snapshot" : "")))); ?></p>
							<br/>
						</div>
						<?php
					} else {
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
				?>
			</div>
			<?php
			$page->show_footer();
			?>
		</div>
	</div>
</body>
</html>
