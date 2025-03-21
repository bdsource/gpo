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
	background-color:#FFF880;
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
<?php include_once('submenus_startblock.php'); ?>
<div style="text-align:center;"><h1 style="font-weight:bold">Search News</h1></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="search" />
<input type="hidden" name="controller" value="news" />
<input type="hidden" id="news_many" name="news[many]" value="0" />

<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" for="news_published_range_from" title="Published From, Search by Date Published hides all items prior to 1970">Published Range: from</label>
			<input class="input_field published" type="text" readonly="readonly" id="news_published_range_from" name="news[published_range][from]" value="<?php echo $this->oNews->published_range->from; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_from_trigger">*</button> 
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" for="news_published_range_to" title="Published To, Search by Date Published hides all items prior to 1970">Published Range: to</label>
			<input class="input_field published" type="text" readonly="readonly" id="news_published_range_to" name="news[published_range][to]" value="<?php echo $this->oNews->published_range->to; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="news_published_range_to_trigger">*</button>
		</p>
	</div>
</div>
<div class="clear"></div>

<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" id="news_locations_label" title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Location:</label>
			<select id="select_news_country">
			<option value="">Country</option>
			<?php foreach($select_data['country'] as $cat) :
//to deal with the format the text file is in
	  	$value = str_replace("&nbsp;",'',$cat );
	 	echo <<<EOB
      <option value="{$value}">{$cat}</option>
EOB;
		endforeach;
?>
			</select>
			<select id="select_news_region">
			<option value="">Region</option>
			<?php foreach($select_data['region'] as $cat) :
//to deal with the format the text file is in
	  	$value = str_replace("&nbsp;",'',$cat );
	 	echo <<<EOB
      <option value="{$value}">{$cat}</option>
EOB;
		endforeach;
?>
			</select>
			
			<span id="news_txt_locations"></span>
			<input type="hidden" id="news_hidden_locations" name="news[locations]" value="" />
		</p>
	</div>
</div>

<div class="clear"></div>


<div class="row">
	<div class="cell">
<p>

	<label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Source</label>

	<select id="select_news_source">
    <option value="">Source</option>
	  <?php foreach($select_data['source'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select> 

	<input class="input_field" type="text" id="news_source" name="news[source]" style="width:670px;" value="<?php
	if( !empty( $this->oNews->source ) )
	{
		echo $this->oNews->source;
	}
	?>" />

</p>
	</div>
</div>
<div class="clear"></div>

<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Original title of article, chapter name, subtitle or paragraph title of article, subsection, broadcast segment, correspondence, etc., in Title Case.*">Title</label>
			<input class="input_field" type="text" id="news_title" name="news[title]" value="<?php echo $this->oNews->title; ?>" />
		</p>
	</div>

	<div class="cell">
	<p>
	<label title="Verbatim extracts, omitting text entered in other fields. Mark edits with ellipsis ' … '. Add notes in square brackets, e.g. [Table in the original].*">Content</label>&nbsp;&nbsp;<a href="#" id="trigger_search_many" style="font-size:100%;">Click to Search Content, Source, Title, Subtitle, Byline and GPNHeader</a><br />
	<input class="input_field" type="text" id="news_content" name="news[content]" value="<?php
	if( !empty( $this->oNews->content ) )
	{
		echo $this->oNews->content;
	}
	?>" />	
	</p>
	</div>

</div>		
<div class="clear"></div>

<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="Descriptive keywords chosen from GPO thesaurus drop-down list, separated by commas, in Title Case. Search aid for staff: never published.*">Keywords</label>
	<select id="select_news_keywords" >
    <option value="">Select</option>
  <?php foreach($select_data['keywords'] as $cat) echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
?>
	</select>
	<input class="input_field" type="text" id="news_keywords" name="news[keywords]" style="width:275px;" value="<?php
	if( !empty( $this->oNews->keywords ) )
	{
		echo $this->oNews->keywords;
	}
	?>" />
</p>
	</div>
	<div class="cell">
<p>
	<label style="display:block;" title="When a Quotes record is used to generate a Citation, always enter 'QCite nnnn' here, in the first line of Notes. Staff only: often empty, never published.">Notes</label>
	<input class="input_field" type="text" id="news_notes" name="news[notes]" value="<?php
	if( !empty( $this->oNews->notes ) )
	{
		echo $this->oNews->notes;
	}
	?>" />
</p>
	</div>	
</div>		
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Any sub title(s), in Sentence case, multiples separated by a colon. OR: foreign-language heading where Title is the English translation of that heading" >Sub Title</label>
			<input class="input_field" type="text" id="news_sub_title" name="news[subtitle]" value="<?php echo $this->oNews->subtitle; ?>" />
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" title="First, then last name of the principal author(s), multiples separated with a comma, and/or 'and.' No periods after initials, usually no rank, title, etc. ">Byline</label>
			<input class="input_field" type="text" id="news_byline" name="news[byline]" value="<?php echo $this->oNews->byline; ?>" />
		</p>
	</div>	
</div>
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
			<input class="input_field" type="text" id="news_websource" name="news[websource]" value="<?php echo $this->oNews->websource; ?>" />
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" title="Explanatory GPN headline as posted to Gun Policy News, in Title Case">GpnHeader</label>
			<input class="input_field" type="text" id="news_gpnheader" name="news[gpnheader]" value="<?php echo $this->oNews->gpnheader; ?>" />
		</p>		
	</div>	
</div>
<div class="clear"></div>

<div class="row">
	<div class="cell">
<?php
$selected = $this->oNews->category;
?>
<p>
	<label style="display:block;" title="Type of article, from drop-down list. Opinion is signed, Editorial is collective, unsigned. Default is a printed news item (empty field)">Category</label>
	<select id="news_category" name="news[category]">
		 <option value="">Select</option>
 <?php 
  foreach($select_data['category'] as $cat)
  {
  	if( $cat !== $this->oNews->category )
  	{
  		$selected = '';
  	}else{
  		$selected = 'selected="selected"';
  	}
  	echo <<<EOB
      <option value="$cat" $selected>$cat</option>
EOB;

  }
?>
	</select>
</p>		
	</div>		
	<div class="cell">
<?php 

if( !isset( $this->oNews->share )
	|| $this->oNews->share === '1'
	|| $this->oNews->share === "" 
){
	$share_public = 'checked="true"';
	$share_member ='';	
}else{
	$share_public ='';
	$share_member = 'checked="true"';	
}
?>
	<p>	
		<label title="Publish to GPO open web site." for="news_share_public">Public</label><input type="radio" id="news_share_public" name="news[share]" value="1" <?php echo $share_public; ?>/>
		<label title="Restrict to Members-only web pages (default)." for="news_share_member">Members</label><input type="radio" id="news_share_member" name="news[share]" value="0" <?php echo $share_member; ?>/>
	</p>
	</div>
</div>
<div class="clear"></div>

<div id="tool-tip-box"></div>
</form>
<?php include_once('submenus_endblock.php'); ?>
<script type="text/javascript">
//<![CDATA[	

var tab_order_str="news_content,news_published,select_news_country,select_news_region,select_news_source,news_source,select_news_keywords,news_keywords,news_title,news_sub_title,news_byline,news_category,news_websource,news_gpnheader,news_notes,btn_save_news,btn_clear_news";
var locations = [];
<?php 
if( count( $this->locations ) > 0 ): ?>
var current_locations = '<?php echo json_encode( $this->locations ); ?>';	
<?php  else: ?>
var current_locations = null;	
<?php
	 endif;
?>
location_populate();
Event.observe(window,'load',function(){


dd = document.body;
Element.extend( dd );
$('tool-tip-box').hide();
//Allow the left click of the mouse to trigger a new item
$('select_news_source').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_news_source', 'news_source' );
	}
});
//this is required to reset the list options
$('news_source').observe('focus',function(event){
	$('select_news_source').selectedIndex = '0';
});

$('select_news_source').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_news_source', 'news_source' );
	}
});

