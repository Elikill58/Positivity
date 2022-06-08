<?php
require_once './include/page.php';
$page = new Page("admin");

if(!(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"])){
	header("Location: ./error/access-denied.php");
	die();
}
$userCreatingFailed = false;
if(isset($_POST["id"])){
  $userDel = $page->conn->prepare("DELETE FROM positivity_user WHERE id = ?;");
  $userDel->execute(array($_POST["id"]));
  $userDel->closeCursor();
} else if(isset($_POST["name"]) && isset($_POST["special"]) && isset($_POST["password"])){
	$name = $_POST["name"];
  $st = $page->conn->prepare("SELECT * FROM positivity_user WHERE username = ?");
  $st->execute(array($name));
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);
  if(count($rows) == 0) { // don't exist
	  $userCreate = $page->conn->prepare("INSERT INTO positivity_user (username, password, admin, special) VALUES (?,?,?,?);");
	  $userCreate->execute(array($name, hash("sha256", $_POST["password"]), (isset($_POST["is_admin"]) && $_POST["is_admin"] ? 1 : 0), $_POST["special"]));
	  $userCreate->closeCursor();
  } else {
  	$userCreatingFailed = true;
  }
  $st->closeCursor();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page->print_common_head(); ?>
    <title>Positivity - Index</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script>
    function togglePasswordVisibility() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
    var users = [];
    function checkCreateUser(e) {
      var name = document.getElementById("name");
    	if(users.includes(name.value)) {
    		e.preventDefault();
    		document.getElementById("create-user-duplicate").style.display = "block";
    	}
    }
    </script>
</head>
<body>
	<div class="page-wrapper">
		<?php
		$page->show_header();
		$allUsers = $page->run_query();
		echo "<script>";
		foreach ($allUsers as $content) {
			echo "users.push(\"" . $content["username"] . "\");";
		}
		echo "</script>";
		?>
		<div class="content-wrapper">
			<div class="content">
				<form class="container" action="./admin.php" method="POST">
					<h2><?php echo $page->msg("admin.create_user"); ?></h2>
					<br>
					<div class="row" style="display: flex; padding-bottom: 10px; justify-content: normal;">
            <div class="input col-3" style="margin: 0 10px;">
              <i class="material-icons">person</i>
              <input style="border: none;" type="text" name="name" id="name" placeholder="<?php echo $page->msg("admin.column.name"); ?>" required />
            </div>
            <div class="input col-3" style="display: flex; margin: 0 10px;">
              <i class="material-icons">lock</i>
              <input style="border: none; height: fit-content;" type="password" name="password" id="password" placeholder="<?php echo $page->msg("admin.column.password"); ?>" required />
              <i class="material-icons" onclick="togglePasswordVisibility()" style="cursor: pointer;">visibility</i>
            </div>
            <div class="input col-2" style="margin: 0 10px; padding-top: 10px;">
						<span class="text-white"><?php echo $page->msg("admin.column.is_admin"); ?></span>
						<input type="checkbox" id="customCheck" name="is_admin" style="width: fit-content;">
						</div>
	          <div class="input col-2" style="margin: 0 10px;">
							<select name="special" class="custom-select custom-select-sm" style="width:150px;">
								<option value="nothing" selected="selected"><?php echo $page->msg("admin.special.nothing"); ?></option>
								<option value="un_removable"><?php echo $page->msg("admin.special.un_removable"); ?></option>
							</select>
						</div>
	          <div class="col-2">
							<button class="btn-outline" onclick="checkCreateUser(event)"><div class="text"><?php echo $page->msg("admin.button.create"); ?></div></button>
						</div>
					</div>
					<div class="text" style="padding-bottom: 10px; display: <?php echo ($userCreatingFailed ? "block" : "none"); ?>; color: red;" id="create-user-duplicate"><?php echo $page->msg("admin.duplicate"); ?></div>
				</form>
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
						foreach ($allUsers as $content) {
							echo '<tr>';
							echo "<td>" . $content["username"] . "</td>";
							echo "<td>" . $page->msg($content["admin"] ? "yes" : "no") . "</td>";
							echo "<td>" . $page->msg(isset($content["special"]) ? "admin.special." . $content["special"] : "admin.special.nothing") . "</td>";
							if($content["special"] != "un_removable"){
								echo '<td>
									<form action="./admin.php" method="POST">
										<input type="hidden" name="id" value="' . $content["id"] . '">
										<button  class="btn btn-light btn-sm" >Delete</button>
									</form>
									</td>';
							} else {
								echo '<td>-</td>';
							}
							echo "</tr>";
						}
            $page->show_page_mover();
						?>
					</table>
				</div>
			</div>
			<?php $page->show_footer(); ?>
		</div>
	</div>
</body>
</html>
