<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

$select_data['city'] = GpoGetHtmlForType( 'city' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');



$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

if( is_object( $this->oCitation ) )
{
	foreach( $this->oCitation as $key => $value )
	{
		if( is_string( $value ) )
		{
			$this->oCitation->$key = htmlspecialchars( $value, ENT_QUOTES );
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
	/*
	 * background color changed from yellow to green to match FRT
	   background-color:#FFF880;
	*/
    background-color: #CFC;
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
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:8px;}
.location_txt{font-size:larger;}

#citation_published{text-align:center;}
#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}
</style>
<div id="message_box"></div>
<?php include_once('submenus_startblock.php'); ?>
<div style="text-align:center;"><h1 style="font-weight:bold">Search QCite</h1></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="search" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />



<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" for="citation_published_range_from" title="Published From, Search by Date Published, Provide lower to upper date">Published Range: from</label>
			<input class="input_field published" readonly type="text" id="citation_published_range_from" name="citation[published_range][from]" value="<?php echo $this->oCitation->published_range->from; ?>" /><button title="Type in DD-MM-YY, and/or select your date from the Date Picker. Enter lower to upper date" id="citation_published_range_from_trigger">*</button> 
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" for="citation_published_range_to" title="Published To, Search by Date Published, Provide lower to upper date, If you left empty To Date, the system will search up to current date.">Published Range: to</label>
			<input class="input_field published" readonly type="text" id="citation_published_range_to" name="citation[published_range][to]" value="<?php echo $this->oCitation->published_range->to; ?>" /><button title="Type in DD-MM-YY, and/or select your date from the Date Picker. Enter lower to upper date" id="citation_published_range_to_trigger">*</button> 
                        <label> (Provide lower to upper date.
                                If you left empty "To Date", the system will search up to current date.)                        
                       </label>
		</p>
	</div>
</div>
<div class="clear"></div>



<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Original title of article, chapter name, subtitle or paragraph title of article, subsection, broadcast segment, correspondence, etc., in Title Case.*">Title</label>
			<input class="input_field" type="text" id="citation_title" name="citation[title]" value="<?php echo $this->oCitation->title; ?>" />
		</p>
	</div>

	<div class="cell">
	<p>
	<label style="display:block;" title="Verbatim extracts, omitting text entered in other fields. Mark edits with ellipsis ' … '. Add notes in square brackets, e.g. [Table in the original].*">Content</label>
	<input class="input_field" type="text" id="citation_content" name="citation[content]" value="<?php
	if( !empty( $this->oCitation->content ) )
	{
		echo $this->oCitation->content;
	}
	?>" />	
	</p>
	</div>

</div>		
<div class="clear"></div>






<div class="row">
	<div class="cell">
	<p>
		<label style="display:block;" title="Name of publication or medium in which the text appeared.">Source</label>
		<input class="input_field" type="text" id="citation_source" name="citation[source]" style="width:200px;" value="<?php
		if( !empty( $this->oCitation->source ) )
		{
			echo $this->oCitation->source;
		}
		?>" />
	
	</p>
	</div>
	
	<div class="cell">
	<p>
	<label style="display:block;" title="Place of publication. Use a single city name suitable for use in a citation, such as 'Canberra', 'Washington, DC' or 'Fairfax, VA'.">City</label>
	<select id="select_citation_city">
		 <option value="">Select</option>
 <?php 
  foreach($select_data['city'] as $cat)
  {
  	echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
  }
?>
	</select>
	<input type="text" id="citation_city" name="citation[city]" style="width:90px;" value="<?php
	if( !empty( $this->oCitation->city ) )
	{
		echo $this->oCitation->city;
	}
	?>" />
</p>
	</div>
</div>
<div class="clear"></div>






<div class="row">
	<div class="cell">
	<p>
		<label style="display:block;" title="Last, first name of first author, then first, last names of subsequent author(s), (use comma, ‘and’). Authorless articles: ‘PNG’, ‘UNIDIR’, ‘Australia’, ‘SIPRI’, etc.">Author</label>
		<input class="input_field" type="text" id="citations_author" name="citation[author]" value="<?php echo $this->oCitation->author; ?>" />
	</p>
	</div>
	
	<div class="cell">
	<p>
		<label style="display:block;" title="Institution or individual responsible for publication. If possible, should not duplicate Author or Source.">Publisher</label>
		<input class="input_field" type="text" id="citations_publisher" name="citation[publisher]" value="<?php echo $this->oCitation->publisher; ?>" />
	</p>
	</div>
	
</div>
<div class="clear"></div>


<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="News Id">Quote Id</label>
	<input style="width:100px;" class="input_field" type="text" id="citation_ext_id" name="citation[ext_id]" value="<?php
	if( !empty( $this->oCitation->ext_id ) )
	{
		echo $this->oCitation->ext_id;
	}
	?>" />
</p>
	</div>
	
	<div class="cell">
	<p>
		<label style="display:block;" title="Volume or chapter in which the cited quotation appeared">Volume</label>
		<input style="width:100px;" class="input_field" type="text" id="citations_volume" name="citation[volume]" value="<?php echo $this->oCitation->volume; ?>" />
	</p>
	</div>
	<div class="cell">
	<p>
		<label style="display:block;" title="Journal or magazine issue; number, month or local season (June, June/July, Summer, Autumn, Fall, etc.)">Issue</label>
		<input style="width:100px;" class="input_field" type="text" id="citations_issue" name="citation[issue]" value="<?php echo $this->oCitation->issue; ?>" />
	</p>
	</div>
	<div class="cell">
	<p>
		<label style="display:block;" title="All page numbers and ranges should be prefixed, as in 'p. 3', 'pp. 243-45', 'pp. 1, 4, 7-8', etc.">Page</label>
		<input style="width:100px;" class="input_field" type="text" id="citations_page" name="citation[page]" value="<?php echo $this->oCitation->page; ?>" />
	</p>
	</div>
</div>

<div class="clear"></div>






<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="When a Quotes record is used to generate a Citation, always enter 'QCite nnnn' here, in the first line of Notes. Staff only: often empty, never published.">Notes</label>
	<input class="input_field" type="text" id="citation_notes" name="citation[notes]" value="<?php
	if( !empty( $this->oCitation->notes ) )
	{
		echo $this->oCitation->notes;
	}
	?>" />
</p>
	</div>	
</div>		
<div class="clear"></div>




<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
			<input class="input_field" type="text" id="citation_websource" name="citation[websource]" value="<?php echo $this->oCitation->websource; ?>" />
		</p>
	</div>
</div>
<div class="clear"></div>
<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">Sourcedoc</label>
			<input class="input_field" type="text" id="citation_sourcedoc" name="citation[sourcedoc]" value="<?php echo $this->oCitation->sourcedoc; ?>" />
		</p>
	</div>
</div>

<div class="clear"></div>

<div class="row">		
	<div class="cell">
<?php 

if( !isset( $this->oCitation->share )
	|| $this->oCitation->share === '1'
	|| $this->oCitation->share === "" 
){
	$share_public = 'checked';
	$share_member ='';	
}else{
	$share_public ='';
	$share_member = 'checked';	
}
?>
	<p>	
		<label title="Publish to GPO open web site." for="citation_share_public">Public</label><input type="radio" id="citation_share_public" name="citation[share]" value="1" <?php echo $share_public; ?>/>
		<label title="Restrict to Members-only web pages (default)." for="citation_share_member">Members</label><input type="radio" id="citation_share_member" name="citation[share]" value="0" <?php echo $share_member; ?>/>
	</p>
	</div>
</div>
<div class="clear"></div>

<div id="tool-tip-box"></div>
</form>
<?php include_once('submenus_endblock.php'); ?>
<script type="text/javascript">
//<![CDATA[	
function select_split( id_select_box, id_text_box )
{
	var value = $( id_select_box ).value;
	if( value != '' )
	{
		new_current = [];
		$( id_text_box ).value.split(',').each( function( s ){
			s = s.strip();
			if( s !='' )
			{
				new_current.push(s);
			}
		});
		new_current.push( value );
		$( id_text_box ).value = new_current.uniq().join(', ');
		$( id_select_box ).selectedIndex = '0';
	}
}

var tab_order_str="citation_content,citation_published,citation_source,citation_title,citation_websource,citation_notes";

Event.observe(window,'load',function(){
		
dd = document.body;
Element.extend( dd );
$('tool-tip-box').hide();
//Allow the left click of the mouse to trigger a new item

$('select_citation_city').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_citation_city', 'citation_city' );
	}
});
$('select_citation_city').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_citation_city', 'citation_city' );
	}
});
//this is required to reset the list options
$('citation_city').observe('focus',function(event){
	$('select_citation_city').selectedIndex = '0';
});

