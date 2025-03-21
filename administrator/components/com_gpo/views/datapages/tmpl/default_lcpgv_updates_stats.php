<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>
<style>
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
</style>
<script type="text/javascript">
    var check = function(param){
  


var clssName = document.getElementById(param.id).className;
if(clssName=='crawlbutton'){
  $('crawl_id').value = param.id;  
}

else{
 $('checked_id').value = param.id;    
}
document.getElementById(param.id).disabled = true;
$('adminForm').submit(function(e){
    e.preventDefault();
new Ajax.Updater( 'message_box', $('adminForm').action,{
			parameters :  $('adminForm').serialize( true ),                    
			evalScripts : true,
			onComplete: function(transport){
				var response,id;

				response = transport.responseText.strip();
				id = transport.request.options.parameters.id;
				//e = $("row-" + id );				
				if(response === 'ok')
				{
				$("message_box").html( "<span style='color:blue'>Last checked date successfully updated as of today's date</span>");	                           
                                document.getElementById(param.id).disabled = false;
				    }
                                    else{
			        $("message_box").html( "<span style='color:blue'>Last checked is not successfully updated</span>");
                                document.getElementById(param.id).disabled = false;
				}
			}}
		);
       });
      // ev.preventDefault();
    }
</script>


<?php

$front_end = str_replace( "administrator",'',JURI::base(true));
$frontBase = str_replace( "administrator",'',JPATH_BASE);

//require_once($frontBase . '/components/com_gpo/models/region.php');
//require_once(JPATH_BASE . '/components/com_gpo/models/datapages.php');
//
//require_once($frontBase . '/components/com_gpo/helpers/datapage.php');

//$is_post = JRequest::getMethod();



//$datapageModel  = new GpoModelDatapages();
//$datapageHelper = new DatapageHelper();
//$regionModel = new GpoModelRegion();
//
//$selectedLocationId   = $this->selectedLocationId;
//$websource = $this->websource;
//$selectedLocationType = $this->selectedLocationType;
//$selectedColumn       = $this->selectedColumn->column_name;
//$aggregateLabel       = ('average' == $this->selectedColumn->region_aggregation_type) ? 'Averaged Data' : 'Aggregated Data';
//
//if($selectedLocationType == 'region') {
//    $regionInfo      = $datapageHelper->locationExists($selectedLocationId);
//    $regionLocations = $regionModel->getAllLocationsByRegion($regionInfo->id);
//    $dp_data         = $datapageHelper->getDPByRegion($regionLocations);
//    $columnData      = $datapageHelper->getRegionDPTabular($regionInfo->name,$dp_data,$selectedColumn,$regionInfo->id);
//}
//else if($selectedLocationType == 'group') {
//    $regionInfo       = $datapageHelper->getGroupById($selectedLocationId);
//    $regionInfo->type = 'Group';
//    $groupLocations   = $datapageHelper->getAllLocationsByGroupId($regionInfo->id);
//    $regionLocations  = array();
//    foreach( $groupLocations as $key=>$val ) {
//        $regionLocations[] = $val['location_id'];
//    }
//    
//    $dp_data         = $datapageHelper->getDPByGroup($regionInfo->id);
//    $columnData      = $datapageHelper->getRegionDPTabular($regionInfo->name,$dp_data,$selectedColumn,$regionInfo->id);
//}
//var_dump($this->selectedrows);
?>

<form method="POST" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=lcpgv_updates');?>" id="adminForm" name="adminForm">
<!--    <input type="hidden" name="controller" value="datapages" />-->
    <input type="hidden" name="option" value="com_gpo" />
    <input type="hidden" name="ispost" value="1" />
