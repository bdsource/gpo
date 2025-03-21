<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();

$file = 'select';
//$path = JPath::find(JHTML::addIncludePath(), strtolower($file).'.php');
$path = JPATH_LIBRARIES."/cms/html/".$file.'.php';

/* default values coming from quick search */
 $jinput = JFactory::getApplication()->input;

 
 
$content_val = $jinput->get('contentadv', false);
$country_val = $jinput->get('countryadv', false);
$region_val = $jinput->get('regionadv', false);

$select_data = array();

$data = explode( "\n", GpoGetTypeFromCache( 'public_country' ) );
$select_data['country'] = '';
if( !empty( $data ) )
{
	foreach( $data as $v )
	{
		$value = str_replace("&nbsp;","",$v);
		$selected = ( $country_val !== $value ) ? '' : 'selected="selected"';
		$select_data['country'] .= '<option value="' . $value . '" . ' . $selected . '>' . ucwords( $v ) . '</option>';
	}	
}

$data = explode( "\n", GpoGetTypeFromCache( 'public_region' ) );
$select_data['region'] = '';
if( !empty( $data ) )
{
	foreach( $data as $v )
	{
		$value = str_replace("&nbsp;","",$v);
		$selected = ( $region_val !== $value ) ? '' : 'selected="selected"';
		$select_data['region'] .= '<option value="' . $value . '" . ' . $selected . '>' . ucwords( $v ) . '</option>';
	}	
}


$select_data['categories'] = GpoGetHtmlForType( 'category' );
$select_data['source'] = GpoGetHtmlForType( 'source' );
$select_data['keywords'] = GpoGetHtmlForType( 'keywords' );
?>

<div id="advancedSearch" style="padding:0 40 20 40px;">
<link rel="stylesheet" href="media/system/css/calendar-jos.css" type="text/css"  title="Green"  media="all" />
<script type="text/javascript" src="includes/js/joomla.javascript.js"></script>
<script type="text/javascript" src="media/system/js/mootools.js"></script>
<script type="text/javascript" src="media/system/js/caption.js"></script>
<!-- script type="text/javascript" src="media/system/js/calendar.js"></script>
<script type="text/javascript" src="media/system/js/calendar-setup.js"></script -->

<script type="text/javascript">
/*
Calendar._DN = new Array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");Calendar._SDN = new Array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"); Calendar._FD = 0;	Calendar._MN = new Array ("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");	Calendar._SMN = new Array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");Calendar._TT = {};Calendar._TT["INFO"] = "About the Calendar";
Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years.";Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months.";	Calendar._TT["GO_TODAY"] = "Go to today";Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months.";Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years.";Calendar._TT["SEL_DATE"] = "Select a date";Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";Calendar._TT["PART_TODAY"] = " (Today)";Calendar._TT["DAY_FIRST"] = "Display %s first";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "Close";Calendar._TT["TODAY"] = "Today";Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value.";Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d"; Calendar._TT["TT_DATE_FORMAT"] = "%A, %B %e";Calendar._TT["WK"] = "wk";Calendar._TT["TIME"] = "Time:";

var siteVar = {};
siteVar.baseUrl = '<?php echo JURI::base();?>'; */
</script>

<h1 style="padding-left:6px;">Admin-only Advanced Full-text News Search</h1>


<form id="frmAdvSearch" method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo&task=msearch'); ?>"> 
<table id="advSearchTable" style="width: 570px;" border="0" cellspacing="6">
<tbody>
<tr style="margin-bottom:10px;">
<td style="padding:0px;" width="78"><span class="hasTip" title="Country:: Select a country from the drop-down list. The Country and Region lists cannot be combined in the same search."> <span> Country</span> </span></td>
<td><span class="hasTip" title="Country:: Select a country from the drop-down list. The Country and Region lists cannot be combined in the same search."> <select id="selLoc" style="width: 195px;" name="country"> 
<option value=""></option>
<?php echo $select_data['country']; ?>
</select>
</span>
<span id="left-margin" style="margin-left:150px;"></span>
<span class="hasTip" title="Help::Members Advanced search help tips">
<a href="javascript:popup=window.open('<?php echo JRoute::_( 'index.php?option=com_gpo&task=search&view=help', false );?>','GunPolicySearchHelp','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600'); popup.focus();">

