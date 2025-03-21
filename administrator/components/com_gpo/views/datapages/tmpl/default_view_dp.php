<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));

// get dp hierarchy & column titles
$columnTitles = getDPColumnTitles();
$dp_hierarchy = getDPHierarchy(3);
$dp_tree = processDPHierarchy($dp_hierarchy);
$gatewayColumns = getDPHierarchyGatewayColumns();

global $treeLeaves;
$treeLeaves = array();

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

<script>
    jQuery.noConflict();
</script>
    
<style type="text/css">
.error_warning {
	color: #ff0000;
}

.hidden {
	display: none;
}

#message_box a {
	display: block;
}

#adminForm {
	padding: 10px;
}


.input_field {
	width: 370px;
}

.clear {
	float: none;
	clear: both;
}

a.buttonlink {
	background-position: 3px top;
	background-repeat: no-repeat;
	color: #000000 !important;
	display: block;
	float: left;
	font-weight: normal;
	line-height: 20px;
	margin: 0 5px 0 0;
	padding-left: 25px;
	padding-right: 5px;
	text-decoration: none;
}

a.buttonlink:hover {
	color: #048DD4 !important;
	border-color: #048DD4;
	background-position: 3px bottom;
}

.expandall {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/exp.gif);
}

.collapseall {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/coll.gif);
}

.plus {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/exp.gif);
    background-repeat: no-repeat;
    padding-left: 25px;
}

.minus {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/coll.gif);
    background-repeat: no-repeat;
    padding-left: 25px;
}

#adminForm p {
	margin: 1px auto;
	line-height: 15px;
	padding: 1px;
	font-size: 8px;
}

#tool-tip-box {
	width: 250px;
	border: 1px solid #cccccc;
	background-color: #ccff99;
	color: #000000;
}
	
#edit_column td{
    padding: 5px 10px;
}
#edit_column th{
    font-weight: bold;
    font-size: large;
    padding: 10px;
}
.expandall {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/exp.gif);
}

.collapseall {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/coll.gif);
}

.metacol {
   display: none;
}
.gateway {
   color:#666666;
   font-weight: bold;
}
.level2col {
   color:#666666;
   font-weight: bold;
}
#adminList {
    line-height: 45px !important;
}
</style>

<?php 
    /*
    $langCodeToReplace = '&lang='.$this->currentLanguage;
    $currentURI = str_ireplace($langCodeToReplace,'',substr(JURI::root(true),0,-1) . $_SERVER['REQUEST_URI']);
    $u =& JURI::getInstance();
    $requestURI = $u->toString();
    $currentURI = str_ireplace($langCodeToReplace,'',$requestURI);
    */
?>

<!-- Language Switching Panel -->
<div class="langFloatBar" title="DP Language: <?php echo getLanguageName($this->currentLanguage);?>">
   <a href="#switchLang">
   <span class="title"><?php echo strtoupper($this->currentLanguage);?></span>
   <br />
   <img border="0" src="<?php echo getLanguageFlag($this->currentLanguage);?>"
        alt="<?php echo getLanguageName($this->currentLanguage);?>"
   />
   </a>
</div>

<div class="langPanel">
      <a name="switchLang"></a>
      <h1 style="float:right;padding-left:200px; padding-right:15%;margin-top:0px;"> 
          ☞ Changes made here will only be applied to the 
          <strong style="color:red;"> <?php echo getLanguageName($this->currentLanguage);?> </strong> DP 
      </h1>
      <div id="langOptionsWrapper">
           <?php echo getLanguageOptionsHTML($this->currentLanguage);?>
      </div>
</div>
<!-- Language Switching panel done -->

<div class="clr"></div>
<br />

<?php
### show warning message for non-existent DPs ###
if( empty($this->dp_data->id) || empty($this->dp_data->location_id) ):
?>
<div class="error message fade" style="color: #FF0000; font-weight: bold; text-align:left;">
<img style="padding-right:10px;padding-left:5px;" src="<?php echo JURI::root();?>media/system/images/notice-alert.png" alt="Warning! Missing DP" align="left">
<p>
WARNING! The ‘[<?php echo $this->location->name;?>]’ DP has not been created. 
When ready, click once on the Save or Apply button to generate the DP, at the same time propagating its Preambles from the master table.
</p>
</div>
<?php
$passed_location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
endif; 
?>


<?php
$fields_per_column = floor($dp_total_fields/2);
$display = 2; //number of columns
$cols = 0;

function getLevel2( $l2Array = array(), $colRowsArray = array(), $columnTitles = array(), $class='' ) {  
         $dataHtml = '';
         global $treeLeaves;

   	   foreach( $l2Array as $v )
   	   {
              $columnTitle = !empty($columnTitles[$v]) ? $columnTitles[$v] : camelize($v);

              if( in_array($v, $treeLeaves) ) { continue; } //already printed, SKIP.

   	        $dataHtml .= '<tr title="'.$v.'" class="l2 '.$class.' hidden">'
   	                  .  '<td width="10">&nbsp;</td>'
   	                  . '<td width="370"><label for="'.$v.'">'. $columnTitle . '</label></td>'          
                      . $colRowsArray[ $v ] 
                      . '</tr>';
              $dataHtml .= "\n";

              $treeLeaves[] = $v; //push the outputted columns
         }

   	   return $dataHtml;
}

