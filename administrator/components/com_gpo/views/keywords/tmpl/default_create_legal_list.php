<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}


$filename = JPATH_BASE . '/components/com_gpo/cache/keywords.txt';
$select_data = array();
$select_data['locations'] = '';
if( file_exists( $filename ) )
{
	$select_data['locations'] = file_get_contents( $filename );
}
?>

<div id="message-box"></div>


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="a_save_legal_list" />
<input type="hidden" name="controller" value="keywords" />
<?php include_once('submenus_startblock.php'); ?>
<p>
Enter all legal keywords, 1 line per keyword. ( The system will automatically order them, no need )
</p>
<p>
<textarea style="width:400px;height:500px;" id="legal-list" name="legal-list"><?php
echo $select_data['locations'];
?></textarea>
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>
<script type="text/javascript">
//<![CDATA[	

Event.observe(window,'load',function(){

$('save-changes').observe('click',function(event)
{
	Event.stop(event);
	new Ajax.Updater( 'message-box', $('adminForm').action,{
		parameters :  $('adminForm').serialize(true),
		evalScripts : true
	});
    return false;
});

});//end load

//]]>
</script>