<?php
/**
 * Camiloo Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.camiloo.co.uk/license.txt
 *
 * @category   Camiloo
 * @package    Camiloo_Amazonimport
 * @copyright  Copyright (c) 2011 Camiloo Limited (http://www.camiloo.co.uk)
 * @license    http://www.camiloo.co.uk/license.txt
 */

class Camiloo_Amazonimport_VariationsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout();	
		return $this;
	}   
	
	public function editAction(){
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),'camiloo_amazon_variations_marketplace');
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/variations_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/variations_edit_tabs'));
		}
		$this->renderLayout();
	
	}
	
	public function reloadmappingsAction(){
		
		$data = $this->getRequest()->getPost();
		$currentvt = $data['currentvt'];
		$productid = $data['productid'];
		

		echo "<script type='text/javascript'>parent.document.getElementById('mappingarea').innerHTML = unescape('";
        ob_start();
       	?>
                <table cellspacing="0" class="form-list">
											            <tbody>
            
            <?php 
            
         	    $col = Mage::getModel('amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace').'/amazonimportcategorise'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'))->getCollection()->addFieldToFilter('productid',array('eq'=>$productid));
			  	foreach($col as $model){
			  		break;  		
			  	}
			  	$row = $model->getData();
			  				  	
			  	if(isset($row2['variation_theme'])){
			  		$currentvt = $row2['variation_theme'];  		
			  	}else{
			  		$currentvt = "";  		
			  	}
			  	
			  	$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml();
					
			 	$node = $xml->xpath('//ProductData/'.$row['producttype'].'/ProductType/'.$row['productdatatype'].'/VariationData');
				if(sizeof($node) == 0){
					$node = $xml->xpath('//ProductData/'.$row['producttype'].'/VariationData');		
					$startofcombined = $row['producttype'].'/VariationData/';
				}else{
					$startofcombined = $row['producttype'].'/ProductType/'.$row['productdatatype'].'/VariationData/';
				}
				
				
				Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),'camiloo_amazon_mapping_marketplace');
				
            
            foreach($node[0] as $vdata){
            	
            	if(("".$vdata->getName()."" != "VariationTheme")&&("".$vdata->getName()."" != "Parentage")){
		            	$ismapped = false;
			            $combined = $startofcombined.$vdata->getName();
			  			$val = Mage::getModel('amazonimportmapping'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace').'/amazonimportmapping'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'))->getCollection()->addFieldToFilter('xmlkey',array('eq'=>$combined));
			  			foreach($val as $model){
							$ismapped = true;
			  				break;	
			  			}
		            	
				?>
								  				        
				<tr>
		    		<td class="label"><label for="name"><?php echo Mage::getModel('amazonimport/amazonimport')->FormatCamelCase("".$vdata->getName()."");	?></label></td>
		    		<td class="value"><?php 
		  				if($ismapped == true){
		    				echo "<div id='$combined-values'>Mapped to attribute with code ".$model->getMappingvalue().'</div>';	
		    				$valuetosend = $model->getMappingvalue();	
		  				}else{
							echo "<div id='$combined-values'>Not mapped</div>";
		    				$valuetosend = "";	
		  				}
		  
		    			if(isset($vdata['CamElementRepeatLimit'])){
							echo "<br /><small>Can contain up to ".$vdata['CamElementRepeatLimit']." values separated by | marks.</small>";  					
		  				}
		    			unset($ismapped);
		    		?></td>
		 			<td class="scope-label"></td>
		       		<td class="value"><?php echo "<div id='$combined-buttons'>"; ?><button class="scalable" type="button" onclick="displaySelectForm('<?php echo $combined; ?>','<?php echo $valuetosend; ?>');" name="submit" value="Select Attribute"><span>Select Attribute</span></button>
		       		<button class="scalable" type="button" onclick="displayEnterCodeForm('<?php echo $combined; ?>','<?php echo $valuetosend; ?>');" name="submit" value="Enter Attribute Code"><span>Enter Attribute Code</span></button></div></td>
				</tr>

		<?php 
            		} 
            	}?>
            </tbody>
        </table>        
        	
        <?php
        $pagecontent=ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
		
		
	}
	
	public function from_camel_case($str) {
  		  $str[1] = strtolower($str[1]);
  		  $func = create_function('$c', 'return " " . strtolower($c[1]);');
  		  return preg_replace_callback('/([A-Z])/', $func, $str);
  	}
	
	
	public function indexAction() {
		
		error_reporting(E_ALL);
		ini_set("display_errors","on");
		
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		$_mkt = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace');
	
		// need to find which configurable products on the system have been categorised, and what they have been categorised as.
		// from there, we can loop through and display the required grids.
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_categorise_".$_mkt.
			" WHERE productdatatype != '' GROUP BY productdatatype");
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);

		$blocks = array();
		$xml = Mage::getModel('amazonimport/amazonimport')->getTemplateXml($_mkt);
		
		foreach($rows as $row)
		{
		
                $collection = Mage::getModel('catalog/product')->getCollection()
	         ->addAttributeToSelect('sku')
	         ->addAttributeToSelect('name')
	         ->joinTable('amazonimportcategorise'.$_mkt.'/amazonimportcategorise'.$_mkt,
	         'productid=entity_id',
	         array('productid'=>'productid','productdatatype'=>'productdatatype'),
	         null,
	         'left')->joinTable('amazonimportlistthis'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace').'/amazonimportlistthis'.Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),
         'productid=entity_id',
         array('is_active'=>'is_active'),null,'left')

	         ->addFieldToFilter('productdatatype',array('eq'=>$row['productdatatype'])) /* ->addFieldToFilter('type_id',array('neq'=>'simple')) */ ;
	         $collection->addFieldToFilter('is_active',array('eq'=>'1'));

			if($collection->getSize() > 0)
			 {
				
				 try {
				$test = $xml->xpath('//ProductData/'.$row['producttype'].'/ProductType/'.$row['productdatatype'].'/VariationData');
				 } catch(Exception $e){
					$test = array();
				 }
				
				
				if(sizeof($test) == 0)
				{
					try {
					$test = $xml->xpath('//ProductData/'.$row['producttype'].'/VariationData');
					} catch (Exception $e){
						$test = array();
					}
					if(sizeof($test) > 0)
					{
						$blocks[] = $this->getLayout()->createBlock('amazonimport/variations_griddivider')->setData('productdatatype',$row['productdatatype']);
						$blocks[] = $this->getLayout()->createBlock('amazonimport/variations_grid')->setData('productdatatype',$row['productdatatype']);
					}	
				}
				else
				{
					$blocks[] = $this->getLayout()->createBlock('amazonimport/variations_griddivider')->setData('productdatatype',$row['productdatatype']);
					$blocks[] = $this->getLayout()->createBlock('amazonimport/variations_grid')->setData('productdatatype',$row['productdatatype']);
				}
			  }
			  
		 }
		
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/variations_gridheader'));
		foreach($blocks as $block){
			$this->_addContent($block);
		}
		}
		
		$this->renderLayout();
	}
	
	public function cloneAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),'camiloo_amazon_variations_marketplace');
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/variations_clone_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/variations_clone_edit_tabs'));
		}
		$this->renderLayout();
	}
	
	public function bulkAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),'camiloo_amazon_variations_marketplace');
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/variations_bulk_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/variations_bulk_edit_tabs'));
		}
		$this->renderLayout();
	}
			
	public function comAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("com",'camiloo_amazon_variations_marketplace');
		$this->indexAction();		
	}
	
	public function ukAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("uk",'camiloo_amazon_variations_marketplace');
		$this->indexAction();		
	}
	
	public function frAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("fr",'camiloo_amazon_variations_marketplace');
		$this->indexAction();		
	}
	
	public function deAction() {
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue("de",'camiloo_amazon_variations_marketplace');
		$this->indexAction();		
	}
	
	public function clonefromAction() {
		$iview = Mage::getModel('amazonimport/amazonlink')->initiateview($this);
		
		Mage::getModel('amazonimport/amazonimport')->saveSessionValue(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace'),'camiloo_amazon_variations_marketplace');
		$this->loadLayout();
		if($iview == true){
		$this->_addContent($this->getLayout()->createBlock('amazonimport/variations_clone_edit'));
		$this->_addLeft($this->getLayout()->createBlock('amazonimport/variations_clone_edit_tabs'));
		}
		$this->renderLayout();
	}
	
	public function getattributesAction(){
		$data = $this->getRequest()->getPost();
		$element = $data['getattributes-xmlkey'];
		$curval = $data['getattributes-keyvalue'];
		
		$attribout = "<select name='".$element."-newvalue' id='".$element."-newvalue' />";  		

		$attribout .= "<option value=''>--- Not mapped ---</option>";
  		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() );	
		
			$attribs = array();
			$count = 0;
			foreach($collection as $item){
				if(($item->getFrontendInput() != "media_image")&&($item->getFrontendInput() != "gallery")){
					$attribout .= "<option value='".$item->getAttributeCode()."' ";
					
						if($item->getAttributeCode() == $curval){
							$attribout .= 'selected="selected"';
						}
					
					
					$attribout .= ">".$item->getFrontendLabel()." (".$item->getAttributeCode()." ".$item->getFrontendInput().")"."</option>";
				}
			}
			
		$attribout .= '</select>';

		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('".rawurlencode($attribout)."');</script>"; 
        echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        ob_start();
        ?>
        <button class="scalable" type="button" onclick="saveEnterCode('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Save"><span>Save</span></button>
		<button class="scalable back" type="button" onclick="cancelUpdate('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Cancel"><span>Cancel</span></button>
        <?php
        $pagecontent=ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
        
	}
	
	public function texteditAction(){		
		$data = $this->getRequest()->getPost();
		$element = $data['textedit-xmlkey'];
		$curval = $data['textedit-keyvalue'];
		
		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
        ob_start();
       	?>
			<input type="text" name="<?php echo $element; ?>-newvalue" id="<?php echo $element; ?>-newvalue" value="<?php echo $curval; ?>" />
		<?php 
        $pagecontent=ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
        echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        ob_start();
        ?>
		<button class="scalable" type="button" onclick="saveEnterCode('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Save"><span>Save</span></button>
		<button class="scalable back" type="button" onclick="cancelUpdate('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Cancel"><span>Cancel</span></button>
        <?php
        $pagecontent= ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
	}
	
	public function canceleditAction(){		
		$data = $this->getRequest()->getPost();
		$element = $data['canceledit-xmlkey'];
		$curval = $data['canceledit-keyvalue'];
		
		echo "<script type='text/javascript'>parent.document.getElementById('".$element."-values').innerHTML = unescape('";
        ob_start();
       	
        		if($curval == ""){
        			?>
       				Not mapped	
        			<?php 	
        		}else{
        			?>
        			Mapped to attribute with code <?php echo $curval; ?>
        			<?php 
        		}
        	
        $pagecontent=ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
        echo "<script type='text/javascript'>parent.document.getElementById('".$element."-buttons').innerHTML = unescape('";
        ob_start();
        ?>
		<button class="scalable" type="button" onclick="displaySelectForm('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Select Attribute"><span>Select Attribute</span></button>&nbsp;<button class="scalable" type="button" onclick="displayEnterCodeForm('<?php echo $element; ?>','<?php echo $curval; ?>');" name="submit" value="Enter Attribute Code"><span>Enter Attribute Code</span></button>
		<?php
        $pagecontent= ob_get_contents(); 
        ob_end_clean(); 
        echo rawurlencode($pagecontent);        
        echo "');</script>"; 
	}
	
	public function saveAction(){
		$data = $this->getRequest()->getPost();
		
        $redirectBack   = $this->getRequest()->getParam('back', false);
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_variations_marketplace');
		// first, we must check if there is a row for this product id - if so, delete it.
		$model = Mage::getModel('amazonimportvariations'.$country.'/amazonimportvariations'.$country)->getCollection()
			->addFieldToFilter('configurable_product_id',array($data['configurable_product_id']));
		if(sizeof($model) > 0){
			foreach($model as $mdl){
				$mdl->delete();
			}	
		}
		
		$model = Mage::getModel('amazonimport/amazonimportvariations'.$country);
		$model->setData($data);
		$model->save();	
		
		
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$result = $db->query("select * from {$table_prefix}amazonimport_setup_".$country." WHERE initial_setup_complete = 1 AND productid=".$data['configurable_product_id']." AND productid not in (select productid from {$table_prefix}amazonimport_errorlog_".$country." where productid=".$data['configurable_product_id']." AND submission_type='Product')");
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				$db->query("INSERT INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$data['configurable_product_id'].",'Relation')");
			}
			
	 	if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $data['configurable_product_id'],
                '_current'=>true
            ));
	 	} else {
            $this->_redirect('*/*/');
        }
		
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amazonimport')->__('Variation theme has been set'));
		$this->_redirect('*/*/');	
		
		
	}
	
	
}

?>
