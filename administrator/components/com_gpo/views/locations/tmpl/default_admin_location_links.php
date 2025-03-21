<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>
<?php use \Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('behavior.core'); ?>

<style>
.error_warning{color:#ff0000;}
#message_box a{display:block;}
#adminForm{
	padding:10px;
}
/*#adminForm p label{display:block;padding-right:10px;}*/
.input_field{width:370px;}

.clear{float:none;}


.row{
	clear:both;
	display:block;
	padding:0px;
	margin:0px auto;
}
.cell{
	float:left;
	padding:0px;
	margin:0px auto;
}
.cell label{
	padding:0px;
	margin:0px auto;
	display:inline;
}

.published{
	width:100px;
}

.input120{
	width:120px;
}
.clear{
	clear:both;
}

#quotes_txt_locations a{padding-left:5px;}

.location_txt{font-size:larger;}

#quotes_published{text-align:center;}

#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}

#link_tree span{
	display:block;
}

.depth1{
padding-left:20px;
}
.depth2{
padding-left:40px;
}
.depth3{
padding-left:60px;
}
.depth4{
padding-left:80px;
}
.depth5{
padding-left:100px;
}

#current_link{ 
	font-weight:bold;
	font-size:2em;
}

#menu_close_links{
	padding-left:20px;
	color:#ff0000;
}

#menu_save_links{
}
</style>

<div id="message_box"></div>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php');?>

<p>
	Pick a Location you want to create links to.
</p>
<p style="color:#ff0000">
	Note that lists are not always in alphabetical order
</p>

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="locations" />
<input type="hidden" name="quotes[id]" value="<?php echo $this->oQuote->id; ?>" />
<input type="hidden" id="new_record" name="new_record" value="0" />

<input type="hidden" id="links" name="links" value="" />


<div id="pick_view">

<p><a href="#" id="menu_show_links">Show All Links</a></p>
<p>
Select a Region:<br />
<select id="select_region_pick"><option value="0"></option></select> <a id="menu_pick_region" href="#" class="menu">Create links to this Region</a>
</p>
<p>
Sub Region:<br />
<select id="select_subregion_pick"><option value="0"></option></select> <a id="menu_pick_subregion" href="#" class="menu">Create links to this Sub Region</a>
</p>
<p>
Country:<br />
<select id="select_country_pick"><option value="0"></option></select> <a id="menu_pick_country" href="#" class="menu">Create links to this Country</a>
</p>

<!--
<p>
Jurisdiction:<br />
<select id="select_jurisdiction_pick"><option value="0"></option></select> <a id="menu_pick_jurisdiction" href="#" class="menu">Create links to this jurisdiction ( in a country )</a>
</p>
-->
</div>


<div id="link_tree"></div>

<div id="pick_links">
<div style="border:1px solid #333;width:350px;padding:5px;">
<p>Select the links you want to link to <span id="current_link"></span></p>
<p><a href="#" id="menu_save_links">Save Links</a> <a href="#" id="menu_close_links">Close ( without saving new changes )</a></p>
</div>
<p id="select_panel_region">
Select a Region:<br />
<select id="select_region"><option value="0"></option></select> <a id="menu_add_region" href="#" class="menu">Create a Link</a>
</p>
<p id="select_panel_subregion">
Sub Regions:<br />
<select id="select_subregion"><option value="0"></option></select> <a id="menu_add_subregion" href="#" class="menu">Create a Link</a>
</p>
<p id="select_panel_country">
Countries:<br />
<select id="select_country"><option value="0"></option></select> <a id="menu_add_country" href="#" class="menu">Create a Link</a>
</p>

<p id="select_panel_jurisdiction">
Jurisdiction:<br />
<select id="select_jurisdiction"><option value="0"></option></select> <a id="menu_add_jurisdiction" href="#" class="menu">Create a Link</a>
</p>

<div id="edit_view"></div>
</div>

<?php include_once('submenus_endblock.php');?>
</form>


<?php

foreach( $this->locations as $location )
{
	$locations['id'][] = $location['id'];
	$locations['type'][] = $location['type'];
	$locations['name'][] = $location['name'];
	$locations['display'][] = ( !empty( $location['display'] ) ) ? $location['display'] : '';
}

//$locations['id'][]= "4001";
//$locations['type'][]= "jurisdiction";
//$locations['name'][]= "Oslo";
//$locations['display'][]= "1";

$links=array();
foreach( $this->location_links as $link )
{
	$key = $link['location_id'];
	if( !isset( $links[$key]) )
	{
		$links[$key]=array();
	}
	$links[$key][]=$link['link_id'];
}
?>
<script type="text/javascript">
//<![CDATA[

var oEdit = new Hash();

function can_edit()
{
	id = oEdit.get('id');
	if( parseInt( id ) > 0 ) 
	{
		return true;
	}
	return false;
}



