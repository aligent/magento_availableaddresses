var AligentRegionUpdater = Class.create(RegionUpdater, {
    initialize: function($super, countryEl, regionTextEl, regionSelectEl, regions, disableAction, clearRegionValueOnDisable){
        if(countryShippingRegions && regionTextEl == 'shipping:region'){
            regions = countryShippingRegions;
        }
        $super(countryEl, regionTextEl, regionSelectEl, regions, disableAction, clearRegionValueOnDisable);
        //New Code Addded
    }
});