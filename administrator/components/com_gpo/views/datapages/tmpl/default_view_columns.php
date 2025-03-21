<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator/",'',JURI::base());
$dp_hierarchy = getDPHierarchy(3);
$dp_tree = processDPHierarchy($dp_hierarchy);

$columnTitles = getDPColumnTitles();
$gatewayColumns = getDPHierarchyGatewayColumns();
$columnDisplayTypes = getDPColumnDisplayTypes();
$columnGCiteIds = getDPGCiteIds();
$columnIDs = array();

$display_types = array(''               => 'Not Set',
                       'bar_chart'      => 'Bar Chart',
                       'switch_table'   => 'Switch Table (Sort on Name)',
                       'switch_table_switch_sort' => 'Switch Table (Sort on Switch)',
                       'rank_table'     => 'Rank Table',
                       'no_comparison'  => 'No Comparison Display'
                 );

function getLevel2( $l2Array = array(), $colRowsArray = array(), $columnTitles = array(), $class='' ) 
{
   	   $dataHtml = '';
   	   foreach( $l2Array as $v )
   	   {
   	        $dataHtml .= '<tr title="'.$v.'" class="l2 '.$class.' hidden">'
   	                  .  '<td>&nbsp;</td>'          
                      .  $colRowsArray[ $v ] 
                      .  '</tr>'; 
            $dataHtml .= "\n";
   	     
   	   }
   	   return $dataHtml;
}
?>

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
}

.minus {
	background-image:
		url(<?php echo $front_end;?>templates/gunpolicy/images/coll.gif);
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
.gateway {
   color:red;
   font-weight: bold;
}
td.gateway span {
    font-size: 1.091em;
    margin: 5px 0;
    float: left;
}
.level2col {
   color:green;
   font-weight: bold;
}
tr.l0 label{
   font-weight:bold;
   font-size:15px;
}
td.tlo {
   height: 40px;
}
a.del {
   display:block;
   width:16px;
   height:16px;
   background-image: url(<?php echo $front_end;?>templates/gunpolicy/images/delete.png);
}
.editable{
   width: 290px !important;
   float: left;
   margin: 5px 5px 5px 0;
   cursor: auto;
   letter-spacing: normal;
   word-spacing: normal;
   text-transform: none;
   text-indent: 0px;
   text-shadow: none;
   display: inline-block;
   padding: 1px;
}
.editable textarea {
   width: 290px !important;
   height: 30px !important;
}
#adminList select, input, textarea {
   font-size: 10px !important;
}
#adminList td, tr, label, span {
    word-wrap: break-word;
}

/* color schemes */
.l0 {
    color: #000;
    font-weight: bold;
}
.l1 {
    color: green;
}
.l2 {
    color: blue;
}
.linkCat {
   color:#8e44ad;
}
.help_popup img{
    margin: 0px;
    vertical-align: middle;
    padding-bottom:6px;
}

input{
    width:auto;
}
</style>

