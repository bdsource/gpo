<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<style>
#citation-splash-screen p a{
	border:1px solid #999;
	padding:2px;
}
</style>
<div id="citation-splash-screen">
<p>
	Select the citation type you are looking for.
</p>

<p>
	News - 
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=unpublished'); ?>">Unpublished</a> 
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=published'); ?>">Published</a>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=news&task=create'); ?>">New</a>
</p>

<p>
	Quotes -
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=unpublished'); ?>">Unpublished</a> 
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=published'); ?>">Published</a>
	<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=citations&type=quotes&task=create'); ?>">New</a>
</p>

</div>