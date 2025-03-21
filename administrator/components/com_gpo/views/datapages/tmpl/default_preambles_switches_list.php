<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base());
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
function getLevel2( $l2Array = array(), $master_list = array(), $columnTitles = array(), $gatewayColumns = array(), $class='' ) 
{
	   $dataHtml = '';
	   global $treeLeaves;
   	   foreach( $l2Array as $v )
   	   {
			if( in_array($v, $treeLeaves) ) { continue; } //already printed, SKIP.
			
   	   	    $dataHtml .= '<tr title="'.$v.'" class="l2 '.$class.' hidden">';
   	      	$dataHtml .= getRow( $v, $master_list, $columnTitles, $gatewayColumns );
			$dataHtml .= "</tr>";   
			
			$treeLeaves[] = $v; //push the outputted columns
		}
		  
   	   return $dataHtml;
}


function getRow( $column, $master_list = array(), $columnTitles = array(), $gatewayColumns = array() )
{
$row = '
    <td>
        &nbsp;
    </td>';
		
    if( in_array($column,$gatewayColumns) )
    {
       $row .= '<td><label class="gateway" title="'.$column.'">' . $columnTitles[$column] . '</label>';
       $row .= '</td>    
               <td> (Gateway: no data)	</td>
               <td>  (Gateway: no data) </td>
               <td>N/A</td>
               ';
    }
    else
    {
       $row .= '<td><label title="'.$column.'">' . $columnTitles[$column] . '</label>';	         
	$row .= '<input type="hidden" name="column_name[]" value="'.$column.'" >
	</td>    
    <td>		
	    <textarea style="width:98%;" id="preambles[]" name="preambles[]" rows="1" class="textarea">' . htmlspecialchars( $master_list[$column]->preamble, ENT_QUOTES ) . '</textarea>  
	</td>
    <td>
	    <textarea style="width:98%;" id="switches[]" name="switches[]" rows="1" class="textarea">' . htmlspecialchars( $master_list[$column]->switches, ENT_QUOTES ) . '</textarea>
	</td>
	<td><input type="text" name="sorting_orders[]" value="'.htmlspecialchars($master_list[$column]->sorting_orders, ENT_QUOTES).'" /></td>
	';
    }
return $row;
}
?>


<div id="message_box"></div>


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
      <div id="langOptionsWrapper">
           <?php echo getLanguageOptionsHTML($this->currentLanguage);?>
      </div>
</div>
<div class="clr"></div>
<br />
<!-- Language Switching panel done -->

<fieldset>
<legend><span style="font-size:13px;"> <?php echo JText::_('Edit Preambles & Switches Master List');?></span></legend>
<p>
Rows in <strong>bold</strong> are Level 2, rows in regular type are Level 3. 
Substitutions such as # for Location or ~ for Value are only for the Preambles column. 
In the Data and Switches column, an asterisk* shows where to insert the value string in the corresponding data field in each Location DP.  
</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />
<?php //include_once('submenus_startblock.php'); ?>


<?php
$dp_imagepath = '/images/datapages/';
$dp_tree = $this->dp_tree;

$treeHtml = '<div id="dataset" class="dataset">';

$treeHtml .= '<div class="buttonbar">
                    <a class="buttonlink expandall" href="javascript: void(0);">expand all</a> 
                    <a class="buttonlink collapseall" href="javascript: void(0);">collapse all</a>
             </div>
             <div class="clear"></div><div class="clear"></div><br />';


$treeHtml .= '<table class="adminlist" id="adminList" style="table-layout:fixed;" cellspacing="2px" cellpadding="5px" width="99%">
             <thead>
             <tr>
             <th width="3%">&nbsp;</th>
             <th width="20%">'. JText::_('Category Name').'</th>
             <th width="35%">'.JText::_('Preamble').'</th>'.
             '<th width="25%" align="center">'.JText::_('Data and Switches').'</th>'.
             '<th align="center">'.JText::_('Sorting Orders').'</th>'.
             '</tr>
             </thead>
             <tbody>';

     foreach( $dp_tree['level0'] as $key => $val ) {
     	
		$class = 'zero'.$key;
		 
		if( in_array($val, $treeLeaves) ) { continue; } //already printed, SKIP.

     	$treeHtml .= '<tr class="l0" title="'.$val.'" style="font-weight:bold;font-size:15px;">'
     	              . '<td><a class="tree plus" onClick="toggleview(\''.$class.'\',this);" href="javascript:void(0);" style="color: #000000">'
     	              . '&nbsp;' 
     	              . '</a></td>'
     	              . '<td>' . $this->columnTitles[ $val ] . '</td>'
     	              . '<td>&nbsp;</td>'
     	              . '<td>&nbsp;</td>'
                      . '<td>&nbsp;</td>'
					  . '</tr>'; //level0 title   

		$treeLeaves[] = $val; //push the outputted columns       
     			 
     	foreach( $dp_tree['level1'][$val] as $k => $v )
     	{
			   if( in_array($v, $treeLeaves) ) { continue; } //already printed, SKIP.

     		   $treeHtml .= '<tr title="'.$v.'" class="l1 level2col '.$class.' hidden">' 
     		                         . getRow($v,$this->preambles_master_list, $this->columnTitles, $gatewayColumns) 
									  . '</tr>'; //level1 title

			   $treeLeaves[] = $v; //push the outputted columns
     		   	  	        
			   $treeHtml .= getLevel2($dp_tree['level2'][$val][$v],$this->preambles_master_list, $this->columnTitles,$gatewayColumns,$class); //$l2Html; //level2 data						  
     	}
     	
     }
     
     $treeHtml .= '</tbody>
                  </table>';
     $treeHtml .= '</div>';
     $treeHtml .= '<br />';
     
     echo $treeHtml;
?>

<div class="clear"></div>
<div class="clear"></div>
<?php //include_once('submenus_endblock.php'); ?>
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
});

function toggleview( cls, obj ){
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
              var newLangURI = '<?php echo $this->currentURI;?>'+newLangURIPart;
             // similar behavior as an HTTP redirect
             window.location.replace(newLangURI);
           }
        }
    });
});

</script>

<!--
<script type="text/javascript">
window.addEvent('domready', function() {
	  $$('#dataset div.l0').addEvent('click', function(e) {
			if (!e) e = window.event;
			var object = $(e.target || e.srcElement); 
			var l2Obj = object.getParent().getNext('div.level1data');
            if( l2Obj == null ){
               return false;
            }
            if( object.tagName != 'A' ){
               return false;
            }
            
			if( l2Obj.getStyle('display') == 'none' ){
				 l2Obj.setStyle('display','');
			}else{
				 l2Obj.setStyle('display','none');
			}
	  });

	  //expand all
	  $$('a.expandall').addEvent('click', function(e) {
	      $$('#dataset div.level1data').setStyle('display','');
	      $$('#dataset a').removeClass('plus').addClass('minus');
	  });

	  //collapse all
	  $$('a.collapseall').addEvent('click', function(e) {
		  $$('#dataset div.level1data').setStyle('display','none');
		  $$('#dataset a').removeClass('minus').addClass('plus');
	  });  
});
</script>
-->