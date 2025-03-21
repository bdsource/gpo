<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$jView = new JViewLegacy();
?>

<h3>
  Last 600 Replaces
</h3>
<style>
pre{
	padding:1px;
	margin:1px;
	display:inline;
}
.adminlist td a{
	display:inline;
}
.center{
	text-align:center;
}
</style>
<?php include_once('submenus_startblock.php'); ?>
<form action="index.php?option=com_gpo&amp;controller=quotes" method="post" name="adminForm">
<div class="responsive">
<table class="adminlist table-striped table-hover" id="adminList">
	<thead>
	<tr>
		<th>Serial</th>
		<th>From</th>
		<th>To</th>
		<th>Table</th>
		<th>Column</th>
		<th>Options</th>
		<th>Total Updated Rows</th>
		<th>Updated IDs</th>
		<th>Date</th>
		<th>Author Name</th>
	</tr>
	</thead>
	
	<tbody>
<?php
$i = 0;
foreach( $this->items as $item ):
?>	
		<tr class="<?php echo "row$k"; ?>">
			<td> <?php echo $item->id;?> </td>
			<td> <?php echo $item->from;?> </td>
			<td> <?php echo $item->to;?> </td>
			<td> <?php echo $item->table_name;?> </td>
			<td> <?php echo $item->column_name;?> </td>
			<td> <?php echo $item->options;?> </td>
			<td> <?php echo $item->total_updated_rows;?> </td>
			<td> <?php echo $item->updated_ids;?> </td>
            <td> <?php echo $item->created_at;?> </td>
            <td> <?php echo $item->author;?> </td>
		</tr>
<?php
$i++;
$k = 1 -$k;
endforeach;
?>
</tbody>
</table>
</div>
    <input type="hidden" name="option" value="com_gpo" />
	<input type="hidden" name="controller" value="datapages" />
	<input type="hidden" name="task" value="<?php echo $this->task;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="action" value="<?php echo $action;?>" />
</form>
<?php include_once('submenus_endblock.php'); ?>