<?php


	require ('../includes/configure.php');
	$servername = DB_SERVER;
	$username   = DB_SERVER_USERNAME;
	$password   = DB_SERVER_PASSWORD;
	$dbname     = DB_DATABASE;

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

			$sql = "SELECT products_id, products_url
			FROM products_description
			WHERE language_id=2
			ORDER BY products_id ASC";
			
	$result = $conn->query($sql);
	$conn->close();

$info = "/* BACKUP SLOGAN ".date("Y-m-d H:i:s")." */ \n";
file_put_contents("backup.sql",$info);


			if ($result->num_rows > 0) 
			{
									
				while($row = $result->fetch_assoc())
				{     

						$txt = "UPDATE products_description SET products_url = '".$row['products_url']."' WHERE products_id=".$row["products_id"]." AND language_id=2;\n"; 
						file_put_contents("backup.sql",$txt, FILE_APPEND);
                }
			
			}
			
			echo "OK - kopia poprawna"; 
?>
