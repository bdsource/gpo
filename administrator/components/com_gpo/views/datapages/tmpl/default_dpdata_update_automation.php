<?php
defined('_JEXEC') or die('Restricted Access');
?>

<style>
    #h1column, #h2column, #h3column {
        min-height: 220px; 
    }
    #h3column {
        min-width: 350px;
    }
    #h2column {
        min-width: 350px;
    }
    .hide {
        
    }
    table tr {
        padding-bottom: 20px;
    }
</style>

<?php
$front_end = str_replace("administrator", '', JURI::base(true));
$columnTitles = getDPColumnTitles();
$sampleFilePathByCategory = "../media/gpo/files/Data_Import_Sample_Excel_File_By_Category.xlsx";
$sampleFilePathByLocation = "../media/gpo/files/Data_Import_Sample_Excel_File_By_Location.xlsx";

$options_source = array('unodc'   => 'UNODC DATA UPDATE',
                        'who'     => 'WHO DATA UPDATE',
                        'cdc'     => 'CDC DATA UPDATE',
                        'wisqars' => 'WISQARS DATA UPDATE'
                  );
?>

<form id="adminForm" name="adminForm" method="post" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php');?>" >

    <?php include_once('submenus_startblock.php'); ?>
    <table border="0" cellpadding="0" cellspacing="0">

        <?php
        if (!empty($this->errorMessage)) {
            echo "<tr border='1' style='color:red'><td>" . $this->errorMessage . "</td></tr>";
        }
        ?>

        <tr>
            <td>
                <h4> Select a Data Source to add/update DP yearly values: </h4>
                    <!-- add Region/Group/Location Dropdown -->
                    <select id="data_source" name="data_source"  title="Find armed violence reduction facts by Conflict, Peace and Security (CPS) investment sectors" class="inputboxselect" style="width:200px;">
                        <option value="">Select Data Source</option>
                        <?php
                        foreach ($options_source as $key => $val):
                            echo "<option value='$key'>$val</option>";
                        endforeach;
                        ?>
                    </select>
                     
                    <input type="hidden" name="option" value="com_gpo" />
                    <input type="hidden" name="controller" value="datapages" />
                    <input type="hidden" name="task" value="dpdata_update_automation" />
                    <input type="hidden" name="ParentId" id="ParentId" value="0" />
                    <input type="hidden" name="isSubmitted" id="isSubmitted" value="1" />
            </td>
        </tr>
        
         <tr>
            <td width="35%">
                <h4> Select Data Import Type </h4>
                <input type="radio" name="importType" id="importType1" value="by_location" checked="checked" /> By Location
                &nbsp;&nbsp;
                <input type="radio" name="importType" id="importType2" value="by_category" /> By Category 
            </td>
            <td width="65%">
                <h4> Update Data Only for Blank Years </h4>
                <input type="checkbox" name="importOnlyBlankYears" id="importOnlyBlankYears" value="1" /> Import Data for Blank Years Only
            </td>
         </tr>
        
        <tr id="sampleFileByCategory" class="hide" width="100%">
            <td colspan="2">
                    <h4>Choose an Excel file to upload - Standard format of the file:
                        <a href="<?php echo $sampleFilePathByCategory;?>" target="_blank">Sample Excel (Xlsx/xls) File For Import By Category</a>) <br> 
                        Uploaded file must follow the format of the Sample Excel file for the script to work properly
                    </h4>
            </td>
        </tr>
        <tr id="sampleFileByLocation">
            <td colspan="2">
                    <h4>Choose an Excel file to upload - Standard format of the file:
                        <a href="<?php echo $sampleFilePathByLocation;?>" target="_blank">Sample Excel (Xlsx/xls) File For Import By Location</a>) <br> 
                        Uploaded file must follow the format of the Sample Excel file for the script to work properly
                    </h4>
            </td>
        </tr>
        <tr id="fileSource">
            <td>
                    <input type="file" id="sourceFile" name="sourceFile">
            </td>
        </tr>
        
        
        <tr id="location_list_title">
            <td colspan="3"> <h4>Select a Location to Update:</h4></td>
        </tr> 
        <tr>
            <td colspan="3">
                <div id="location_list_select">
                        <select name="location" id="location">
                            <option value="0"> -- Select a Location -- </option>
                            <?php 
                            foreach($this->allLocations as $key=>$val)
                            {
                                if( empty($val) ){continue;}
                            ?>
                                <option title="<?php echo $val['name'];?>" value="<?php echo $val['id'];?>"> <?php echo $val['name'];?> </option>
                            <?php
                            }
                            ?>
                        </select>
                </div>
            </td>
        </tr>
        
        
        <tr id="category_list_title" class="hide">
            <td colspan="3"> <h4>Select a Category to Update:</h4></td>
        </tr> 
        <tr id="category_list_select" class="hide">
            <td colspan="3">
                <table cellpadding="2" cellspacing="2">
                    <tr valign="top">
                        <td>
                            <select name="h1column" id="h1column"  multiple="multiple">
                                <option value="0"> --Select top header-- </option>
                                <?php
                                foreach ($this->topLevelHeaders as $key => $val) {
                                ?>
                                    <option title="<?php echo $columnTitles[$val]; ?>" value="<?php echo $key; ?>"> <?php echo $columnTitles[$val]; ?> </option>
                                <?php
                                }
                                ?>
                            </select>
                        </td>

                        <td>
                            <div style="border:2px; background-color:#CCCCFF;"> &gt;&gt; 
                            </div> 
                            <img id="h2loader" src="/media/system/images/move-spinner.gif" style="display:none;"/>
                        </td>

                        <td>
                            <select name="h2column" id="h2column" multiple="multiple">
                                <option value="0"> -- Select level-2 category -- </option>
                            </select>
                        </td>

                        <td>
                            <div style="border:2px; background-color:#CCCCFF;"> &gt;&gt; 
                            </div>
                            <img id="h3loader" src="/media/system/images/move-spinner.gif" style="display:none;"/>
                        </td>

                        <td>
                            <select name="h3column" id="h3column" multiple="multiple">
                                <option value="0"> -- Select level-3 category -- </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                </table>

            </td></tr>
    </table>

    <?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
    jQuery.noConflict();
