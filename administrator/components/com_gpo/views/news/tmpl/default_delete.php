<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<h1>Delete News Item</h1>

<div id="message_box"></div>

<h1><?php echo $this->item->title; ?></h1>
<p>
	ID:N<?php echo $this->item->id; ?>
</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="published_delete" />
<input type="hidden" name="controller" value="news" />

<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<?php include_once('submenus_startblock.php'); ?>
<h3>WARNING</h3>
<p>
	Clicking the button below, will delete this quote. This cannot be reversed.
</p>
<p>
	<input type="submit" value="Delete ( clicking this will delete the item )" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>