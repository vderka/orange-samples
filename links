<?php 

/**********************************************/
$pokaz=$_GET["pokaz"]; 

$warunek="";
if  ($pokaz=="on") $warunek=" AND products.products_status=1 ";
if  ($pokaz=="off") $warunek=" AND products.products_status=0 ";

/*********************************************/

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

			$sql = "SELECT products.products_id, products.products_status,  products_description.products_name, products_description.products_description
			FROM products
			INNER JOIN products_description ON products.products_id=products_description.products_id WHERE products_description.language_id=2".$warunek."
			ORDER BY products_id ASC";
			
	$result = $conn->query($sql);
	$conn->close();

?>
<!DOCTYPE html>
<html lang="pl">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">

  <title>Weryfikator linkow</title>
<style>
.off {background-color:#ffcccc}
.on {background-color:#ccffcc}
.found {float:left; width: 550px; font-weight:bold;}
.slogan {padding:5px; background-color: #e6e6e6; margin:10px;}
.ile {background-color:#ccccff; padding:5px;}
.siz {font-size:13px;}
</style>
</head>

<body style="font-family:Verdana;">

<a href="?pokaz=all">Pokaż Wszystkie</a> <hr> <a href="?pokaz=on">Pokaż Aktywne</a> <hr><a href="?pokaz=off">Pokaż Nieaktywne</a>


<br><hr><br>
<?php
			
		//$myfile = fopen("data.sql", "w") or die("Unable to open file!");
		$bledy=array();
		$klucze=array();
		$pol_klucze=array();
			
			if ($result->num_rows > 0) 
			{
									
				while($row = $result->fetch_assoc())
				{  
			
			      $num_links=0; $num_kol=0; $slogan="";

					
					if ($row["products_status"]) $styl="on"; else $styl="off";
					
					echo "<div><p class='$styl'><b>", $row["products_id"] , " - " , $row["products_name"], "</b></p>";
					

							$products_description=$row["products_description"];;
				
							$dom = new DOMDocument;
							@$dom->loadHTML(mb_convert_encoding($products_description, 'HTML-ENTITIES', 'UTF-8'));
							$xpath = new DOMXPath($dom);
							
							$szukane_slowa=array("kolo","funkcj","galer","typ","rodz","kolek","elem","akces","pok","ofer","ciel","eczka","bezpiec","opcj");
							$zakazany_url="opinie";
							
							$nodeList = $xpath->query('//fieldset//a');
							//print_r($nodeList); 
							foreach ($nodeList as $node) 
							{	
							
							 $kol_anchor=$node->textContent;	
							 
									if ($kol_anchor<>'')
									{	 
										
											  foreach ($szukane_slowa as $slowo)
											  {
												  if ((stripos($kol_anchor, $slowo)) !== false)
												  {
													$kol_url=$node->getAttribute('href');
													if ((stripos($kol_url, $zakazany_url)) == false)
													{
													  
							                          $kol_url=str_ireplace($dino_del,'',$kol_url);
													  
													  if ((stripos($kol_url, "%")) !== false) 
													  {	  
														  $kol_url=str_replace ($zle,$dobre,$kol_url); 
														  
													  }	  
													  
													      $klucz=mb_strtolower($kol_anchor, 'UTF-8');
													  	  $po_dwa=(str_word_count($klucz, 1,'ąćęłńóśźż'));
														  $slowo_razem=$po_dwa[0].$po_dwa[1];
														  //$klucz = mb_strtolower($slowo_razem, 'UTF-8');
														  $klucze[trim($slowo_razem)]++;	
														  $pol_klucze[trim($kol_anchor)]++;	
														  
													  $slogan.= trim($kol_anchor)." | " . strtolower(trim($kol_url)). ", ";
													  ++$num_kol;
													  
													}  
													
													break;									 
												  }
											  }	  
									
									}
									
								 echo "<p class='siz'><span class='found'>", ++$num_links , ": ", $kol_anchor ,"</span> ", $kol_url=$node->getAttribute('href')."</p>";
							}	                 
							                      if ($num_links<>$num_kol) $danger="<b>!!!</b>"; else $danger="";
												  
													echo "<p class='ile'>$danger Znaleziono: $num_links - Pobrano: $num_kol</p>";
												 
                                                   if ($slogan<>"")
												   {	   
														echo "<div class='slogan'>";
															echo str_replace(",",",<br>",$slogan);
														echo "</div>";
															
												   }	
												   
													  /* $txt = "UPDATE products_description SET products_url = '$slogan' WHERE products_id=".$row["products_id"]." AND language_id=2;\n"; 
													   fwrite($myfile, $txt);*/
												   
												    if ((stripos($slogan, "%")) !== false) $bledy[]=$slogan;
													if ((stripos($slogan, "zenid")) !== false) $bledy[]=$slogan;
													
													$slogan=""; $num_kol=0;


					
						echo '<hr style="border:1px dashed red"></div>'; 
				 

				}
			}
			
			
	 //fclose($myfile);	
 
		 foreach($bledy as $key) 
		 {
		   echo $key;
		   echo "<hr>";
		 }
			
	   echo "<br><br><br>";
			
		 foreach($zle as $key) 
		 {
		   echo $key;
		   echo "<hr>";
		 }
		 
		echo "<br><br><br>";
		
		  foreach($dobre as $key) 
		 {
		   echo $key;
		   echo "<hr>";
		 }
		 
		echo "<br><br><br>";
		
		 print_r($dino_del);
		 
	    echo "<br><br><br>";
		
		 print_r($szukane_slowa);
		 
		 echo "<br><br><br>";
			
			arsort ($klucze); $i=1;
			
		foreach($klucze as $key => $value) 
		 {
		   echo $i++," | ", $key , " => " .$value;
		   echo "<hr>";
		 }
		 
		  echo "<br><br><br>";
		 
		 foreach($klucze as $key => $value) 
		 {
		   echo '$KOL_PL["'.trim($key).'"] = "";'; 
		   echo "<br>";
		 }
		 
		 		 echo "<br><br><br>";
			
			arsort ($pol_klucze); $i=1;
			
		foreach($pol_klucze as $key => $value) 
		 {
		   echo $i++," | ", $key , " => " .$value;
		   echo "<hr>";
		 }
?>

</body>

</html>
