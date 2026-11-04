<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    // Sanitize the input to ensure it's only alphanumeric (prevents injection)
    $filename = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['refsetid']);
    $table_name = "referenceset_" . $filename;

    // Clear old .csv's
    array_map('unlink', glob(ABS_PATH . "references/*.csv"));

    try {
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	]);

	// Open the file for writing
	$file_path = ABS_PATH . "references/" . $table_name . ".csv";
	$fp = fopen($file_path, 'w');

	$stmt = $pdo->query("SELECT * FROM `$table_name`");

	// first row for headers
	if ($row = $stmt->fetch()) {
            fputcsv($fp, array_keys($row), ",", '"', ""); // column names
            fputcsv($fp, $row, ",", '"', "");             // first row data
            
            while ($row = $stmt->fetch()) {
		fputcsv($fp, $row, ",", '"', ""); // rows
            }
	}

	fclose($fp);
	echo $filename;

    } catch (PDOException $e) {
	error_log("Export failed: " . $e->getMessage());
	echo "error";
    }

}

?>
