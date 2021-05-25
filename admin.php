<?php
require_once './include/page.php';
if(isset($_POST["id"])){
	$nextUser = json_decode(file_get_contents("./include/user.txt"), true);
	unset($nextUser[$_POST["id"]]);
	file_put_contents("./include/user.txt", json_encode($nextUser));
} else if(isset($_POST["name"]) && isset($_POST["special"]) && isset($_POST["password"])){
	$nextUser = json_decode(file_get_contents("./include/user.txt"), true);
	$nextUser[$_POST["name"]] = json_encode(array("password" => hash("sha256", $_POST["password"]), "admin" => isset($_POST["is_admin"]), "special" => $_POST["special"]));
	file_put_contents("./include/user.txt", json_encode($nextUser));
}
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
		$page = new Page("admin");
		if(!(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"])){
			header("Location: ./error/access-denied.php");
			die();
		}
		$page->show_header();
		$allUsers = json_decode(file_get_contents("./include/user.txt"), true);
		?>
		<div class="content-wrapper">
			<div class="container">
				<table>
					<thead>
						<tr>
							<th style="width: 25%;"><?php echo $page->msg("admin.column.name"); ?></th>
							<th style="width: 25%;"><?php echo $page->msg("admin.column.is_admin"); ?></th>
							<th style="width: 25%;"><?php echo $page->msg("admin.column.special"); ?></th>
							<th style="width: 25%;"><?php echo $page->msg("admin.column.option"); ?></th>
						</tr>
					</thead>
					<?php
					foreach ($allUsers as $key => $value) {
						echo "<tr>";
						echo "<td>" . $key . "</td>";
						$content = json_decode($value, true);
						echo "<td>" . $page->msg($content["admin"] ? "yes" : "no") . "</td>";
						echo "<td>" . $page->msg(isset($content["special"]) ? "admin.special." . $content["special"] : "admin.special.nothing") . "</td>";
						if($content["special"] != "un_removable"){
							echo '<td>
								<form action="./admin.php" method="POST">
									<input type="hidden" name="id" value="' . $key . '">
									<button  class="btn btn-light btn-sm" >Delete</button>
								</form>
								</td>';
						} else {
							echo '<td>-</td>';
						}
						echo "</tr>";
					}
					?>
				</table>
			</div>
			<br/>
			<form class="container" action="./admin.php" method="POST">
				<h2><?php echo $page->msg("admin.create_user"); ?></h2>
				<br>
				<table class="table table-striped table-bordered table-condensed text-white">
					<div class="form-row justify-content-center">
						<div class="form-group col-md-3">
							<div class="input-group mx-auto mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text bg-dark font-weight-bold text-white"><?php echo $page->msg("admin.column.name"); ?></span>
								</div>
								<input class="form-control" type="text" name="name" required></input>
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center">
						<div class="form-group col-md-3">
							<div class="input-group mx-auto mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text bg-dark font-weight-bold text-white"><?php echo $page->msg("admin.column.password"); ?></span>
								</div>
								<input class="form-control" type="text" name="password" required></input>
							</div>
						</div>
					</div>
					<div class="form-row justify-content-center">
						<div class="form-group col-md-3">
							<div class="input-group mx-auto mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text bg-dark font-weight-bold text-white"><?php echo $page->msg("admin.column.is_admin"); ?></span>
								</div>
								<div class="input-group-append">
									<div class="input-group-text bg-dark font-weight-bold text-white">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="customCheck" name="is_admin">
											<label class="custom-control-label" for="customCheck"></label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<tr>
						<td style="text-align: right;"><label><?php echo $page->msg("admin.column.special"); ?></label> : </td>
						<td style="text-align: left;">
							<select name="special" class="custom-select custom-select-sm" style="width:150px;">
								<option value="nothing" selected="selected"><?php echo $page->msg("admin.special.nothing"); ?></option>
								<option value="un_removable"><?php echo $page->msg("admin.special.un_removable"); ?></option>
							</select>
						</td>
					</tr>
				</table>
				<button class="btn-outline"><?php echo $page->msg("admin.button.create"); ?></button>
			</form>
			<?php $page->show_footer(); ?>
		</div>
	</div>
</body>
</html>
