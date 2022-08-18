<?php
$linksPath="/home/admin/domains/example.pl/public_html";
$dinurl="https://example.pl";

require_once("/home/admin/domains/adm.example.pl/public_html/ean/eanarray.php");
require('/home/admin/domains/example.pl/public_html/nowy/k_euro.php');

$GoogleCategories = file('../merchant/mapowanie.dat'); $GcatArr=array();
foreach ($GoogleCategories as $line) {$pieces = explode("#", $line); $GcatArr[$pieces[0]]=$pieces[1];}

$ENGoogleCategories = file('../merchant/taxonomy-with-ids.en-US.txt'); $ENGcatArr=array();
foreach ($ENGoogleCategories as $line) {$pieces = explode("-", $line); $ENGcatArr[(int)$pieces[0]]=trim($pieces[1]);}


$xml_header = <<<XMLHEAD
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>Dino</title>
<link>https://example.pl</link>
<description>Strollers, Car seats, Babies Furniture</description>

XMLHEAD;

$xml_footer = <<<XMLFOOT
</channel>
</rss>
XMLFOOT;


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function cleanStr($string) {
   $string = str_replace(' ', '_', $string); 
   return  preg_replace("/[^ \w]+/", "", $string); 
}


function xml_item ($id,$title,$brand,$link,$description,$normal_price,$special_price,
	$product_type,$custom_label_0,$custom_label_1,$custom_label_2,$image_link,$additional_image_link,$mpn,$category_id,$weight,$shipping,$block_country) 
{
	
		        global $GcatArr, $EANarray, $k_euro, $ENGcatArr;

			$x_id = "<g:id>".$id."_en</g:id>\n";
			$x_title = "<g:title><![CDATA[$title]]></g:title>\n";
			$x_description = "<g:description><![CDATA[$description]]></g:description>\n";
			$x_link = "<g:link><![CDATA[$link&language=en]]></g:link>\n";
			$x_image = "<g:image_link><![CDATA[$image_link]]></g:image_link>\n";
			$x_brand = "<g:brand><![CDATA[$brand]]></g:brand>\n";
			$x_mpn = "<g:mpn><![CDATA[$mpn]]></g:mpn>\n";
            	        $x_condition = "<g:condition>new</g:condition>\n";
			$x_availability = "<g:availability>in stock</g:availability>\n";
			//$x_product_type = "<g:product_type><![CDATA[$product_type]]></g:product_type>\n";
			$x_custom_label_0 = "<g:custom_label_0><![CDATA[$custom_label_0]]></g:custom_label_0>\n";
			$x_custom_label_1 = "<g:custom_label_1><![CDATA[$custom_label_1]]></g:custom_label_1>\n";
			$x_custom_label_2 = "<g:custom_label_2><![CDATA[$custom_label_2]]></g:custom_label_2>\n";
			$x_normal_price = "<g:price>".round($normal_price/$k_euro,0)." EUR</g:price>\n";
			
			
			if ($additional_image_link<>"")
				$x_image_lrg = "<g:additional_image_link><![CDATA[$additional_image_link]]></g:additional_image_link>\n";
			else 	$x_image_lrg = "";


			if ($special_price>0) 
				$x_special_price = "<g:sale_price>".round($special_price/$k_euro,0)." EUR</g:sale_price>\n";
			else    $x_special_price=""; 
			
			 $en_gcat_id=$GcatArr[$category_id];
			 $en_gcat_name= htmlspecialchars($ENGcatArr[$en_gcat_id]);
			
			if (array_key_exists((int)$en_gcat_id,$ENGcatArr))
				$x_product_type="<g:product_type><![CDATA[$en_gcat_name]]></g:product_type>\n";				
			else $x_product_type="";
			
			if (array_key_exists((int)$category_id,$GcatArr))
		            	$x_google_category="<g:google_product_category>$GcatArr[$category_id]</g:google_product_category>\n";
		        else 	$x_google_category="";
	
		        if (array_key_exists(intval($id),$EANarray))
			{
			   $x_gtin = "<g:gtin>".$EANarray[$id][0]."</g:gtin>\n";
			    if($EANarray[$id][1]) $bundle="yes"; else $bundle="no";
			   $x_bundle = "<g:is_bundle>$bundle</g:is_bundle>\n";
			} 
			else  $x_gtin = $x_bundle = "";
			
				$x_weight = "<g:shipping_weight>$weight g</g:shipping_weight>\n";
				$shpLabel = "UE";        //if ($shipping==1) $shpLabel="FREE"; else $shpLabel="KG";
				$x_shipping = "<g:shipping_label>$shpLabel</g:shipping_label>\n";
				$x_shopping_ads_excluded_country = $block_country;
			
	
			$xml_item="<item>\n";

				$xml_item.=$x_id;
				$xml_item.=$x_title;
				$xml_item.=$x_description;
				$xml_item.=$x_link;
				
				$xml_item.=$x_image;
				$xml_item.=$x_image_lrg;
				
				$xml_item.=$x_normal_price;
				$xml_item.=$x_special_price;
				
				$xml_item.=$x_gtin; 
				$xml_item.=$x_bundle;
				$xml_item.=$x_brand;
				$xml_item.=$x_mpn;
				
				$xml_item.=$x_condition; 
				$xml_item.=$x_availability;
				$xml_item.=$x_product_type;
				
				$xml_item.=$x_custom_label_0;
				$xml_item.=$x_custom_label_1;
				$xml_item.=$x_custom_label_2;
				
				$xml_item.=$x_google_category;
				
				$xml_item.=$x_weight;
				$xml_item.=$x_shipping;
				$xml_item.=$x_shopping_ads_excluded_country;
						

			$xml_item.="</item>\n";


			
	return $xml_item;	
}	


