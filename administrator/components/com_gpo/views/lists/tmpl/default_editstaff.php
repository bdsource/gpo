<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$url = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' );
$jView = new JViewLegacy();
?>
<div id="message_box"></div>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=addstaff' ); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>
<?php if( !empty( $this->staffs ) ){?>
    <div class="responsive">
    <table class="adminlist table-striped table-hover">
	<thead>
	<tr>
		<th>Name</th><th>Initial</th><th>Action</th>
	</tr>
	</thead>
	<tbody id="list-data">

	<?php
foreach( $this->staffs as $staff ):
	$delete_link = JRoute::_('index.php?option=com_gpo&controller=lists&task=deletestaff&id='.$staff->id);
	echo '
		<tr>
			<td class="center" id="row-' . $row->id . '" align="center">' . $jView->escape( $staff->name ) .'</td>
			<td class="center" id="row-' . $row->id . '" align="center">' . $jView->escape( $staff->initial ) .'</td>
			<td class="center" align="center"><a href="'. $delete_link.  '" class="remove">Delete</a></td>
		</tr>
';
endforeach;
?>
</tbody>
</table>
    </div>
<?php } else {
	echo '<p>Sorry, no staff is created so far! Please click Add Staff button to add one now!</p>';
}
    ?>
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="lists" />
<input type="hidden" name="task" value="addstaff" />
<?php include_once('submenus_endblock.php'); ?>
</form>

