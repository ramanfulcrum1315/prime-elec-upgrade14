<?php

class OCM_Quotedispatch_Helper_Data extends Mage_Core_Helper_Abstract
{

/*
 * Provides very very low security needs true incrypption
 * TODO :: Refactor could run into length issues eventually
 */

    public function getEmailHash($email) {
    
        $key = $this->getKey();
        $salted_email = str_replace('@',$key, $email);
        return md5($salted_email);
        
    }
    
    public function encryptQuote($object) {
        
        $email_hash = $this->getEmailHash($object->getEmail());
        $prefix = substr($email_hash, 0,4);
        $suffix = substr($email_hash, 4);
        return $prefix.$object->getQuotedispatchId().$suffix;
        
    }

    public function decryptHash($hash) {
    
        $id = substr($hash, 0, -28);
        $id = substr($id, 4);

        $prefix = substr($hash, 0,4);
        $suffix = substr($hash, -28);
        $email_hash = $prefix.$suffix;
        
        $object = Mage::getModel('quotedispatch/quotedispatch')->load($id);
        
        if($this->getEmailHash($object->getEmail()) == $email_hash) {
            return $object->getEmail();
        }
        return false;
        
    }

    private function getKey() {
        return Mage::getConfig()->getNode('global/crypt/key');
    }

/*
 * Send Emails
 */
    
    public function sendEmail($object, $subject = null){

        // FORCE THAT ALL DATA IS AVAILABLE WITH OBJECT : TODO FIND BETTER WAY
        $object = Mage::getModel('quotedispatch/quotedispatch')->load($object->getId());

        $mail  = Mage::getModel('core/email_template')->loadDefault('ocm_quotedispatch_request_processed');
        
        $user = Mage::getModel('admin/user')->load($object->getCreatedBy(),'username');
        if($user->getId()) {
            $sender_name = implode(' ',array($user->getFirstname(),$user->getLastname()));
            $sender_email = $user->getEmail();
        } else {
            $sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
        }

        $mail->setSenderName($sender_name);
        $mail->setSenderEmail($sender_email);

        
        if ($subject) $mail->setTemplateSubject($subject);
        
        $customer_name = implode(' ',array($object->getFirstname(),$object->getLastname()));
        $items_list = Mage::app()->getLayout()->createBlock('quotedispatch/view_list','item.list')->setTemplate('ocm/quotedispatch/list.phtml')->setQuote($object)->toHtml();
        
        $variables = array(
            'quote_name' => $object->getTitle(),
            'expires_at' => Mage::helper('core')->formatDate($object->getExpireTime(),'medium',false),
            'sender_email' => $sender_email,
            'customer_email' => $object->getEmail(),
            'customer_name' => $customer_name,
            'items_list' => $items_list,
            'subtotal' => Mage::helper('core')->currency($object->getSubtotal(),true,false),
            'view_quote_url' => Mage::getBaseUrl().'quotedispatch/index/view/id/'.$object->getId().'/?uid='.$this->encryptQuote($object),
            'view_all_quotes_url' => Mage::getBaseUrl().'quotedispatch/?uid='.$this->encryptQuote($object),
        );

         try {
            $mail->send($object->getEmail(),$customer_name, $variables);
         } catch (Exception $e) {
             Mage::log($e->getMessage(),null,'exception.log');
         }

        
        return $this;
    }


    // Not needed should be able to use _getReadAdapter()->select() in resource the point model method resource method
    public function getDb() {
        $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");
        $dbinfo = array(
            "host" => $config->host,
            "username" => $config->username,
            "password" => $config->password,
            "dbname" => $config->dbname
        );

        return Zend_Db::factory('Pdo_Mysql',$dbinfo);
    }


}