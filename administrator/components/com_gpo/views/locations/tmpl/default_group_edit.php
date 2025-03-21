<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/jquery1.6.2.js' );

//JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
//$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));


$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}
$base_link='index.php?option=com_gpo&controller=locations';

$groupLocationsId = array();
foreach( $this->groupLocations as $key => $row ){
    $groupLocationsId[] = $row['id'];       
}
?>

<div id="message-box" style="padding:10px 5px;"></div>

<style>
<!--
#allLocations, .allLocations {
   width:250px;
   height:500px;
   font-size: 13px;
   font-weight: bold;
}
#selectedLocations, .selectedLocations {
   width:250px;
   height: 500px;
   font-size: 13px;
   font-weight: bold;
}
#separator, .separator {
   width: 100px;
   height: 500px;
}
#separator2, .separator2 {
   width: 50px;
   height: 500px;
}

.name { 
   font-size: 15px;
   font-weight: bold;
}
-->
</style>


<form method="post" id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_gpo');?>">

<p>
    <span style="color:#000;font-weight:bold">
        ☞ &nbsp; Please avoid using "Underscore" (_) in group names. 
    </span>
</p>
    
<h1> 
    Group Name: <input class="name" type="text" name="group_name" id="group_name" size="25" 
                       value="<?php echo $this->groupDetails['name'];?>" />
</h1>

<?php if( count($this->allLocations) > 0 ): ?>

<input type="hidden" name="option" id="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" id="task" value="group_edit" />
<input type="hidden" name="controller" id="controller" value="locations" />

<table class="locations">
	<tr>
        <td>
            <span>All Locations</span>
            <span style="padding-left:120px;">
                  <input type="button" name="reSort" id="reSort" value="&#10226 Re Sort">
            </span>
        </td>
        <td>
           &nbsp;
        </td>
        <td>
            In this Group (<?php echo count($this->groupLocations);?>)
        </td>
    </tr>
    
    <tr>
		<td class="selectedLocations">
            <select name="allLocations" id="allLocations" multiple="multiple">
                <?php
                foreach( $this->allLocations as $key => $row ):
                    if( in_array($row['id'], $groupLocationsId) ) {
                        continue;
                    }
                ?>
                   <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>       
                <?php
            	endforeach;
                ?>
            </select>        
        </td>
        <td class="separator" style="vertical-align:middle;" align="center">
           <span style="font-size:20px;">
               Swap All:<br />
               <input type="button" id="swapAllLeft" name="swapAllLeft" value="&lang;&lang;" title="Move all from right to left">
               &nbsp; &nbsp;
               <input type="button" id="swapAllRight" name="swapAllRight" value="&rang;&rang;" title="Move all from left to right"> 
               
               <br />
               <hr>

               <a href="javascript:;" id="swapRight" title="add the selected items to the right">
                  <img src="<?php echo JURI::root().'images/add-to-next-icon.png';?>" />
               </a>
               <br />
               <a href="javascript:;" id="swapLeft" title="add the selected items to the right">
                  <img src="<?php echo JURI::root().'images/add-to-left-icon.png';?>" />
               </a>
               &nbsp;
           </span>
           
        </td>
        <td class="selectedLocations">
            <select name="selectedLocations[]" id="selectedLocations" multiple="multiple">
                <?php
                foreach( $this->groupLocations as $key => $row ):
                ?>
                   <option value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>       
                <?php
            	endforeach;
                ?>
            </select>
            <input type="hidden" name="groupid" id="groupid" value="<?php echo $this->groupId;?>" />
        </td>
        <td class="separator2" style="vertical-align:middle;" align="center">
           <span style="font-size:20px;">
               <hr>
               <br />
               Move Positions: <br />
               <input type="button" id="moveUP" name="moveUP" value="&#x25B2;" title="Move UP">
               <br /> <br />
               <input type="button" id="moveDown" name="moveDown" value="&#x25BC;" title="Move Down"> 
               <br /><br />
               <hr>
           </span>
        </td>
    </tr>
</table>
</form>

<?php endif;?>

<script type="text/javascript">
   var SWAPLIST = {};
   SWAPLIST.swap = function(from, to) {
   $(from)
   .find(':selected')
   .appendTo(to);
   }
   SWAPLIST.swapAll = function(from,to) {
   $(from)
    .children()
    .appendTo(to);
   }
   SWAPLIST.invert = function(list) {
   $(list)
    .children()
    .attr('selected', function(i, selected) {
      return !selected;
    });
   }
   SWAPLIST.search = function(list, search) {
   $(list)
    .children()
    .attr('selected', '')
    .filter(function() {
      if (search == '') {
        return false;
      }
      return $(this)
        .text()
        .toLowerCase()
        .indexOf(search) > - 1
    })
    .attr('selected', 'selected');
   }
    
   /* select all */
   SWAPLIST.selectALL = function(selector) {
   $(selector)
   .each(function() {
      $(selector + " option").attr("selected","selected"); 
   });
   }
   
   SWAPLIST.removeALL = function(selector) {
   $(selector)
   .each(function() {
      $(selector + " option").removeAttr("selected","selected"); 
   });
   }
   
   function sortSelect(selElem) {
        var tmpAry = new Array();
        for (var i=0;i<selElem.options.length;i++) {
            tmpAry[i] = new Array();
            tmpAry[i][0] = selElem.options[i].text;
            tmpAry[i][1] = selElem.options[i].value;
        }
        tmpAry.sort();
        while (selElem.options.length > 0) {
            selElem.options[0] = null;
        }
        for (var i=0;i<tmpAry.length;i++) {
            var op = new Option(tmpAry[i][0], tmpAry[i][1]);
            selElem.options[i] = op;
        }
        return;
   }
   
   /** 
    * originally declared in joomla.javascript.js.  
    * overriden by this component
    */
    Joomla.submitbutton = function(task) {
       SWAPLIST.selectALL('#selectedLocations');
	   Joomla.submitform(task);

       return false;
    };

   $(document).ready(function() {
       $('#reSort').click(function() {
           $('#reSort').attr('disabled','disabled');
           var selObj = document.getElementById('allLocations');
           sortSelect( selObj );
           $('#reSort').removeAttr('disabled');
           alert("Thanks! Resorted alphabetically!");
           
       });
       $('#swapRight').click(function() {
           SWAPLIST.swap('#allLocations', '#selectedLocations');
       });
       $('#swapLeft').click(function() {
           SWAPLIST.swap('#selectedLocations', '#allLocations');
       });
       $('#swapAllRight').click(function() {
           SWAPLIST.swapAll('#allLocations', '#selectedLocations');
       });       
       $('#swapAllLeft').click(function() {
           SWAPLIST.swapAll('#selectedLocations', '#allLocations');
       });
     
       $('#moveUP, #moveDown').click(function() {
        var $op = $('#selectedLocations option:selected'),
            $this = $(this);
        if($op.length){
            ($this.attr('id') == 'moveUP') ? 
                $op.first().prev().before($op) : 
                $op.last().next().after($op);
        }
       });       
   });
</script>