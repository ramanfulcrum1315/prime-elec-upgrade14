<?php
class MW_Navigationmenu_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
 
    public function render(Varien_Object $row)
    {
    	$size[0]="100";
    	$size[1]="100";
    	$html= '';
    	$imagename=$row->getData($this->getColumn()->getIndex());
    	if ($imagename!="")
    	{
    		$html = '<img ';
    		$html .= 'id="' . $this->getColumn()->getId() . '" ';
    		$html .= 'style="max-height: '.$size[1].'px;max-width: '.$size[0].'px;" ';
    		$html .= 'src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $row->getData($this->getColumn()->getIndex()) . '"';
    		$html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
    		return $html;
    	}
        return $html;
    }
}