function colour_xml_item ($id,$title,$brand,$link,$description,$normal_price,$special_price,$product_type,$mpn, $custom_label_2, $category_id,$colours,$weight,$shipping,$block_country)
{
	
		        global $GcatArr, $EANarray, $linksPath, $dinurl, $k_euro, $ENGcatArr; 
				$nothing=""; $xml_item="";  $num=1;
		        
		        if (array_key_exists(intval($id),$EANarray)) return $nothing;
				
				$pieces = explode(",", $colours); $ahref = explode("|", $pieces[0]);
				$k_anchor=$ahref[0]; $k_url=$ahref[1];
				
				if ( stristr($k_anchor, 'kolor' ) and stristr($k_url, 'linki' ) ) 		
		        {
					
					 $LnkPath=$linksPath.trim($k_url);
					
				
				   if (file_exists($LnkPath)) 
				   {
				      $colFile=file_get_contents($LnkPath);
				      $koloryStr=get_string_between($colFile,"zdjecia=new Array(",");");
					  $koloryStr=str_replace('"','',$koloryStr);
					  
					   $kolArr=explode("," , $koloryStr);
                       
                       for ($i=1; $i<count($kolArr); $i+=3)
					   {
						   $mainCol[trim($kolArr[$i])]=cleanStr($kolArr[$i+1]);
					   }   
				  
				   } else return $nothing;
				   
				   
				   foreach ($mainCol as $zdjecie => $nazwakoloru)
				   {
					    
					    $img=$dinurl.trim(dirname($k_url))."/".$zdjecie;
						$kid=str_replace(".jpg","",$zdjecie);
						
						$x_id = "<g:id>".$id."_en_C".$num++."</g:id>\n";
						$x_title = "<g:title><![CDATA[$title | $nazwakoloru]]></g:title>\n";
						$x_description = "<g:description><![CDATA[".mb_substr($description, 0, 1000, "UTF-8")."]]></g:description>\n";
						$x_link = "<g:link><![CDATA[$link&language=en#$kid]]></g:link>\n";
						$x_image = "<g:image_link><![CDATA[$img]]></g:image_link>\n";
						$x_brand = "<g:brand><![CDATA[$brand]]></g:brand>\n";
						$x_mpn = "<g:mpn><![CDATA[$mpn]]></g:mpn>\n";
						$x_custom_label_2 = "<g:custom_label_2><![CDATA[$custom_label_2]]></g:custom_label_2>\n";
						$x_condition = "<g:condition>new</g:condition>\n";
						$x_availability = "<g:availability>in stock</g:availability>\n";
						//$x_product_type = "<g:product_type><![CDATA[$product_type]]></g:product_type>\n";
						$x_normal_price = "<g:price>".round($normal_price/$k_euro,0)." EUR</g:price>\n";
						

						if ($special_price>0) 
							$x_special_price = "<g:sale_price>".round($special_price/$k_euro,0)." EUR</g:sale_price>\n";
						else    $x_special_price=""; 
						
						$en_gcat_id=$GcatArr[$category_id];
						$en_gcat_name= htmlspecialchars($ENGcatArr[$en_gcat_id]);
			
						if (array_key_exists((int)$en_gcat_id,$ENGcatArr))
						  $x_product_type="<g:product_type><![CDATA[$en_gcat_name]]></g:product_type>\n";				
						else 	$x_product_type="";
						
						
						if (array_key_exists((int)$category_id,$GcatArr))
								$x_google_category="<g:google_product_category>$GcatArr[$category_id]</g:google_product_category>\n";
						else 	$x_google_category="";
					   
					    $x_weight = "<g:shipping_weight>$weight g</g:shipping_weight>\n";
						$shpLabel = "UE";        //if ($shipping==1) $shpLabel="FREE"; else $shpLabel="KG";
						$x_shipping = "<g:shipping_label>$shpLabel</g:shipping_label>\n";
					    $x_shopping_ads_excluded_country = $block_country;
					   			
						$xml_item.="<item>\n";

							$xml_item.=$x_id;
							$xml_item.=$x_title;
							$xml_item.=$x_description;
							$xml_item.=$x_link;
							$xml_item.=$x_image;
							$xml_item.=$x_normal_price;
							$xml_item.=$x_special_price;
							$xml_item.=$x_brand;
							$xml_item.=$x_mpn;
							$xml_item.=$x_custom_label_2;
							$xml_item.=$x_condition; 
							$xml_item.=$x_availability;
							$xml_item.=$x_product_type;
							$xml_item.=$x_google_category;
							$xml_item.=$x_weight;
							$xml_item.=$x_shipping;
							$xml_item.=$x_shopping_ads_excluded_country;
							

						$xml_item.="</item>\n";
					   
				   }   
				  
					
			}
			else return $nothing;


			
	return $xml_item;	
}	

?>
