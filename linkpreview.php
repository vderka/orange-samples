<!DOCTYPE html>
<html>

<head>
	<title>Podglad linki</title>
	<meta charset="UTF-8">
	
	<style>
		.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 16px 32px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  transition-duration: 0.4s;
		  cursor: pointer;
		  font-family:Verdana;
		}

		.button2:hover {
		  background-color: white;
		  color: black;
		  border: 2px solid #008CBA;
		}

		.button2 {background-color: #008CBA; font-size:24px;  margin:70px;}  

		.button3 {background-color:#f44336;}


		pre
		{
			width: 60%;
			padding: 10px;
			margin: 30px;
			overflow: auto;
			overflow-y: hidden;
			font-size: 12px;
			line-height: 20px;
			background: #efefef;
			border: 1px solid #777;
		}	


	</style>
	
</head>
<body style="font-family:Verdana">

	<?php

		$id=$_POST['product_id'];
		$do_zapisu="";
		$anchors=array();
		$urls=array();
		$remove=array("https://example.pl","https://www.example.pl","http://www.example.pl","http://www.example.pl","www.example.pl","example.pl");

		foreach ($_POST["anchors"] as $value) 
		{
			if (empty($value)) 
				$anchors[] ="X";
			else
				$anchors[]=trim($value)." |";
		}

		foreach ($_POST["urls"] as $value) 
		{
			
			if (empty($value)) 
				$urls[] ="X";
			else
			{		
				$value=filter_var($value, FILTER_SANITIZE_URL);
				$value=str_replace($remove,"",$value);
				$findhttp = substr($value,0,4);
				
				if (($findhttp <> "http") and $value[0]<>"/") $value="/".$value;
				$urls[]=strtolower(trim($value));
				
			}	
		}

				for($i=0;$i<count($anchors);$i++)
				{
					if ($anchors[$i]<>"X" and $urls[$i]<>"X")
						$do_zapisu.=$anchors[$i]." ".$urls[$i].",";
				}	
				

				echo "<a href='linkbyid.php?product_id=$id'>&larr; Wstecz</a> | "," Produkt: ", $id , "<pre>".str_replace(",",",<br>",$do_zapisu)."</pre>";
				
							  $links = explode(",",$do_zapisu);
						
									foreach ($links as $pair) 
									{
										$a_url = explode ("|", $pair);
										if ($a_url[0])
										{
											
											
																						
											$find_linki  = strpos($a_url[1], "linki");
											$find_search  = strpos($a_url[1], "search");
											$find_category =   strpos($a_url[1], "-c-");
											$find_product  =   strpos($a_url[1], "-p-");
											$find_percent = strpos($a_url[1], "%");
											
											if (($find_linki === false) and ($find_search === false) and ($find_category === false) and ($find_product === false)) $error=" button3"; else $error="";
											if ($find_percent === false) $eproc=""; else $eproc=" [ USUŃ Z LINKU % ]";
											
											
											 $findhttp = substr(trim($a_url[1]), 0, 4);
											if ($findhttp <> "http") $lnk="https://example.pl".trim($a_url[1]); else $lnk=trim($a_url[1]);
																		
										  
										  echo '<a href="'.$lnk.'" class="button'.$error.'" target="_blank">'.trim($a_url[0]).$eproc.'</a> ';
										 
										}
											  
									}
	?>

	<p><a href="saveLink.php?id=<?php echo $id;?>&txt=<?php echo urlencode($do_zapisu);?>" class="button button2">Zapisz linki</a></p>
	
</body>
</html>
