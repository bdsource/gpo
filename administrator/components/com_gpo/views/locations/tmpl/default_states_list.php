<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$base_link='index.php?option=com_gpo&controller=locations';

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_country.txt';
if( !file_exists( $filename ) )
{
    echo 'Error: Remember to create your country list';
    return;
}
$data = trim(file_get_contents( $filename ) );
$select_data['country'] =  explode("\n",$data);
?>

<?php include_once('submenus_startblock.php');?>

<h1>View States/Provinces</h1>


<div class='responsive' style='padding-left:10px;'> 
    <p>
            <label style="display:block;" id="mas_location" title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Country*</label>
            <select id="select_mas_location" name="select_mas_location">
                <option value="0" selected="selected">Country</option>
            <?php 
                foreach($select_data['country'] as $cat) :
                    //to deal with the format the text file is in
                    $value = trim(str_replace("&nbsp;",'',$cat ));
                    $isSelected = empty($value) ? '' : (($value==$this->oMas->country_name) ? ' selected' : '');
                    echo "<option value='{$value}'". $isSelected . ">{$cat}</option>";
                endforeach;
            ?>
            </select>
            <span id="mas_txt_locations"></span>
    </p>
</div>

<div class='responsive' style='padding-left:10px;'>
    <h4>States/Provinces</h4>
    <select multiple id="select_mas_states" name="select_mas_states" style="height: 500px;width:300px;overflow: hidden;">
                <option value="0" selected="selected">No States/Provinces found</option>
    </select>
</div>

<?php include_once('submenus_startblock.php');?>

<script type="text/javascript">
   jQuery('#select_mas_location').on('change',function(event)
   {
        var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=mas&task=getStates", false); ?>';
        URL += '&countryName=' + jQuery(this).val();
        jQuery(this).prop('disabled', true);
        jQuery.ajax({
                     url: URL,
                     success: function(result) {
                         jQuery("#select_mas_states").html(result);
                         jQuery('#select_mas_location').prop('disabled', false);
                     }
                 });
    });
</script>