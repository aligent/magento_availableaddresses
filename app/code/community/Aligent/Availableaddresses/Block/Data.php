<?php
class Aligent_Availableaddresses_Block_Data extends Mage_Directory_Block_Data
{

    /**
     * Rewrite to lookup the countries specified by administrator for SHIPPING
     */
    public function getCountryCollection()
    {
        $aShipCountryCollection = Mage::getSingleton('directory/country')->getResourceCollection();
        $aShipCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow_shipping'));
        if (!empty($aShipCountries)) {
            $aShipCountryCollection->addFieldToFilter("country_id", array('in' => $aShipCountries));
        }
        return $aShipCountryCollection;
    }

    /**
     * Rewrite to add cache key for Shipping Country cache key
     */
    public function getCountryHtmlSelect($defValue = null, $name = 'country_id', $id = 'country', $title = 'Country')
    {
        Varien_Profiler::start('TEST: ' . __METHOD__);
        if (is_null($defValue)) {
            $defValue = $this->getCountryId();
        }
        $cacheKey = 'DIRECTORY_SHIPPING_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = $this->getCountryCollection()->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        $html = $this->getLayout()->createBlock('core/html_select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('directory')->__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $html;
    }

}