/*    
    Event.observe(window, 'load', function () {

        $('h1column').observe('change', function (event)
        {
            var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
            URL += '&pid=' + $('h1column').value + '&level=2';
            $('h2loader').show();
            new Ajax.Updater('h2column', URL, {
                method: 'GET',
                onComplete: function (transport) {
                    $('h2loader').hide();
                }
            });

            $('h3column').update('<option value="0">' + '-- select 3rd level Category --' + '</option>');
            $('ParentId').setValue($('h1column').value);

        });

        $('h2column').observe('change', function (event)
        {
            var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
            URL += '&pid=' + $('h2column').value + '&level=3';
            $('h3loader').show();
            new Ajax.Updater('h3column', URL, {
                method: 'GET',
                onComplete: function (transport) {
                    $('h3loader').hide();
                }
            });
            $('ParentId').setValue($('h1column').value);
        });

    });//end load
*/
    
jQuery(document).ready(function() {
    
       jQuery('#h1column').on('change',function(event){
       var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
       URL += '&pid=' + jQuery(this).val() + '&level=2';
       jQuery('#h2loader').show();
       jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h2column").html(result);
                       jQuery('#h2loader').hide();    
                      }});
       jQuery('#h3column').text('<option value="0">' + '-- select 3rd level Category --' + '</option>');
       jQuery('#ParentId').val( jQuery('#h1column').val() );
   }); 
   
   jQuery('#h2column').on('change',function(event) {
       var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
       URL += '&pid=' + jQuery(this).val() + '&level=3';
       jQuery('#h3loader').show();
       jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h3column").html(result);
                       jQuery('#h3loader').hide();    
                      }});
        jQuery('#ParentId').val(jQuery('#h1column').val());
    });
    
    
    jQuery('#importType2').on('click',function(event) {
            jQuery('#location_list_title').hide();
            jQuery('#location_list_select').hide();
            jQuery('#sampleFileByLocation').hide();
            
            jQuery('#category_list_title').show();
            jQuery('#category_list_select').show();
            jQuery('#sampleFileByCategory').show();
    });
    
    jQuery('#importType1').on('click',function(event) {
            jQuery('#category_list_title').hide();
            jQuery('#category_list_select').hide();
            jQuery('#sampleFileByCategory').hide();
            
            jQuery('#location_list_title').show();
            jQuery('#location_list_select').show();
            jQuery('#sampleFileByLocation').show();
    });
    
});
</script>
