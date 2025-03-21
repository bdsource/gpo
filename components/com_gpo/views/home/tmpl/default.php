<?php
defined('_JEXEC') or die('Restricted Access');
//error_reporting(E_ALL);
jimport( 'joomla.html.parameter' );
$jView = new JViewLegacy();

$lang    = JFactory::getLanguage();
$langTag = $lang->getTag();

$locationString = 'name';

$document = JFactory::getDocument();

$urlDivided = explode("/", JUri::getInstance());
$urlDivided = array_map('strtolower',$urlDivided);
if( in_array("firearms", $urlDivided)){
    $document->addHeadLink(JURI::base().'en/'.'firearms/home', 'alternate', $relType = 'rel', array('hreflang' => 'en-GB'));
    $document->addHeadLink(JURI::base().'fr/'.'firearms/home', 'alternate', $relType = 'rel', array('hreflang' => 'en-FR'));
    $document->addHeadLink(JURI::base().'es/'.'firearms/home', 'alternate', $relType = 'rel', array('hreflang' => 'en-ES'));
}else{
    $document->addHeadLink(JURI::base(), 'alternate', $relType = 'rel', array('hreflang' => 'en-GB'));
    $document->addHeadLink(JURI::base().'fr', 'alternate', $relType = 'rel', array('hreflang' => 'en-FR'));
    $document->addHeadLink(JURI::base().'es', 'alternate', $relType = 'rel', array('hreflang' => 'en-ES'));
}

if (strlen($langTag) > 2) {
    $currentLangCode = strtolower(substr($langTag, 0, -3));
}

if (in_array($currentLangCode, array('es','fr'))) {
    $locationString = 'name_' . $currentLangCode;
}

$allLocations = GpoGetAllLocationNames();

$langurl =  "";
if($currentLangCode == "en"){
    $langurl  = "";
}
 else {
     $langurl  = $currentLangCode;
}

$stylelink= '<link rel="canonical"  href="'.JURI::base().$langurl.'"  />' ."\n";
$document->addCustomTag($stylelink);

?>

<div class="introbox facts">
    <div class="<?php echo JText::_('COM_GPO_HOME_FACTS_IMAGE_CSS'); ?>">

<?php
    $_fp_intro_content = null;
    $_fp_cond = 'fp-intro';

    /* get the desired custom_mod module for the home page intro html */
    $_fp_modules = JModuleHelper::getModules('insert-custom');
    
    foreach ($_fp_modules as $_module) {
        $_fp_params = new JRegistry($_module->params);
        //$_fp_params = new JParameter($_module->params);
        if (strpos($_fp_params->get('moduleclass_sfx'), $_fp_cond) !== false) {
            // first match wins
            $_fp_intro_content = clone($_module);
            break;
        }
    }

    /* now print the intro content */
    if (is_object($_fp_intro_content)) {
        $_options = array('style' => 'xhtml');
        echo JModuleHelper::renderModule($_fp_intro_content, $_options);
    }
    ?>

    <a class="<?php echo JText::_('COM_GPO_HOME_FACTS_FOOTER_CSS'); ?>" href="<?php echo $this->factsUrl;?>" title="<?php echo JText::_('TPL_GUNPOLICY_HOME_INTRO_FOOTER'); ?>">
        <?php echo JText::_('TPL_GUNPOLICY_HOME_INTRO_FOOTER'); ?>
    </a>
    </div>
</div>