$('select_citation_city').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_citation_city', 'citation_city' );
	}
});


$('toolbar-Link').observe('click',function(event)
{
	Event.stop(event);
	$('adminForm').submit();
});

function display_tip(el)
{
	pos = $( el ).viewportOffset();
	$('tool-tip-box').hide();
	title = el.readAttribute('title');
	$('tool-tip-box').update( title );
	$('tool-tip-box').setStyle({
		'position':'fixed',
		'top': pos.top + 'px',
		'left': pos.left + 'px',
		'z-index':100
	});
	$('tool-tip-box').show();
	new PeriodicalExecuter(function(pe){
	$('tool-tip-box').hide();
	pe.stop();
	},2);
}

$('adminForm').select('label').each(function(s){
	str = s.readAttribute('title');
	if( str == null || str.length == 0 ){return;}
	s.observe('click', function(event){
		Event.stop(event);
		display_tip( s );
	});
	s.observe('focus',function(event){Event.stop(event);});
	s.observe('mouseover',function(event){Event.stop(event);});
});

tab_order = tab_order_str.split(",");
tab_order.each(function(s, i){
	if( $(s) )
	{
		$(s).writeAttribute( 'tabindex', i+1 );
	}
});
//this catches return
$('adminForm').select('input').each( function( el ){

	if( el.readAttribute( 'type' ) != 'text' )
	{
		return;
	}
	el.observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		Event.stop(event);
		if( this.readAttribute( 'tabindex' ) )
		{
			tabto = this.readAttribute( 'tabindex' );
			++tabto;
			el = $('adminForm').select('[tabindex="' + tabto + '"]').first();
			if( Object.isElement( el ) )
			{
				el.focus();
			}
		}
	}
	});
});
		
dd.observe('keypress',function(event){
	
	if( event.keyCode == Event.KEY_TAB )
	{
		element = Event.element(event);
		if( element.readAttribute( 'tabindex' ) > 0 )
		{
			tabto = parseInt( element.readAttribute('tabindex') );
			++tabto;
		}else //if( current_tab >= tab_order.length )
		{
			tabto = 1;
		}
		el = this.select('[tabindex="' + tabto + '"]').first();
		if( Object.isUndefined(el) )
		{
			tabto = 1;
			el = this.select('[tabindex="' + tabto + '"]').first();
		}
		Event.stop(event);
		el.focus();
	}
});

Calendar.setup({
    inputField     :    "citation_published_range_from",     // id of the input field
    ifFormat       :    "%e %b %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    min: 19700101,
    button         : "citation_published_range_from_trigger",
	singleClick	: true
});    
Calendar.setup({
    inputField     :    "citation_published_range_to",     // id of the input field
    ifFormat       :    "%e %b %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    button         : "citation_published_range_to_trigger",
	singleClick	: true
});    
	id = tab_order.first();
$(id).focus();

},false);
//]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#citation_published_range_from" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#citation_published_range_to" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>