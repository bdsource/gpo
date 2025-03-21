<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

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
.highlight {
    color: red;
}
</style>

<br />

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

<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" name="adminForm" id="adminForm">
    
<h3>
  Replace history for ID: <?php echo $this->id;?> / Total Rows Updated: <?php echo count($this->items);?>
</h3>

<table class="adminlist" id="adminList">
	<thead>
	<tr>
        <th width="5%">SL</th>
		<th width="5%">Id</th>
		<th width="20%">From</th>
		<th width="20%">To</th>
		<th width="10%">Table Name</th>
		<th width="10%">Column Name</th>
		<th width="10%">Location</th>
		<th width="5%">Language</th>
        <th width="5%">DataPage Id</th>
		<th width="5%">Search History Id</th>
        <th width="5%">Created</th>
	</tr>
	</thead>
	
	<tbody>
<?php
$i = 0;
foreach( $this->items as $item ):
    $isPreamble  = (strpos($item->table_name,'preamble') !== false) ? TRUE : FALSE;
    $columnKey   = ($isPreamble) ? substr($item->column_name,0,-2) : $item->column_name;
    $columnTitle = empty($this->columnTitles[$columnKey]) ? $item->column_name : $this->columnTitles[$columnKey];
?>
		<tr align="center" class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $i + 1; ?>
            </td>
            <td> 
                <a class="load_popup" id="id_<?php echo $item->id;?>" href="javascript:;" title="Show details">
                   <?php echo $item->id;?>
                </a>
            </td>
			<td class="fromText" id="fromText<?php echo $item->id;?>"> <?php echo $item->from;?> </td>
			<td class="toText"   id="toText<?php echo $item->id;?>"> <?php echo $item->to;?> </td>
			<td> <?php echo $item->table_name;?> </td>
			<td title="<?php echo $item->column_name;?>"> 
                <?php echo $columnTitle;?> 
            </td>
			<td> <?php echo $item->location_name;?> </td>
            <td> <?php echo $item->language;?> </td>
            <td> <?php echo $item->datapage_id;?> </td>
            <td> <?php echo $item->search_history_id;?> </td>
            <td> <?php echo $item->created_at;?> </td>
		</tr>
<?php
$i++;
$k = 1 -$k;
endforeach;

if( count($this->items) ==0 ):
    echo "<tr><td colspan='10'>Sorry, No replace histories found...</td></tr>";
endif;
?>

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="<?php echo $this->task;?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="action" value="<?php echo $action;?>" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

</tbody>
</table>
</form>

<script type="text/javascript">
/*
 * For language Switching 
 * 
 */
function highlight(newElem, oldElem){
    var newText = newElem.text().trim();
    var oldText = oldElem.text().trim();
     
    var text = "";
    newText.split("").forEach(function(value, index){
        if (value != oldText.charAt(index))
            text += "<span class='highlight'>" + value + "</span>";         
        else
            text += value;
    });
    newElem.html(text);
}

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

jQuery('td.fromText').each(function(index, element){
      var fromID = jQuery(element).attr('id');
      var toID = fromID.replace('fromText','toText');
      highlight(jQuery("#"+toID), jQuery("#"+fromID));  
});
</script>
