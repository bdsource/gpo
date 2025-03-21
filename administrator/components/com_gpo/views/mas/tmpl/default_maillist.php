<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
if( $this->testMode === true )
{
	echo '<p style="color:#ff0000;">Test Mode, this will fail to send mails.</p>';
	
}
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="maillist" />
<input type="hidden" name="controller" value="mas" />
<?php include_once('submenus_startblock.php'); ?>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Source</th>
			<th>Title</th>
            <th>GPNHeader</th>
            <th><?php echo JText::_( 'Access' ); ?></th>
            <th>Remove</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $this->rows as $pos=>$row ):?>
<?php
	$link_lookup = JRoute::_( 'index.php?option=com_gpo&controller=mas&task=lookup&id='. $row['id'],false );	
	$access = ( (int)$row['share'] == (int)'1' ) ? 'Public' : '<span style="color:#ff0000;">Members</span>';
	if( $row['global_interest'] === '1' )
	{
		$access = '<span style="color:#00ff00;">Public( Global )</span>';
	}
?>
		<tr class="gpo-row" id="gpo-row-<?php echo $pos; ?>-<?php echo $row['id']; ?>">
			<td class="id"><a href="<?php echo $link_lookup;?>"  title="View Mas Item"><?php echo $row['id']; ?></a></td>
			<td class="source"><?php echo $row['source']; ?></td>
            <td class="title"><?php echo $row['title']; ?></td>
            <td class="gpnheader"><a href="<?php echo $link_lookup;?>"  title="View Mas Item"><?php echo $row['gpnheader']; ?></a></td>
			<td class="access">
				<?php echo $access; ?>
			</td>			
			<td class="remove"><a href="#" class="remove">remove</a></td>
		</tr>
<?php endforeach;?>
	</tbody>
	</table>
</div>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript">
//<![CDATA[	
Event.observe(window,'load',function(){
	
$("submit-send").observe("click",function(event){
	Event.stop(event);
	$("adminForm").submit();
});

});
//]]>
</script>