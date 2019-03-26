<?php
$file = fopen("php://input","r");
$jsonInput ="";

while(!feof($file))
{
	$jsonInput .= fgets($file);	
}
fclose($file);

$input_params = json_decode($jsonInput,true);

    include("include/common_vars.php");
	include("include/common_class.php");
	include("include/session.php");
	include("include/config.php");
	include("include/function.php");
	include("include/classes/SMTPClass.php");
	include("include/classes/PHPMailer.class.php");
	include("include/classes/SMTP.class.php");
	include("include/postNotification.php");	

   define("mode",mysql_real_escape_string($input_params['mode']));
   define("reg_fname",mysql_real_escape_string($input_params['reg_fname']));
   define("reg_lname",mysql_real_escape_string($input_params['reg_lname']));
   define("reg_id",mysql_real_escape_string($input_params['reg_id']));
   define("reg_mobile",mysql_real_escape_string($input_params['reg_mobile']));
   define("reg_email",mysql_real_escape_string($input_params['reg_email']));
   define("reg_password",mysql_real_escape_string($input_params['reg_password']));
  define("reg_address",mysql_real_escape_string($input_params['reg_address'])); 
  define("cat_id",mysql_real_escape_string($input_params['cat_id']));
   define("cat_name",mysql_real_escape_string($input_params['cat_name']));
   define("cat_image",mysql_real_escape_string($input_params['cat_image']));
   define("pro_id",mysql_real_escape_string($input_params['pro_id']));
   define("pro_name",mysql_real_escape_string($input_params['pro_name']));
   define("pro_image",mysql_real_escape_string($input_params['pro_image']));
   define("pro_desc",mysql_real_escape_string($input_params['pro_desc']));
   define("pro_price",mysql_real_escape_string($input_params['pro_price']));
  define("pro_usefor",mysql_real_escape_string($input_params['pro_usefor']));
  define("pro_type",mysql_real_escape_string($input_params['pro_type']));
  define("pro_color",mysql_real_escape_string($input_params['pro_color']));
  define("pro_storagespace",mysql_real_escape_string($input_params['pro_storagespace']));
  define("pro_shape",mysql_real_escape_string($input_params['pro_shape']));
  define("pro_brand",mysql_real_escape_string($input_params['pro_brand']));
  define("pro_material",mysql_real_escape_string($input_params['pro_material']));
  define("pro_folded",mysql_real_escape_string($input_params['pro_folded']));
  define("quantity",mysql_real_escape_string($input_params['quantity']));
  define("cart_id",mysql_real_escape_string($input_params['cart_id']));
  define("total_price",mysql_real_escape_string($input_params['total_price']));
  define("user_id",mysql_real_escape_string($input_params['user_id']));
  define("product_id",mysql_real_escape_string($input_params['product_id']));
  define("reg_newpassword",mysql_real_escape_string($input_params['reg_newpassword']));
  

  $Productpic="http://192.168.1.16:8080/FCB/upload/";
  $CategoryPic="http://192.168.1.16:8080/FCB/images/";
 
if(mode=="register")
{
	$reg_fname=reg_fname;
	$reg_lname=reg_lname;
	$reg_mobile=reg_mobile;
	$reg_email=reg_email;
  	$reg_password=reg_password;
	$reg_address=reg_address;
 
	if (empty($reg_fname)||empty($reg_lname)||empty($reg_mobile)||empty($reg_email)||empty($reg_password)) 
	{
		header('Content-type: application/json'); 
		echo json_encode(array("Status"=>0,"Message"=>"Please fill all require fields."));
	}
	else
	{
		  $check_email_query = $con->select_query("reg","*","where reg_mobile='".$reg_mobile."' OR reg_email='".$reg_email."'","","");
            $rowFetch = mysql_fetch_assoc($check_email_query);
            
            	if(mysql_num_rows($check_email_query)>0)
            	{
               		header('Content-type: application/json');
               		echo json_encode(array("Status"=>0,"Message"=>"Mobile Number OR Email is already exists"));
            	}
		else
		{
			$arruser=array(
				"reg_fname"=>$reg_fname,
				"reg_lname"=>$reg_lname,
				"reg_mobile"=>$reg_mobile,
        		"reg_email"=>$reg_email,
        		"reg_password"=>$reg_password,
				"reg_address"=>$reg_address
			);
			$insertUser = $con->insert_record("reg",$arruser);

			
			header('Content-type: application/json');
			echo json_encode(array("Status"=>1,"User"=>$arruser,"Message"=>"You add Register Successfully"));
	}
}

}



