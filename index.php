<?php
require_once './include/page.php';

$page = new Page("index");

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
			<div class="container">
				<br/>
				<h2><?php echo str_replace("%server%", $page->settings["server_name"], $page->msg("index.main")); ?></h2>
				<p><?php echo $page->msg("index.sub"); ?></p>
				<br/>
			</div>
			<?php $page->show_footer(); ?>
		</div>
	</div>
</body>
</html>
