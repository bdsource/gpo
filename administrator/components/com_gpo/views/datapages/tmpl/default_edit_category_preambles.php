<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
$columnTitles = getDPColumnTitles();
$dp_hierarchy = getDPHierarchy(3);
$dp_tree = processDPHierarchy($dp_hierarchy);
$gatewayColumns = getDPHierarchyGatewayColumns();

global $treeLeaves;
$treeLeaves = array();

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root() . 'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base() . 'templates/system/css/language.css');
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

.container_category{width:95%;padding:5px;}
.left_col{float:left;width:13%;}
.right_col{float:right;width:85%;}
.footer_col{text-align:center;clear:both;}
.left_col .language_label {
    height:20px;
    padding-top:13px !important;
    font-size: 13px !important;
}
.language_label input {
    margin: 0px 0px !important;
    font-size: 13px !important;
}
.right_col .data {padding-bottom:5px;}
.footer_col .dp_button {margin-right:20px;float:none;}
.hovercell:hover {
   background-color: #f39c12;
}​
.green {
    color: green;
    font-weight: bold;
}
.error {
    color: red;
    font-weight: bold;
}

.dp_button {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf) );
	background:-moz-linear-gradient( center top, #ededed 5%, #dfdfdf 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf');
	background-color:#ededed;
	-webkit-border-top-left-radius:0px;
	-moz-border-radius-topleft:0px;
	border-top-left-radius:0px;
	-webkit-border-top-right-radius:0px;
	-moz-border-radius-topright:0px;
	border-top-right-radius:0px;
	-webkit-border-bottom-right-radius:0px;
	-moz-border-radius-bottomright:0px;
	border-bottom-right-radius:0px;
	-webkit-border-bottom-left-radius:0px;
	-moz-border-radius-bottomleft:0px;
	border-bottom-left-radius:0px;
	text-indent:0;
	border:1px solid #dcdcdc;
	display:inline-block;
	color:#777777;
	font-family:Arial Black;
	font-size:11px;
	font-weight:normal;
	font-style:normal;
	height:40px;
	line-height:35px;
	width:87px;
	text-decoration:none;
	text-align:center !important;
	text-shadow:1px 1px 0px #ffffff;
}
.dp_button:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed) );
	background:-moz-linear-gradient( center top, #dfdfdf 5%, #ededed 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed');
	background-color:#dfdfdf;
}.dp_button:active {
	position:relative;
	top:1px;
}
.textarea {
    font-size: 13px;
}
.preview img {
    width: 16px;
    height: 16px;
}
#adminList {
    line-height: 45px !important;
}
</style>
<script type="text/javascript">
    jQuery.noConflict();
</script>


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
<!-- Language Switching panel done -->

<?php
### show warning message for non-existent DPs ###
if( empty($this->dp_data->id) || empty($this->dp_data->location_id) ):
?>
<div class="error message fade" style="color: #FF0000; font-weight: bold; text-align:left;">
<img style="padding-right:20px;padding-top:10px;" src="<?php echo JURI::base();?>templates/system/images/notice-alert.png" alt="Warning! Missing DP" align="left">
<p>
WARNING! The ‘[<?php echo $this->location->name;?>]’ DP has not been created. 
When ready, click once on the Save or Apply button to generate the DP, at the same time propagating its Preambles from the master table.
</p>
</div>
<?php
endif;

$passed_location_id = Joomla\CMS\Factory::getApplication()->getInput()->get('id', false);
?>

<?php
function getLevel2( $l2Array = array(), $colRowsArray = array(), $columnTitles = array(), $class='' ) {  
        $dataHtml = '';
        global $treeLeaves;

   	    foreach( $l2Array as $v )
   	    {
   	   	    $columnTitle = getColTitle( $v, $columnTitles );
   	   	   
   	        $dataHtml .= '<tr title="'.$v.'" class="l2 '.$class.' hidden">'
   	                  .  '<td width="10">&nbsp;</td>'
   	                  .  '<td width="370"><label for="'.$v.'">'. $columnTitle . '</label></td>'          
                      .  $colRowsArray[ $v ] 
                      .  '</tr>';
            $dataHtml .= "\n";

            $treeLeaves[] = $v; //push the outputted columns

        }
           
   	    return $dataHtml;
}

function getTopLevelColRow( $column_name, $columnTitles = array() ) {
	 $columnTitle = !empty($columnTitles[$column_name]) ? $columnTitles[$column_name] : camelize($column_name);
	 $colRowTL = '<td width="200"><label for="'.$column_name.'">'. $columnTitle . '</label></td>
	              <td width="310">&nbsp;</td>';
	 return $colRowTL;
}

function getColTitle( $val, $columnTitles = array() ) {
     //$nVal = (strpos($val,'_p') !== false) ?  substr($val,0,-2) : $val;
     $nVal = $val;
     $columnTitle = !empty($columnTitles[$nVal]) ? $columnTitles[$nVal] : camelize($nVal);
     return $columnTitle;
}

