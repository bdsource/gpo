<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>
<h1>Publish Green</h1>
<div id="message_box"></div>

<p>Hitting return will publish all records. ( This may take some time )</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="controller" value="quotes" />
<input type="hidden" name="task" value="publish_green" />
<input type="hidden" name="publish[approve]" value="1" />
<?php include_once('submenus_startblock.php'); ?>
<p>
	<input type="submit" value="Publish All Green" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript"> 
//<![CDATA[
document.observe("keydown",function(event){
	
	if( event.keyCode === Event.KEY_RETURN )
	{
		Event.stop(event);
		$('adminForm').submit();
	}
});
//]]> 
</script>