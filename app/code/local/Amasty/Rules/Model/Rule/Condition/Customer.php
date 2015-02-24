<?php
/**
 * @copyright   Copyright (c) 2009-11 Amasty
 */
class Amasty_Rules_Model_Rule_Condition_Customer extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('customer');
        $attributes = array(
            'email'     => $hlp->__('Email'),
            'firstname' => $hlp->__('First Name'),
            'lastname'  => $hlp->__('Last Name'),
            'dob'       => $hlp->__('Date of Birth'),
            'gender'    => $hlp->__('Gender'),
            'entity_id' => $hlp->__('ID'),
        );
        
        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'entity_id':
                return 'numeric';
            case 'dob':
                return 'date'; 
            case 'gender':
                return 'select'; 
            default:
                return 'string';                
        }
    }
    
    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }
        return $element;
    }

    public function getExplicitApply()
    {
        return ($this->getInputType() == 'date');
    }     

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'dob':
                return 'date';
                
            case 'gender':
                return 'select';
                
            default:
                return 'text';
        } 
    }

    public function getValueSelectOptions()
    {
        $options = array();
        
        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            switch ($this->getAttribute()) {
                case 'gender':
                    $options = array(
                        array('value' => '1', 'label' => Mage::helper('amrules')->__('Male')),
                        array('value' => '2', 'label' => Mage::helper('amrules')->__('Female')),
                    ); 
                    break; 
            }            
            $this->setData($key, $options);
        }
        return $this->getData($key);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $customer = $object;
        if (!$customer instanceof Mage_Customer_Model_Customer) {
            $customer = $object->getQuote()->getCustomer();
            $attr = $this->getAttribute();
            if ($attr != 'entity_id' && !$customer->getData($attr)){
                $address = $object->getQuote()->getBillingAddress();
                $customer->setData($attr, $address->getData($attr));
            }
        }
        return parent::validate($customer);
    }
}
