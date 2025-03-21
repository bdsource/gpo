<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/system/js/jquery1.6.2.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';

if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_country.txt';
if( !file_exists( $filename ) )
{
	echo 'Error: Remember to create your country list';
	return;
}
$data = trim(file_get_contents( $filename ));
$select_data['country'] =  explode("\n",$data);
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="state_or_province_new" />
<input type="hidden" name="controller" value="locations" />
<input type="hidden" name="action" value="<?php echo $this->action;?>" />

<?php include_once('submenus_startblock.php'); ?>
<input type="hidden" name="d" value="1" />

<?php
if( $this->action == 'delete' ):
?>
    <h1> Delete State/Province: </h1>

    <div id="message-box" style="padding: 20px 5px;color:green;font-weight:bold;"></div>
    
    <p>
        State/Province ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="text" name="locationid" value="<?php echo $this->locationDetails['id'];?>" style="width:200px" />
    </p>
    
    <p>
        State/Province Name: <input type="text" id="location_name" name="location_name" value="<?php echo $this->locationDetails['name'];?>" style="width:200px" />
    </p>
        
    <p>
        <input id="action-save" type="button" value="Delete Group" />
    </p>
    
<?php
else:
?>
    <h1> Create New State/Province: </h1>
    
    <p>
        <span style="color:#000;font-weight:bold">
             ☞ &nbsp; Please avoid using "Underscore (_)" or "Dashes (-)" in State/Province Names. 
        </span>
    </p>
            
    <div id="message-box" style="padding: 10px 5px;"></div>

    <div class="cell">
        <p>
            <label style="display:block;" id="country_name" title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Location*</label>
            <select id="select_country_name" name="select_country_name">
            <option value="">Country</option>
            <?php
                /*foreach($select_data['country'] as $k => $cat) :
                $value = trim(str_replace("&nbsp;",'',$cat ));
                echo <<<EOB
                <option value="{$k}">{$cat}</option>
EOB;
                endforeach;*/
            
                foreach($select_data['country'] as $cat) :
                $value = trim(str_replace("&nbsp;",'',$cat ));
                echo <<<EOB
                <option value="{$cat}">{$cat}</option>
EOB;
                endforeach;
            ?>
            </select>
            
            <span id="mas_txt_locations"></span>
            <input type="hidden" id="mas_hidden_locations" name="mas[location]" value="" />
        </p>
    </div>

    <p>
        Enter the State/Province Name:
    </p>
            
    <p>
        <input type="text" id="location_name" name="location_name" value="" style="width:200px"/>
    </p>
            
    <p>
        <input id="action-save" type="button" value="Create a new State/Province" />
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