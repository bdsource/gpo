<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();
?>
<h3>
	Find non-standard characters, then replace them to match GPO house style
</h3>
<style>
pre{
	padding:1px;
	margin:1px;
	display:inline;
	font-size:150%;
}
.adminlist td a{
	display:inline;
}
.center{
	text-align:center;
}

</style>
<table class="adminlist">
	<thead>
	<tr>
		<th>Id</th><th>Find</th><th>Replace</th><th>Decription</th><th>Action</th><th>Lookup</th>
	</tr>
	</thead>
	<tbody id="cleanup-list">
<?php
foreach( $this->items as $item ):
	$from = $jView->escape( $item->from );
	$to = $jView->escape( $item->to );
	$notes = $jView->escape( $item->notes );
	
	$href_edit = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=edit&id=' . $item->id );	
	$href_lookup = JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=lookup&id=' . $item->id );	
	echo '
		<tr>
			<td class="center">' . $item->id .'</td><td class="center"><a href="' . $href_lookup . '" title="Lookup">[<pre>' . $from . '</pre>]</a></td><td class="center">[<pre>' . $to . '</pre>]</td><td >' . $notes . '</td><td class="center"><a href="' . $href_edit . '">Edit Row</a> | <a href="#' . $item->id . '">Delete Row</a></td><td class="center">
			<select class="lookup" >
				<option value=""></option>
				<option value="gpo_news">News</option>
				<option value="gpo_quotes">Quotes</option>
				<option value="gpo_citations_news">NCite</option>				
				<option value="gpo_citations_quotes">QCite</option>
			</select>
			</td>
		</tr>
';
endforeach;
?>
</tbody>
</table>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=remove' ); ?>" id="form-remove">
<input type="hidden" id="remove_id" name="id" />
</form>
<script>
$("cleanup-list").observe("click", function( event ){
	var e,ans;
	e = Event.element( event );
	if( e.hasClassName( "remove" ) )
	{
		ans = confirm( "Are you sure you want to delete this?" );
		if( ans )
		{
			$("remove_id").value = e.readAttribute("href").substr( 1 );
			$("form-remove").submit();
		}
	}
});

$("cleanup-list").observe("change",function(event){
	var e,id,url,table;
	e = Event.element(event);
	
	if( e.getValue().empty() === true )
	{
		return;
	}
	
	table = e.getValue();	
	id = e.up('tr').down('td',0).innerHTML;
	id = id.strip();
	
	url = "<?php echo JRoute::_( 'index.php?option=com_gpo&controller=cleanup&task=picker&table=', false ); ?>" + table + "&id=" + id;
	window.location = url;
});

//$$("tr select.lookup").each( function(event){
//	
//});
</script>