else if(mode=="userprofile")
{
	$reg_id=reg_id;
	if (empty($reg_id))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$CountryDetailsQuery = $con->select_query("reg","*","where reg_id='".$reg_id."'","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["reg_id"]=intval($row['reg_id']);
					$CountryList[$x]["FirstName"]=$row['reg_fname'];
					$CountryList[$x]["LastName"]=$row['reg_lname'];
					$CountryList[$x]["Mobile"]=$row['reg_mobile'];
					$CountryList[$x]["Email"]=$row['reg_email'];
					$CountryList[$x]["Address"]=$row['reg_address'];
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"UserDetails"=>$CountryList,"Message"=>"User Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
}     
}


elseif (mode=="login") 
{
   	$reg_mobile=reg_mobile;
	$reg_password=reg_password;
	
	if (empty($reg_mobile)||empty($reg_password))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		
		 	$check_loginQuery=mysql_query("select * FROM reg where reg_mobile='".$reg_mobile."' AND reg_password='".$reg_password."'");
			if($con->total_records($check_loginQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($check_loginQuery))
				{
					$CountryList[$x]["reg_id"]=intval($row['reg_id']);
					$CountryList[$x]["FirstName"]=$row['reg_fname'];
					$CountryList[$x]["LastName"]=$row['reg_lname'];
					$CountryList[$x]["Mobile"]=$row['reg_mobile'];
					$CountryList[$x]["Email"]=$row['reg_email'];
					$CountryList[$x]["Address"]=$row['reg_address'];
					$x++;
				}	
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"UserDetails"=>$CountryList,"Message"=>"Your Login Successfully"));			
			}
			else
			{
				header('Content-type: application/json');
				echo json_encode(array("Status"=>0,"Message"=>"Mobile Number Or Password are Invalid.."));
			}
		
			}	                  
} 




elseif (mode=="addcategory") 
{
	$cat_name=cat_name;
	$cat_image=cat_image;
	
	
	if (empty($cat_name)||empty($cat_image)) 
	{
		header('Content-type: application/json'); 
		echo json_encode(array("Status"=>0,"Message"=>"Please fill all require fields."));
	}
	else
	{
		 
			$arruser=array(
        		"cat_name"=>$cat_name
			);
			$insertUser = $con->insert_record("category",$arruser);
			$cat_id1=mysql_insert_id();
			if($cat_image != "")
			{
				$binarytoimage = binarytoimage_doctor($cat_image,$cat_name,$cat_id1);
						$fields_image =array("cat_image"=>$binarytoimage);
						$images_insert=$con->update("category",$fields_image," where cat_id=".$cat_id1);
			}
			// if ($cat_image!='') 
			// {
			// 	$binarytoimage = binarytoimageSmall($cat_image,$cat_name,$cat_id);
			// 	$fields_image =array("cat_image"=>$binarytoimage);
			// 	$images_insert=$con->update("category",$fields_image," where cat_id=".$cat_id);
			// }
			header('Content-type: application/json');
			echo json_encode(array("Status"=>1,"Message"=>"Category add Successfully"));
	}
} 