function set_edit( id )
{
	if( can_edit() == true ) 
	{
		save_links();
	}
	index = getIndex( locations.ids, id ).id;

	type = locations.types[index];
/*
	if( type == 'country' )
	{
		alert( 'At the moment, you are unable to add anything to a country, please select from region or subregion' );
		return false;
	}
 */	

	oEdit.set('id',id);
	oEdit.set('links',[]);
	oEdit.set('type',type);
	links = locations.links.get(id);
	
	$('edit_view').update('');
	
	if( !Object.isUndefined( links ) )
	{
		oEdit.set('links',links.clone() );
		display_links(links);
//		alert( 'json: ' + Object.toJSON( oEdit) );		
	}

	index = getIndex( locations.ids, id ).id;
	$('current_link').update( "&quot;" + locations.names[index] + "&quot;");
	
	$('pick_links').show();
	$('pick_view').hide();
	$('link_tree').hide();
}


function close_without_saving_links()
{
	oEdit.set( 'id','0' );
	oEdit.set( 'links',[] );
	reset_edit();
}



function reset_edit()
{
	if( can_edit() == true ) 
	{
		save_links();
	}
	oEdit.set( 'id','0' );
	oEdit.set( 'links',[] );
	$('edit_view').update('');
	$('pick_links').hide();
	$('link_tree').hide();
	$('pick_view').show();
	
	$('select_region_pick').value='0';
	$('select_subregion_pick').value='0';
	$('select_country_pick').value='0';
	
	$('select_region').value='0';
	$('select_subregion').value='0';
	$('select_country').value='0';
}



function save_links()
{
	if( can_edit() == false ) 
	{
		return false;
	}
	locations.links.set( oEdit.get('id'), oEdit.get('links') );
}



function add_edit( id )
{
	if( can_edit() != true ) 
	{
		return false;
	}

	links = oEdit.get('links');
	data = getIndex( links, id );
//	if( data.id == false Object.isUndefined( data ) )
	if( data.id == false )	
	{
		index = getIndex( locations.ids, id ).id;
		new_type = locations.types[index];
	
		switch( oEdit.get('type') )
		{
			case 'region':
				if( new_type == 'region' )
				{
					alert( 'At the moment we do not allow regions to be added to regions' );
					return;
				}
				break;
			case 'subregion':
				var dontAllow = ['subregion','region'];
				if( dontAllow.indexOf( new_type ) !== -1 )
				{
					alert( 'You cant add a subregion or region at the moment.' );
					return;
				}
				break;
			case 'country':
				var dontAllow = ['subregion','region','country'];
				if( dontAllow.indexOf( new_type ) !== -1 )
				{
					alert( 'The location is not allowed to be added to a country, look for order.' );
					return;
				}
				break;
				alert( id );
			default:
				return;
				break;						
		}
		links.push( id );
		oEdit.set('links',links );	
		display_links(links);	
	}
}

function remove_edit( id )
{
	if( can_edit() == false ) 
	{
		return false;
	}

	links = oEdit.get('links');
	data = getIndex( links, id );
	links = links.without( id );
	oEdit.set('links',links );	
	display_links(links);
}


function display_links(links)
{
	if( can_edit() != true ) 
	{
//		alert( 'nothing to link too' );
		return false;
	}
	$('edit_view').update('');
	t='<p>#{name} <a href="#" onmousedown="remove_edit(#{id});return false">Remove this Link</a></p>';
	temp = new Template( t );

	remove_null = [];
	links.each(function(id){
	
	id = String( id );
	index = getIndex( locations.ids, id ).id;
	if( index !== false )
	{
		data =new Hash();
		data.set( 'id', id );
		data.set('name', locations.names[index] );
		html = temp.evaluate( data );
		$('edit_view').insert({bottom:html});	
	}else{
		remove_null.push(id);
	}
});
//this is to remove "null"
	if( remove_null.size() > 0 )
	{
		links = oEdit.get('links');
		remove_null.each(function(id){
			links = links.without( id );
		});
		oEdit.set('links', links );
		locations.links.set( oEdit.get('id'), oEdit.get('links') );	
	}
}



function getIndex( arr, find )
{
	if( Object.isNumber( find ) )
	{
		find = String( find );
	}
	data = new Hash();
	
	if( arr.indexOf( find ) != '-1' )
	{
		data.id = arr.indexOf( find );
		data.value = arr[data.id];
	}else{
		data.id = false;
	}
	return data;
}


reset_edit();


locations = new Hash();
locations.ids = <?php echo json_encode( $locations['id'] ); ?>;
locations.types = <?php echo json_encode( $locations['type'] ); ?>;
locations.names = <?php echo json_encode( $locations['name'] ); ?>;
locations.displays = <?php echo json_encode( $locations['display'] ); ?>;

locations.links = new Hash();
locations.links = '<?php echo json_encode( $links ); ?>'.evalJSON();
locations.links = new Hash( locations.links );