function getTopLevelColRow( $column_name, $columnTitles = array() ) {
	 $columnTitle = !empty($columnTitles[$column_name]) ? $columnTitles[$column_name] : camelize($column_name);
	 $colRowTL = '<td width="200">'. $columnTitle . '</td>
	              <td width="310">&nbsp;</td>';
	 return $colRowTL;
}
?>

<fieldset>
<legend>Data Page Values for '<?php echo $this->display_location; ?>'</legend>
<p>☞ &nbsp;Citation syntax is {q1234}. To favour a value and its citation to display in a chart, bracket both with carets: ^10,000{q1234}^. To hide text and spaces in charts, &lt;surround them&gt; with angle brackets.<br />
☞ &nbsp;Substitutions such as # for Location or ~ for Value are only for Preambles, 
and should not be entered in Data fields. Rows in <strong>bold</strong> are Level 2, rows in regular type are Level 3</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo', false); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />
<?php include_once('submenus_startblock.php'); ?>


<?php
echo  '<div id="dataset" class="dataset">';

echo  '<div class="buttonbar">
                    <a class="buttonlink expandall" href="javascript: void(0);">Expand all</a> 
                    <a class="buttonlink collapseall" href="javascript: void(0);">Collapse all</a>
             </div>
             <div class="clear"></div> <div class="clear"></div><br />';

 
     echo '<table class="adminlist" id="adminList" width="1270" style="table-layout:fixed;">
         <thead><tr><th width="10">&nbsp;</th><th width="140">'. JText::_('Category Title').'</th><th width="370">'.JText::_('Data Value').'</th></tr></thead><tbody>';
 
     foreach( $this->dp_metadata as $key=>$val )
     {
       if($val == 'location'){
       	  $this->dp_data->{$val} = $this->location->name; 
       }
       
       $colRow = '<td>';
       
       if( in_array($val, $gatewayColumns) ){
          $colRow .= '<span class="gateway">(Gateway: no data)</span>'; 
       }else{
       	  $colRow .= '<textarea style="width:98%;" name="'.$val.'" id="'.$val.'" cols="" rows="1" class="textarea" ';
          if( is_readonly($val) || in_array($val, $gatewayColumns) ){ $colRow .= 'readonly'; }
          $colRow .= ' >'. htmlspecialchars($this->dp_data->{$val}) . '</textarea>';          
       }
       $colRow .= '</td>';
       
       $colRowsArray[$val] = $colRow;
     }
     
     
     /* print the location sub_header meta info column */
      echo '<tr class="l0" style="font-weight:bold;font-size:10px;">'
     	   . '<td width="10">&nbsp;</td>'
     	   . '<td width="370"><label for="location_subheader">'. camelize('location_subheader') . '</label></td>'
     	   . $colRowsArray[ 'location_subheader' ] 
     	   . '</tr>'; //location subheader field, a meta info column           	
        
     
     
     foreach( $dp_tree['level0'] as $key => $val ) {
     	
      $class = 'zero'.$key;
      
      if( in_array($val, $treeLeaves) ) { continue; } //already printed, SKIP.

     	$treeHtml .= '<tr class="l0" title="'.$val.'" style="font-weight:bold;font-size:15px;">'
     	              . '<td width="10"><a class="tree plus" onClick="toggleview(\''.$class.'\',this);" href="javascript:void(0);" style="color: #000000">'
     	              . '&nbsp;' 
     	              . '</a></td>'
     	              . getTopLevelColRow( $val, $columnTitles ) 
                      . '</tr>'; //level0 title  

      $treeLeaves[] = $val; //push the outputted columns          	
         
     	foreach( $dp_tree['level1'][$val] as $k => $v ) {
     		
                     $columnTitle = !empty($columnTitles[$v]) ? $columnTitles[$v] : camelize($v);
                         
                     if( in_array($v, $treeLeaves) ) { continue; } //already printed, SKIP.

     		            $treeHtml .= '<tr class="l1 '.$class.' hidden" title="'.$v.'" style="color:red;">'
     		                         . '<td width="10">&nbsp;</td>'
     		                         . '<td width="370" class="level2col"><label for="'.$v.'">'. $columnTitle . '</label></td>'
     		                         . $colRowsArray[ $v ] 
                                    . '</tr>'; //level1 title
                                    
                     $treeLeaves[] = $v; //push the outputted columns
     		   	  	    
     		   	  	   //level2data
						   $treeHtml .= getLevel2($dp_tree['level2'][$val][$v], $colRowsArray, $columnTitles, $class); //level2 data
     	}
     	
     }
     
     echo $treeHtml;
     
     //now print the meta info columns but make it hidden
     foreach( $this->dp_metadata as $key=>$val )
     {
     	if( is_nondata_column($val) )
     	{
     		$columnTitle = !empty($columnTitles[$val]) ? $columnTitles[$val] : camelize($val);
     		$class = "metacol";
     		if( 'updated_at' == $val ){
     		   $columnTitle = 'Page Last Updated: ';
     		   $updatedAtHtml = '<div style="font-weight:bold;font-size:10px;color:#666666;">'.     		     
     		     $columnTitle .
     		     '<input class="update_time" style="width:260px;" name="'.$val.'" id="'.$val.'" value="'.$this->dp_data->{$val}.'" cols="" rows="1" class="textarea">'.     		     
     		     '</div>';
     		}else{
     		     $mHtml = '<tr class="'.$class.'" style="font-weight:bold;font-size:10px;">'.
     		     '<td width="5">&nbsp;</td>'.
     		     '<td width="370"><label for="'.$val.'">'. $columnTitle . '</label></td>'.
     		     $colRowsArray[ $val ].
     		     '</tr>';
     		
     		     echo $mHtml;
     		}
     		
     	}
     }

     echo '</tbody></table>';
     echo '<input type="hidden" name="update_time" id="update_time" value="0">';

     echo'</div>';
     echo '<br />';
     
     echo '<p>' . $updatedAtHtml . '</p>';