elseif (mode=="viewcategory") 
{
	
		$CountryDetailsQuery = $con->select_query("category","*","","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["cat_id"]=intval($row['cat_id']);
					$CountryList[$x]["CategoryName"]=$row['cat_name'];
					$CountryList[$x]["CategoryImage"]=$CategoryPic.$row['cat_image'];
					
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"CategoryDetails"=>$CountryList,"Message"=>"Category Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
  
} 

elseif (mode=="addproduct") 
{
	$pro_name=pro_name;
	$cat_id=cat_id;
   	$pro_image=pro_image;
   	$pro_desc=pro_desc;
   	$pro_price=pro_price;
  	$pro_usefor=pro_usefor;
 	$pro_type=pro_type;
  	$pro_color=pro_color;
  	$pro_storagespace=pro_storagespace;
  	$pro_shape=pro_shape;
  	$pro_brand=pro_brand;
  	$pro_material=pro_material;
  	$pro_folded=pro_folded;
	
	
	if (empty($cat_id)||empty($pro_name)||empty($pro_desc)||empty($pro_price)||empty($pro_usefor)||empty($pro_type)||empty($pro_color)||empty($pro_storagespace)||empty($pro_shape)||empty($pro_brand)||empty($pro_material)||empty($pro_folded)) 
	{
		header('Content-type: application/json'); 
		echo json_encode(array("Status"=>0,"Message"=>"Please fill all require fields."));
	}
	else
	{
		 
			$arruser=array(
        		"pro_name"=>$pro_name,
        		"cat_id"=>$cat_id,
        		"pro_desc"=>$pro_desc,
        		"pro_price"=>$pro_price,
        		"pro_usefor"=>$pro_usefor,
        		"pro_type"=>$pro_type,
        		"pro_color"=>$pro_color,
        		"pro_storagespace"=>$pro_storagespace,
        		"pro_brand"=>$pro_brand,
        		"pro_shape"=>$pro_shape,
        		"pro_material"=>$pro_material,
        		"pro_folded"=>$pro_folded
			);
			$insertUser = $con->insert_record("product",$arruser);
			$pro_id=mysql_insert_id();
			if ($pro_image!='') 
			{
				$binarytoimage = binarytoimage_user($pro_image,$pro_name,$pro_id);
				$fields_image =array("pro_image"=>$binarytoimage);
				$images_insert=$con->update("product",$fields_image," where pro_id=".$pro_id);
			}
			header('Content-type: application/json');
			echo json_encode(array("Status"=>1,"Message"=>"Product add Successfully"));
	}
} 
                                  


elseif (mode=="viewproduct") 
{
		$pro_id=pro_id;
	if (empty($pro_id))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$CountryDetailsQuery = $con->select_query("product","*"," INNER JOIN category on category.cat_id=product.cat_id where product.pro_id='".$pro_id."'","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["pro_id"]=intval($row['pro_id']);
					$CountryList[$x]["CategoryName"]=$row['cat_name'];
					$CountryList[$x]["ProductName"]=$row['pro_name'];
					$CountryList[$x]["ProductDesc"]=$row['pro_desc'];
					$CountryList[$x]["ProductPrice"]=intval($row['pro_price']);
					$CountryList[$x]["Productusefor"]=$row['pro_usefor'];
					$CountryList[$x]["ProductType"]=$row['pro_type'];
					$CountryList[$x]["ProductColor"]=$row['pro_color'];
					$CountryList[$x]["ProductStoragespace"]=$row['pro_storagespace'];
					
					$CountryList[$x]["ProductShape"]=$row['pro_shape'];
					$CountryList[$x]["ProductBrand"]=$row['pro_brand'];
					$CountryList[$x]["ProductMaterial"]=$row['pro_material'];
					$CountryList[$x]["ProductFolded"]=$row['pro_folded'];
					$CountryList[$x]["ProductImage"]=$Productpic.$row['pro_image'];
					
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"ProductDetails"=>$CountryList,"Message"=>"Product Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
}     
}



elseif (mode=="viewproductbycategory") 
{
		$cat_id=cat_id;
	if (empty($cat_id))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$CountryDetailsQuery = $con->select_query("product","*","where cat_id='".$cat_id."'","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["pro_id"]=intval($row['pro_id']);
					$CountryList[$x]["ProductName"]=$row['pro_name'];
					$CountryList[$x]["ProductDesc"]=$row['pro_desc'];
					$CountryList[$x]["ProductPrice"]=intval($row['pro_price']);
					$CountryList[$x]["ProductImage"]=$Productpic.$row['pro_image'];
					
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"ProductDetails"=>$CountryList,"Message"=>"Product Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
}     
}



elseif (mode=="addcart") 
{
	$quantity=quantity;
	$total_price=total_price;
   	$user_id=user_id;
   	$product_id=product_id;
  
	if (empty($product_id)||empty($user_id)||empty($total_price)||empty($quantity)) 
	{
		header('Content-type: application/json'); 
		echo json_encode(array("Status"=>0,"Message"=>"Please fill all require fields."));
	}
	else
	{
		$check_email_query = $con->select_query("cart","*","where product_id='".$product_id."'","","");
            $rowFetch = mysql_fetch_assoc($check_email_query);
            
            	if(mysql_num_rows($check_email_query)>0)
            	{
               		header('Content-type: application/json');
               		echo json_encode(array("Status"=>0,"Message"=>"Product is already exists"));
            	}
            	else
            	{
		 
			$arruser=array(
        		"total_price"=>$total_price,
        		"user_id"=>$user_id,
        		"product_id"=>$product_id,
        		"quantity"=>$quantity
			);
			$insertUser = $con->insert_record("cart",$arruser);
			header('Content-type: application/json');
			echo json_encode(array("Status"=>1,"Message"=>"Cart add Successfully"));
	}}
}
elseif (mode=="viewcart")
{
	$user_id=user_id;
	if (empty($user_id))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$CountryDetailsQuery = $con->select_query("cart","*","inner join product on product.pro_id=cart.product_id where cart.user_id='".$user_id."'","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["cart_id"]=intval($row['cart_id']);
					$CountryList[$x]["pro_id"]=intval($row['pro_id']);
					$CountryList[$x]["ProductName"]=$row['pro_name'];
					$CountryList[$x]["ProductDesc"]=$row['pro_desc'];
					$CountryList[$x]["ProductPrice"]=intval($row['pro_price']);
					$CountryList[$x]["Productusefor"]=$row['pro_usefor'];
					$CountryList[$x]["ProductType"]=$row['pro_type'];
					$CountryList[$x]["ProductColor"]=$row['pro_color'];
					$CountryList[$x]["ProductStoragespace"]=$row['pro_storagespace'];
					$CountryList[$x]["Quantity"]=$row['quantity'];
					$CountryList[$x]["Total_Price"]=$row['total_price'];
					$CountryList[$x]["ProductShape"]=$row['pro_shape'];
					$CountryList[$x]["ProductBrand"]=$row['pro_brand'];
					$CountryList[$x]["ProductMaterial"]=$row['pro_material'];
					$CountryList[$x]["ProductFolded"]=$row['pro_folded'];
					$CountryList[$x]["ProductImage"]=$Productpic.$row['pro_image'];
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"ProductDetails"=>$CountryList,"Message"=>"Cart Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
}     
}


elseif (mode=="removeproduct")
{
	$cart_id=cart_id;
	if (empty($cart_id)) 
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$fetchItemQuery = $con->select_query("cart","*","where cart_id=".$cart_id."","","");

        if(mysql_num_rows($fetchItemQuery)==0)
        {
          header('Content-type: application/json');
          echo json_encode(array("Status"=>0,"Message"=>"No Product is available"));
          exit; 
        }
        else
        {
            $deleteItemQuery = $con->delete("cart","where cart_id='".$cart_id."' ");
          
            header('Content-type: application/json');
            echo json_encode(array("Status"=>1,"Message"=>"Remove Product Successfully"));
            exit;
        }     
	}
}

