<?php
/**
 * Author: Swapna Palaniswamy
 * Date: 13/05/13
 */
Class Aligent_Availableaddresses_Model_Observer {

    /**
     * Binds to the customer_address_save_before Event and checks if the customer is splitting the address.
     * If so, creates a new address entity.
     *
     * @param Varien_Event_Observer $observer
     */
    public function interceptSaveAddress($oObserver){
        if(Mage::registry('default_address_split')){
            return $this; //this method has already been executed once in this request (see comment below)
        }
        if(Mage::app()->getRequest()->getActionName() == 'saveOrder'){
            return $this; //shipping and billing addresses are assumed to be default in checkout, so we can't play here
        }
        $oAddress = $oObserver->getCustomerAddress();
        $iObservedEntityTypeId = $oAddress->getEntityTypeId();
        $iAddressTypeId = Mage::getSingleton('eav/config')->getEntityType('customer_address')->getEntityTypeId();
        if($iAddressTypeId != $iObservedEntityTypeId){
            return $this; //make sure that we are only observing customer addresses not order addresses
        }
        if(!$oAddress->getId()){
            return $this; //this is a new address so we are not interested
        }

        //Set when the entered address is ticked for 1)Use as my default shipping address or 2)Use as my default billing address
        $bSubmittedDefaultShipping = Mage::app()->getRequest()->getParam('default_shipping',false);
        $bSubmittedDefaultBilling = Mage::app()->getRequest()->getParam('default_billing',false);
        //Set when the current address is stored in the db as default shipping/billing address
        $bCurrentDefaultShipping = ($oAddress->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping());
        $bCurrentDefaultBilling = ($oAddress->getId()==Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling());

        //check if currently single address for both bill & ship and customer wants to split them
        if($bCurrentDefaultShipping && $bCurrentDefaultBilling && (!$bSubmittedDefaultShipping || !$bSubmittedDefaultBilling)){
            $oAddress->setId(null); //forces the model layer to save this address as a new entity

        }
        /* Customer Addresses seem to call the before_save event twice,
         * so we need to set a variable so we only process it once, otherwise we get duplicates
         */
        Mage::register('default_address_split',true);
        return $this;
    }

}