<?php
$document = &JFactory::getDocument();
$document->addScript( '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript( $front_end . 'media/system/js/jeditable/jquery.jeditable.js');

$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet(JURI::root(true).'/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');

$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
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
<legend><span style="font-size:14px;"> <?php echo JText::_('View Categories:');?></span></legend>

<p>
<span style="color:#000;font-weight:bold">☞ &nbsp;Top Header = bold black </span>&nbsp;&nbsp;
<span style="color:red;font-weight:bold">☞ &nbsp; Gateway = red </span>&nbsp;&nbsp;
<span style="color:#8e44ad;font-weight:bold">☞ &nbsp; Link Category = purple</span>&nbsp;&nbsp;
<span style="color:green;font-weight:bold">☞ &nbsp; Level 2 = bold green</span>&nbsp;&nbsp;
<span style="color:blue;font-weight:bold">☞ &nbsp; Level 3 = regular blue</span>
<br>
☞ &nbsp;Click on any Category Title to edit it, then be sure to click ‘OK’ to update the value in Database.
<br>
☞ &nbsp;To view the Category Alias, hover over any white space in the first two columns
<br>
☞ &nbsp;Caution: Any change made here will propagate to every Location, 
and must not affect the comparability of category data.
<?php
if( TRUE === canDeleteColumn() ):
    echo 'Only a SuperAdmin can delete a Category across all Locations.';
endif;
?>
</p>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=view_columns'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

<?php
echo  '<div id="dataset" class="dataset">';

echo  '<div class="buttonbar">
           <a class="buttonlink expandall" href="javascript: void(0);">expand all</a> 
           <a class="buttonlink collapseall" href="javascript: void(0);">collapse all</a>
      </div>
      <div class="clear"></div> <div class="clear"></div><br />';

 
     $columns_info = $this->columns_info;
     echo '<table class="adminlist" id="adminList" cellspacing="0px" cellpadding="2px" width="100%">
          <thead>
               <tr>
                  <th width="2%">&nbsp;</th>
                  <th width="3%">' . JText::_('ID').'</th>
                  <th width="35%">'. JText::_('Category Title').'</th>
                  <th width="35%">'. JText::_('Category Alias').'</th>
                  <th width="15%">'. JText::_('Display Type').'</th>
                  <th width="5%">' . JText::_('Parent ID').'</th>
                  <th width="5%">' . JText::_('Sort Order').'</th>';
     echo '</tr>
           </thead>
           <tbody>
           ';
       
     foreach( $columns_info as $key=>$val ) {

        $columnIDs[$key] = $val->id;
        $parentId = is_null($val->parent_id) ? 'NULL' : $val->parent_id;
        $parentIdAttr =  is_null($val->parent_id) ? ' readonly ' : '';
        
        $columnTitle = in_arrayi($this->currentLanguage, $this->languages) ? 'column_title_'.$this->currentLanguage
                       : 'column_title';
        $columnTitle = empty($val->{$columnTitle}) ? camelize($key) : $val->{$columnTitle};
        $href = JRoute::_('index.php?option=com_gpo&controller=datapages&task=edit_columns&delete='.$key);
     	if(in_array($key,$gatewayColumns)) {
             $colRow = '<td>'.$val->id.'</td>
                        <td class="gateway">
                        <label class="edit_area" id="'.$key.'">'. $columnTitle . ' &nbsp(Gateway: no data)</label>
                        <span></span>
                        </td>
                        <td>'.$key.'</td>';
                        
        }
        else if(isALinkCategory($key)) {
             $colRow = '<td>'.$val->id.'</td> 
                        <td class="linkCat">
                        <label class="edit_area" id="'.$key.'">'. $columnTitle . '
                        </label>
                        </td>
                        <td>'.$key.'</td>';
        }
        else {
            $colRow = '<td>'.$val->id.'</td>
                       <td>
                       <label class="edit_area" id="'.$key.'">'. $columnTitle . '</label>
                       </td>
                       <td>'.$key.'</td>';
                       
        }
        //show the display type if it is not a gateway column
        if(in_array($key,$gatewayColumns) || isALinkCategory($key)) {
             //hidden field is kept for maintaining the key after post.
             $colRow .= '<td>N/A <input type="hidden" name="display_type[]" value="" /> </td>
                         <td>
                             <input readonly="readonly" type="text" name="parent_id[]" value="'.$parentId.'" '. $parentIdAttr . ' size="5">
                             <a title="Click here for Help. The category alias is '.$key.'" 
                                id="popup_parent_id" class="help_popup" href="javascript:;">
                                <img src="'.$front_end.'templates/gunpolicy/images/help_icon.gif">
                             </a>
                         </td>
                         <td>
                             <input readonly="readonly" type="text" name="sort_order[]" value="'.$val->sort_order.'" size="5">
                             <a title="Click here for Help. The category alias is '.$key.'" 
                                id="popup_sort_order" class="help_popup" href="javascript:;">
                                <img src="'.$front_end.'templates/gunpolicy/images/help_icon.gif">
                             </a>
                         </td>';
        } else {
             
             $colRow .= '<td><select name="display_type[]" style="width:85%">';
             foreach($display_types as $type_key=>$type_value) {
                    if($columnDisplayTypes[$key]==$type_key){
                        $colRow .= '<option value="'.$type_key.'" selected="selected">'.$type_value.'</option>';
                                   
                    } else {
                        $colRow .= '<option disabled="disabled" value="'.$type_key.'">'.$type_value.'</option>';
                    }
             }
             $colRow .= '</select>
                         <a title="Click here for Help. The category alias is '.$key.'" 
                            id="popup_display_type" class="help_popup" href="javascript:;">
                            <img src="'.$front_end.'templates/gunpolicy/images/help_icon.gif">
                         </a>
                         </td>';

             //add Parent Id
             $colRow .= '<td>
                         <input readonly="readonly" type="text" name="parent_id[]" size="5" value="'.$parentId.'" '. $parentIdAttr . ' />
                         <a title="Click here for Help. The category alias is '.$key.'"  
                            id="popup_parent_id" class="help_popup" href="javascript:;">
                            <img src="'.$front_end.'templates/gunpolicy/images/help_icon.gif">
                         </a>
                         </td>';

             //add Sort Order
             $colRow .= '<td>
                         <input readonly="readonly" type="text" name="sort_order[]" size="5" value="'.$val->sort_order.'" />
                         <a title="Click here for Help. The category alias is '.$key.'"  
                            id="popup_sort_order" class="help_popup" href="javascript:;">
                            <img src="'.$front_end.'templates/gunpolicy/images/help_icon.gif">
                         </a>
                         </td>';
             
         }

       
        $colRowsArray[$key] = $colRow;
     }
     
     foreach( $dp_tree['level0'] as $key => $val ) {

        $class = 'zero'.$key;
     	$treeHtml .= '<tr title="'.$val.'" class="l0">'
     	              . '<td class="tl0" title="'.$val.'">'
                      . '<a class="tree buttonlink plus" onClick="toggleview(\''.$class.'\',this);" href="javascript:void(0);" style="color: #000000">'
     	              . "&nbsp;" 
     	              . '</a></td>'
     	              . $colRowsArray[ $val ]
     	              . '</tr>'; //level0 title
     	               	
         
     	foreach( $dp_tree['level1'][$val] as $k => $v ) {
     		   	  	
     		            $treeHtml .= '<tr title="'.$v.'" class="l1 level2col '.$class.' hidden">'
     		                         . '<td title="'.$v.'">&nbsp;</td>'
     		                         . $colRowsArray[ $v ] 
     		                         . '</tr>'; //level1 title
     		   	  	    
     		   	  	    //level2data
						$treeHtml .= getLevel2($dp_tree['level2'][$val][$v], $colRowsArray, $columnTitles, $class); //level2 data
     	}
     	
     }
     
     echo $treeHtml;
     
     echo '</tbody></table>';

     echo'</div>';
     echo '<br />';

?>

<div class="clear"></div>
</form>
</fieldset>


<!-- Help POPUP Contents --> 
<div id="help_display_type" style="display:none;">
    <p>
       <h1> Display Type </h1>
       Choose the type of chart or table to be displayed for this Category
    </p>
</div>

<div id="help_region_aggregation" style="display:none;">
    <p>
       <h1>Region Aggregation </h1>
       When values from a Region or Group are totalled for this Category, choose Summation (the sum of all the values); Average (the sum of values divided by the number of Locations in the Group or Region); or Off (Switch tables; Rank tables; No Comparison Display).
    </p>
</div>

<div id="help_vertical_chart_label" style="display:none;">
    <p>
       <h1>Y Axis Chart Label </h1>
       Choose the label to be displayed on the left hand vertical axis (Bar Charts only). Select None for No Comparison Display or for Switch and Rank tables.
    </p>
</div>

<div id="help_sort_order" style="display:none;">
    <p>
       <h1>Sort Order</h1>
       Select the order in which each Category should be displayed on the DP, from top to bottom.
    </p>
</div>

<div id="help_parent_id" style="display:none;">
    <p>
       <h1>Parent Id</h1>
       Like sort order, parent id is also very important in maintaining column's hierarchy. Parent Id decides which columns sit under which category. 
       So, be extra cautious and make sure about doing any changes in this field. 
    </p>
    
    <p>
       Select the Parent Id in which this category should be grouped under. <br />
       If you want to move one category from a top level category to another top level category, 
       then all you need to do is to find the ID of the top-level category and give this ID into the parent_id column
       of the category that you want to be moved. 
    </p>
    
    <p>
        Note: Top Level categories don't have a parent. That's why their parent Id is kept as "NULL". NULL as a parent Id designates 
        that they are TOP Level Categories.
        Changing parent id for Top level categories are kept as read-only, so that no one accidentally updates the parent Id and lost or 
        moved a TOP level category entirely. If you need to edit the parent id of a top level category, you need to ask the developer to do 
        that. 
    </p>
</div>

<div id="help_gcite_id" style="display:none;">
    <p>
       <h1>GCite ID</h1>
       Create a Glossary citation for each sub-Category, then enter its ID number here. The descriptive gCite pop-up will display when its (i) Information icon is clicked on any DP. For top-level Categories, enter zero.
    </p>
</div>


<script type="text/javascript">
jQuery = $.noConflict();

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
</script>

<script type="text/javascript">
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

/* inline edit */
jQuery(document).ready(function() {
     var imgDIR  = '<?php echo $front_end;?>'+'media/system/js/jeditable/';
     var postURL = '<?php echo JRoute::_('index.php?option=com_gpo&controller=datapages&task=editColumnTitle&lang='.$this->currentLanguage,false);?>';
     
     jQuery(".help_popup").on("click",function(event){
        var helpId = jQuery(this).attr('id');
        var helpId = helpId.replace('popup_','');
        var helpContentId = helpId.replace('_',' ').toUpperCase();
        var helpHtml = jQuery('#'+'help_'+helpId).html();
	    new Messi(helpHtml, {title: 'Help on '+helpContentId, modal: true, titleClass: 'anim'});
     });
 });
</script>
