<?php
defined('_JEXEC') or die('Restricted Access');
$DPHelper = new DatapageHelper();
// default chart height
$height = 480;
?>

<link rel="stylesheet" type="text/css" href="<?php echo JURI::root(true);?>/media/gpo/css/charts.css"/>

<?php
   $locNameString = 'name';
   $formURLPrefix = '';
   $columnNameString = 'column_title';
   if( in_array($this->currentLangCode, array('es','fr') ) ):
       $locNameString = 'name_' . $this->currentLangCode;
       $columnNameString .= '_' . $this->currentLangCode;
       $formURLPrefix     = '/' . $this->currentLangCode;
   endif;
   
   if( 'fr' == $this->currentLangCode ) {
       $factsClass = 'frFacts';
       $cBtnClass  = 'cBtn cBtnFr'; 
   }elseif( 'es' == $this->currentLangCode ) {
       $factsClass = 'esFacts';
       $cBtnClass  = 'cBtn cBtnEs'; 
   }else {
       $factsClass = 'enFacts';
       $cBtnClass  = 'cBtn cBtnEn'; 
   }
?>

<div class="comTitle <?php echo $factsClass;?>">

<div>
<?php
    if (!$this->base_location_info) {
    echo '<p>You must select the location from the left</p>';
} else {
    ?>

    <h1>
        <?php
        $pageHeader = JTEXT::_('COM_GPO_COMPARE_PAGE_COMPARE');
        if( $this->isGroup ) {
             $locationDisplayName = 'the ' . $this->base_location_info->name . ' group';
        }
        else if( $this->isRegion ) {
             $locationDisplayName = 'the ' . $this->base_location_info->name . ' region';
        }
        else{
            if ($DPHelper->isNeedToAddThe($this->base_location_info->name)) {
                $locationDisplayName = JText::_('COM_GPO_COMPARE_PAGE_THE') . $this->base_location_info->{$locNameString};
                                     
            } else {
                $locationDisplayName = $this->base_location_info->{$locNameString};
            }
        }
        echo $pageHeader = str_replace('#', $locationDisplayName, $pageHeader);
        
        $defineUrl = JRoute::_('index.php?option=com_gpo&task=glossary&id=' . $this->column_info->gcite_id, false);
        ?>
    </h1>
    
    <h3>
        <?php
        echo $this->column_info->{$columnNameString};
        echo '<div class="optionbar">
                <div class="a2a_kit a2a_kit_size_18 a2a_default_style" style="float:right;margin:2px 0px 0 5px;"><a title="Share this page on social media" class="a2a_dd" href="#"></a></div>
                   <a class="btn define" title="' . JText::_('COM_GPO_DP_INFO').$this->column_info->{$columnNameString} . '" target="_blank" onclick="popDefinition(\'' . $defineUrl . '\');"></a>&nbsp;
                   <a id="btnprint" class="btn print" title="'.JText::_('COM_GPO_CHARTS_PRINT_ICON_TITLE').'" onclick="window.print();"></a>&nbsp;
              </div>';
        ?>
    </h3>


    <div id="chart">
        <?php
            if (!empty($this->chartxml)) {
                ?>
                <div id="loading" style="text-align: center;" class="noprint">
                    <img src="<?php echo $this->live_url;?>/media/system/images/mootree_loader.gif"/>
                    <span id="loadspan"><?php echo JText::_('COM_GPO_CHARTS_DRAWING');?>....</span>
                </div>
                <div id="chartContainer"></div>
            <?php
            }
            ?>
    </div>
    
    <?php
    if (!empty($this->chartxml)): 
        $padding = (count($this->comparion_location) > 20) ? 
                   '5px 10px 20px 105px' : '5px 30px 20px 75px';
    ?>
    <!--<div style="margin-left: 65px;padding-left: 70px; padding-right: 70px">-->
    <div style="padding:<?php echo $padding;?>">
        <!-- 
        <p class="chartCaption">
           <?php echo JText::_('COM_GPO_CHARTS_PAGE_CHARTCAPTION');?>
        </p>
        -->
    </div>
    
    <?php
    endif;
    ?>

    <div>
        <div class="noprint">
        <?php
            $comparePageIntro = JText::_('COM_GPO_COMPARE_PAGE_INTRO');
            $chartPageIntro   = JText::_('COM_GPO_CHARTS_PAGE_INTRO');
            $comparePageIntro = str_replace('#', $locationDisplayName, $comparePageIntro);
            $chartPageIntro   = str_replace('#', $locationDisplayName, $chartPageIntro);
            $compareBtnTitleTag = str_replace('#',$locationDisplayName,JText::_('MOD_GPO_FIND_COMPARE_TITLE_TAG'));
            if ($this->chartxml) {
               echo "<p>$chartPageIntro</p>";
            } else {
               echo "<p>$comparePageIntro</p>";
            }
        ?>
        </div>
        <?php
        $base_location_id = $this->base_location_info->id;
        
        if (count($this->comparion_locations)) {
            $availableCountries = array();
	    //get the selected locations. we will not add these locations in this box. we will add them in next element
            $selectedLocationsArray = explode(',', trim($this->selected_locations));
            
            //if(count($selectedLocationsArray)>30):
                foreach ($this->location_data as $location) {
                    if (in_array($location['location_id'], $selectedLocationsArray)) {
                        $selected_locations[] = $location['location_id'];
                    }
                }
            //endif;
        ?>

            <div id="block_country_available" class="noprint" style="float:left;width:180px;">

                <select name="country_list" multiple="multiple" id="country_list" size="20"
                        style="padding:0px 5px;width:180px;">
                    <?php
                    foreach ($this->comparion_locations as $location) {
                        if ($base_location_id != strtolower($location->location_id) AND !in_array($location->location_id, $selected_locations)) {
                            $availableCountries[$location->location_id] =  $location->{$locNameString};
                            //echo '<option value="' . $location->location_id . '">' . $location->{$locNameString} . '</option>' . PHP_EOL;
                        }
                    }
                    $availableCountries = sortLocationNames($availableCountries, $this->currentLangCode);
                    foreach($availableCountries as $locId => $locName ):
                        echo '<option title="'.$locName.'" value="' . $locId . '">' . $locName . '</option>' . PHP_EOL;
                    endforeach;
                    ?>
                </select>

            </div>
            <!--            <div id="handlers" style="float:left;text-align:center;width:165px;margin:0 auto;"><br/>
            -->
            <div class="noprint" id="handlers" style="float:left;text-align:center;width:130px;padding: 50px 17px 0px 17px;"><br/>
                <input class="cSelect" type="button" value="<?php echo JText::_('COM_GPO_COMPARE_PAGE_ADD');?>" id="btnAdd"/><br/><br/>
                <input class="cSelect" type="button" value="<?php echo JText::_('COM_GPO_COMPARE_PAGE_REMOVE');?>" id="btnDel"/><br/><br/><br/>
                <br/>
                
                <?php
                    if( $this->isGroup ){
                       $actionURI = JURI::base(true) . $formURLPrefix . '/firearms/compare/group/' . $this->base_location_info->id . '/' 
                                    . $this->column_info->column_name.'/';
                    }
                    else if( $this->isRegion ){
                       $actionURI = JURI::base(true) . $formURLPrefix . '/firearms/compare/region/' . $this->base_location_info->id . '/' 
                                    . $this->column_info->column_name.'/';
                    }
                    else {
                       $actionURI = JURI::base(true) . $formURLPrefix . '/firearms/compare/' . $this->base_location_info->id . '/' 
                                    . $this->column_info->column_name.'/';
                    }
                ?>
                <form id="frmcompare"
                      action="<?php echo $actionURI;?>"
                      method="post" onsubmit="return populate_locations();" style="display:inline">
                    <input type="hidden" name="selected_locations" id="selected_locations" value=""/>
                    <input type="hidden" name="cmdCompare" value="1"/>
                    <input type="hidden" name="isGroup" id="isGroup"  value="<?php echo $this->isGroup;?>"/>
                    <input type="hidden" name="isRegion" id="isRegion" value="<?php echo $this->isRegion;?>"/>
                    
                    <a class="<?php echo $cBtnClass;?>" href="javascript:jQuery('#frmcompare').submit();" 
                       title="<?php echo $compareBtnTitleTag;?>">
                       &nbsp;
                    </a>
                </form>
            </div>

            <div id="block_country_selected" style="float:right" class="noprint">

                <select name="country_selected[]" id="country_selected" size="20" multiple="multiple"
                        style="padding:0px 5px;width:180px;">
                    <?php
                    if (count($selected_locations)) {
                        foreach ($this->comparion_locations as $location) {
							$locLen = strlen( $location->{$locNameString} );
							if($locLen > 25) {
								$height = 540;
							}
                            if ($location->location_id == $base_location_id) continue; //do not show base location here.
                            if (in_array($location->location_id, $selected_locations)) {
                                echo '<option title="'.$location->{$locNameString}.'" value="' . $location->location_id . '">' . $location->{$locNameString} . '</option>' . PHP_EOL;
                            }
                        }
                    }
                    ?>
                </select><br/>

            </div>


            <div class="clear"></div>

            <div class="citations">
                    <?php
                        $jinput = JFactory::getApplication()->input;
                        $jconfig = &JFactory::getConfig();
                        $live_url = JURI::current();
                        $footer = &$this->footer;
                        if (!empty($footer)) {
                            $title = $footer->title;
                            $ts_modified = strtotime($footer->modified);
                            $article_authors = $footer->authors;
                            $now = $jinput->get('REQUEST_TIME','', 'SERVER');

                            ?>
                            <h3 id="region-footer-head">
                                <?php echo JText::_('COM_GPO_COMPARE_PAGE_CITATION_TITLE')?>
                            </h3>
                            <div id="region-footer">
                                <span><?php echo $article_authors . " " . date('Y.', $ts_modified) . ' <span class="footer-title">' . GpoEndWith(".", $title); ?></span></span>
                                <span>Global Action on Gun Violence (GAGV), <a href="http://www.actiononguns.org">www.actiononguns.org</a>, <?php echo date('j F.', $ts_modified); ?></span>
                                <span>Accessed <?php echo date('j F Y.', $now); ?> at: <a
                                        href="<?php echo $live_url;?>"><?php echo $live_url; ?></a></span>
                            </div>
                            <?php

                        }
                        ?>
            </div>
                    <?php

        } else {
            echo 'Sorry, no country has data in that column!';
        }
        ?>
    </div>

    <p>&nbsp;  </p>
        <?php

}
    ?>
