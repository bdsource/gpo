<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="publishAll" />
<input type="hidden" name="controller" value="citations" />

<input type="hidden" name="ids" value="" />
<input type="hidden" name="cmd" value="publishall" />
<input type="hidden" name="publish[approve]" value="1" />
<?php include_once('submenus_startblock.php'); ?>
<h3>WARNING</h3>
<p>
Clicking 'Publish All' will publish or re-publish every QCite record in the queue.<br />

</p>
<p>
	<input type="submit" value="Publish (clicking this will approve all queued items)" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>
