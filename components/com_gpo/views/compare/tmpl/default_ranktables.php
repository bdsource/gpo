<?php
defined('_JEXEC') or die('Restricted Access');
$DPHelper = new DatapageHelper();

$locNameString = 'name';
$columnNameString = 'column_title';
if( in_array($this->currentLangCode, array('es','fr') ) ):
    $locNameString = 'name_' . $this->currentLangCode;
    $columnNameString .= '_' . $this->currentLangCode;
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
        else {
             if ($DPHelper->isNeedToAddThe($this->base_location_info->name)) {
                $locationDisplayName = 'the ' . $this->base_location_info->name;
                $locationDisplayName = JText::_('COM_GPO_COMPARE_PAGE_THE') . $this->base_location_info->{$locNameString};
             } else {
                $locationDisplayName = $this->base_location_info->{$locNameString};
             }
        }
        $pageHeader = str_replace('#', $locationDisplayName, $pageHeader);
        echo $pageHeader;
        
        $compareBtnTitleTag = str_replace('#',$locationDisplayName,JText::_('MOD_GPO_FIND_COMPARE_TITLE_TAG')); 
        $defineUrl = JRoute::_('index.php?option=com_gpo&task=glossary&id=' . $this->column_info->gcite_id, false);
        ?>
    </h1>
    <h3>
        <?php echo $this->column_info->{$columnNameString};
        echo '<div class="optionbar">
                <div class="a2a_kit a2a_kit_size_18 a2a_default_style" style="float:right;margin:2px 0px 0 5px;"><a title="Share this page on social media" class="a2a_dd" href="#"></a></div>
                <a class="btn define" title="'.JText::_('COM_GPO_DP_INFO').$this->column_info->{$columnNameString} . '" target="_blank" onclick="popDefinition(\'' . $defineUrl . '\');"></a>';
        if(!empty($this->tablehtml)){
                echo '<a class="btn print" title="'.JText::_('COM_GPO_RANKTABLE_PRINT_ICON_TITLE').'" onclick="window.print();"></a>';
        }

        ?>
    </h3>
    </div>
    <br/>
    <div id="ranktable">
        <?php
            if (!empty($this->tablehtml)) {
                echo  $this->tablehtml;
            }
        ?>
    </div>

    <div>
        <div class="noprint">
        <?php
        $comparePageIntro = str_replace('#', $locationDisplayName, JText::_('COM_GPO_COMPARE_PAGE_INTRO'));
        $chartPageIntro   = str_replace('#', $locationDisplayName, JText::_('COM_GPO_CHARTS_PAGE_INTRO'));
            
        if ($this->tablehtml) {
            echo '<p>' . $chartPageIntro . '</p>';
        } else {
            echo '<p>' . $comparePageIntro . '</p>';
        }
        ?>
        </div>
            <?php
            $base_location_id = $this->base_location_info->id;
            if (count($this->comparion_locations)) {
                $availableCountries = array();
                ?>

                <br/>
                <div id="block_country_available" class="noprint" style="float:left;width:137px;">
                    <select name="country_list" multiple="multiple" id="country_list" size="20"
                            style="padding:0px 5px;width:137px;">
                        <?php
                        //get the selected locations. we will not add these locations in this box. we will add them in next element
                        $selected_locations = explode(',', trim($this->selected_locations));

                        foreach ($this->comparion_locations as $location) {
                            if ($base_location_id != strtolower($location->location_id) AND !in_array($location->location_id, $selected_locations)) {
                                $availableCountries[$location->location_id] =  $location->{$locNameString};
                            }
                        }
                        $availableCountries = sortLocationNames($availableCountries, $this->currentLangCode);
                        foreach ($availableCountries as $locId => $locName):
                            echo '<option title="' . $locName . '" value="' . $locId . '">' . $locName . '</option>' . PHP_EOL;
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
                        if($this->isGroup) {
                            $actionURI = JURI::base(true) . '/firearms/compare/group/' . $this->base_location_info->id . 
                                        '/' . $this->column_info->column_name;
                        }
                        else if($this->isRegion) {
                            $actionURI = JURI::base(true) . '/firearms/compare/region/' . $this->base_location_info->id . 
                                        '/' . $this->column_info->column_name;
                        }
                        else {
                            $actionURI = JURI::base(true) . '/firearms/compare/' . $this->base_location_info->id . 
                                        '/' . $this->column_info->column_name; 
                        }
                    ?>
                    <form id="frmcompare"
                          action="<?php echo $actionURI;?>"
                          method="post" onsubmit="return populate_locations();" style="display:inline">
                        <input type="hidden" name="selected_locations" id="selected_locations" value=""/>
                        <input type="hidden" name="cmdCompare" value="1"/>
                        <a class="<?php echo $cBtnClass;?>" href="javascript:jQuery('#frmcompare').submit();"
                           title="<?php echo $compareBtnTitleTag;?>">
                            &nbsp;
                        </a>
                    </form>
                </div>

                <div id="block_country_selected" style="float:right" class="noprint">

                    <select name="country_selected[]" id="country_selected" size="20" multiple="multiple"
                            style="padding:0px 5px;width:137px;">
                        <?php
                                            if (count($selected_locations)) {
                            foreach ($this->comparion_locations as $location) {
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
                <div>
                        <?php
                         $jinput = JFactory::getApplication()->input;
                            $jconfig = &JFactory::getConfig();
                            $live_url = JURI::current();
                            $footer = &$this->footer;
                            if (!empty($footer)) {
                                $title = $footer->title;
                                $ts_modified = strtotime($footer->modified);
                                $article_authors = $footer->authors;
                                $now = $jinput->get('REQUEST_TIME', '', 'SERVER');

                                ?>
                                <h3 id="region-footer-head"><?php echo JText::_('COM_GPO_COMPARE_PAGE_CITATION_TITLE');?></h3>
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

<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/gpo/js/charts.js"></script>
<script type="text/javascript">
    function moveRight() {
        jQuery('#country_list option:selected').each(function(index, obj) {
            jQuery('#country_selected').append(obj);
        })

    }
</script>

<p>&nbsp;</p>