function getEditCategoryHTML($colKey, $previewDPURL, $colVal, $colValEs, $colValFr) {
 
    $catHTML = '
        <div class="container_category hovercell" id="cat_'.$colKey.'">
        <form method="post" id="form_'.$colKey.'" name="form_'.$colKey.'">
        <div class="left_col">
            <p class="language_label">
            <input name="language_en" type="checkbox" value="en" checked />
            &nbsp;English
            </p>
          
            <p class="language_label">
            <input name="language_es" type="checkbox" value="es" checked />
            &nbsp;Español
            </p>
         
            <p class="language_label">
            <input name="language_fr" type="checkbox" value="fr" checked />
            &nbsp;Français
            </p>
        </div>
        
        <div class="right_col">
            <p class="data">
            <textarea name="data_en_'.$colKey.'" id="data_en_'.$colKey.'" style="width:95%;" class="textarea" rows="2">'.htmlspecialchars($colVal).'</textarea>
            </p>
         
            <p class="data">
            <textarea name="data_es_'.$colKey.'" id="data_es_'.$colKey.'" style="width:95%;" class="textarea" rows="2">'.htmlspecialchars($colValEs).'</textarea>
            </p>
         
            <p class="data">
            <textarea name="data_fr_'.$colKey.'" id="data_fr_'.$colKey.'" style="width:95%;" class="textarea" rows="2">'.htmlspecialchars($colValFr).'</textarea>
            </p>
        </div>
    
        <div class="footer_col" align="center">
            <span class="updateResult"></span> <br />
            <input class="dp_button save" name="save" type="button" value="Save" title="Save for selected languages" />
            <input class="dp_button save_all" name="save_all" type="button" value="Save All" title="Save for all languages" />
            <a title="Reload the page" class="preview" href="javascript:location.reload();" target="_blank">
               <img alt="Reload this page" src="'.$front_end.'/templates/gunpolicy/images/reload_page.png'.'">
            </a>
            &nbsp; &nbsp;
            <a title="Preview this category data in a new window" class="preview" href="'.$previewDPURL.'" target="_blank">
               <img alt="Preview data in a new window" src="/templates/gunpolicy/images/preview_new_window.gif">
            </a>
            
        </div>
        </form>
        </div>
        ';
    
    return $catHTML;
}
?>

<fieldset>
<legend>Data Page Preambles for '<?php echo $this->display_location;?>'</legend>
<p>In Preambles, substitute # for Location, and ~ for Value. 
Rows in <strong>bold</strong> are Level 2, rows in regular type are Level 3</p>

<!-- <form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo', false); ?>" 
id="adminForm" name="adminForm"> -->

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />


<?php
echo  '<div id="dataset" class="dataset">';

echo  '<div class="buttonbar">
                    <a class="buttonlink expandall" href="javascript: void(0);">Expand all</a> 
                    <a class="buttonlink collapseall" href="javascript: void(0);">Collapse all</a>
             </div>
             <div class="clear"></div> <div class="clear"></div><br />';

 
     echo '<table class="adminlist" id="adminList" width="1270" style="table-layout:fixed;">
          <thead><tr><th width="10">&nbsp;</th><th width="140">'. JText::_('Category Title').'</th><th width="370">'.JText::_('Preamble Text').'</th></tr></thead><tbody>';
 
     foreach( $this->dp_metadata as $key=>$val )
     {
       if($val == 'location'){
       	  $this->dp_data->{$val} = $this->location->name; 
       }
       
       $nVal = (substr($val,-2) == '_p') ?  substr($val,0,-2) : $val; //strip out preambles suffix(_p) if exists
       
       $colRow = '<td>';
       
       if( in_array($nVal, $gatewayColumns) ){
          $colRow .= '<span class="gateway">(Gateway: no data)</span>'; 
       }else{
       	  //$colRow .= '<textarea style="width:98%;" name="'.$val.'" id="'.$val.'" cols="" rows="1" class="textarea" ';
          //if( is_readonly($nVal) || in_array($nVal, $gatewayColumns) ){ $colRow .= 'readonly'; }
          //$colRow .= ' >'. $this->dp_data->{$val} . '</textarea>';
          $colRow .= getEditCategoryHTML($val, $this->previewDPURL.'#'.$val, $this->dp_data->{$val}, 
                                         $this->dp_data_es->{$val}, $this->dp_data_fr->{$val}
                                        );
       }
       $colRow .= '</td>';
       
       $colRowsArray[$nVal] = $colRow;       
     }

     
     foreach( $dp_tree['level0'] as $key => $val ) {
     	
        $class = 'zero'.$key;
         
        if( in_array($val, $treeLeaves) ) { continue; } //already printed, SKIP.
     	
     	$treeHtml .= '<tr title="'.$val.'" class="l0" style="font-weight:bold;font-size:15px;">'
     	              . '<td width="10"><a class="tree plus" onClick="toggleview(\''.$class.'\',this);" href="javascript:void(0);" style="color: #000000">'
     	              . '&nbsp;' 
     	              . '</a></td>'
     	              . getTopLevelColRow( $val, $columnTitles ) 
                      . '</tr>'; //level0 title    

        $treeLeaves[] = $val; //push the outputted columns        	
         
     	foreach( $dp_tree['level1'][$val] as $k => $v ) {
     		
                        $columnTitle = getColTitle( $v, $columnTitles );
                        if( in_array($v, $treeLeaves) ) { continue; } //already printed, SKIP.
     		            
                        $treeHtml .= '<tr title="'.$v.'" class="l1 '.$class.' hidden" >'
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
     	    $columnTitle = getColTitle( $val, $columnTitles );
     	    if( 'updated_at' == $val ){
     		   $columnTitle = 'Page Last Updated';
     		     echo '<tr style="font-weight:bold;font-size:10px;">'.
     		     '<td width="10">&nbsp;</td>'.
     		     '<td width="370" class="level2col"><label for="'.$val.'">'. $columnTitle . '</label></td>'.
     		     '<td>'.
     		     '<input style="width:260px;" name="'.$val.'" id="'.$val.'" value="'.$this->dp_data->{$val}.'">'.
     		     '</td>'.
     		     '</tr>';
     		}else{
     		    echo '<tr class="metacol" style="font-weight:bold;font-size:10px;">'.
     		     '<td width="10">&nbsp;</td>'.
     		     '<td width="370" class="level2col"><label for="'.$val.'">'. $columnTitle . '</label></td>'.
     		     $colRowsArray[ $val ].
     		     '</tr>';
     		}     
     	}
     }
     
     echo '</tbody></table>';
     echo '<input type="hidden" name="update_time" id="update_time" value="0">';

     echo'</div>';
     echo '<br />';

