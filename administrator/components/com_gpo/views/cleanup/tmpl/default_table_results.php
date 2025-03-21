<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<p>
	Only displaying fields which have an cleanup issue.
</p>

<!-- 
<p>
<a href="#" id="clean-all">Clean all</a>
</p>
-->
<form id="cleaner" method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=find_replace' );?>">
<table class="adminlist">
	<thead>
	<tr>
		<th>Find</th><th>Total</th><th>Table</th><th>Field</th><th>Action</th>
	</tr>
	</thead>
	<tbody id="cleaner-data">
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
			<td>' . $from . '</td><td>' . $total . '</td><td>' . $table . '</td><td class="field">' . $field . '</td><td><a href="#' . $field . '">Find and Replace</a></td>
		</tr>
';
endforeach;
?>
</tbody>
</table>
<input type="hidden" id="cleanup-field" name="field" value="" />
<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
<input type="hidden" name="table" value="<?php echo $this->table; ?>" />
<input type="hidden" name="cmd" value="fnr" />
</form>
<script>
$("cleaner").observe("click",function(event){
	var e,field;
	e = Event.element(event);
	if( e.tagName.toLowerCase() !== 'a' )
	{
		return;
	}
	Event.stop(event);
		
	field = e.readAttribute('href').substr(1);
	$("cleanup-field").value = field;
	$("cleaner").submit();
});

$("clean-all").observe("click",function(event){
	var field;
	Event.stop(event);

	field = [];
	$$("td.field" ).each(function(e){
		var s = e.innerHTML;
		field.push( s.strip() );
	});
	

	$("cleanup-field").value = Object.toJSON( field );
	$("cleaner").submit();
});

</script>