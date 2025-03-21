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

if($this->isGroup) {
   $displayLocationName = $this->base_location_info->{$locNameString} . ' group';
}else if($this->isRegion) {
   $displayLocationName = $this->base_location_info->{$locNameString} . ' region';
}else {
   $displayLocationName = $this->base_location_info->{$locNameString};
}
?>

<div class="comTitle <?php echo $factsClass;?>">

    <div>
        <?php
        if ( empty($this->column_info) && !empty($this->base_location_info) ) {
        	echo "<h1>" . $displayLocationName . "</h3>";        	
        	echo "<h3>You must select a category</h3>";
        	echo "<br /><a href='" . JURI::base() . "firearms/region/" . $this->base_location_info->alias . "'>Go back</a>";;
        } else if ( empty($this->base_location_info) ) {        	
            echo '<h3>You must select a Country or Region from the left</h3>';
            echo "<br /><a href='" .  "javascript:history(-1);" . "'>Go back</a>";;
        } else {
            ?>

            <h1>
                <?php
                echo $displayLocationName;
                $defineUrl = JRoute::_('index.php?option=com_gpo&task=glossary&id=' . $this->column_info->gcite_id, false);
                if($this->isGroup) {
                   $compareUrl = JURI::base() . 'firearms/compare/group/' . 
                                                  $this->base_location_info->id . '/' . urlencode($this->column_info->column_name);
                }else if($this->isRegion) {
                   $compareUrl = JURI::base() . 'firearms/compare/region/' . 
                                                  $this->base_location_info->id . '/' . urlencode($this->column_info->column_name);
                }else {
                   $compareUrl = JURI::base() . 'firearms/compare/' . 
                                                  $this->base_location_info->id . '/' . urlencode($this->column_info->column_name);
                }
                ?>
            </h1>
            <h3><?php echo $this->column_info->{$columnNameString};
                echo '<div class="optionbar">
                    <div class="a2a_kit a2a_kit_size_18 a2a_default_style" style="float:right;margin:2px 0px 0 5px;"><a title="Share this page on social media" class="a2a_dd" href="#"></a></div>
                <a class="btn define" title="'. JText::_('COM_GPO_DP_INFO'). $this->column_info->{$columnNameString} . '" target="_blank" onclick="popDefinition(\'' . $defineUrl . '\');"></a>&nbsp;
                <a id="btnprint" class="btn print" title="'.JText::_('COM_GPO_CHARTS_PRINT_ICON_TITLE').'" onclick="window.print();"></a>&nbsp;
                </div>';
                ?>
            </h3>
            <br/>

            <div id="chart">
                <?php
                if (!empty($this->chartxml)) {
                    ?>
                    <div id="loading" style="text-align: center;" class="noprint">
                        <img src="<?php echo JURI::root(true);?>/media/system/images/mootree_loader.gif"/>
                        <span id="loadspan"><?php echo JText::_('COM_GPO_CHARTS_DRAWING');?>....</span>
                    </div>
                    <div id="chartContainer"></div>
                    <link rel="stylesheet" type="text/css" href="<?php echo JURI::root(true);?>/media/gpo/css/charts.css"/>
                    
                    <?php } ?>

                <div class="clear"></div>

                <div class="citations">
                    <?php
                     $jinput = JFactory::getApplication()->input;
                    $jconfig = &JFactory::getConfig();
                    $live_url = JUri::getInstance()->toString();
                    $footer = $this->footer;
                    if (!empty($footer)) {
                        $title = $footer->title;
                        $ts_modified = strtotime($footer->modified);
                        $article_authors = $footer->authors;
                       $now = $jinput->get('REQUEST_TIME','', 'SERVER');

                        ?>
                        <h3 id="region-footer-head"><?php echo JText::_('COM_GPO_COMPARE_PAGE_CITATION_TITLE')?></h3>
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
            </div>

            <?php

        }

        ?>
    </div>


</div>


<p>&nbsp;</p>

<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/fusioncharts.charts.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(true);?>/media/gpo/js/charts.js?v=3"></script>

<script type="text/javascript">
    /*
    FusionCharts.addEventListener(FusionChartsEvents.BeforeInitialize, function () {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_INITIALIZING'); ?>';
    })
    FusionCharts.addEventListener(FusionChartsEvents.DataLoaded, function () {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_DRAWING'); ?>';
    })

    FusionCharts.addEventListener(FusionChartsEvents.DrawComplete, function () {
        document.getElementById('loadspan').innerHTML = '<?php echo JText::_('COM_GPO_CHARTS_PRINTER_FRIENDLY'); ?>';
    })
    FusionCharts.printManager.enabled(true);
    */
    
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
    FusionCharts.options.license({
        key: 'kJ-7B9snlC4E3A3F2G1A1B2E6B6B3F1F2i1sB-22B2A6C-11zpnH-8G2C11rfwB4F1D4G2B1A2B2D6C1F1C4F1B3G2A1A4fyF-10E2F4D1F-7B3D5D1nmdG4A9A32bfuC6B5G4fB-7zA9C5A5D7E1E5E1H4A1C3A4B-16uwG2F4FF1ycrC7A3B4crC3UA7A5nhyD3G2F2A10B8D7D5D2B4F4G2F3C8A2a==', 
        creditLabel: false 
    });
    
    FusionCharts.ready(function(){
        var lineChart = new FusionCharts({
            "type": "line",
            "renderAt": "chartContainer",
            "width": "550",
            "height": "370",
            "linethickness": "2",
            "dataFormat": "json",
            "dataSource": <?php echo json_encode($this->chartxml);?>
        });
       
        lineChart.render();
    });
    
    FusionCharts.addEventListener('rendered',function (eventObj, argsObj) {
        document.getElementById('loading').style.display = "none";
        document.getElementById('btnprint').style.display = "inline";
    });
</script>
