<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$url = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' );
$jView = new JViewLegacy();

?>
<div id="message_box"></div>

<p>
Click to add it. ( You will still need to re-order if need be ) 
</p>
<style>
.lookup a{
	display:block;
}

.current{
	color:#ff0000;
}
</style>
<div class="responsive">
<table class="adminlist table-striped table-hover">
	<thead>
	<tr>
		<th>Entry</th><th>Look up in Table(s)</th><th>Action</th>
	</tr>
	</thead>
	<tbody id="list-data">
<?php 

foreach( $this->unique as $k => $row ):
	
	$html_lookup = '';
	foreach( $row['table'] as $table )
	{
		$item = rawurlencode( $row[ 'item' ] );
		if( $table === 'news' )
		{
			$url = JRoute::_( 'index.php?option=com_gpo&task=search&ignore=1&controller=news&news[' . $this->type . ']=' . $item );
		}else if( $table === 'quotes' )
		{
			$url = JRoute::_( 'index.php?option=com_gpo&task=search&ignore=1&controller=quotes&quotes[' . $this->type . ']=' . $item );
		}else if( $table === 'qcite' )
		{
			
			$url = JRoute::_( 'index.php?option=com_gpo&task=search&ignore=1&controller=citations&&type=quotes&citation[' . $this->type . ']=' . $item );
		}else if( $table === 'ncite' )
		{
			$url = JRoute::_( 'index.php?option=com_gpo&task=search&ignore=1&controller=citations&&type=news&citation[' . $this->type . ']=' . $item );
		}
		$html_lookup .= '<a href="' . $url . '" >' . $table . '</a>';	
	}
	echo '
<tr>
	<td id="row-' . $k. '" class="value">' . $jView->escape( $row['item'] ) . '</td>
	<td class="lookup">' . $html_lookup . '</td>
	<td><a href="#' . $jView->escape( $row['item'] ) .'" class="add">Add</a></td>
</tr>	
';

endforeach;

?>
	</tbody>
</table>
</div>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=a_addlistitem' ); ?>" id="form-do">
<input type="hidden" id="add_id" name="id" />
<input type="hidden" id="add_value" name="value" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
</form>
<script>

$("list-data").observe( "click", function( event ){
	
	var e = Event.element( event );
	if( e.hasClassName( "add" ) )
	{
		Event.stop( event );
		if( e.up('tr').down('td').hasClassName("current") )
		{
			alert( "This is already in the system." );
			return;	
		}
		
		$("add_value").value = e.readAttribute("href").substr( 1 );
		$("add_id").value = e.up('tr').down('td').readAttribute("id").substr( 4 );

		new Ajax.Updater( 'message_box', $('form-do').action,{
			parameters :  $('form-do').serialize( true ),
			evalScripts : true,
			onComplete: function(transport){
				var response,id,e,o;

				response = transport.responseText.strip();
				id = transport.request.options.parameters.id;
				e = $("row-" + id );
				
				if(  response === 'ok' )
				{
					if( e.hasClassName("current") !== true )
					{
						e.addClassName("current");	
					}
					$("message_box").update( e.innerHTML + " has been added" );
					o = new Element('span');
					o.update( e.innerHTML );
					e.update( o );
				}else{
					$("message_box").update( 'Failed to add &quot;' + e.innerHTML + '&quot; at this time, please try again.' );
				}
			}}
		);

	}
});
</script>