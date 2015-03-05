<?php
/**
 * Data.php
 *
 * @category  Aligent
 * @package   Aligent_Availableaddresses
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @link      http://www.aligent.com.au/
 */

/**
 * Aligent_Availableaddresses_Helper_Data
 *
 * @category  Aligent
 * @package   Aligent_Availableaddresses
 * @author    Luke Mills <luke@aligent.com.au>
 * @copyright 2015 Aligent Consulting.
 * @link      http://www.aligent.com.au/
 */
class Aligent_Availableaddresses_Helper_Data extends Mage_Core_Helper_Abstract
{

    private $_countryConfigCodes = array(
        'billing'  => 'general/country/allow',
        'shipping' => 'general/country/allow_shipping',
    );

    private $allowedCountryCodes = array(
        'billing'  => null,
        'shipping' => null,
    );

    public function getAllowedCountryCodes($addressType)
    {
        if (!array_key_exists($addressType, $this->allowedCountryCodes)) {
            throw new RuntimeException(sprintf('Invalid address type "%s"', $addressType));
        }
        if (is_null($this->allowedCountryCodes[$addressType])) {
            $this->allowedCountryCodes[$addressType] = explode(',', Mage::getStoreConfig($this->_countryConfigCodes[$addressType]));
        }
        return $this->allowedCountryCodes[$addressType];
    }

    public function isValidCountry($addressType, $countryCode)
    {
        $allowedCountryCodes = $this->getAllowedCountryCodes($addressType);

        return in_array($countryCode, $allowedCountryCodes);
    }

}
