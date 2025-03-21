<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$message = "";
if( strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post' )
{
	$message = "Sorry, that ID number was not found, try again.";
}
?>
<div id="msg"><?php echo $message; ?></div>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminFormLookup" name="adminFormLookup">
<?php include_once('submenus_startblock.php'); ?>
<input type="hidden" name="task" value="lookup" />
<input type="hidden" name="controller" value="quotes" />
<span>ID:<input type="text" name="id" value="" /><input type="submit" value="Go" /></span>
<?php include_once('submenus_endblock.php'); ?>
</form>