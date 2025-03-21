<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<h1>Delete Topic</h1>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="delete" />
<input type="hidden" name="controller" value="topics" />
<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>" />



<h1><?php echo $jView->escape( $this->topic->window_title ); ?></h1>
<p>
	<?php echo $this->topic->seo; ?>
</p>


<h3>WARNING</h3>
<p>
	Clicking the button below, will delete this Topic. This cannot be reversed.
</p>
<p>
	<input type="submit" value="Delete ( clicking this will delete the item )" />
</p>
</form>