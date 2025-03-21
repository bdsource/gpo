<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); 
switch( $this->can_publish )
{
	case true:
		$publish_text = "Publish Now!";
		break;
	default:
		$publish_text = "Place in the publish queue.";	
		break;
}

?>
<h1>Publish</h1>
<div id="message_box"></div>

<h1><?php echo $this->oQuote->title; ?></h1>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="controller" value="quotes" />
<input type="hidden" name="task" value="publish" />
<input type="hidden" name="id" value="<?php echo $this->oQuote->id; ?>" />
<input type="hidden" name="publish[approve]" value="1" />
<?php include_once('submenus_startblock.php'); ?>
<p>
	<input type="submit" value="<?php echo $publish_text; ?>" />
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