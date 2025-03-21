<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="group_new" />
<input type="hidden" name="controller" value="locations" />
<input type="hidden" name="action" value="<?php echo $this->action;?>" />
<?php include_once('submenus_startblock.php'); ?>
<input type="hidden" name="d" value="1" />

<?php
if( $this->action == 'delete' ):
?>
    <h1> Delete Group: </h1>

    <div id="message-box" style="padding: 20px 5px;"></div>
    
    <p>
        Group ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="text" name="groupid" value="<?php echo $this->groupDetails['id'];?>" style="width:200px" />
    </p>
    
    <p>
        Group Name: <input type="text" id="group_name" name="group_name" value="<?php echo $this->groupDetails['name'];?>" style="width:200px" />
    </p>
        
    <p>
        <input id="action-save" type="button" value="Delete Group" />
    </p>
    
<?php
else:
?>
    <h1> Create New Group: </h1>
    
    <p>
        <span style="color:#000;font-weight:bold">
             ☞ &nbsp; Please avoid using "Underscore (_)" or "Dashes (-)" in group names. 
        </span>
    </p>
            
    <div id="message-box" style="padding: 10px 5px;"></div>
   
    <p>
         Enter the Group Name:
    </p>
            
    <p>
         <input type="text" id="group_name" name="group_name" value="" style="width:200px"/>
    </p>
            
    <p>
         <input id="action-save" type="button" value="Create a new Group" />
    </p>

<?php
endif;
?>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script>
//<![CDATA[
$("#action-save").live("click", function(event){
	event.preventDefault();
    
    var buttonValue = $(this).attr('value');   
    if( 'Delete Group' == buttonValue ) {
        if ( !confirm('Are you sure you want to remove this Group? Although links to the Group will be deleted, no Locations or their data will be affected.') ) {
            return false;
        }
    }
    
    var form = $("#adminForm");
   
    $.ajax({
            type: "POST",
            url: form.attr('action'),
            data:  form.serialize()
          }).done(function( msg ) {
            $( "#message-box" ).html(msg);
    });
    
});
//]]>
</script>