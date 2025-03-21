<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');


$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

$options['types'] = array( 
				'country'=>'Country',
				'jurisdiction'=>'Jurisdiction',
				'region'=>'Region',
				'subregion'=>'Sub Region'				
				);
$options['display'] = array(
				'0'=>'No',
				'1'=>'Yes'
				);
?>


<div id="message-box"></div>

<script>
//<![CDATA[
<?php if( count($this->rows) > 0 ): 
$lo = array();
foreach( $this->rows as $row ):

	$lo[ $row['id'] ] = $row;
endforeach;
?>
var locations='<?php echo json_encode_plus( $lo ); ?>'.evalJSON();
<?php 
else:
?>
var locations=[];
<?php 
endif;
?>


//]]>
</script>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="locations" />
</form>

<div id="message_box"></div>
<p>
If you make any changes you will need to rebuild frontend lists for regions + countries<br />
<span style="color:#ff0000;">To Show a Location to all users, or to Hide it from unregistered users, first click on its name.</span>
</p>

<?php if( count($this->rows) > 0 ): ?>

<style>
.locations td{ vertical-align:text-top; }
</style>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=locations&task=a_edit'); ?>">
<table class="locations" width="90%">
	<tr align="left">
		<th width="20%">Location (English)</th>
		<th width="15%">Type </th>
		<th width="15%">Show</th>
        <th width="25%">Location (Español)</th>
        <th width="25%">Location (français)</th>
	</tr>
	<tr id="location-edit">
	<td>
		<input type="text" name="n" value="" style="width:200px"/>
        <br />
		<a id="action-cancel" href="#">cancel</a>
		<input type="hidden" name="c" value="" />
	</td>
	<td>	
		<select name="t">
			<option value="country">Country</option>
			<option value="subregion">Sub Region</option>
			<option value="jurisdiction">Jurisdiction</option>
			<option value="region">Region</option>		
		</select>
	</td>
	<td>
		<select name="d">
			<option value="1">Show</option>
			<option value="0">Hide</option>
		</select><br />
		<input type="hidden" name="data" value="" />
		<a id="action-save" href="">Save</a>
	</td>
	</tr>
<?php 
	foreach( $this->rows as $key => $row ):
	echo '
<tr id="location-' . $row['id'] . '" class="edit">
	<td>' . $row['name'] . '</td>
	<td>' . $options['types'][ $row['type'] ] . '</td>
	<td>' . $options['display'][ $row['display'] ] . '</td>
    <td>' . $row['name_es'] . '</td>
    <td>' . $row['name_fr'] . '</td>
</tr>';
	endforeach;
?>
</table>
</form>

<?php endif; ?>
<script>
//<![CDATA[	

var EditLocation = Class.create({
	initialize: function() {
		this.reset();
	},
	reset: function(){
		this._new = null;//new Hash( ['id','name','type','display'] );
		this._org = null;
		this.dosave = false;
	},
	set: function( obj ) {
		this._org = new Hash( obj );		
		this._new = new Hash( obj );
	},
	save: function( form ) {
		this.dosave = true;	
		if( this.dosave )
		{
			form['data'].value = Object.toJSON( this._new );
			new Ajax.Updater( $( "message-box" ), form.action,{
				parameters : form.serialize( true ),
				evalScripts : true	
			});
		}
	},
	setName: function( str )  {
		this._new.set("name", str );
	},
	setType: function( str ) {
		this._new.set("type", str );
	},
	setDisplay: function( str ) {
		this._new.set("display", str );
	},
	getId: function() {
		return this._org.get("id");
	}
});

EditLocation = new EditLocation();

var edit_status='hide';
$("location-edit").hide();
$$("tr.edit").each(function(el){
	el.observe("click",function(event){
		Event.stop(event);
		if( edit_status !== 'hide' )
		{
			$("location-edit").hide();
			$("location-edit").previous().show();
		}
		var id = this.readAttribute("id").replace("location-","");
		this.insert( { "after" : $("location-edit") });
		this.hide();
		$("location-edit").show();
		edit_status='show';
		$("location-edit").down("select").value = locations[id].type;
		$("location-edit").down("select","1").value = locations[id].display;
		$("location-edit").down("input[type='hidden']").value = locations[id].name;
		$("location-edit").down("input[type='text']").value = locations[id].name.unescapeHTML();
		
		EditLocation.set( locations[id] );
	});
});
$("action-cancel").observe("click",function(event){
	Event.stop(event);
	$("location-edit").hide();
	edit_status='hide';
	$("location-edit").previous().show();
});

$("action-save").observe("click",function(event){
	Event.stop(event);
		
	EditLocation.setName( $("location-edit").down("input[type='text']").getValue() );
	EditLocation.setType( $("location-edit").down("select").getValue() );
	EditLocation.setDisplay( $("location-edit").down("select","1").getValue() );
	form = $("location-edit").up("form");
	EditLocation.save( form );
});

var options_type = new Hash( {"country":"Country","jurisdiction":"Jurisdiction","region":"Region","subregion":"Sub Region"} );
var options_display = new Hash( { "0":"No","1":"Yes" } );
//]]>
</script>
