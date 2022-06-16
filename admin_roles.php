<?php
require_once './include/page.php';
$page = new Page("admin_roles");

if(!(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"])){
	header("Location: ./error/access-denied.php");
	die();
}
$roleCreatingFailed = false;
if(isset($_POST["id"])){
  $roleDel = $page->conn->prepare("DELETE FROM positivity_roles WHERE id = ?;");
  $roleDel->execute(array($_POST["id"]));
  $roleDel->closeCursor();
} else if(isset($_POST["name"])){
	$name = $_POST["name"];
  $st = $page->conn->prepare("SELECT * FROM positivity_roles WHERE name = ?");
  $st->execute(array($name));
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);
  if(count($rows) == 0) { // don't exist
	  $roleCreate = $page->conn->prepare("INSERT INTO positivity_roles (name) VALUES (?);");
	  $roleCreate->execute(array($name));
	  $roleCreate->closeCursor();
  } else {
  	$roleCreatingFailed = true;
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
    		document.getElementById("create-role-duplicate").style.display = "block";
    	}
    }
    </script>
</head>
<body>
	<div class="page-wrapper">
		<?php
		$page->show_header();
		$allRoles = $page->run_query();
		echo "<script>";
		foreach ($allRoles as $content) {
			echo "users.push(\"" . $content["name"] . "\");";
		}
		echo "</script>";
		?>
		<div class="content-wrapper">
			<div class="content">
				<form class="container" action="./admin_roles.php" method="POST">
					<h2><?php echo $page->msg("admin.create_roles"); ?></h2>
					<br>
					<div class="row" style="display: flex; padding-bottom: 10px; justify-content: normal;">
            <div class="input col-6" style="margin: 0 10px;">
              <i class="material-icons">person</i>
              <input style="border: none;" type="text" name="name" id="name" placeholder="<?php echo $page->msg("column.role_name"); ?>" required />
            </div>
	          <div class="col-6">
							<button class="btn-outline" onclick="checkCreateUser(event)"><div class="text"><?php echo $page->msg("admin.button.create_roles"); ?></div></button>
						</div>
					</div>
					<div class="text" style="padding-bottom: 10px; display: <?php echo ($roleCreatingFailed ? "block" : "none"); ?>; color: red;" id="create-role-duplicate"><?php echo $page->msg("admin.duplicate"); ?></div>
				</form>
				<div class="container">
          <table>
          <?php
            if(count($allRoles) == 0) {
                $page->print_no_row();
            } else {
              foreach ($allRoles as $row) {
                $page->print_row($row);
              }
              $page->show_page_mover();
            }
          ?>
          </table>
				</div>
			</div>
			<?php $page->show_footer(); ?>
		</div>
	</div>
</body>
</html>
