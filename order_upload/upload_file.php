<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../app/Mage.php';
umask(0);
Mage::app('default');



$website_id = Mage::app()->getWebsite()->getId();
$email=$_POST['email'];
$customer = Mage::getModel('customer/customer');
$customer->setWebsiteId($website_id);
$customer->loadByEmail($email);
$customer_id=$customer->getId();
if(!$customer_id){
	echo 'That email address does not exist in our system.  Please go back and try again.';
	exit;
}
$shipping_cost=$_POST['shipping_cost'];
	/// Empty the upload directory
	function EmptyDir($dir) {
		$handle=opendir($dir);

		while (($file = readdir($handle))!==false) {
		//echo "$file <br>";
		@unlink($dir.'/'.$file);
		}

		closedir($handle);
	}

	EmptyDir('upload'); 


	function get_file_extension($file_name)
	{
	  return substr(strrchr($file_name,'.'),1);
	}
	$extension=get_file_extension($_FILES["file"]["name"]);
	//echo $extension.'<br />';
	if($extension=='csv'){
		if ($_FILES["file"]["error"] > 0)
			{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
			}
		else
		{
			move_uploaded_file($_FILES["file"]["tmp_name"],
			"upload/" . $_FILES["file"]["name"]);
			echo "CSV successfully stored in: " . "upload/" . $_FILES["file"]["name"].'<br />';
			?>
				<a href="single_order_import.php?filename=<?php echo $_FILES["file"]["name"]; ?>&cust_id=<?php echo $customer_id; ?>&shipping_cost=<?php echo $shipping_cost; ?>">Run the upload script</a>
			<?php
		}
	}
	else{
		echo 'Invalid file type.  Please go back and try again.';
	}

?> 