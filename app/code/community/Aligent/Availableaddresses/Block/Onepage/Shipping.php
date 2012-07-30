<?php
class Aligent_Availableaddresses_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Initialize shipping address step
     */
    protected function _construct()
    {
        parent::_construct();
    }
    
    /**
     * Rewrite to add cache key for Shipping Country cache key
     */
    public function getCountryOptions()
    {
        $options    = false;
        $useCache   = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId    = 'DIRECTORY_SHIPPING_COUNTRY_SELECT_STORE' . Mage::app()->getStore()->getCode();
            $cacheTags  = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        return $options;
    }
    
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
     * Use the shipping country collection to generate the JSON list of region codes
     * @TODO: consider moving this to a helper so other elements can use it
     */
    public function getShippingRegionJson(){
        foreach ($this->getCountryCollection() as $country) {
            $countryIds[] = $country->getCountryId();
        }
        $collection = Mage::getModel('directory/region')->getResourceCollection()
            ->addCountryFilter($countryIds)
            ->load();
        $regions = array();
        foreach ($collection as $region) {
            if (!$region->getRegionId()) {
                continue;
            }
            $regions[$region->getCountryId()][$region->getRegionId()] = array(
                'code' => $region->getCode(),
                'name' => $this->__($region->getName())
            );
        }
        $json = Mage::helper('core')->jsonEncode($regions);
        return $json;
    }
    
    /**
     * insert the shipping-specific allowed countries, and perform a string replace to use the Aligent extended RegionUpdater
     */
    public function _toHtml() {
        $vBaseHtml = parent::_toHtml();
        $vShippingRegionJson = '<script type="text/javascript">countryShippingRegions = '.$this->getShippingRegionJson().'</script>';
        
        $vUpdatedBaseHtml = str_replace('RegionUpdater','AligentRegionUpdater',$vBaseHtml);
        
        $html = $vShippingRegionJson.$vUpdatedBaseHtml;
        return $html;
    }

}
