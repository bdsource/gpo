<div id="breadcrumbs">
<p>
	<a href="<?php 
echo JRoute::_('index.php?option=com_gpo&task=news&id=archive', true ); 
?>">Archive</a>
<?php if( !empty( $this->year ) ):?>
	<a href="<?php 
echo JRoute::_('index.php?option=com_gpo&task=news&id=archive&y=' . $this->year, true ); 
?>"><?php echo $this->year; ?></a>
	<?php if( !empty( $this->month ) ):?>
		<a href="<?php 
echo JRoute::_('index.php?option=com_gpo&task=news&id=archive&y=' . $this->year . '&m=' . $this->month, true ); 
?>"><?php echo $this->month_name; ?></a>
	<?php endif; ?>	
<?php endif;?>
</p>
</div>