function link_tree( links, depth, template, html )
{
	if( Object.isString( links ) )
	{
		index = locations.ids.indexOf( links );
		data =new Hash();
		data.set( 'depth', depth );
		data.set('name', locations.names[index] );
		html += template.evaluate( data );
	}else if( Object.isArray( links ) )
	{
		++depth;
		links.each( function( link ){
			id_links = locations.links.get( link );
			html = link_tree( link, depth, template, html );
		if( Object.isArray( id_links ) )
		{
			html = link_tree( id_links, depth, template, html );
		}
		
		});	
	}
	return html;
}



//Event.observe(window,'load',function(){

locations.ids.each( function( s,index ){ 

	type = locations.types[index];
	name = locations.names[index];
	oOption = new Element('option',{
									'value': s
					}).update( name );
	oOption2 = new Element('option',{
					'value': s
					}).update( name );
	switch( type )
	{		
		case 'country':
			$( 'select_country_pick' ).insert({bottom:oOption2});
			$( 'select_country' ).insert({bottom:oOption});
			break;
		case 'region':
			$( 'select_region' ).insert({bottom:oOption});
			$( 'select_region_pick' ).insert({bottom:oOption2});
			break;
		case 'subregion':
			$( 'select_subregion' ).insert({bottom:oOption});
			$( 'select_subregion_pick' ).insert({bottom:oOption2});
			break;
		case 'jurisdiction':
//			$( 'select_jurisdiction_pick' ).insert({bottom:oOption2});
			$( 'select_jurisdiction' ).insert({bottom:oOption});
			break;			
	}
	
});




$('menu_add_region').observe('click',function(event){
	Event.stop(event);
	
	if( oEdit.id == '' )
	{
		return;
	}
	id = $('select_region').getValue();
	if( id > 0 )
	{
		add_edit( id );
		
	}
});

$('menu_add_subregion').observe('click',function(event){
	Event.stop(event);
	if( can_edit() != true )
	{
		return;
	}
	id = $('select_subregion').getValue();
	if( id > 0 )
	{
		add_edit( id );
		
	}
});

$('menu_add_country').observe('click',function(event){
	Event.stop(event);
	if( can_edit() != true )
	{
		return;
	}
	id = $('select_country').getValue();
	if( id > 0 )
	{
		add_edit( id );
		
	}
});

$('menu_add_jurisdiction').observe('click',function(event){
	Event.stop(event);
	if( can_edit() != true )
	{
		return;
	}
	id = $('select_jurisdiction').getValue();
	if( id > 0 )
	{
		add_edit( id );
	}
});



$('menu_save_links').observe('click',function(event){
	Event.stop(event);
	reset_edit();
	$("message_box").update('<p><span style="color:#ff0000;font-size:larger">Click Save to update on the Server.</span></p>');
});

$('menu_show_links').observe('click',function(event){
	//alert('test');
	Event.stop(event);
	depth = 0;
	t='<span class="depth#{depth}">#{name}</span>';
	//temp = new Template( t );
	locations.links.each( function( links ){
		depth=0;
		index = getIndex( locations.ids, links.key ).id;
		//only allow top level to be region
		if( locations.types[index] !='region' )
		{
			return;
		}
		//if region has no values ignore it
		if( locations.types[index] =='region' )
		{
			if( links.value.toString().empty() )
			{
				return;
			}
		}

		
		html = link_tree( links.key, depth, temp, html );
		++depth;
		html = link_tree( links.value, depth, temp, html );
	});
	$( 'link_tree' ).update( html );
	$('link_tree' ).show();
});




$('menu_pick_region').observe('click',function(event){
	Event.stop(event);
	id = $('select_region_pick').getValue();
	if( id > 0 )
	{
		set_edit( id );
		$("select_panel_region").hide();
		$("select_panel_subregion").show();
		$("select_panel_country").hide();
		$("select_panel_jurisdiction").hide();
	}
	
});

$('menu_pick_subregion').observe('click',function(event){
	Event.stop(event);
	id = $('select_subregion_pick').getValue();
	if( id > 0 )
	{
		set_edit( id );
		$("select_panel_region").hide();
		$("select_panel_subregion").hide();
		$("select_panel_country").show();
		$("select_panel_jurisdiction").hide();
	}
});

$('menu_pick_country').observe('click',function(event){
	Event.stop(event);
	id = $('select_country_pick').getValue();
	if( id > 0 )
	{
		set_edit( id );
		$("select_panel_region").hide();
		$("select_panel_subregion").hide();
		$("select_panel_country").hide();
		$("select_panel_jurisdiction").show();		
	}
});

$('menu_close_links').observe('click',function(event){
	Event.stop(event);
	links = locations.links.get(id);
	close_without_saving_links();
});



$('toolbar-Link').observe('click',function(event)
{
	Event.stop(event);
	$('adminForm_task').value ='a_save_admin_locations';
	$('links').value = locations.links.toJSON();
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize(true),
		evalScripts : true
		});
      	return false;
});

//]]>
</script>
