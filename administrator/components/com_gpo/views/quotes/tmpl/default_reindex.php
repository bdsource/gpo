<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<h1>Update Quotes Indexes</h1>
<form method="post" action="<?php echo JRoute::_( 'index.php' ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="quotes" />
<input type="hidden" name="task" value="reindex" />
<input type="hidden" name="reindex" value="1" />

<?php if( $this->shouldReIndex ): ?>
<p>
	The Quotes Indexes which are used for search are out of date. You should click the button below and update.
</p>

<?php else: ?>
<p>
	The Quotes Indexes don't appear to be out of date.<br />

	Do you have a missing record, which shows up in &quot;published&quot; yet cant be searched for.<br />
	Then the Quotes Indexes are out of date, tick <label><input type="checkbox" name="force" value="1" />force a reindex</label> and then click the button below.
</p>
<?php endif; ?>

<p>
	<input type="submit" value="Update Now!" />
</p>
</form>