<img border="0" align="absmiddle" src="templates/gunpolicy/images/help_icon.gif"></a></span>
<span class="hasTip" title="GO::Search the 'Members Only' archive of full-text Gun Policy News articles">
<input id="advSearch1" align="bottom" class="button gpo_adv_search_frontend" type="submit" value="GO" style="margin-left:0px; margin-right:0px; margin-bottom:0px;" />
</span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr style="margin-bottom:10px;">
<td width="78"><span class="hasTip" title="Region:: Select a region or group of nations from the drop-down list. The Region and Country lists cannot be combined in the same search."> <span> OR Region </span> </span></td>
<td><span class="hasTip" title="Region:: Select a region or group of nations from the drop-down list. The Region and Country lists cannot be combined in the same search."> <select id="selReg" style="width: 195px;" name="region"> 
<option value=""></option>
<?php echo $select_data['region']; ?>
</select> 
  </span>
<span class="hasTip" title="Category:: Select from the drop-down list. For all news articles (the default), this field is empty."> 
  <span style="width:78px;margin-left:5px;margin-right:4px;">Category</span></span><span class="hasTip" title="Category:: Select from the drop-down list. For all news articles (the default), this field is empty.">
  <select style="width: 197px;" name="category">
  <option value=""></option>
  <?php 
  foreach($select_data['categories'] as $cat)
  {
	$selected = '';
  	echo <<<EOB
      <option value="$cat" $selected>$cat</option>
EOB;

  }