//county - mouse click & key = return & onchange 
$('select_news_country').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});
$('select_news_country').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});

$('select_news_country').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		location_select_add( this.readAttribute('id') );			
	}
});
//region - mouse click & key = return & onchange event
$('select_news_region').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});
$('select_news_region').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});
$('select_news_region').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		location_select_add( this.readAttribute('id') );
	}
});

$('select_news_keywords').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_news_keywords', 'news_keywords' );
	}
});
$('select_news_keywords').observe( 'change', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_news_keywords', 'news_keywords' );
	}
});

//this is required to reset the list options
$('news_keywords').observe('focus',function(event){
	$('select_news_keywords').selectedIndex = '0';
});
$('select_news_keywords').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_news_keywords', 'news_keywords' );
	}
});

/*
$('content_remove_html').observe('click',function(event)
{
	Event.stop(event);
	clean = $('quotes_content' ).getValue().stripTags().stripScripts();
	$('quotes_content').update( clean );
});
*/
<?php
## only add this block for the super-administrators ## 
if( $this->can_publish ):
?>
$('createTopic').observe('click', function( event ){
	Event.stop( event );
	$('news_hidden_locations' ).value = locations.compact().uniq().join(',');
	var data = $('adminForm').serialize();

	var data = encodeURIComponent( data );

	var href = this.readAttribute('href');
	
	href += '&d=';
	href += data;
	window.location = href;
});
<?php 
endif;
?>

$('toolbar-Link').observe('click',function(event)
{
	Event.stop(event);
	$('news_hidden_locations' ).value = locations.compact().uniq().join(',');		
	$('adminForm').submit();
});

$('adminForm').observe('submit', function(event) {
	$('news_hidden_locations' ).value = locations.compact().uniq().join(',');	
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
	id = tab_order.first();
$(id).focus();

},false);

document.observe( "search:quick", function( event )
{
	Event.stop( event );
	var i = [ 'news_title', 'news_source', 'news_notes', 'news_sub_title', 'news_byline', 'news_gpnheader', 'news_websource' ];
	
	if( $( i.first() ).hasClassName( 'not-editable' ) === false )
	{
		i.each( function (e){
				e = $( e );
				e.addClassName ( 'not-editable' );
				e.writeAttribute( 'readonly', true );
				e.value = '';
			});
		$( "news_many" ).value = 1;
    $("adminForm").submit();
	}else{
		i.each( function (e){
			e = $( e );
			e.removeClassName ( 'not-editable' );
			e.writeAttribute( 'readonly', false );
			e.value = '';
		});
		$( "news_many" ).value = 0;
	}
});
$("trigger_search_many").observe("click", function( event ){
	Event.stop( event );
	this.fire( "search:quick" );
});
<?php if( isset( $this->oNews->many  ) && $this->oNews->many === '1' ): ?>
document.fire( "search:quick" );
<?php endif; ?>
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