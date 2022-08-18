<?php

function zen_get_dino_prom($product_id) 
{
    global $db;

   // $D_ptoc = $db->Execute("select categories_id  from products_to_categories where products_id = '" . (int)$product_id . "'");
   $D_ptoc = $db->Execute(" 
		SELECT products_to_categories.categories_id, categories.parent_id  FROM products_to_categories 
		JOIN categories
		ON products_to_categories.categories_id = categories.categories_id
		WHERE products_to_categories.products_id  = 
   '" . (int)$product_id . "'");

	$D_cid  = $db->Execute("select category_id, coupon_id from coupon_restrict");
	
	$arr_ptoc=array(); $arr_cid=array(); $arr_res=array();
	
	
		if ($D_ptoc->RecordCount() > 0 and $D_cid->RecordCount() > 0) 
		{
		  //while (!$D_ptoc->EOF) { $cat=(int)$D_ptoc->fields['categories_id']; $arr_ptoc[]=$cat; $D_ptoc->MoveNext();}	
		   while (!$D_ptoc->EOF) 
		  { 
			  $cat=(int)$D_ptoc->fields['categories_id']; $par=(int)$D_ptoc->fields['parent_id'];
			  $arr_ptoc[]=$cat;  $arr_ptoc[]=$par;
			  $D_ptoc->MoveNext();
		  }	
		  
		  while (!$D_cid->EOF) 
		  { 
			  $res=(int)$D_cid->fields['category_id']; $arr_cid[]=$res;  
			  $kid=(int)$D_cid->fields['coupon_id']; $arr_res[$res]=$kid;   
			  $D_cid->MoveNext();
		  }	
		 
		 $arr_ptoc=array_unique($arr_ptoc);
		 
				  foreach ($arr_cid as $value) 
				  {
						if (in_array($value, $arr_ptoc))
						{
							$kupon_id=$arr_res[$value];
						    $rabat=$db->Execute("select coupon_amount from coupons where coupon_id = '" . (int)$kupon_id . "'");
						    $obrazek=(int)$rabat->fields['coupon_amount'];
							return $obrazek;
						} 
						
				  }
		}

}
/**************************************************************************************************************************/
function zen_get_dino_coupon_price($string,$coupon,$precision)
{
	
	$string=strip_tags($string);
	$string=str_replace(",","",$string);
	preg_match_all('!\d+(?:\.\d+)?!', $string, $matches);
	$floats = array_map('floatval', $matches[0]);

	if (count($floats)>1) $price=$floats[1]; else $price=$floats[0];

	 return number_format(($price*((100-$coupon) / 100)),$precision,"."," ");
	
}	
/*************************************************************************************************************************/
 function zen_get_colours_lang($product_id, $jezyk) 
 {
    global $db;
	
	
	$product_query = "select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id =2 limit 1";

    $product = $db->Execute($product_query);
	
	if (strlen($product->fields['products_url'])>0)
	{			 
                $links = explode(",",$product->fields['products_url']);
				
							foreach ($links as $pair) 
							{
								$a_url = explode ("|", $pair);
								  
								  if (strlen($a_url[0])>0 and strlen($a_url[1])>0 )
								  { 
									   if ($jezyk=="en" OR $jezyk=="de")
									   {

											$klucz = mb_strtolower($a_url[0], 'UTF-8');
											$slowo=(str_word_count($klucz, 1,'ąćęłńóśźż'));
											
											
											if (in_array($slowo[2],$triple))
											   $klucz=$slowo[0].$slowo[1].$slowo[2];
										    else 
											   $klucz=$slowo[0].$slowo[1];
											
											
												if (array_key_exists($klucz,$KOL_PL)) 
													$anchor=$KOL_PL[$klucz];
												else
													$anchor=$KOL_PL["std"];
											
								       }
									   else
										   $anchor=$a_url[0];
									   
									    echo '<a href="'.trim($a_url[1]).'" class="button kolory" target="_blank">'.trim($anchor).'</a> ';
								  }  
									  
							}
					   
	}
	  
	
 }
