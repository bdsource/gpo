<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<p>
	Only displaying fields which have an cleanup issue.
</p>


<table class="adminlist">
	<thead>
	<tr>
		<th>Find</th><th>Total</th><th>Table</th><th>Field</th><th>Action</th>
	</tr>
	</thead>
	<tbody>
<?php
foreach( $this->items as $item ):
	$from = $jView->escape( $item->from );
	$total = $item->total;
	$table = $jView->escape( $item->tablename );
	$field = $jView->escape( $item->field );
	$id = $item->id;
		
	$href_view = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=view_issue&id=' . $item->id . '&t=' . $table . '&f=' . $field );
	echo '
		<tr>
			<td>' . $from . '</td><td>' . $total . '</td><td>' . $table . '</td><td>' . $field . '</td><td><a href="' . $href_view . '" target="_blank">View</a></td>
		</tr>
';
endforeach;
?>
</tbody>
</table>