elseif (mode=="changepassword") 
{
	$user_id=user_id;
	$reg_password=reg_password;
	$reg_newpassword=reg_newpassword;
	if (empty($user_id)||empty($reg_password)||empty($reg_newpassword))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$fetchItemQuery = $con->select_query("reg","reg_password","where reg_id=".$user_id."","","");
		$FetchPassword=mysql_fetch_array($fetchItemQuery);
        if($FetchPassword['reg_password']!=$reg_password)
        {
          header('Content-type: application/json');
          echo json_encode(array("Status"=>0,"Message"=>"Invalid Current Password"));
        }
        else
        {
        	$reg_newpassword=array('reg_password'=>$reg_newpassword);
        	$query=$con->update("reg",$reg_newpassword,"where reg_id='".$user_id."'");
        	header('Content-type: application/json');
          	echo json_encode(array("Status"=>1,"Message"=>"Password Change Successfully."));
        }

	}

}



elseif (mode=="addorder") 
{
	$quantity=quantity;
	$total_price=total_price;
   	$user_id=user_id;
   	$product_id=product_id;
  
	if (empty($product_id)||empty($user_id)||empty($total_price)||empty($quantity)) 
	{
		header('Content-type: application/json'); 
		echo json_encode(array("Status"=>0,"Message"=>"Please fill all require fields."));
	}
	else
	{
	$check_email_query = $con->select_query("orderdetails","*","where product_id='".$product_id."'","","");
            $rowFetch = mysql_fetch_assoc($check_email_query);
            
            	if(mysql_num_rows($check_email_query)>0)
            	{
               		header('Content-type: application/json');
               		echo json_encode(array("Status"=>0,"Message"=>"Product is already exists"));
            	}
            	else
            	{
		

			$arruser=array(
        		"total_price"=>$total_price,
        		"user_id"=>$user_id,
        		"product_id"=>$product_id,
        		"quantity"=>$quantity
			);
			$insertUser = $con->insert_record("orderdetails",$arruser);
			header('Content-type: application/json');
			echo json_encode(array("Status"=>1,"Message"=>"Order add Successfully"));
	}}
}
elseif (mode=="vieworder")
{
	$user_id=user_id;
	if (empty($user_id))
	{
		header('Content-type: application/json');
	    echo json_encode(array("Status"=>0,"Message"=>"Please fill all required fields"));
	}
	else
	{
		$CountryDetailsQuery = $con->select_query("orderdetails","*","inner join product on product.pro_id=orderdetails.product_id where orderdetails.user_id='".$user_id."'","");
		if($con->total_records($CountryDetailsQuery) > 0)
			{
				$x=0;
				$CountryList=array();
				while($row = mysql_fetch_assoc($CountryDetailsQuery))
				{
					$CountryList[$x]["Order_id"]=intval($row['order_id']);
					$CountryList[$x]["pro_id"]=intval($row['pro_id']);
					$CountryList[$x]["ProductName"]=$row['pro_name'];
					$CountryList[$x]["ProductDesc"]=$row['pro_desc'];
					$CountryList[$x]["ProductPrice"]=intval($row['pro_price']);
					$CountryList[$x]["Productusefor"]=$row['pro_usefor'];
					$CountryList[$x]["ProductType"]=$row['pro_type'];
					$CountryList[$x]["ProductColor"]=$row['pro_color'];
					$CountryList[$x]["ProductStoragespace"]=$row['pro_storagespace'];
					$CountryList[$x]["Quantity"]=$row['quantity'];
					$CountryList[$x]["Total_Price"]=$row['total_price'];
					$CountryList[$x]["ProductShape"]=$row['pro_shape'];
					$CountryList[$x]["ProductBrand"]=$row['pro_brand'];
					$CountryList[$x]["ProductMaterial"]=$row['pro_material'];
					$CountryList[$x]["ProductFolded"]=$row['pro_folded'];
					$CountryList[$x]["ProductImage"]=$Productpic.$row['pro_image'];
					$x++;
				}
				header('Content-type: application/json');
				echo json_encode(array("Status"=>1,"ProductDetails"=>$CountryList,"Message"=>"Order Details Listed Successfully"));			
			}
			else
			{
				header('Content-type: application/json'); 
				echo json_encode(array("Status"=>0,"Message"=>"No  available"));  
			}			
}     
}

?>