?>
<div class="clear"></div>


<?php
/*
?>
<table width="100%" border="0" align="left" cellpadding="1" cellspacing="0">
  <?php 
     foreach($this->dp_metadata as $key=>$val){
       if( $cols == 0 ){
          echo "    <tr>\n";
       }
       if($val == 'location'){
       	  $this->dp_data->{$val} = $this->location->name; 
       }
       $columnTitle = !empty($columnTitles[$val]) ? $columnTitles[$val] : camelize($val);
  ?>  
    <td>
         <label for="l_textarea"><?php echo $columnTitle;?></label>
	     <div class="div_textarea">
         <textarea name="<?php echo $val;?>" id="<?php echo $val;?>" cols="" rows="2" class="textarea" <?php if(is_readonly($val)){echo 'readonly';}?> ><?php echo $this->dp_data->{$val};?></textarea>
	     </div>
    </td>
   <?php 
      $cols++;
      if($cols == $display){
        echo "    </tr>\n";
        $cols = 0;
      }
   ?>   
  
  <?php
	 }
 
// Display the correct HTML
if($cols != $display && $cols != 0){
   $neededtds = $display - $cols;
   for($i=0;$i<$neededtds;$i++){
       echo "     <td></td>\n";
   }
     echo "    </tr>\n"
          . "   </table>\n";
} 
else {
   echo "   </table>\n";
}
?>

<?php
*/
?>

<?php 
if( $passed_location_id ):
   echo '<input type="hidden" name="passed_location_id" id="passed_location_id" value="'.$passed_location_id.'">';
   echo '<input type="hidden" name="create_dp_status" id="create_dp_status" value="1">';
endif;
?>
<?php include_once('submenus_endblock.php'); ?>
</form>
</fieldset>

<script type="text/javascript">
window.addEvent('domready', function() {
	  //expand all
	  $$('a.expandall').addEvent('click', function(e) {
	      $$('tr.l1').removeClass('hidden');
	      $$('tr.l2').removeClass('hidden');
	      
	      $$('#dataset a.tree').removeClass('plus').addClass('minus');
	  });

	  //collapse all
	  $$('a.collapseall').addEvent('click', function(e) {
		  $$('tr.l1').addClass('hidden');
		  $$('tr.l2').addClass('hidden');

		  $$('#dataset a.tree').removeClass('minus').addClass('plus');
	  });

	  //track change in updated_at column
	  $('updated_at').addEvent('change',function(event)
	  {
		  $('update_time').value = '1';	     
	  });
});
</script>

<script type="text/javascript">
function toggleview( cls, obj ) {
    if( jQuery(obj).hasClass('plus') ){
    	jQuery(obj).removeClass('plus').addClass('minus');
    }else{
    	jQuery(obj).removeClass('minus').addClass('plus');
    }
	jQuery('tr.' + cls).toggleClass('hidden');
}


/*
 * For language Switching 
 * 
 */
var currentLang = '<?php echo $this->currentLanguage;?>';
jQuery(document).ready(function() {
    jQuery('#languageDropdown').ddslick({
        width: 200,
        onSelected: function (data) {
           var selectedLang = data.selectedData.value;
           if (currentLang == selectedLang ) 
           {
              return true;   
           } 
           else {
              var newLangURIPart = '&lang=' + selectedLang;
              var newLangURI = "<?php echo $this->currentURI;?>"+newLangURIPart;
             // similar behavior as an HTTP redirect
             window.location.replace(newLangURI);
             // similar behavior as clicking on a link
             //window.location.href = "http://stackoverflow.com";
           }
        }
    });
});
</script>