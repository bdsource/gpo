<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

$select_data['source']      = GpoGetHtmlForType( 'source' );
$select_data['keywords']    = GpoGetHtmlForType( 'keywords' );
$select_data['category']    = GpoGetHtmlForType( 'category' );

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_region.txt';
if( !file_exists( $filename ) )
{
	echo 'Error: Remember to create your region list';
	return;
}
$select_data['region'] = explode("\n",trim(file_get_contents( $filename )));

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_country.txt';
if( !file_exists( $filename ) )
{
	echo 'Error: Remember to create your country list';
	return;
}
$data = trim(file_get_contents( $filename ) );
$select_data['country'] =  explode("\n",$data);

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/mootools-core.js');
$document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');
$document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/news_location.js');
$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');

if( !empty( $this->oNews ) )
{
	foreach( $this->oNews as $key => $value )
	{
		if( is_string( $value ) )
		{
			$this->oNews->$key = htmlspecialchars( $value, ENT_QUOTES );
		}
	}
}
?>

<script type="text/javascript">
//<![CDATA[	
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

Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years.";Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months.";	Calendar._TT["GO_TODAY"] = "Go to today";Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months.";Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years.";Calendar._TT["SEL_DATE"] = "Select a date.";Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";Calendar._TT["PART_TODAY"] = " (Today)";Calendar._TT["DAY_FIRST"] = "Display %s first";Calendar._TT["WEEKEND"] = "0,6";Calendar._TT["CLOSE"] = "Close";Calendar._TT["TODAY"] = "Today";Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value.";Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%D"; Calendar._TT["TT_DATE_FORMAT"] = "%A, %B %e";Calendar._TT["WK"] = "wk";Calendar._TT["TIME"] = "Time:";
//]]>
</script>

<style>
.error_warning{color:#ff0000;}
#message_box a{display:block;}
#adminForm{
	background-color:#99CCFF;
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

.clear{
	clear:both;
}
#news_txt_locations a{padding-left:5px;}
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:8px;}
.location_txt{font-size:larger;}

#news_published{text-align:center;}
#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}

.not-editable{
	background-color: #e9e9e9;
}

</style>
<div id="message_box"></div>
<?php include_once('submenus_startblock.php');?>
<div style="text-align:center;"><h1 style="font-weight:bold">Search Glossary</h1></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" class="adminFormG" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />

<input type="hidden" name="controller" value="glossary" />
<input type="hidden" id="task" name="task" value="searchresult" />
<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" for="glossary_modified_range_from" title="Modified From">Modified Range: from</label>
			<input class="input_field published" readonly type="text" id="glossary_modified_range_from" name="glossary[modified_from]" value="<?php echo @$_GET['glossary']['modified_from'] ?>" />
      <button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_from_trigger">*</button> 
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" for="glossary_modified_range_to" title="Modified To">Modified Range: to</label>
			<input class="input_field published" readonly type="text" id="glossary_modified_range_to" name="glossary[modified_to]" value="<?php echo @$_GET['glossary']['modified_to'] ?>" />
      <button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_to_trigger">*</button>
		</p>
	</div>
</div>
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Glossary Title">Title</label>
			<input class="input_field" type="text" id="glossary_title" name="glossary[title]" value="<?php echo @$_GET['glossary']['title'] ?>" />
		</p>
	</div>

	<div class="cell">
	<p>
	<label title="Glossary Subtitle">Sub Title</label> <br />
	<input class="input_field" type="text" id="glossary_subtitle" name="glossary[subtitle]" value="<?php echo @$_GET['glossary']['subtitle'] ?>" />	
	</p>
	</div>

</div>		
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Web Source">WebSource</label>
			<input class="input_field" type="text" id="glossary_websource" name="glossary[websource]" value="<?php echo @$_GET['glossary']['websource'] ?>" />
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" title="Glossary Content">Content</label>
			<input class="input_field" type="text" id="glossary_content" name="glossary[content]" value="<?php echo @$_GET['glossary']['content'] ?>" />
		</p>		
	</div>	
</div>
<div class="clear"></div>

<div id="tool-tip-box"></div>
</form>
<?php include_once('submenus_endblock.php');?>
<script type="text/javascript">
//<![CDATA[	
Calendar.setup({
    inputField     :    "glossary_modified_range_from",
    ifFormat       :    "%Y-%m-%d",
    align          :    "Bl",
    min: 19700101,
    button         : "news_published_range_from_trigger",
	singleClick	: true
});    

Calendar.setup({
    inputField     :    "glossary_modified_range_to",
    ifFormat       :    "%Y-%m-%d",
    align          :    "Bl",
    button         : "news_published_range_to_trigger",
	singleClick	: true
});

$('toolbar-Link').observe('click',function(e){
    e.preventDefault();
    document.getElementById('adminForm').submit();
  });

$('close-button').observe('click',function(e){
    e.preventDefault();
    document.getElementById('task').value = 'published';
    document.getElementById('adminForm').submit();
  });

$('reset-button').observe('click',function(e){
    e.preventDefault();
    $$('input[type="text"]').each(function(el){
      $(el).value = '';
    });
  });
//]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#glossary_modified_range_from" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#glossary_modified_range_to" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>