<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}
?>
<div id="message-box"></div>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="a_delete" />
<input type="hidden" name="controller" value="locations" />
<?php include_once('submenus_startblock.php');?>
<p>
	Enter the Location Name that you want to delete:
</p>
<p>
	<input type="text" id="location_name" name="n" value="" style="width:200px"/>
</p>

<p>
	<input id="action-delete" type="submit" value="Delete Location" /> ( This cannot be reversed very easily )
</p>
<?php include_once('submenus_endblock.php');?>
</form>

<script>
//<![CDATA[
$("action-delete").observe("click", function(event){
	Event.stop(event);
	form = $("adminForm");
	new Ajax.Updater( $( "message-box" ), form.action,{
			parameters : form.serialize( true ),
			evalScripts : true	
	});
});
//]]>
</script>