?>
<div class="clear"></div>


<?php 
if( $passed_location_id ):
   echo '<input type="hidden" name="passed_location_id" id="passed_location_id" value="'.$passed_location_id.'">';
   echo '<input type="hidden" name="create_dp_status" id="create_dp_status" value="1">';
endif;
?>

<!-- </form> -->

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
              var newLangURI = "<?php echo $this->currentURI;?>"+newLangURIPart;
              // similar behavior as an HTTP redirect
              window.location.replace(newLangURI);
           }
        }
    });
});

jQuery(document).ready(function() {
     var imgDIR  = '<?php echo $front_end;?>'+'media/system/js/jeditable/';
     var postURL = '<?php echo JRoute::_('index.php?option=com_gpo&controller=datapages&task=updateColumnPreamble&lang='.$this->currentLanguage,false);?>';     
     var indicator = '<img src="'+imgDIR+'img/indicator.gif" align="right">';
     
     jQuery('.save').click(function() {
         var formObj    = jQuery(this).closest("form");
         var data       = formObj.serialize();
         var locationId = jQuery('#passed_location_id').val();
         
         var formId        = jQuery(formObj).attr('id');
         var columnKeyName = formId.replace("form_","");
         var formData      = jQuery(formObj).serialize() + "&locationId=" + locationId + "&columnName=" + columnKeyName;
         
         var updateResult = jQuery(this).parent().children('.updateResult');
         jQuery(updateResult).html(indicator);
         
         jQuery.post( postURL, formData, function( data ) {
            var result = data.split('ERROR||');
            if(result[0] == '') {
               jQuery(updateResult).removeClass('green').addClass('error');
               jQuery(updateResult).html(result[1]).show('slow');
               jQuery(this).removeAttr('disabled');
            }else {
               jQuery(updateResult).removeClass('error').addClass('green');
               jQuery(updateResult).html(result[0]).show('slow');
               jQuery(this).removeAttr('disabled');
            }
        });
         
     });
     
     jQuery('.save_all').click(function() {
         var formObj    = jQuery(this).closest("form");
         var data       = formObj.serialize();
         var locationId = jQuery('#passed_location_id').val();
         
         var formId        = jQuery(formObj).attr('id');
         var columnKeyName = formId.replace("form_","");
         var formData      = jQuery(formObj).serialize() + "&locationId=" + locationId + "&columnName=" + columnKeyName + '&saveAll=1';
         
         var updateResult = jQuery(this).parent().children('.updateResult');
         jQuery(updateResult).html(indicator);
         
         jQuery.post( postURL, formData, function( data ) {
            var result = data.split('ERROR||');
            if(result[0] == '') {
               jQuery(updateResult).removeClass('green').addClass('error');
               jQuery(updateResult).html(result[1]).show('slow');
               jQuery(this).removeAttr('disabled');
            }else {
               jQuery(updateResult).removeClass('error').addClass('green');
               jQuery(updateResult).html(result[0]).show('slow');
               jQuery(this).removeAttr('disabled');
            }
        });
         
     });

 });
</script>