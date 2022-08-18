<?php
$catalog="google_en";

require ("/home/admin/domains/adm.example.pl/public_html/includes/configure.php");
require ("../inc/mainVars.php");
include ("xml.php");

$XMLfile=$xml.$catalog."/".$catalog.".xml";
$fileKolorystyka=$xml.$catalog."/".$sepcialXml[$catalog].".xml";
/******************************************************************************************/

$manIDs = file("dat/manuf.dat");
$catIDs = file("dat/categories.dat");
$prodIDs = file("dat/products.dat");
$delArr = file("dat/names.dat",FILE_IGNORE_NEW_LINES);
/*******************************************************/
$cntrs = file("dat/country.dat",FILE_IGNORE_NEW_LINES);
$countries=array();
$cstart="<g:shopping_ads_excluded_country>";
$cend="</g:shopping_ads_excluded_country>";
$cmid=$cend."\n".$cstart;

foreach ($cntrs as $cntr) 
{
	$kraj = explode("#", $cntr);
	$countries [(int)$kraj[0]]= $cstart.str_replace(",",$cmid,$kraj[1]).$cend;	
}
/*********************************************************/

$servername = DB_SERVER; $username = DB_SERVER_USERNAME; $password = DB_SERVER_PASSWORD; $dbname = DB_DATABASE;


	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

		$sql = '
				   SELECT DISTINCT 
				   p.products_id, 
				   p.products_model, 
				   p.products_weight,
				   p.product_is_always_free_shipping,
				   p.products_price AS normal_price,
				   s.specials_new_products_price AS special_price, 
				   p.products_image, 
				   pl.products_url AS kolory,
				   p.master_categories_id,
				   d.products_name, 
				   d.products_description, 
				   c.categories_name, 
				   m.manufacturers_id, 
				   m.manufacturers_name, 
				   t.tax_rate 
				   FROM products p 
				   LEFT JOIN specials s ON p.products_id = s.products_id 
				   LEFT JOIN products_description d ON p.products_id = d.products_id 
				   LEFT JOIN categories_description c ON c.categories_id=p.master_categories_id 
				   LEFT JOIN manufacturers m ON p.manufacturers_id = m.manufacturers_id 
				   LEFT JOIN tax_rates t ON p.products_tax_class_id=t.tax_class_id 
				   LEFT JOIN products_description pl ON p.products_id = pl.products_id 				   
				   WHERE p.products_type=1
				   AND p.products_status=1 
				   AND p.products_virtual=0 
				   AND p.product_is_free=0 
				   AND p.product_is_call=0 
				   AND d.language_id=3 
				   AND c.language_id=3
				   AND pl.language_id=2
				   AND m.manufacturers_id IN ('.implode(",",$manIDs).')
				   AND p.master_categories_id IN ('.implode(",",$catIDs).')
			   ';
			   
		$result = $conn->query($sql);
		
        	file_put_contents($XMLfile, $xml_header, LOCK_EX);
        	file_put_contents($fileKolorystyka, $xml_header,  LOCK_EX);
        	

				if ($result->num_rows > 0) 
				{
					
					while($row = $result->fetch_assoc())
					{
							if  (!in_array((int)$row["products_id"],$prodIDs))
							{
								/*****************************************************/
								 
								$title = trim(str_ireplace($delArr,"",($row["products_name"])));
								/*******/
								$UPdelArr = array_map('mb_strtoupper', $delArr);
								$title = trim(str_ireplace($UPdelArr,"",$title));
								/*******/
								$link = $shopurl."index.php?main_page=product_info&products_id=".(int)$row["products_id"];
								
								/****************************************************/
								
								$description = $row["products_description"];
								//$description = htmlentities($description, null, 'utf-8');
								$description = str_replace("&nbsp;", "", $description);
								$description = preg_replace( "/\s+/", " ",$description);
								$description = html_entity_decode($description);
								$description = mb_substr(strip_tags($description,"<li>"), 0, 5000, "UTF-8");
                                $description = iconv("UTF-8","UTF-8//IGNORE",$description);
                                    				
                                /****************************************************/
                                    				
								$id = (int)$row["products_id"];
								$brand = trim($row["manufacturers_name"]);
								$mpn = str_replace(" ","_",strtoupper(trim($row["manufacturers_name"])))."-".(int)$row["products_id"];
								
								$weight=$row["products_weight"];
								$shipping=$row["product_is_always_free_shipping"];
								
								/**************************************************/
								
								$normal_price = number_format(($row['normal_price']*((100+$row['tax_rate'])/100)), 2, '.', '');
									$normal_price = round($normal_price,0);//intval($normal_price);
								$special_price = number_format(($row['special_price']*((100+$row['tax_rate'])/100)), 2, '.', '');
									$special_price = round($special_price,0); //intval($special_price);
								
								/**************************************************/
			
								$product_type = trim($row["categories_name"]);
								$custom_label_0 = trim($row["categories_name"]);
								
								if ($special_price>0) $conversion_price=intval($special_price); else $conversion_price=intval($normal_price);
								
								
								if ($conversion_price<=600) $price_section="P600";
								elseif ($conversion_price>600 and $conversion_price<=1200)  $price_section="P1200";
								elseif ($conversion_price>1200 and $conversion_price<=1800) $price_section="P1800";
								elseif ($conversion_price>1800 and $conversion_price<=2400) $price_section="P2400";
								elseif ($conversion_price>2400 and $conversion_price<=3000) $price_section="P3000";
								elseif ($conversion_price>3000) $price_section="PEX";
								
								$custom_label_1 = $price_section;

								if ($conversion_price<=999) $conversion_rate="K100";
								elseif ($conversion_price>999 and $conversion_price<=1999) $conversion_rate="K300";
								elseif ($conversion_price>1999) $conversion_rate="K600";

								$custom_label_2 = $conversion_rate;
								
								/****************************************************************/
								
								$image_link = $shopurl."images/".trim($row["products_image"]);
								$big_photo = str_replace("_m.jpg","_d.jpg",trim($row["products_image"]));
								if (file_exists($imgpath.$big_photo)) 
									$additional_image_link = $shopurl."images/".$big_photo; 
								else $additional_image_link = "";
								
								/***************************************************************/
								
							       $category_id = (int)$row["master_categories_id"];
							       $colours = trim($row["kolory"]);
								   
								/**************************************************************/
								if (array_key_exists($id, $countries)) 
									$block_country=$countries[$id]."\n"; 
								else 
									$block_country="";
								/**************************************************************/
							       
									 
							$item_to_save = xml_item ($id,$title,$brand,$link,$description,$normal_price,$special_price,$product_type,$custom_label_0,$custom_label_1, $custom_label_2, $image_link,$additional_image_link,$mpn,$category_id,$weight,$shipping, $block_country);
							
							 file_put_contents($XMLfile, $item_to_save,  FILE_APPEND | LOCK_EX); 
								 
								 
								 /**************************************************/
								// $colours="Kolory wózka | /linki/test.htm, kolory fotelika | nic.htm";
								 
		$colour_items_to_save = colour_xml_item ($id,$title,$brand,$link,$description,$normal_price,$special_price,$product_type, $mpn, $custom_label_2, $category_id,$colours,$weight,$shipping, $block_country);
							
							 file_put_contents($fileKolorystyka, $colour_items_to_save,  FILE_APPEND | LOCK_EX); 
								 
							}	
					}
				}
		
	$conn->close();
	

	 file_put_contents($XMLfile, $xml_footer,  FILE_APPEND | LOCK_EX);
	 file_put_contents($fileKolorystyka, $xml_footer,  FILE_APPEND | LOCK_EX);  
         		
?>
<p>&larr; OK - <a href="../index.php">Panel administracyjny</a></p>
