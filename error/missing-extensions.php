<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>litebans-php - Missing Extensions</title>
    <link href="../include/css/main.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="jumbotron">
        <h2>Missing Extensions</h2><br>
        <div class="text-warning">
            The following PHP extensions are required by litebans-php but were not found:
            <br>
            <?php if (!extension_loaded("pdo_mysql")) {
                echo "- <a class=\"text-danger\">pdo_mysql</a>";
            }
            $phpini = php_ini_loaded_file();

            echo "These extensions can be enabled in php.ini.<br><br>";
            echo "php.ini location: <a class=\"text-info\">" . $phpini . "</a><br>";
            ?>
        </div>
        <br>
        <a href="../" class="btn btn-default">Try Again</a>
    </div>
</div>
</body>
</html>