<?php
$options_country = '';
$data = GpoGetTypeFromCache( 'public_country' );
if( $data !== false )
{
	$items = explode("\n", $data );
	foreach( $items as $v )
	{
		$value = str_replace("&nbsp;","",$v);
        $locName = ('en' == $currentLangCode) ? ucwords($v) : 
                   $allLocations[ trim($v) ]->{$locationString};
        if(empty($locName)) {
            $locName = ucwords($v); 
        }
        
        $countryList[$value] = $locName;
	}
    
    ##Sorting the location array as of Fr/Es locale
    if ('fr' == $currentLangCode) {
        setlocale("LC_ALL", "fr_FR.utf8");

        $firstLoc  = array_shift($countryList);
        $secondLoc = array_shift($countryList);

        asort($countryList, SORT_LOCALE_STRING);
        array_unshift($countryList, $firstLoc, $secondLoc);
    } else if ('es' == $currentLangCode) {
        setlocale("LC_ALL", "es_ES.utf8");

        $firstLoc = array_shift($countryList);
        $secondLoc = array_shift($countryList);

        asort($items, SORT_LOCALE_STRING);
        array_unshift($countryList, $firstLoc, $secondLoc);
    }

    foreach ($countryList as $value => $locName) {
        $options_country .= '<option value="' . $value . '">' . $locName . '</option>';
    }
}
?>
<div class="introbox charts">
    <div class="<?php echo JText::_('COM_GPO_HOME_FACTS_IMAGE_CSS'); ?>">
        <h2 title="<?php echo JText::_('COM_GPO_HOME_CHARTS_TITLE_TAG'); ?>"><?php echo JText::_('COM_GPO_HOME_CHARTS_TITLE'); ?></h2>

        <div class="ctrlfixed">
            <div class="scroller <?php echo $currentLangCode;?>" title="<?php echo JText::_('COM_GPO_HOME_CHARTS_MORE_EXAMPLE_TITLE_TAG'); ?>">
                <a class="ctls" id="fcprev">&#9668;</a>&nbsp;&nbsp;<?php echo JText::_('COM_GPO_HOME_CHARTS_MORE_EXAMPLE'); ?>&nbsp;&nbsp;<a class="ctls" id="fcnext">&#9658;</a>
            </div>
            <form method="get" action="<?php echo JRoute::_('index.php?option=com_gpo&task=find_facts', true );?>">
               <select  name="country" title="<?php echo JText::_('COM_GPO_HOME_CHARTS_CHOOSE_COUNTRY_TITLE_TAG'); ?>" class="inputboxselect" style="width:160px;" onchange='this.form.submit()' onfocus="reset_select('find_region' );">
                <option value=""><?php echo JText::_('COM_GPO_HOME_CHARTS_CHOOSE_COUNTRY'); ?></option>
                <?php echo $options_country; ?>
            </select>
            </form>
        </div>

        <div class="chartmonitor">
            <ul class="jcarousel-skin-tango" id="mycarousel">
<?php 
    foreach ($this->featuredcharts AS $chart) :
        //$chartLocName = $allLocations[ $chart->location ]->{$locationString};
?>

    <li>
        <img src="<?php echo JURI::root() . 'images/gpo/charts/' . $chart->image?>" height="248px" width="340px" 
             border="0" title="<?php echo JText::_('COM_GPO_HOME_CHARTS_TITLE_TAG'); ?>"/>
        <h4 title="<?php echo JText::_('COM_GPO_HOME_CHARTS_TITLE_TAG'); ?>"><?php echo $chart->location;?></h4>
        <h5 title="<?php echo JText::_('COM_GPO_HOME_CHARTS_TITLE_TAG'); ?>"><?php echo $chart->title;?></h5>
    </li>
    <?php
        endforeach;
    ?></ul>


        </div>
        <a class="<?php echo JText::_('COM_GPO_HOME_CHARTS_FOOTER_CSS'); ?>" href="#" title="<?php echo JText::_('COM_GPO_HOME_CHARTS_FOOTER_IMAGE_TITLE_TAGE'); ?>">
           <?php echo JText::_('COM_GPO_HOME_CHARTS_FOOTER_IMAGE_TITLE'); ?>
        </a>
    </div>
</div>

<?php

/*
<style type="text/css">
    .introbox .newslist h4 a {
        color: #666666;
    }

    .introbox .newslist h4 a:hover {
        color: #E8921E;
    }
</style>
<div class="introbox news">
    <div class="<?php echo JText::_('COM_GPO_HOME_FACTS_IMAGE_CSS'); ?>">
        <h2><a href="/firearms/latest" title="<?php echo JText::_('COM_GPO_HOME_NEWS_TITLE_TAG'); ?>"><?php echo JText::_('COM_GPO_HOME_NEWS_TITLE'); ?></a></h2>

*/
?>

<?php
/*
        ## Show Latest four news title only 
        $i = 0;
        if (count($this->results)) :
            foreach ($this->results as $article):
                ?>

                <div class="newslist">
                    <h4><a href="/firearms/latest"><?php echo $jView->escape($article->gpnheader);?></a></h4>
                </div>
                <?php
                        if (4 == ++$i) {
                break;
            }
            endforeach;
        endif;
        ?>

        <a class="<?php echo JText::_('COM_GPO_HOME_NEWS_FOOTER_CSS'); ?>" href="<?php echo $this->newsUrl;?>" title="<?php echo JText::_('COM_GPO_HOME_NEWS_FOOTER_IMAGE_TITLE_TAG'); ?>">
            <?php echo JText::_('COM_GPO_HOME_NEWS_FOOTER_IMAGE_TITLE'); ?>
        </a>
    </div>
</div>

*/

?>
