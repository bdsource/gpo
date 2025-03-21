<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet('/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');

?>

<script>
    jQuery.noConflict();
</script>

<style>
pre{
padding:1px;
margin:1px;
display:inline;
}
.adminlist td a{
display:inline;
}
.center{
text-align:center;
}
table { 
border-collapse: collapse;
}
table, th, td {
border: 1px solid lightgray;
}
</style>

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

<?php include_once('submenus_startblock.php'); ?>
<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" name="adminForm" id="adminForm">
    
<h3>
  Last 2000 Data-Page Updates/Writes through Excel File Import ...  
</h3>

<table class="adminlist" id="adminList">
	<thead>
	<tr>
		<th width="5%">ID</th>
		<th width="10%">Import Type</th>
                <th width="5%">Import Only Blank Years</th>
		<th width="5%">File Name</th>
		<th width="10%">Category Alias</th>
		<th width="15%">Options</th>
		<th width="5%">Total Updated Rows</th>
                <th width="10%">Affected Categories</th>
                <th width="10%">Affected Locations</th>
                <th width="10%">User</th>
		<th width="10%">Date</th>
                <th width="5%">Language</th>        
	</tr>
	</thead>
	
	<tbody>
<?php
$i = 0;
foreach( $this->items as $item ):
?>
		<tr class="<?php echo "row$k"; ?>" align="center">
			<td>
                            <a class="load_popup" id="id_<?php echo $item->id;?>" title="Show details" target="_blank"
                               href="<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails&id=$item->id",false);?>">
                               <?php echo $item->id;?>
                            </a>
                        </td>
			<td> 
                            <a class="load_popup" id="id_<?php echo $item->id;?>" title="Show details" target="_blank"
                               href="<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails&id=$item->id",false);?>">
                               <?php echo $item->import_type;?>
                            </a>
                        </td>
                        <td> 
                            <a class="load_popup" id="id_<?php echo $item->id;?>" title="Show details" target="_blank"
                               href="<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails&id=$item->id",false);?>">
                               <?php echo empty($item->import_blank_years) ? 'NO' : $item->import_blank_years;?>
                            </a>
                        </td>
			<td>
                            <a class="load_popup" id="id_<?php echo $item->id;?>" title="Show details" target="_blank"
                               href="<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails&id=$item->id",false);?>">
                               <?php echo $item->file_name;?>
                            </a>
                        </td>
			
			<td>
                            <a class="load_popup" id="id_<?php echo $item->id;?>" title="Show details history for: <?php echo $item->column_name;?>" 
                               target="_blank" href="<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails&id=$item->id",false);?>">
                               <?php echo !empty($this->columnTitles[$item->column_name]) ? $this->columnTitles[$item->column_name] : $item->column_name;?>
                            </a>
                        </td>
			<td> <?php echo $item->options;?> </td>
			<td> <?php echo $item->total_updated_rows;?> </td>
                        <td> <?php echo $item->affected_columns;?> </td>
                        <td> <?php echo $item->affected_locations;?> </td>
                        <td> <?php echo $item->username;?> </td>
                        <td> <?php echo $item->created_at;?> </td>
                        <td> <?php echo $item->language;?> </td>
                        <!-- <td> <?php echo $item->affected_locations;?> </td> -->
		</tr>
<?php
$i++;
$k = 1 -$k;
endforeach;
?>
        
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="<?php echo $this->task;?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="action" value="<?php echo $action;?>" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

</tbody>
<?php 
if( count($this->items) ==0 ):
    echo "<tr><td colspan='12'>Sorry, No update/write histories found...</td></tr>";
else:
?>
	<tfoot>
	<tr>
		<td colspan="12" style="padding-top:20px;">
<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
<?php endif;  ?>

</table>
    
</form>
<?php include_once('submenus_endblock.php'); ?>
<script type="text/javascript">

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


/* Ajax page history details */
/*
jQuery(document).ready(function() {
   jQuery(".load_popup").on("click",function(event){
        var helpId = jQuery(this).attr('id');
        var helpId = helpId.replace('id_','');
        var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=dataimport_getdetails",false ); ?>'+'&id='+helpId;
	    //var m = new Messi('<P>Loading Replace History...</P>', {title: 'DP RAW Update History', modal: true, titleClass: 'anim', width:'800px', height:'500px'});
        Messi.load(URL, {params: {idt: helpId},title: 'DP RAW Update History', modal: false, titleClass: 'anim', width:'950px', height:'550px'});
   });
});
*/
</script>
