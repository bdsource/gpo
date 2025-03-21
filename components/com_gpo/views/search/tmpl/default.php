<?php
//echo 'f';die();

defined( '_JEXEC' ) or die( 'Restricted Access' );
 use Joomla\CMS\Uri\Uri;
$jView = new JViewLegacy();  
?>
<?php
 $jinput = JFactory::getApplication()->input;
 
$latest = ( strpos( Uri::getInstance(), 'firearms/latest' ) === false ) ? false: true;
if( !$latest ) :?>
<h1 class="componentheading"><?php echo JText::_('COM_GPO_NEWS_SEARCH_RESULT_HEADER');?>
    <div class="optionbar">
       <a id="btnprint" class="btn print" title="<?php echo JText::_('COM_GPO_NEWS_PRINT_ICON_TITLE');?>" 
          onclick="window.print();">
       </a>
   </div>
</h1>
<?php else: ?>
<h1 class="componentheading"><?php echo JText::_('COM_GPO_NEWS_HEADER');?>
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
<?php endif;


 $limitstart = $jinput->get('limitstart', '0', '', 'int');
if(  !$latest || $limitstart > (int)'0' ): 

?>
<div class="pagination">
<ul><?php echo $this->pagination->getPagesLinks();?></ul>
<span><?php echo $this->pagination->getResultsCounter(); ?></span>
<?php
$searchedFor = "";
if( !empty( $this->query ) )
{
	$searchedFor .= JText::_('COM_GPO_NEWS_SEARCH_RESULT_QUERY') . $this->query;
}
if( !empty( $this->location ) )
{
	$searchedFor .= ( ( !empty( $searchedFor  ) ) ? JText::_('COM_GPO_NEWS_SEARCH_RESULT_QUERY_LOCATION_SHORT') 
                 : JText::_('COM_GPO_NEWS_SEARCH_RESULT_QUERY_LOCATION') ) . 
                   $this->allLocations[trim($this->location)]->{$this->locationString};
}
?>
<span class="searchedfor"><?php echo $jView->escape( $searchedFor ); ?></span>

</div>
<?php endif; ?>
<?php //endif; ?>


<?php

if( count( $this->results ) ) : ?>
<?php
define('NEWS_LIST',1); //it is used in default_abstract.php page to identify whether that file is loaded inside this file or not!

foreach( $this->results as $article ): 

include( JPATH_COMPONENT.DS.'views'.DS.'search'.DS.'tmpl'.DS.'default_abstract.php' );

endforeach;
 
?>

<?php endif;?>

<?php if( $this->pagination->total > $this->pagination->limit ): ?>
<div class="pagination">
<p><?php echo $this->pagination->getResultsCounter(); ?></p>
<ul><?php echo $this->pagination->getPagesLinks();?></ul>
</div>
<?php endif; ?>
