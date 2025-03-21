<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}
/*
$options['types'] = array( 
				'country'=>'Country',
				'places'=>'Place',				
				'region'=>'Region',
				'subregion'=>'Sub Region'			
				);
$options['display'] = array(
				'0'=>'No',
				'1'=>'Yes'
				);
*/
?>
<div id="message-box"></div>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="a_new" />
<input type="hidden" name="controller" value="locations" />
<input type="hidden" name="d" value="1" />
<?php include_once('submenus_startblock.php'); ?>	

<p>
	Enter the Location Name:
</p>
<p>
	<input type="text" id="location_name" name="n" value="" style="width:200px"/>
</p>

<p>
	<select name="t">
		<option value="country">Country</option>
		<option value="jurisdiction">Jurisdiction</option>
		<option value="subregion">Sub Region</option>
		<option value="region">Region</option>
	</select>
</p>
<p>
	<input id="action-save" type="submit" value="Create a new Location" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script>
//<![CDATA[
$("action-save").observe("click", function(event){
	Event.stop(event);
	form = $("adminForm");
	new Ajax.Updater( $( "message-box" ), form.action,{
			parameters : form.serialize( true ),
			evalScripts : true	
	});
});
//]]>
</script>