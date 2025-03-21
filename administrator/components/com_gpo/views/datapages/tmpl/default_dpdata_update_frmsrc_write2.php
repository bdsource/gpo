<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = JFactory::getDocument();
$front_end = str_replace( "administrator",'',JURI::base(true));
$frontBase = str_replace( "administrator",'',JPATH_BASE);
require_once($frontBase . '/components/com_gpo/models/region.php');
require_once(JPATH_BASE . '/components/com_gpo/models/datapages.php');
require_once($frontBase . '/components/com_gpo/helpers/datapage.php');
require_once(JPATH_BASE . '/components/com_gpo/helper/datapage.php');
unset($document->_scripts[JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js']);
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');

$datapageModel  = new GpoModelDatapages();
$datapageHelper = new DatapageHelper();
$regionModel    = new GpoModelRegion();

$selectedLocationId   = $this->selectedLocationId;
$selectedLocationType = $this->selectedLocationType;
$selectedColumn       = $this->selectedColumnInfo->column_name;
$aggregateLabel       = ('average' == $this->selectedColumnInfo->region_aggregation_type) ? 'Averaged Data' : 'Aggregated Data';

if($selectedLocationType == 'region') {
    $regionInfo      = $datapageHelper->locationExists($selectedLocationId);
    $regionLocations = $regionModel->getAllLocationsByRegion($regionInfo->id);
    $dp_data         = $datapageHelper->getDPByRegion($regionLocations);
    $columnData      = $datapageHelper->getRegionDPTabular($regionInfo->name,$dp_data,$selectedColumn,$regionInfo->id);
}
?>

<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_gpo" />
    <input type="hidden" name="controller" value="datapages" />
    <input type="hidden" name="task" value="<?php echo $this->task;?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="action" value="<?php echo $this->action;?>" />
    <input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

    <table class="adminlist"  border="0" cellpadding="2" cellspacing="2">
    <tr valign="top">
	<th width="10%">
        Data Source 
	</th>

	<th width="40%">
    	Column Info
	</th>
	
	<th width="40%">
	   Share Link (Regenerate link)
	</th>
    </tr>
    
    <tr valign="top">
	<td width="15%">
        <strong> Data Source Name: </strong> <?php echo $this->dataSourceName;?> <br>
        <strong> Data Table Name: </strong>  <?php echo $this->selectedColumnInfo->column_name . '_' . $this->dataSourceName;?> <br>
        <strong> ID: </strong>  <?php echo $regionInfo->id;?> <br>
	</td>

	<td width="25%">
        <strong> Name: </strong>  <?php echo $this->selectedColumnInfo->column_title;?> <br>
        <strong> Alias: </strong> <?php echo $this->selectedColumnInfo->column_name;?> <br>
        <strong> Type: </strong>  <?php echo $this->selectedColumnType?> <br>
        <strong> Aggregation Type: </strong>  <?php echo $this->aggregationOptions[$this->selectedColumnInfo->region_aggregation_type];?> <br>
        <strong> Vertical Axis Label: </strong>  <?php echo $this->verticalAxisLabel[$this->selectedColumnInfo->vertical_chart_label];?> <br>
	</td>
	
	<td width="40%">
        <?php 
           $mainURI  = JURI::base() . "index.php?option=com_gpo&controller=datapages&task=dpdata_update_automation";
           $mainURI .= '&' . $selectedLocationType . "=" . $this->selectedLocationId . '&';
           $mainURI .= $this->selectedColumnType   . "=" . $this->selectedColumnInfo->id;
           
           echo htmlentities($mainURI); //to get rid of &reg
        ?>
	</td>
    </tr>
    </table>
   
    <p> &nbsp; </p>

<?php
$currentYear = intval(date('Y'));
$years = array();
while( $currentYear > 1993 ) {
    $years[] = $currentYear;
    $currentYear--;
}

$dpNewSrcDataArray = array();
foreach($this->newSourceData as $key => $val) {
    $dpNewSrcDataArray[$val->Country] = (array) $val;
}
foreach($this->existingDPDataEn as $key => $val) {
    $existingDPDataEn[$val->location] = $val;
}
?>

<table class="adminlist table-striped table-hover table-bordered" id="adminList" style="table-layout:auto;" cellspacing="1px" cellpadding="1px" width="98%" border="1">
    <thead>
        <tr>
            <th width="10%">Location</th>
            <th width="5%" valign="top" align="center">
		<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
            </th>
            <th width="30%">Column Value</th>
            <?php
            foreach($years as $key=>$val):
                echo "<th>$val</th>";
            endforeach;
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $counter = 0;
            $i = 0;
            $k = 0;
            //foreach($this->existingDPDataEn as $key => $val):
            foreach($this->newSourceData as $key => $val):
                $checked 	= JHTML::_('grid.checkedout', $item, $i );
                $locationObj = $datapageModel->getLocationByName($val->Country);
                $locationObj->name = $val->Country;
                $colVal = $existingDPDataEn[$val->Country]->columnValue; //$dpDataArray[$val];
                $yearlyData = $datapageHelper->splitDataValueYearly($colVal);
                $yearlyDataWithoutQuotes = $datapageHelper->getYearlyDataArray($colVal);
                //var_dump($yearlyData);
                $DPEditURL  = JURI::base() . "/index.php?option=com_gpo&controller=datapages&task=view_dp";
                $DPEditURL .= '&id=' . $locationObj->id . '&location=' . $locationObj->name;
                
                echo "<tr class='row$k'>
                           <td rowspan='3'><a title='Edit/View DP of ". $locationObj->name ."' href='".$DPEditURL."' target='_blank'>".
                           $locationObj->name."</a>
                           </td>";
                echo '<td rowspan="3" width="5%" valign="top" align="center"> '
                     . '<input id="cb' . $i . '" type="checkbox" title="Checkbox for row ' . $i . '" onclick="Joomla.isChecked(this.checked);" value="' . $locationObj->id . '" name="cid['. $i . ']"> </td>';

                echo "<td rowspan='2'>".$colVal."</td>";
                foreach($years as $k=>$v):  
                    echo "<td title='".$yearlyData[$v]."'>".$yearlyData[$v]."</td>";
                endforeach;
                echo "</tr>";
                
                echo "<tr style='color:green;font-weight:bold;'>";
                foreach($years as $k => $v):
                    //echo "<td title='".$yearlyData[$v]."'>".$yearlyData[$v]."</td>";
                    $newDataValues = $dpNewSrcDataArray[$locationObj->name];
                    //var_dump($obj); 
                    //$newMergedYearlyData = $obj + $yearlyData;
                    //var_dump($newMergedYearlyData);
                    //exit();
                    //$col = (string) $v;
                    echo "<td title='".$dpNewSrcDataArray[$locationObj->name][$v]."'>".$dpNewSrcDataArray[$locationObj->name][$v]."</td>"; 
                endforeach;
                echo "</tr>";
                
                $counter++;
                $newDataValues = $dpNewSrcDataArray[$locationObj->name];
                $newMergedYearlyData = array();
                if( !empty($newDataValues) ) {
                    $newMergedYearlyData = mergeYearlyDataWithNewValues($yearlyData, $newDataValues, $yearlyDataWithoutQuotes);
                }
                $newUpdatedValue = concatYearlyDataValues($newMergedYearlyData);
                echo "<tr style='color:red;font-weight:bold;'>";
                echo "<td>". $newUpdatedValue;
                echo '<input style="display:none;" type="checkbox" name="location_id['.$i.']" value="'. $locationObj->id .'" class="location_id"  />';
                echo '<textarea style="display:none;width:90%;" name="update'.$i.'" rows="2">'.$newUpdatedValue.'</textarea>';
                echo "</td>";

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
                    echo "<td title='".$newMergedYearlyData[$v]."'>".$newMergedYearlyData[$v]."</td>"; 
                endforeach;
                echo "</tr>";
                
                if( $counter == 10 ) {break;}
            $i++;
            $k = 1 - $k;
            endforeach;
        ?>
        
        <!-- show aggregated value -->
        <?php
        $aggregatedYearlyData = $datapageHelper->getYearlyDataArray($columnData['aggregatedData']);
        echo "<tr class='aggregate'><td>" . $aggregateLabel . "</td>";
        echo "<td>" . $columnData['aggregatedData'] . "</td>";
        foreach ($years as $key => $val):
            echo "<td title='".$aggregatedYearlyData[$val]."'>" . $aggregatedYearlyData[$val] . "</td>";
        endforeach;
        echo "</tr>";
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th width="10%">Location</th>
            <th width="15%">Column Value</th>
            <?php
            foreach($years as $key=>$val):
                echo "<th>$val</th>";
            endforeach;
            ?>
        </tr>
    </tfoot>
</table>
</form>

<script type="text/javascript">
//jQuery.noConflict();
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
});
</script>