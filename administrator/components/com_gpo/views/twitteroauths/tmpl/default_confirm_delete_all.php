<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<h1>Delete All Topics</h1>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="delete" />
<input type="hidden" name="controller" value="topics" />
<input type="hidden" name="id" value="all" />


<h3>WARNING</h3>
<p>
	Clicking the button below, will delete <span style="font-size:larger;color:#ff0000;">ALL</span> Topics. This cannot be reversed.
</p>
<p>
	<input type="submit" value="Delete ( clicking this will delete the item )" />
</p>
</form>