</div>
</div>
<p>&nbsp;</p>

<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/fusioncharts.charts.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/themes/fusioncharts.theme.zune.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/gpo/js/charts.js?v=3"></script>            

<?php
// Chart Drawing Script
if (!empty($this->chartxml)):
?>

<script type="text/javascript">
    // Listening using global events
    FusionCharts.addEventListener('beforeInitialize', function (eventObj, argsObj) {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_INITIALIZING');?>';
    });
                
    FusionCharts.addEventListener('dataLoaded', function (eventObj, argsObj) {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_DRAWING');?>';
    });
                
    FusionCharts.addEventListener('drawComplete', function (eventObj, argsObj) {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_PRINTER_FRIENDLY');?>';
    });

    //FusionCharts.printManager.enabled(true);
    FusionCharts.addEventListener('rendered',function (eventObj, argsObj) {
        document.getElementById('loading').style.display = "none";
        document.getElementById('btnprint').style.display = "inline";
    });
    
    FusionCharts.options.license({
        key: 'kJ-7B9snlC4E3A3F2G1A1B2E6B6B3F1F2i1sB-22B2A6C-11zpnH-8G2C11rfwB4F1D4G2B1A2B2D6C1F1C4F1B3G2A1A4fyF-10E2F4D1F-7B3D5D1nmdG4A9A32bfuC6B5G4fB-7zA9C5A5D7E1E5E1H4A1C3A4B-16uwG2F4FF1ycrC7A3B4crC3UA7A5nhyD3G2F2A10B8D7D5D2B4F4G2F3C8A2a==', 
        creditLabel: false 
    });
                
    FusionCharts.ready(function() {
        var barChart = new FusionCharts({
            "type": "column2d",
            "renderAt": "chartContainer",
            "width": "530",
            "height": "<?php echo $height;?>",
            "dataFormat": "json",
            "dataSource": <?php echo json_encode($this->chartxml);?>
        });
        barChart.render();
    });
</script>

<?php
endif;
?>
