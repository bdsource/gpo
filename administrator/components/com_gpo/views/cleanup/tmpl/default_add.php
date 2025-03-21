<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<input type="hidden" name="swap[id]" value="<?php echo $this->id;?>" />

<input type="hidden" name="controller" value="cleanup" />
<p>
<?php if( $this->task === 'add' ): ?>
	Add new Find and Replace.
<?php endif;?>
<?php if( $this->task === 'edit' ): ?>
	Edit Find and Replace.
<?php endif;?>
</p>
<p>
	Find:<br />
	<input name="swap[from]" value="<?php echo $jView->escape( $this->from ); ?>"/>
</p>

<p>
	Replace:<br />
	<input name="swap[to]" value="<?php echo $jView->escape($this->to ); ?>"/>
</p>

<p>
	Description:<br />
	<textarea name="swap[notes]" value="<?php echo $jView->escape( $this->notes ); ?>"></textarea>
</p>
</form>
<script>
$("action-save").observe("click", function( event ){
	Event.stop(event);
	$("adminForm").submit();
});

</script>