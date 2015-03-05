<?php
/**
 * Onepage.php
 *
 * @category  Aligent
 * @package   Aligent_Availableaddresses
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_Availableaddresses_Model_Checkout_Type_Onepage
 *
 * @category  Aligent
 * @package   Aligent_Availableaddresses
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @link      http://www.aligent.com.au/
 */
class Aligent_Availableaddresses_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        $country = isset($data['country_id']) ? $data['country_id'] : null;

        if (!Mage::helper('availableaddresses')->isValidCountry('billing', $country)) {
            return array('error' => -1, 'message' => Mage::helper('availableaddresses')->__('Invalid billing country.'));
        }

        return parent::saveBilling($data, $customerAddressId);
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data, $customerAddressId)
    {
        $country = isset($data['country_id']) ? $data['country_id'] : null;

        if (!Mage::helper('availableaddresses')->isValidCountry('shipping', $country)) {
            return array('error' => -1, 'message' => Mage::helper('availableaddresses')->__('Invalid shipping country.'));
        }

        return parent::saveShipping($data, $customerAddressId);
    }
}