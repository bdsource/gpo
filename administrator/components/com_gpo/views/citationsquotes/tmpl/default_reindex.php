<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<h1>Update Indexes</h1>
<form method="post" action="<?php echo JRoute::_( 'index.php' ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="task" value="reindex" />
<input type="hidden" name="reindex" value="1" />


<?php if( $this->shouldReIndex ): ?>
<p>
	Citations &quot;<?php echo $this->type; ?>&quot; Indexes which are used for search are out of date. You should click the button below and update.
</p>

<?php else: ?>
<p>
	Citations &quot;<?php echo $this->type; ?>&quot; Indexes don't appear to be out of date.<br />

	Do you have a missing record, which shows up in &quot;published&quot; yet cant be searched for.<br />
	Then the Citations &quot;<?php echo $this->type; ?>&quot; Indexes are out of date, tick <label><input type="checkbox" name="force" value="1" />force a reindex</label> and then click the button below.
</p>
<?php endif; ?>

<p>
	<input type="submit" value="Update Now!" />
</p>
</form>