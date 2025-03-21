<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<style>
#news-splash-screen a{
	border:1px solid #999;
	padding:2px;
}
</style>
<div id="news-splash-screen">

<p>
		<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=news&task=published'); ?>">Published News Articles</a>
</p>
<p>
		<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=news&task=unpublished'); ?>">Unpublished News Articles</a>
</p>
</div>