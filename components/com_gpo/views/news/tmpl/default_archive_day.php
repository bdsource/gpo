<?php defined( '_JEXEC' ) or die( 'Restricted Access' );

?>
<?php if( !isset( $this->years[ '0' ] ) ): ?>
<p>Unable to find any years. This is most likely a mistake. Please try again.</p>
<?php
return;
endif; ?>

<div style="margin-bottom:20px;">
	<h1>Gun Policy News, <?php echo $this->day . ' ' . $this->month_name . ' ' . $this->year; ?>
        <div class="optionbar">
            <a id="btnprint" class="btn print" title="<?php echo JText::_('COM_GPO_NEWS_PRINT_ICON_TITLE');?>" onclick="window.print();">
            </a>
        </div>
    </h1>
</div>


<style>
ul.li-inline li{ display:inline; }
ul.li-inline li a{
	background:url(/images/M_images/arrow.png) no-repeat scroll 0 1px #FFFFFF;
	list-style-type:none;
	margin-left:0;
	padding:0 5px 0 15px;
}

</style>


<?php if( count( $this->oNews ) < 1 ): ?>
<p>At present there are no News items for this month.</p>
<?php
return;
endif; ?>


<?php
if(count($this->oNews)){
    define('NEWS_LIST',1);
}
foreach( $this->oNews as $article ):

include( JPATH_COMPONENT.DS.'views'.DS.'search'.DS.'tmpl'.DS.'default_abstract.php' );

endforeach;
?>

<div style="margin:20px 0px 20px 0px;">
	<ul class="li-inline" style="margin:0px;">
<?php foreach( $this->years as $year ): ?>
		<li><a href="<?php echo JRoute::_( 'index.php?option=com_gpo&task=news&id=archive&y=' . $year ); ?>"><?php echo $year; ?></a></li>
<?php endforeach;?>
	</ul>
</div>	