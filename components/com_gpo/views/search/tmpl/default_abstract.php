<?php
//Is called from above. 
//$jView = new JView();
if( !isset( $jView ) )
{
	$jView = new JViewLegacy();
}

$lang    = JFactory::getLanguage();
$langTag = $lang->getTag();
$locationString = 'name';
if (strlen($langTag) > 2) {
    $currentLangCode = strtolower(substr($langTag, 0, -3));
}
if (in_array($currentLangCode, array('es','fr'))) {
    $locationString = 'name_' . $currentLangCode;
}

$locations = explode(",", $article->locations );
$allLocations = GpoGetAllLocationNames();

$title = GpoEndWith( " ", $article->title );
if( !empty( $article->source ) )
{
	$title .= "— " . trim( $article->source );
	if( !empty( $article->category ) )
	{
		$title = GpoEndWith( ", ", $title ) . $article->category;
	}
}	
		
if( $this->logged_in )
{
	$href_web_source = JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $article->id );	
	//$a_web_source  =  '<a target="_blank" href="' . JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $article->id ) . '" title="Read Full Article">Read Full Article</a>';	
    $a_web_source  =  '<a href="javascript:NewsWindow=window.open(\'' 
	                  . JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $article->id ) 
	                  . '\',\'newNewsWindow\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600\');'
	                  . 'NewsWindow.focus();" title="'.JText::_('COM_GPO_READ_FULL_ARTICLE').'">'.JText::_('COM_GPO_READ_FULL_ARTICLE').'</a>';	
}else{
	$href_web_source = addOnlyHTTP($article->websource);
        
	if( $article->websource !== 'NoWebSource' && $article->websource !== 'No Web Source')
	{
		$a_web_source  =  '	<a href="' . addOnlyHTTP($article->websource) . '" title="' . JText::_('COM_GPO_NEWS_PUBLISHERS_LINK') . $jView->escape( $article->source ) . '" target="_blank">' . JText::_('COM_GPO_READ_MORE') . $jView->escape( $article->source ) . '</a>';
	}else{
		$a_web_source = "<a href=\"javascript:NewWindow=window.open('" . JRoute::_( 'index.php?option=com_gpo&task=search&view=nowebsource&source=' . $jView->escape( $article->source ), false ) . "','newWin','width=560,height=230,left=0,top=0,toolbar=yes,location=center,scrollbars=No,status=No,resizable=No,fullscreen=No');NewWindow.focus();void(0);\">" . JText::_('COM_GPO_READ_MORE') . $jView->escape( $article->source ) . "</a>";
	}
}

if(!defined('NEWS_LIST')){ //declared in default.php page. Identifies whether this file is loaded inside that file or not
   ?>
   <h1 class="componentheading">Gun Policy News
   
   <div class="optionbar icons">
    	<ul class="dropdown-menu">
        <li>
            <div class="a2a_kit a2a_kit_size_20 a2a_default_style">
                <a class="a2a_button_facebook"></a>
                <a class="a2a_button_twitter"></a>
                <a class="a2a_button_google_plus"></a>
                <a class="a2a_dd" href="#"></a>
            </div>
        <li>
        <a id="btnprint" class="btn print" title="<?php echo JText::_('COM_GPO_NEWS_PRINT_ICON_TITLE');?>" 
           onclick="window.print();">
        </a>
        </li>
        </ul>
    </div>
   
   </h1>
   <?php
}
?>


<div class="na_news_abstract">

<p class="na_locations">
<?php if ( isset( $locations['0'] ) ): ?>
<?php
$size = count( $locations )-1;
foreach(  $locations as $key => $location ):
$url = JRoute::_( 'index.php?option=com_gpo&task=search&q=&l=' ) . urlencode($location);
$str = $jView->escape( $allLocations[ trim($location) ]->{$locationString} ); //$jView->escape($location);
$str .= ( ( $size !== $key ) ? ",":'' );
echo '<a href="' . $url . '" title="Link to articles related to ' . $jView->escape( $location ) .'">' . $str . '</a>';
endforeach;
endif; ?>
</p>

<?php if( $this->logged_in === false ): ?>
<p class="na_title"><a href="<?php echo $href_web_source; ?>" title="<?php echo $jView->escape( $title ); ?>"><?php echo $jView->escape( $article->gpnheader ); ?></a></p>
<?php else: ?>
<style>
    .single-news-title-link:hover {
        color: #F79646;
    }
</style>
<!--<p class="na_title single-news-title-link"><a href="<?php echo $jView->escape($article->websource); ?>" title="<?php echo $jView->escape($article->title) . " &#8212; " . $jView->escape($article->source); ?>"><?php echo $jView->escape( $article->gpnheader ); ?></a></p>-->
<p class="na_title single-news-title-link"><a href="<?php echo addOnlyHTTP($article->websource); ?>" title="<?php echo $jView->escape($article->title) . " &#8212; " . $jView->escape($article->source); ?>"><?php echo $jView->escape( $article->gpnheader ); ?></a></p>
<?php endif; ?>

<p class="na_published"><?php echo date( 'j F Y', strtotime( $article->published ) ) ?></p>
<p class="na_source"><?php
echo $jView->escape( $article->source );
if( !empty( $article->category) )
{
	echo ", " . $jView->escape( $article->category );
}
?></p>

<div class="na_content">
<?php echo gpo_helper::short( $article->content ); ?>
</div>
<p class="na_further_reading">
<?php echo $a_web_source; ?>
</p>
<?php //is member or not ?
//$this->oUser=JFactory::getUser();
if ($this->logged_in) { ?>
            <p class="na_article_id">
             <a class="link" href="javascript:popup=window.open('<?php echo JRoute::_( 'index.php?option=com_gpo&task=news&id=' . $article->id ); ?>','GunPolicy','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600'); popup.focus();" title=""><?php echo $article->id; ?></a>
            </p>
<?php        } else { ?>
            <p class="na_article_id">
             <?php echo $article->id; ?>
            </p>
<?php        }  ?>

</div>

<?php
// Additional text and link on 'Latest' page

if($this->isSingleNews){
    $url = JRoute::_( 'firearms/latest' , false);
    ?>
    <style>
        .single-news-link-text h4:hover {
            color: #F79646;
        }
        .single-news-link-text h5:hover {
            color: #0070C0;
        }
    </style>
    <div class="single-news-link-text" style="margin-top: 50px; text-align: left; line-height: 3px;">
        <a title="Read more global armed violence, firearm law and gun control news" style="color:#0070C0; hover: black;" href="<?php echo $url; ?>"><h4><strong>Read More Global Alpers News</strong></h4></a><br/>
        <a title="Read more global armed violence, firearm law and gun control news" style="color:#F79646;" href="<?php echo $url; ?>"><h5><strong>Armed violence, firearm law and gun control</strong></h5></a>
    </div>
<?php } ?>