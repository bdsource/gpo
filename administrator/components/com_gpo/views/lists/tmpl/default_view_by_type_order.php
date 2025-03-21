<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$url = JRoute::_( 'index.php?option=com_gpo&controller=lists&type=' );
$jView = new JViewLegacy();

$current = '';

if( is_array( $this->order) )
{
	foreach( $this->order as $item )
	{
		$current .= $jView->escape( $item ) . "\n";		
	}
}
?>
<style>
#addPanel a{
	display:block;
}
.listedit{
	height:500px;
	width: 100%;
}
</style>
<div id="message_box"></div>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=lists&task=order&type=' . $this->type );?>" id="adminForm">
<input type="hidden" name="controller" value="lists" />
<input type="hidden" name="task" value="order" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<p>
<a href="#" id="showAdd">Add from list</a> or <a href="#" id="resetOrder">Reset Order based on A-Z</a> 
</p>
<div id="addPanel">
<p>
Insert <?php echo $this->current_entries_html; ?> before: ( click on the link to insert before ) or <a href="#" id="addAtEnd" style="display:inline">add to the end</a>.
</p>
</div>
<div id="editPanel">
<textarea class="listedit" id="order" name="order"><?php echo $current; ?></textarea>
</div>
</form>

<script>
var unique_entries = <?php echo json_encode( $this->unique ); ?>;
$("addPanel").hide();
var current_order = '';


$("resetOrder").observe("click", function( event ){
	Event.stop( event );
	var str;
	str = '';
	unique_entries.each( function( e ){
		if( !e.strip().empty() )
		{
			str += e + "\n";	
		}
	});
	
	$("order").value = str;
	$("showAdd").update("Add from list");
	$("showAdd").removeClassName("showadd");
	$("editPanel").show();
	$("addPanel").hide();	
});

$( "showAdd" ).observe( "click", function( event ){
	Event.stop( event );
	if( this.hasClassName( "showadd" ) )
	{
		this.update("Add from list");
		$("editPanel").show();
		$("addPanel").hide();
		this.removeClassName( "showadd" );
		return;
	}

	if(!this.hasClassName( "showadd" ) )
	{
		this.addClassName( "showadd" );
		this.update("Back to Edit");
	}
	
	
	$("addPanel").down("p").nextSiblings().invoke("remove");
	
	var current_order = $("order").getValue();
	current_order = current_order.strip().split("\n");
	current_order.each( function(e,i){
		current_order[i] =e.strip();
	});
	var left = [];
	unique_entries.each( function( e ){
		if( current_order.indexOf( e ) === -1 )
		{
			left.push( e );
		}
	});
	
	if( left.size() > 0 )
	{
		$("addEntry").down('option').nextSiblings().invoke('remove');
		left.each( function( e ){
			var o;
			o = new Element( "option" );
			o.writeAttribute( "value", e );
						
			o.update( e );
			$("addEntry").insert( { 'bottom' : o } );
		});
	}else{
		alert( 'You have no entries left to select from' );
		this.update("Add from list");
		$("editPanel").show();
		$("addPanel").hide();
		this.removeClassName( "showadd" );
		return;
	}
	
	current_order.each( function( e ){
		var o;
		o = new Element( "a" );
		o.writeAttribute( "href", "#" );
		o.writeAttribute( "class", "item" );
		if( e.strip().empty() )
		{
			e = ' ';
		}
		o.update( e );
		$("addPanel").insert( { 'bottom' : o } );
	});
	$("editPanel").hide();
	$("addPanel").show();
	
});


$('addAtEnd').observe( "click", function( event ){
	Event.stop( event );
	var str,current_order;

	str = $("addEntry").getValue();
	if( str.empty() )
	{
		alert("Select from the drop down menu, or click back to edit");
		return;
	}
	$("addEntry").value="";
	
	current_order = $("order").getValue();
	current_order = current_order.strip();
	if( !current_order.empty() )
	{
		current_order += "\n" + str;	
	}else{
		current_order = str;
	}
	
	$("order").value = current_order;
	$("addPanel").hide();
	$("editPanel").show();
	$("showAdd").update("Add from list");
});

$('addPanel').observe("click", function( event ){
	
	Event.stop(event);
	var e = Event.element(event);
	if( !e.hasClassName( "item" ) ) return;
	
	var str,current_orde,new_order,pos;
	
	str = $("addEntry").getValue();
	if( str.empty() )
	{
		alert("Select from the drop down menu, or click back to edit");
		return;
	}	
	
	$("addEntry").value="";
	
	current_order = $("order").getValue();
	current_order = current_order.strip().split("\n");
	current_order.each( function(e,i){
		current_order[i] =e.strip();
	});
	pos = current_order.indexOf( e.innerHTML.strip()  );
	new_order = [];
	current_order.each( function(e,i)
	{
		if( pos === i )
		{
			new_order.push( str );
		}
		new_order.push( e );
	});
	
	$("order").value = new_order.join("\n");
	$("addPanel").hide();
	$("editPanel").show();
	$("showAdd").update("Add from list");
});


$("action-save").observe("click", function( event ){
	Event.stop(event);
	var order;
	order = $("order").getValue();
	$("order").hide();
	$("order").value = Object.toJSON( order.strip().split("\n") );
	$("adminForm").submit();
});

</script>