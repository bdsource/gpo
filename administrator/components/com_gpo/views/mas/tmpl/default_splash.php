<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<style>
#mas-splash-screen a{
	border:1px solid #999;
	padding:2px;
}
</style>
<div id="mas-splash-screen">

<p>
		<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=mas&task=published'); ?>">Published Mas Articles</a>
</p>
<p>
		<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=mas&task=unpublished'); ?>">Unpublished Mas Articles</a>
</p>
</div>