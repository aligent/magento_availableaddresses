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
     * Rewrite to add cache key for Shipping Country cache key
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
    
    public function _toHtml() {
        $vBaseHtml = parent::_toHtml();
//        $vShippingRegionJson = '<script type="text/javascript">countryShippingRegions = '.$this->helper('directory')->getRegionJson().'</script>';
        $vShippingRegionJson = '<script type="text/javascript">countryShippingRegions = {"AU":{"493":{"code":"ACT","name":"Australian Capital Territory"},"494":{"code":"NSW","name":"New South Wales"},"495":{"code":"NT","name":"Northern Territory"},"496":{"code":"QLD","name":"Queensland"},"497":{"code":"SA","name":"South Australia"},"498":{"code":"TAS","name":"Tasmania"},"499":{"code":"VIC","name":"Victoria"},"500":{"code":"WA","name":"Western Australia"}}}</script>';
        
        $vUpdatedBaseHtml = str_replace('RegionUpdater','AligentRegionUpdater',$vBaseHtml);
        
        $html = $vShippingRegionJson.$vUpdatedBaseHtml;
        return $html;
    }

}
