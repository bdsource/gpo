<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset($document->_scripts[$mootools]) )
{
	unset($document->_scripts[$mootools]);
}
?>

<script>
    jQuery.noConflict();
</script>

<style>
.error_warning{color:#ff0000;}
#message_box a{display:block;}
#adminForm{
	padding:10px;
}
/*#adminForm p label{display:block;padding-right:10px;}*/
.input_field{width:370px;}

.clear{float:none;}


.row{
	clear:both;
	display:block;
	padding:0px;
	margin:0px auto;
}
.cell{
	float:left;
	padding:0px;
	margin:0px auto;
}
.cell label{
	padding:0px;
	margin:0px auto;
	display:inline;
}

.published{
	width:100px;
}

.input120{
	width:120px;
}
.clear{
	clear:both;
}

#quotes_txt_locations a{padding-left:5px;}
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:8px;}
.location_txt{font-size:larger;}

#quotes_published{text-align:center;}

#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}

.location_menu a{
padding-right:5px;
text-decoration:underline;
}

.no-dp{
color: #FF0000;
font-weight: bold;
}
</style>

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


<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>
<table>
    <tr>
        <td width="100%">
                <?php echo JText::_( 'Find Location' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo $this->filter_key;?>" class="text_area" onchange="document.adminForm.submit();" />
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
        </td>
        <td nowrap="nowrap">
                <?php
                echo $lists['sectionid'];
                echo $lists['catid'];
                ?>
        </td>
    </tr>
</table>
<!-- <h3> Click any of the locations below to see its Data Page values: </h3> -->
<h3>Data Page Values and Switches</h3>
<p>(Only Preambles substitute Location for # and Value for ~)</p>

<div class="table-responsive">
<table class="adminList table-striped table-hover" width="100%" id="adminList">
	<thead>
	<tr>
		
		<th valign="top" align="center" width="15%">
            <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('en');?>" />
            <?php echo JHTML::_('grid.sort', JText::_('Location Name'), 'name', $this->filter_order_Dir ,$this->filter_order);?>
        </th>
		<th valign="top" align="center" width="15%">
            <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag($this->currentLanguage);?>" />
            <?php echo JText::_('Location Name (').getLanguageName($this->currentLanguage).')';?>
        </th>
        <th valign="top" align="center" width="10%">Edit Category Data</th>
        <th valign="top" align="center" width="10%">Edit Category Preamble</th>
        <th valign="top" align="center" width="10%">Edit DP Data</th>
		<th valign="top" align="center" width="10%">Edit DP Preamble</th>
		<th valign="top" align="left"   width="5%"><?php echo JHTML::_('grid.sort', JText::_('Published'), 'published', $this->filter_order_Dir ,$this->filter_order);?></th>
        <th valign="top" align="left"   width="10%"> <?php echo JHTML::_('grid.sort', JText::_('Created'), 'published_at', $this->filter_order_Dir ,$this->filter_order);?></th>
        <th valign="top" align="left"   width="10%"><?php echo JHTML::_('grid.sort', JText::_('Updated'), 'updated_at', $this->filter_order_Dir ,$this->filter_order);?></th>
		<th valign="top" align="center" width="5%"><?php echo JHTML::_('grid.sort', JText::_('Location ID'), 'location_id', $this->filter_order_Dir ,$this->filter_order);?></th>
	</tr>
	</thead>
	<tbody>
		
<?php
$db = JFactory::getDBO();
$k  = 0;
$i  = 0;
foreach( $this->allDPWithLocations as $location ):
	//$id = JHTML::_('grid.id', ++$i, $location->id);
        //$published  = JHTML::_('grid.published', $location, $i);

        // prepare link for id column
	$edit_data_link     =  JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=view_dp&id='.$location->id.'&location=' ).urlencode($location->name).'&lang='.$this->currentLanguage;
        $edit_category_link =  JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=edit_category_data&id='.$location->id.'&location=' ).urlencode($location->name).'&lang='.$this->currentLanguage;
        $edit_category_preamble_link =  JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=edit_category_preambles&id='.$location->id.'&location=').urlencode($location->name).'&lang='.$this->currentLanguage;
    
        $rowId = $location->id;
        $edit_preamble_link = JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=view_preambles&id='.$location->id.'&location='.$location->name ).'&lang='.$this->currentLanguage;
        $rowId = $location->id;
	$isPublished = $location->published;
	$img = (1 == $isPublished) ? 'publish_g.png' : 'publish_x.png'; 
	$alt = (1 == $isPublished) ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
	$times = '';
	
	$nullDate = $db->getNullDate();
	if( ($location->published_at != $nullDate) && $isPublished ){
	   $times .= 'published: ' . $location->published_at . '<br />';
	}
	$times = 'Created: ' . $location->created_at;
	if( $location->updated_at != $nullDate ){
	   $times .= '<br />Updated: ' . $location->updated_at;	
	}
	$dp_p_title = JText::_( 'Publish Information' ) . '::' . $times;
	$dp_edit_title = 'Click to edit '. $location->name . ' DP';
	
	$dp_id = $location->dp_id;
	$location_id = $location->id;
	$class = '';
	if(empty($location->location_id) && empty($dp_id) ){
	   $class = 'no-dp';
	   $dp_p_title = JText::_( 'Missing DP' ) . "::" . 'This DP is not exist yet. Create it first.';
	   $dp_edit_title = JText::_( 'Missing DP' ) . "::" . 'This DP may not exist yet. Click here to create it.';
	}
	
	//if( 'Marcustan' == $location->name ){ continue; } //don't print the test location marcustan
?>
        <tr class="<?php echo "row$k"; ?> <?php echo $class; ?>">
                <td valign="top" align="left"> 
                    <span class="<?php echo $class; ?>"><?php echo $location->name; ?></span>
                </td>
                <td valign="top" align="left"> 
                    <span class="<?php echo $class; ?>"><?php echo $location->{'name_'.$this->currentLanguage};?></span>
                </td>
                <td valign="top" align="center">
                    <a href="<?php echo $edit_category_link; ?>" title="<?php echo $dp_edit_title; ?>">
                        <span class="<?php echo $class; ?>">Edit Category Data</span>
                    </a>
                </td>
                <td valign="top" align="center">
                    <a href="<?php echo $edit_category_preamble_link; ?>" title="<?php echo $dp_edit_title; ?>">
                        <span class="<?php echo $class; ?>">Edit Category Preamble</span>
                    </a>
                </td>
                <td valign="top" align="center">
                    <a href="<?php echo $edit_data_link; ?>" title="<?php echo $dp_edit_title; ?>">
                        <span class="<?php echo $class; ?>">Edit DP Data</span>
                    </a>
                </td>
                <td valign="top" align="center">
                    <a href="<?php echo $edit_preamble_link; ?>" title="<?php echo $dp_edit_title; ?>">
                        <span class="<?php echo $class; ?>">Edit DP Preamble</span>
                    </a>
                </td>
                
                <td valign="top" align="center">
                    <span class="editlinktip hasTip" title="<?php echo $dp_p_title; ?>">       
                        <a href="javascript:void(0);" 
                           onclick="return customListItemTask('<?php echo $dp_id; ?>',
                         '<?php echo $isPublished ? 'unpublish' : 'publish' ?>',
                         '<?php echo $location_id; ?>')">
                            <img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
                        </a>
                    </span>
                </td>
                <td class="replace" valign="top" align="center">
                    <?php
                    echo ($nullDate == $location->published_at || empty($location->published_at) ) ? $location->published_at : date("j M Y, H:i:s", strtotime($location->published_at));
                    ?>
                </td>
                <td class="replace" valign="top" align="center">
                    <?php
                    echo ($nullDate == $location->updated_at || empty($location->updated_at) ) ? $location->updated_at : date("j M Y, H:i:s", strtotime($location->updated_at));
                    ?>
                </td>
                    
                <td valign="top" align="center"><?php echo $location->id; ?></td>
      </tr>
<?php
$i++;
$k = 1 -$k;
endforeach;
?>
</tbody>
</table>
</div>

<?php 
/*
## old location list view commented out
* 

<ul id="original_order">
<?php
//add the test location Marcustan* for the time being
echo '<li>';
echo '<span class="location_name">';

$link =  JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=view_dp&id=345&location=Marcustan*');
echo '<a id="0" href="'.$link.'">' . 'Marcustan*' . '</a>';

echo '</span>';
echo '<span class="location_menu"></span>';
echo '</li>';
?>

<?php foreach( $this->countries_on_record as $location ): ?>

<?php
echo '<li>';
echo '<span class="location_name">';
if(!empty( $location['name'] ) ){
   $link =  JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=view_dp&id='.$location['id'].'&location='.$location['name']);
   echo	'<a id="'.$location['id'] .'" href="'.$link.'">' . $location['name'] . '</a>';
}
echo '</span>';
echo '<span class="location_menu"></span>';
echo '</li>';
?>
<?php endforeach; ?>
</ul>

## old location list view commented out finished ###
*/
?>

<input type="hidden" id="option" name="option" value="com_gpo" />
<input type="hidden" id="controller" name="controller" value="datapages" />
<input type="hidden" id="task" name="task"  />
<input type="hidden" id="dp_id" name="dp_id" />
<input type="hidden" id="location_id" name="location_id"  />
<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value=""/>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
function customListItemTask( id, task, location_id ) {
    var f = document.adminForm;
    if (id) {
    	f.dp_id.value=id;
    	if (location_id) {
    	   f.location_id.value=location_id;
    	}
    	f.boxchecked.value = 1;
        submitbutton(task);
    }
    return false;
}


/*
 * For language Switching 
 * 
 */
var currentLang = "<?php echo $this->currentLanguage;?>";
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
