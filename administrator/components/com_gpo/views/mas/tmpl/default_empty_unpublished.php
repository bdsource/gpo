<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="unpublished_empty" />
<input type="hidden" name="controller" value="mas" />
<?php include_once('submenus_startblock.php'); ?>

<input type="hidden" name="cmd" value="del" />
<h3>WARNING</h3>
<p>
Clicking delete will remove all unpublished items from the queue.
</p>
<p>
	<input type="submit" value="Delete ( clicking this will delete all unpublished items )" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>