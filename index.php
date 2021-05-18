<?php
require_once './include/page.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Negativity - Index</title>
    <link href="./include/css/main.css" rel="stylesheet">
</head>
<body>
	<div class="page-wrapper">
		<?php
		$page = new Page("index");
		$page->show_header();
		?>
		<br>
		<div class="content-wrapper">
			<div class="container">
				<h2><?php echo str_replace("%server%", $page->settings["server_name"], $page->msg("index.main")); ?></h2>
				<p><?php echo $page->msg("index.sub"); ?></p>
			</div>
		</div>
		<?php $page->show_footer(); ?>
	</div>
</body>
</html>
