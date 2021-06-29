<?php
if(!isset($_POST["request"])) {
	die("Can't understand request.");
}

/**
 * Check if a table exists in the current database.
 *
 * @param PDO $pdo PDO instance connected to a database.
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 */
function tableExists($pdo, $table) {

    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        // We got an exception == table not found
        return FALSE;
    }

    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}


$request = $_POST["request"];
if($request == "bdd"){
    try{
	    $dbh = new PDO('mysql:host=' . $_POST["host"] . ':' . $_POST["port"] . ';dbname=' . $_POST["database"], $_POST["username"], $_POST["password"],
	    	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	    if(tableExists($dbh, "negativity_migrations_history")) {
			echo '<p style="color: lime;">Connection sucessfull and Negativity available.</p>';
	    } else {
			echo '<p style="color: lime;">Connection sucessfull !</p><p style="color: red;">Negativity not available.</p>';
	    }
	} catch(PDOException $ex){
		echo '<p style="color: red;">Failed to connect to database:<br>' . $ex->getMessage() . '</p>';
	}
} else {
	echo '<p style="color: red;">Unknow request</p>';
}

?>