?>
</select>
</span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span class="hasTip" title="Text:: Search the body text of every article for a word or words, and/or an &quot;exact phrase&quot;."> <span>Text</span> </span></td>
<td><span class="hasTip" title="Text::Search the body text of every article for a word or words, and/or an &quot;exact phrase&quot;."> <input id="txtText" style="width: 457px;" name="content" type="text" value="<?php echo $content_val;?>" /> </span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr style="margin-bottom:10px;">
<td width="78"><span class="hasTip" title="Title:: The original headline or name of the article, broadcast programme, speech, etc."> <span>Title</span></span></td>
<td><span class="hasTip" title="Title:: The original headline or name of the article, broadcast programme, speech, etc."> <input id="txtTitle" style="width: 457px;" name="title" type="text" /> </span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr style="margin-bottom:10px;">
<td width="78"><span class="hasTip" title="Sub-Title:: Secondary title, name of broadcast segment, etc."> <span>Sub-Title</span> </span></td>
<td><span class="hasTip" title="Sub-Title:: Secondary title, name of broadcast segment, etc."> <input id="txtSubTitle" style="width: 457px;" name="subtitle" type="text" /> </span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span class="hasTip" title="Source:: Publication, broadcaster, news agency or Web source."> <span>Source</span> </span></td>
<td><span class="hasTip" title="Source:: Publication, broadcaster, news agency or Web source."> 
<select id="txtSource" name="source"> 
<option value=""></option>
<?php foreach($select_data['source'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
</select>
</span>
</td>
</tr>
<tr>
<td colspan="2"></td>
</tr>

<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span class="hasTip" title="Byline:: Name of writer, speaker, presenter or director."> <span>Byline</span> </span></td>
<td><span class="hasTip" title="Byline:: Name of writer, speaker, presenter or director."> <input id="txtByline" style="width: 457px;" name="byline" type="text" /> </span></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span class="hasTip" title=" ::Optional keyword(s) selected from the GunPolicy.org thesaurus of research terms, for example 'gender', 'celebratory', or 'destruction'. These were only added to articles from June 2007 onwards."><span>Keywords</span> </span>
</td>
<td><span class="hasTip" title="Keywords::Optional keyword(s) selected from the GunPolicy.org thesaurus of research terms, for  example 'gender', 'celebratory', or 'destruction'. These were only added to articles from June 2007 onwards.">
<select id="txtKeywords" name="keywords"> </span>
<option value=""></option>
  <?php foreach($select_data['keywords'] as $cat) echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
?>
</select>
</td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span>Date Range</span></td>
<td width="470">
<table style="width: 100%;" border="0">
<tbody>
<tr>
<td width="32%" align="center"><span class="hasTip" title="From:: Enter the first date in the format dd/mm/yyyy, or select a date from the calendar.">From:                                     <input id="fromdate" style="width: 137px;" name="fromdate" type="text" /> <a href="javascript:void(0);" onclick="return showCalendar('formdate', '%d/%m/%Y');"> <img style="display:none;" src="templates/system/images/calendar.png" alt="Click Here to Pick up the date" width="16" height="16" border="0" align="bottom" class="calendar" /></a> </span></td>
<td width="28%"><span class="hasTip" title="To:: Enter the last date in the format dd/mm/yyyy, or select a date from the calendar."> To:                                     
  <input id="todate" style="width: 137px;" name="todate" type="text" /> <a href="javascript:void(0);" onclick="return showCalendar('todate', '%d/%m/%Y');"> <img style="display:none;" src="templates/system/images/calendar.png" alt="Click Here to Pick up the date" width="16" height="16" border="0" align="bottom" class="calendar" /></a> </span></td>
<td width="40%">
<span class="hasTip" title="ID:: Find articles by their GunPolicy.org News ID number or comma-separated numbers (1234, 3456), or display a range of ID numbers (3000-3500)."> <span style="margin-right:2px;">ID</span> </span>
<span class="hasTip" title="ID:: Find articles by their GunPolicy.org News ID number or comma-separated numbers (1234, 3456), or display a range of ID numbers (3000-3500)."> 
<input id="txtId" style="width: 155px;" name="id" type="text" /> </span>
</td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td width="78"><span class="hasTip" title="Header::The explanatory headline added to each Gun Policy News item. These often differ from the   Title published in the original."><span>Header</span> </span></td>
<td><span class="hasTip" title="Header::The explanatory headline added to each Gun Policy News item. These often differ from the Title published in the original."> <input id="txtHeader" style="width: 457px;" name="gpnheader" type="text" /> </span> <br /></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>

<tr>
<td colspan="2"></td>
</tr>
<tr>
<td></td>
<td width="470">
<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0" align="center">
<tbody>
<tr>
<td style="text-align:center;">
<span class="hasTip" title="CLEAR::Clear all fields in this form">
<input id="resetButton" class="button gpo_adv_search_frontend_clear" name="resetButton" type="reset" value="Clear" />
</span></td>
<td style="text-align:center;">
<span class="hasTip" title="Help::Members Advanced search help tips">
<a href="javascript:popup=window.open('<?php echo JRoute::_( 'index.php?option=com_gpo&task=search&view=help', false );?>','GunPolicySearchHelp','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600'); popup.focus();"><img src="templates/gunpolicy/images/help_icon.gif" border="0" align="absmiddle"></a></span>
<span class="hasTip" title="GO::Search the 'Members Only' archive of full-text Gun Policy News articles.">
<input id="advSearch" class="button gpo_adv_search_frontend" type="submit" value="GO" style="margin-left:0px;" />
</span></td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table>
<input name="cmd" type="hidden" value="advanced" /></td>

		<input name="option" type="hidden" value="com_gpo" />
		<input name="task" type="hidden" value="msearch" />

</form></div>
<div id="copyright-caution" style="padding-top:15px;">
<table border="0">
<tbody>
<tr>
<td style="padding: 5px 10px; font-size: 8pt; width: 100%; background-color: #b6cfe4; margin-left: 0px; text-align: center;">For personal or academic use only. As a condition of accessing this page, the reader has agreed to                     protect the intellectual property rights of the original publisher of the material, and to comply                     with 'Fair Use' copyright provisions in relevant jurisdictions.</td>
</tr>
</tbody>
</table>
</div>
<script type="text/javascript">
             jQuery( function() {
            jQuery( "#fromdate" ).datepicker({ dateFormat: 'dd MM yy'});
            jQuery( "#todate" ).datepicker({ dateFormat: 'dd MM yy'});
          } );
</script>
<style>
	#ui-datepicker-div {
        padding:10px;
        background:white;
        z-index:10000!important;
        border: 1px silver solid;
    }
    .ui-datepicker-prev , .ui-datepicker-next{
        margin-right:10px;
        cursor: pointer;
    }
</style>