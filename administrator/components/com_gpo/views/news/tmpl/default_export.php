<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

if( $this->inprogress )
{
?>
<p>The system is still processing your last request</p>
<?php
return;
}


$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/mootools-core-uncompressed.js');

$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');



$document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/news_location.js');

$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');



$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
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

<form method="post" action="<?php echo JRoute::_( 'index.php',false );?>" id="build-form">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="export" />
<input type="hidden" id="download" name="download" value="0" />
<input type="hidden" id="nocigar" name="nocigar" value="0" />
<input type="hidden" name="controller" value="news" />
<?php include_once('submenus_startblock.php'); ?>
<p>
	<span><input type="radio" name="type" value="last_24hrs" /> Export all records <select name="last_24hrs_type"><option value="published">Published</option><option value="modified">Modified</option></select> in the last 24 hours</span>
</p>

<p>
	<label><input type="radio" name="type" value="since_id" /> Export all records added after this ID:</label>
	<br /> 
	<input type="text" name="since_id" value="" />
</p>


<p>
	<label><input type="radio" name="type" value="following_ids" /> Export record(s) by ID number (comma-separate multiple IDs, or enter a range, as in 1300-1305</label>
	<br />
	<input type="text" name="following_ids" value="" />
</p>



<p>
	<span><input type="radio" name="type" value="all" /> Export all records ( this will be a slow process )</span>
</p>

<p>
	<span><input type="radio" name="type" value="date_range" /> Export all records within a date range:</span>	
</p>

<p>
	<label style="display:block;" for="news_published_range_from" title="Published From, Search by Date Published hides all items prior to 1970">Published Range: from</label>
	<input class="input_field published" type="text" readonly="readonly" id="news_published_range_from" name="published_range[from]" value="<?php echo $this->oNews->published_range->from; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_from_trigger">*</button> 
</p>
		
<p>
	<label style="display:block;" for="news_published_range_to" title="Published To, Search by Date Published hides all items prior to 1970">Published Range: to</label>
	<input class="input_field published" type="text" readonly="readonly" id="news_published_range_to" name="published_range[to]" value="<?php echo $this->oNews->published_range->to; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_to_trigger">*</button> <span style="color:#0000ff;">( Do not enter dates before 1970 )</span>
</p>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
//<![CDATA[	
Event.observe(window,'load',function(){

$("submit-download").observe("click",function(event){
	Event.stop(event);
	$("download").value="1";
	$("build-form").submit();
});


$("submit-export-nocigar").observe("click",function(event){
	Event.stop(event);
	$("nocigar").value="1";
	$("build-form").submit();
});

$("submit-export").observe("click",function(event){
	Event.stop(event);
	$("build-form").submit();
});


Calendar.setup({
    inputField     :    "news_published_range_from",     // id of the input field
    ifFormat       :    "%e %b %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    min: 19700101,
    button         : "news_published_range_from_trigger",
	singleClick	: true
});    
Calendar.setup({
    inputField     :    "news_published_range_to",     // id of the input field
    ifFormat       :    "%e %b %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    button         : "news_published_range_to_trigger",
	singleClick	: true
});    

},false);
//]]>
</script>

<script type="text/javascript">
     jQuery( function() {
    jQuery( "#news_published_range_from" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#news_published_range_to" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>