<!--    <input type="hidden" name="task" value="lcpgv_updates"/>-->
    <input type="hidden" name="country" value="<?php echo $this->SelectedLocationName;?>"/>
    <input type="hidden" id="websource" name="websource" value="<?php echo $this->websource;?>"/>
    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order;?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir;?>"/>
    
    <input type="hidden" id="checked_id" name="id"/>
    <input type="hidden" id="crawl_id" name="crawlsource"/>

    
    <input type="hidden" id="crawledbutton" name="crawledbutton" value="false"/> 
        
    <strong>Last Crawled: <?php echo $this->selectedrows[0]->lastcrawled; ?></strong><br>
    
    <?php 
    if($this->SelectedLocationName=='showall'){
        echo "<strong> Searched In: </strong>".$this->showall."&nbsp;&nbsp;&nbsp";
     }
    else{
        echo "<strong> Searched In: </strong>".$this->SelectedLocationName."&nbsp;&nbsp;&nbsp";
    }
    ?>
    
    
    <strong> Websource:  </strong> <?php echo $this->websource; ?> <br><br>
    <strong> Total Found: <?php echo count($this->selectedrows);?></strong><br>
    
   
  <div id="message_box"><?php echo $this->message; ?></div>    
  <table class="adminlist table-striped table-hover">
	<thead>
		<tr>
<!--                      <th style="width:3%;"> <?php echo JHTML::_('grid.sort', JText::_('#SL'), 'id', $this->filter_order_Dir, $this->filter_order ); ?> </th>-->
                      <th style="width:10%;">  <?php echo JHTML::_('grid.sort', JText::_('QCite ID'), 'id', $this->filter_order_Dir, $this->filter_order ); ?>
                      </th>	
		      <th style="width:10%"><?php echo JHTML::_('grid.sort', JText::_('Author'), 'author', $this->filter_order_Dir ,$this->filter_order);?></th>
                      <th style="width:35%"><?php echo "<p style='color:#0088cc'>".JText::_('Websource')."</p>"; ?></th>
		      <th style="width:20%"><?php echo JHTML::_('grid.sort', JText::_('LCPGV Modified Date'), 'lastmodified', $this->filter_order_Dir ,$this->filter_order);?></th>
                      <th style="width:25%"><?php echo JHTML::_('grid.sort', JText::_('GPO DP Updated'), 'lastchecked',$this->filter_order_Dir ,$this->filter_order);?></th>		      
		</tr>
	</thead>    
    
    <?php
    $sequence = 1;
    //print_r($this->selectedColumn); 
    foreach($this->selectedrows as $key => $val)
      {
        echo "<tr>";?>
<!--        <td width = '3%'><?php echo $sequence++;?></td>-->
        <td width='10%'><a href= "<?php echo JRoute::_('index.php?option=com_gpo&controller=citations&type=quotes&task=lookup&state=published&live_id='.$val->id);?>" target='_blank'><?php echo $val->id ?></a></td>
       <?php
        echo "<td width='10%'>". $val->author. "</td>";        
        echo "<td width='35%'><a href='".$val->websource."' target='_blank'>". $val->websource. "</a></td>";
        if(strtotime($val->lastmodified)==0){
            $val->lastmodified = "Date unavailable<br><button title='Crawl this entry' id='".$val->websource."' class='crawlbutton' onclick =\"check(this)\">Crawl Now</button> ";
        }
        else{
               $date = Datetime::createFromFormat('Y-m-d',$val->lastmodified);      
               $val->lastmodified = $date->format('j M Y');              
        }
        
        echo "<td width='20%'>". $val->lastmodified. "</td>";
        if(strtotime($val->lastchecked)>0){
            $date = Datetime::createFromFormat('Y-m-d',$val->lastchecked);
            if($date){
             $val->lastchecked = $date->format('j M Y');
             }
           }
          else{
             $val->lastchecked = NULL;
          }        
        echo "<td width='25%'>".$val->lastchecked." <br><button title='Update last updated date to as of todays date' id='".$val->qcitesid."' class='checkbutton' onclick =\"check(this)\">GPO DP Updated</button></td>";
        echo "</tr>";
    }
  ?>        
</table>
</form>