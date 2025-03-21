<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = JFactory::getDocument();
$front_end = str_replace( "administrator",'',JURI::base(true));
$frontBase = str_replace( "administrator",'',JPATH_BASE);
require_once($frontBase . '/components/com_gpo/models/region.php');
require_once(JPATH_BASE . '/components/com_gpo/models/datapages.php');
require_once($frontBase . '/components/com_gpo/helpers/datapage.php');
require_once(JPATH_BASE . '/components/com_gpo/helper/datapage.php');
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript('//cdnjs.cloudflare.com/ajax/libs/floatthead/2.1.2/jquery.floatThead.js');

$datapageModel  = new GpoModelDatapages();
$datapageHelper = new DatapageHelper();
$regionModel    = new GpoModelRegion();

$selectedLocationId   = $this->selectedLocationId;
$selectedLocationType = $this->selectedLocationType;
$selectedColumn       = $this->selectedColumnInfo->column_name;
?>
<style>
    .red {
        color: red;
    }
    .green {
        color: green;
    }
    .blue {
        color: blue;
    }
    .blank {
        background-color: lightgreen !important;
    }
    .stickyHead0 {
        thead tr:nth-child(1) th{
        background: white;
        position: sticky;
        top: 0;
        z-index: 10;
        }
    .stickyHead1 {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    }
</style>

<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_gpo" />
    <input type="hidden" name="controller" value="datapages" />
    <input type="hidden" name="task" value="<?php echo $this->task;?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="action" value="<?php echo $this->action;?>" />
    <input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />
    <input type="hidden" name="search_options" value="<?php echo urlencode( serialize($this->searchOptions) );?>" />

    <?php 
        if($this->importOnlyBlankYears):
             echo "<h4 class='red'> N.B: Import/Replace Data for the Blank Years Only is set to TRUE</h4>";
        endif;
    ?>
    
    <table class="adminlist"  border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr valign="top">
	<th width="10%">
        Data Source 
	</th>

	<th width="40%">
    	Column Info
	</th>
	
	<th width="40%">
	   Check DP FrontEnd (Preview URL)
	</th>
    </tr>
    
    <tr valign="top">
	<td width="15%">
            <strong> Data Source Name: </strong> <?php echo $this->dataSourceName;?> <br>
	</td>

	<td width="25%">
            <strong> Name: </strong>  <?php echo $this->selectedColumnInfo->column_title;?> <br>
            <strong> Alias: </strong> <?php echo $this->selectedColumnInfo->column_name;?> <br>
            <strong> Type: </strong>  <?php echo $this->selectedColumnType?> <br>
	</td>
	
	<td width="40%">
        <?php 
            $previewURL = JURI::root() . '/preview_dp.php?location=Afghanistan&category=' . $this->selectedColumnInfo->column_name;
            echo '<a href="' . $previewURL . '" target="_blank">' . $previewURL . '</a>';
        ?>
	</td>
    </tr>
    </table>
   
    <p> &nbsp; </p>

<?php
$currentYear = intval(date('Y'));
$years = array();
$dpNewSrcDataArray = $this->newSourceData;
foreach( $dpNewSrcDataArray as $key => $dpNewSrcDataYears ):
    foreach( $dpNewSrcDataYears as $key => $val ):
        if( is_integer($key) ):
            $years[] = $key;
        endif;
    endforeach;
    break; // stop after processing first element 
endforeach;

foreach($this->existingDPDataEn as $key => $val) {
    $existingDPDataEn[$val->location] = $val;
}
?>

<p style="color:red;">
    <input type="checkbox" name="replace_en" value="1" checked disabled="1" /> Replace EN (English) DPs &nbsp;
    <input type="checkbox" name="replace_fr" value="1" checked /> Replace FR (French) DPs &nbsp;
    <input type="checkbox" name="replace_es" value="1" checked /> Replace ES (Espanol) DPs &nbsp;
</p>
<div> Color Code: 
    <span class="green">Green: Existing Values; &nbsp;</span>
    <span class="blue">Blue: New Values from Excel File; &nbsp;</span>
    <span class="red">Red: Merged Values to be updated in the DB; &nbsp;</span>
    (Tick/Select the Location Names you want to update)
</div>
<table class="adminlist table-striped table-hover table-bordered stickyHead" id="adminList" style="table-layout:auto;" cellspacing="1px" cellpadding="1px" width="100%" border="1">
    <thead>
        <tr>
            <th width="8%">Location</th>
            <th width="5%" valign="top" align="center">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
            </th>
            <th width="30%" style='min-width:250px;'>Column Value</th>
            <?php
            foreach($years as $key=>$val):
                echo "<th style='max-width:95px;'>$val</th>";
            endforeach;
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $counter = 0;
            $i = 0;
            $k = 0;
            $unknownLocations = array();
            //foreach($this->existingDPDataEn as $key => $val):
            foreach($this->newSourceData as $key => $val):
                $checked     = JHTML::_('grid.checkedout', $item, $i );
                $locationObj = $datapageModel->getLocationByName($key);
                $locationObj->name = $key; //$val->Country;
                
                if( empty($locationObj->id) ) {
                    $unknownLocations[] = $key;
                    continue;
                }
                
                $colVal = $existingDPDataEn[$key]->columnValue; //$dpDataArray[$val];
                $yearlyData = $datapageHelper->splitDataValueYearly($colVal);
                $yearlyDataWithoutQuotes = $datapageHelper->getYearlyDataArray($colVal);
                //var_dump($yearlyData);
                $DPEditURL  = JURI::base() . "/index.php?option=com_gpo&controller=datapages&task=view_dp";
                $DPEditURL .= '&id=' . $locationObj->id . '&location=' . $locationObj->name;
                
                echo "<tr style='color:green;' class='row$k'>
                           <td rowspan='3'>
                           <a title='Edit/View DP of ". $locationObj->name ."' href='".$DPEditURL."' target='_blank'>".
                           $locationObj->name."</a>
                           </td>";
                echo '<td rowspan="3" width="5%" valign="top" align="center"> '
                     . '<input id="cb' . $i . '" type="checkbox" title="Checkbox for row ' . $i . '" onclick="Joomla.isChecked(this.checked);" value="' . $locationObj->id . '" name="cid['. $i . ']"> </td>';

                echo "<td width='30%' style='min-width:250px;' valign='top' align='left'>".$colVal.
                     '<textarea style="display:none;width:99%;" name="existing'.$i.'" rows="6">'.$colVal.'</textarea>'.
                     "</td>";
                foreach($years as $k=>$v):
                    $bClass =  isset($yearlyData[$v]) ? '' : "class='blank'";
                    echo "<td style='max-width:95px;' $bClass title='".$yearlyData[$v]."'>".$yearlyData[$v]."</td>";
                endforeach;
                echo "</tr>";
                
                ###
                ### Caluculate and show new data values ###
                ###
                $counter++;
                $newDataValues = $dpNewSrcDataArray[$locationObj->name];
                $newMergedYearlyData = array();
                if( isset($newDataValues) ) {
                    $newMergedYearlyData = mergeYearlyDataWithNewValues($yearlyData, $newDataValues, $yearlyDataWithoutQuotes, $this->importOnlyBlankYears);
                }
                $newUpdatedValue = concatYearlyDataValues($newMergedYearlyData);
                
                echo "<tr style='color:blue;'>";
                //echo '<td rowspan="2" align="center">&nbsp;</td>';
                echo "<td width='30%' style='min-width:250px;' class='red replace' valign='top' align='left' rowspan='2'> <div align='left'>". $newUpdatedValue . '</div>';
                echo '<input style="display:none;" type="text" name="location_id['.$i.']" value="'. $locationObj->id .'" class="location_id"  />';
                echo '<textarea style="display:none;width:99%;" name="replace'.$i.'" rows="6">'.$newUpdatedValue.'</textarea>';
                echo "</td>";

                foreach($years as $k => $v):
                    $QCite = $dpNewSrcDataArray[$locationObj->name]['QCite'];
                    $QCiteFormatted = '';
                    if ( !empty(trim($QCite)) ) {
                         $QCiteFormatted =  '{' . $QCite . '}';
                    }
                    if ( isset($dpNewSrcDataArray[$locationObj->name][$v]) ) {
                         $newColValue = formatNumberAndQCiteForYearlyData($dpNewSrcDataArray[$locationObj->name][$v], $v, $QCite, $dpNewSrcDataArray[$locationObj->name]);
                    } else {
                         $newColValue = formatNumberForYearlyData($dpNewSrcDataArray[$locationObj->name][$v]);
                    }
                    echo "<td style='max-width:95px;' title='".$newColValue."'>" . $newColValue."</td>";
                endforeach;
                echo "</tr>";
                
                echo "<tr style='color:red;font-weight:bold;'>";
                //echo "<td>". $newUpdatedValue;
                //echo '<input style="display:none;" type="text" name="location_id['.$i.']" value="'. $locationObj->id .'" class="location_id"  />';
                //echo '<textarea style="display:none;width:90%;" name="replace'.$i.'" rows="2">'.$newUpdatedValue.'</textarea>';
                //echo "</td>";

                foreach($years as $k => $v):
                    //echo "<td title='".$yearlyData[$v]."'>".$yearlyData[$v]."</td>";
                    //$newDataValues = $dpNewSrcDataArray[$locationObj->name];
                    //$newMergedYearlyData = array();
                    //var_dump($obj);
                    //if( !empty($newDataValues) ) {
                    //    $newMergedYearlyData = mergeYearlyDataWithNewValues($yearlyData, $newDataValues, $yearlyDataWithoutQuotes);
                    //}
                    //exit();
                    //$col = (string) $v;
                    echo "<td style='max-width:95px;' title='".$newMergedYearlyData[$v]."'>".$newMergedYearlyData[$v]."</td>"; 
                endforeach;
                echo "</tr>";
                
                //if( $counter == 10 ) {break;}
            $i++;
            $k = 1 - $k;
            endforeach;
        ?>
    </tbody>
    
    <tfoot>
        <tr>
            <th width="10%">Location</th>
            <th width="5%">&nbsp;</th>
            <th width="15%">Column Value</th>
            <?php
            foreach($years as $key=>$val):
                echo "<th>$val</th>";
            endforeach;
            ?>
        </tr>
    </tfoot>
</table>

<p>
    Selected Category Name: <strong> <?php echo $this->selectedColumnInfo->column_title;?> &nbsp; &dash; &nbsp; <?php echo $selectedColumn;?></strong>
                      : <input type="hidden" name="selectedColumn" id="selectedColumn" value="<?php echo $selectedColumn;?>" />
</p>

<?php
if( !empty($unknownLocations) ):
?>
<h4 class="red">
    WARNING:: Unknown Location names found in the Excel File. These locations are excluded from the import. Please check the spelling of: <br>
    <i> 
    <?php
        foreach( $unknownLocations as $lk => $lv ):
            echo $lv . '<br>'; 
        endforeach;
        //$a = implode(', ', $unknownLocations);
        //echo $a;
    echo '</i>';
    echo '';
    ?>
</h4>
<?php
endif;
?>
</form>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {
    jQuery('input[name="toggle"]').click(function(){
        var chk = jQuery(this).is(':checked');
        jQuery('.location_id').each(function(){
             jQuery(this).prop('checked', chk);
        });
    });
    
    jQuery('input[type="checkbox"]').click(function(){
        var chk = jQuery(this).is(':checked');
        jQuery(this).parent().parent().find('.location_id').prop('checked', chk);
    });
    
    //Make the table header fixed/scrollable
    jQuery('.stickyHead').floatThead({
	useAbsolutePositioning: true,
	top: 80
    });


SearchUtil = {

        onClickShowEditable: function(pSelector) {
	   jQuery(pSelector).each( function(index) {
		   jQuery(this).click( function(event) {
		      var el = jQuery(this);
                      var tagName = jQuery(this).prop('tagName');
		      if ('DIV' == tagName) {
		    	  jQuery(this).find('textarea').show();
		      }
		      else if ('TD' == tagName) {
		    	  jQuery(this).find('textarea').show();
		      }
		   });
		});
	 }
};

SearchUtil.onClickShowEditable('td.replace');

});
</script>
