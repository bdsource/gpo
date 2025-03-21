<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<style>
<!--
#h1column {
   width:267px;
   height: 120px;

}
#h1column option {
   width:200px;
}

#h2column, #h3column {
   width:290px;
   height: 120px;
}
#h2column option, #h3column option {
   width:280px;
}
#adminList tr td {
   text-align: center;
   word-break: break-word;
}

tr.aggregate td {
    background-color: #006dcc !important;
    color: #fff;
}
-->
</style>

<?php
$front_end = str_replace( "administrator",'',JURI::base(true));
$frontBase = str_replace( "administrator",'',JPATH_BASE);
require_once($frontBase . '/components/com_gpo/models/region.php');
require_once(JPATH_BASE . '/components/com_gpo/models/datapages.php');
require_once($frontBase . '/components/com_gpo/helpers/datapage.php');

$datapageModel  = new GpoModelDatapages();
$datapageHelper = new DatapageHelper();
$regionModel = new GpoModelRegion();

$selectedLocationId   = $this->selectedLocationId;
$selectedLocationType = $this->selectedLocationType;
$selectedColumn       = $this->selectedColumn->column_name;
$aggregateLabel       = ('average' == $this->selectedColumn->region_aggregation_type) ? 'Averaged Data' : 'Aggregated Data';

if($selectedLocationType == 'region') {
    $regionInfo      = $datapageHelper->locationExists($selectedLocationId);
    $regionLocations = $regionModel->getAllLocationsByRegion($regionInfo->id);
    $dp_data         = $datapageHelper->getDPByRegion($regionLocations);
    $columnData      = $datapageHelper->getRegionDPTabular($regionInfo->name,$dp_data,$selectedColumn,$regionInfo->id);
}
else if($selectedLocationType == 'group') {
    $regionInfo       = $datapageHelper->getGroupById($selectedLocationId);
    $regionInfo->type = 'Group';
    $groupLocations   = $datapageHelper->getAllLocationsByGroupId($regionInfo->id);
    $regionLocations  = array();
    foreach( $groupLocations as $key=>$val ) {
        $regionLocations[] = $val['location_id'];
    }
    
    $dp_data         = $datapageHelper->getDPByGroup($regionInfo->id);
    $columnData      = $datapageHelper->getRegionDPTabular($regionInfo->name,$dp_data,$selectedColumn,$regionInfo->id);
}
?>

    <table class="adminlist"  border="0" cellpadding="2" cellspacing="2">
    <tr valign="top">
	<th width="10%">
        Location Info
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
        <strong> Name: </strong> <?php echo $regionInfo->name;?> <br>
        <strong> Type: </strong>  <?php echo $regionInfo->type;?> <br>
        <strong> ID: </strong>  <?php echo $regionInfo->id;?> <br>
	</td>

	<td width="25%">
        <strong> Name: </strong>  <?php echo $this->selectedColumn->column_title;?> <br>
        <strong> Alias: </strong> <?php echo $this->selectedColumn->column_name;?> <br>
        <strong> Type: </strong>  <?php echo $this->selectedColumnType?> <br>
        <strong> Aggregation Type: </strong>  <?php echo $this->aggregationOptions[$this->selectedColumn->region_aggregation_type];?> <br>
        <strong> Vertical Axis Label: </strong>  <?php echo $this->verticalAxisLabel[$this->selectedColumn->vertical_chart_label];?> <br>
	</td>
	
	<td width="40%">
        <?php 
           $mainURI  = JURI::base() . "index.php?option=com_gpo&controller=datapages&task=groups_dp_tabular";
           $mainURI .= '&' . $selectedLocationType . "=" . $this->selectedLocationId . '&';
           $mainURI .= $this->selectedColumnType . "=" . $this->selectedColumn->id;
           
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

$dpDataArray = array();
foreach($dp_data as $key => $val) {
    $dpDataArray[$val->location_id] = $val->{$selectedColumn};
}
?>

<table class="adminlist" id="adminList" style="table-layout:fixed;" cellspacing="0px" cellpadding="2px" width="98%">
    <thead>
        <tr>
            <th width="10%">Location</th>
            <th width="20%">Column Value</th>
            <?php
            foreach($years as $key=>$val):
                echo "<th>$val</th>";
            endforeach;
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($regionLocations as $key=>$val):
                $locationObj = $datapageModel->getLocationById($val);
                $colVal = $dpDataArray[$val];
                $yearlyData = $datapageHelper->getYearlyDataArray($colVal);
                $DPEditURL  = JURI::base() . "/index.php?option=com_gpo&controller=datapages&task=view_dp";
                $DPEditURL .= '&id=' . $locationObj->id . '&location=' . $locationObj->name;
                
                echo "<tr>
                           <td><a title='Edit/View DP of ". $locationObj->name ."' href='".$DPEditURL."' target='_blank'>".
                           $locationObj->name."</a>
                           </td>";
                echo "<td>".$colVal."</td>";
                foreach($years as $k=>$v):
                    echo "<td title='".$yearlyData[$v]."'>".$yearlyData[$v]."</td>";
                endforeach;
                echo "</tr>";
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