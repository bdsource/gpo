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
<h1>Delete Citation (QCite)</h1>
<!--
<pre>
This is yet to be finalised, and will change as we roll the sphinxsearch into it.
In the meantime, you can delete from the system.
</pre>
-->
<div id="message_box"></div>

<h1><?php echo $this->item->title; ?></h1>
<p>
	ID:Q<?php echo $this->item->id; ?>
</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="published_delete" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="quotes" />
<?php include_once('submenus_startblock.php'); ?>

<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<h3>WARNING</h3>
<p>
	Clicking the button below, will delete this citation. This cannot be reversed.
</p>
<p>
	<input type="submit" value="Delete ( clicking this will delete the item )" />
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript">
//<![CDATA[
/*
    $('adminForm').observe('submit', function(event) {
    	Event.stop(event);
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize( true ),
		evalScripts : true
		});
      	return false;
    });
	
	$('item_delete').observe('click', function(event){
    	Event.stop(event);
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize( true ),
		evalScripts : true
		});
      	return false;
	});
*/
//]]>
</script>