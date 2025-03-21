<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');
//$filename = JPATH_BASE . '/components/com_gpo/cache/quotes_keywords.txt';
//$select_data['keywords'] = explode("\n",trim(file_get_contents( $filename )));
//$select_data['cities'] = GpoGetHtmlOptionCities();

$select_data['keywords'] = GpoGetHtmlForType( 'keywords' );
$select_data['city'] = GpoGetHtmlForType( 'city' );


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
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/quotes_location.js');

$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));
$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');



$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

foreach( $this->oQuote as $key => $value )
{
	if( is_string( $value ) )
	{
		$this->oQuote->$key = htmlspecialchars( $value, ENT_QUOTES );
	}
}
//var_dump($select_data);
?>

<script>

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

</script>

<style>
.error_warning{color:#ff0000;}
#message_box a{display:block;}
#adminForm{
   /* background color changed from blue to orange
     background-color: #99CCFF;
    */
    background-color: #F96; 
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
</style>
<div id="message_box"></div>
<?php
if(isset($this->noresult)){
    echo '<h3>No matching quotes found!</h3>';
}
?>
<?php include_once('submenus_startblock.php'); ?>
<div style="text-align:center;"><h1 style="font-weight:bold">Search Quotes (PoAIM) </h1></div>
<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="poaimsearch" />
<input type="hidden" name="controller" value="quotes" />
<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" for="quotes_published_range_from" title="Published From, Search by Date Published hides all items prior to 1970">Published Range: from</label>
			<input class="input_field published" type="text" readonly id="quotes_published_range_from" name="quotes[published_range][from]" value="<?php echo $this->oQuote->published_range->from; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="quotes_published_range_from_trigger">*</button> 
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" for="quotes_published_range_to" title="Published To, Search by Date Published hides all items prior to 1970">Published Range: to</label>
			<input class="input_field published" type="text" readonly id="quotes_published_range_to" name="quotes[published_range][to]" value="<?php echo $this->oQuote->published_range->to; ?>" /><button title="Type in DD/MM/YY, and/or select your date from the Date Picker. Searching by Date Published hides all items prior to 1970" id="quotes_published_range_to_trigger">*</button> <span style="color:#0000ff;">( Do not enter dates before 1970 )</span>
		</p>
	</div>
    <div class="cell" style="margin-left: 50px;">
        <p><label for="input_field_range_from" title="Enter a range of ID numbers, or enter one ID in both field to find a single record">ID Range: from </label><br/><input type="number" min="1" id="input_field_range_from" name="quotes[id_range][from]" /></p>
    </div>
    <div class="cell">
        <p><label for="input_field_range_to" title="Enter a range of ID numbers, or enter one ID in both field to find a single record">ID Range: to </label><br/><input type="number" min="1" id="input_field_range_to" name="quotes[id_range][to]" /></p>
    </div>
</div>
<div class="clear"></div>
		
<div class="row">
	<div class="cell">
		<p>
			<label id="quotes_locations_label" style="display:block;" title="Country finds any record in which the selected country name(s) appear in the Location field. Region finds all records whose Location field lists any country in that region">Location:</label>
			<select id="select_quotes_country">
			<option value="">Country</option>
			<?php foreach($select_data['country'] as $cat) :
//to deal with the format the text file is in
	  	$value = str_replace("&nbsp;",'',$cat );
	 	echo '<option value="'.$value.'">'.$cat.'</option>';
		endforeach;
?>
			</select>
			<select id="select_quotes_region">
			<option value="">Region</option>
			<?php foreach($select_data['region'] as $cat) :
//to deal with the format the text file is in
	  	$value = str_replace("&nbsp;",'',$cat );
            echo '<option value="'.$value.'">'.$cat.'</option>';
		endforeach;
?>
			</select>
			
			<span id="quotes_txt_locations"></span>
			<input type="hidden" id="quotes_hidden_locations" name="quotes[locations]" value="" />
		</p>
	</div>
</div>

<div class="clear"></div>

<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Name of book, journal, publication or series title, as in: 'Small Arms Survey 2004: Rights at Risk', 'British Medical Journal', or 'SIPRI Backgound Paper'.">Source</label>
			<input type="text" id="quotes_source" name="quotes[source]" style="width:470px;" value="<?php
			if( !empty( $this->oQuote->source ) )
			{
				echo $this->oQuote->source;
			}
			?>" />
		</p>
	</div>
	
	<div class="cell">
	<p>
	<label style="display:block;" title="Place of publication. Use a single city name suitable for use in a citation, such as 'Canberra', 'Washington, DC' or 'Fairfax, VA'.">City</label>
	<select id="select_quotes_city">
		 <option value="">Select</option>
 <?php 
  foreach($select_data['city'] as $cat)
  {
  	if( $cat !== $this->oQuote->city )
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
	<input type="text" id="quotes_city" name="quotes[city]" style="width:90px;" value="<?php
	if( !empty( $this->oQuote->city ) )
	{
		echo $this->oQuote->city;
	}
	?>" />
</p>
	</div>
</div>

	
<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Original title of article, chapter name, subtitle or paragraph title of article, subsection, broadcast segment, correspondence, etc.">Title</label>
			<input class="input_field" type="text" id="quotes_title" name="quotes[title]" value="<?php echo $this->oQuote->title; ?>" />
		</p>
	</div>
	<div class="cell">
	<p>
	<label title="Find text string(s) in the main body of text">Content</label> <br />
	<input class="input_field" type="text" id="quotes_content" name="quotes[content]" value="<?php
	if( !empty( $this->oQuote->content ) )
	{
		echo $this->oQuote->content;
	}
	?>" />	
	</p>
	</div>	
</div>		
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Authorless articles: 'PNG', 'UNIDIR', 'Australia', 'SIPRI', etc.">Author</label>
			<input class="input_field" type="text" id="quotes_author" name="quotes[author]" value="<?php echo $this->oQuote->author; ?>" />
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
			<input class="input_field" type="text" id="quotes_websource" name="quotes[websource]" value="<?php echo $this->oQuote->websource; ?>" />
		</p>
	</div>	
</div>		
<div class="clear"></div>


<div class="row">
	<div class="cell">
		<p>
			<label style="display:block;" title="Institution responsible for publication, as in: 'Penguin', 'Control Arms', 'Small Arms Survey', or 'Government of Fiji'.">Publisher</label>
			<input class="input_field" type="text" id="quotes_publisher" name="quotes[publisher]" value="<?php echo $this->oQuote->publisher; ?>" />
		</p>
	</div>
	<div class="cell">
		<p>
			<label style="display:block;" title="File name, location of any source document.">SourceDoc</label>
			<input class="input_field" type="text" id="quotes_sourcedoc" name="quotes[sourcedoc]" value="<?php echo $this->oQuote->sourcedoc; ?>" />
		</p>
	</div>	
</div>		
<div class="clear"></div>




<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="Descriptive keywords chosen from the GPO thesaurus, beginning with records entered from June, 2007">Keywords</label>
	<select id="select_quotes_keywords" >
    <option value="">Select</option>
  <?php foreach($select_data['keywords'] as $cat) echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
?>
	</select>
	<input class="input_field" type="text" id="quotes_keywords" name="quotes[keywords]" style="width:275px;" value="<?php
	if( !empty( $this->oQuote->keywords ) )
	{
		echo $this->oQuote->keywords;
	}
	?>" />
</p>
	</div>
	<div class="cell">
<p>
	<label style="display:block;" title="Staff only: often empty, never published.">Notes</label>
	<input class="input_field" type="text" id="quotes_notes" name="quotes[notes]" value="<?php
	if( !empty( $this->oQuote->notes ) )
	{
		echo $this->oQuote->notes;
	}
	?>" />
</p>
	</div>	
</div>		
<div class="clear"></div>


<div class="row">
	<div class="cell">
<?php 

if( $this->oQuote->share === '1' || $this->oQuote->share === "" ){
	$share_public = 'checked="true"';
	$share_member ='';

}else{
	$share_public ='';
	$share_member = 'checked="true"';
}
?>
	<p>	
		<label title="Publish to GPO open web site." for="quotes_share_public">Public</label><input type="radio" id="quotes_share_public" name="quotes[share]" value="1" <?php echo $share_public; ?>/>
		<label title="Restrict to Members-only web pages (default)." for="quotes_share_member">Members</label><input type="radio" id="quotes_share_member" name="quotes[share]" value="0" <?php echo $share_member; ?>/>
	</p>
	</div>
    <div class="cell" style="margin-left:100px">
        <p>
            <label title="Search by staff member initials (2010 and later)">Staff <input type="text" name="quotes[staff]"  value="<?php echo $this->oQuote->staff;?>" /></label>
            <label title="Search by PoAIM scoring protocol clause number">PoAIM <input type="text" name="quotes[poaim]"  value="<?php echo $this->oQuote->poaim;?>"/></label>
        </p>
    </div>
</div>

<div class="clear"></div>

<div id="tool-tip-box"></div>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
//<![CDATA[	

var tab_order_str="quotes_content,select_quotes_country,select_quotes_region,quotes_source,select_quotes_city,quotes_city,quotes_title,quotes_author,quotes_publisher,select_quotes_keywords,quotes_keywords,quotes_websource,quotes_sourcedoc,quotes_notes";
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
$('select_quotes_city').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_quotes_city', 'quotes_city' );
	}
});
//this is required to reset the list options
$('quotes_city').observe('focus',function(event){
	$('select_quotes_city').selectedIndex = '0';
});

$('select_quotes_city').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_quotes_city', 'quotes_city' );
	}
});




//county - mouse click & key = return
$('select_quotes_country').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});
$('select_quotes_country').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		location_select_add( this.readAttribute('id') );			
	}
});


//region - mouse click & key = return
$('select_quotes_region').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		location_select_add( this.readAttribute('id') );	
	}
});
$('select_quotes_region').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		location_select_add( this.readAttribute('id') );
	}
});






$('select_quotes_keywords').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_quotes_keywords', 'quotes_keywords' );
	}
});
//this is required to reset the list options
$('quotes_keywords').observe('focus',function(event){
	$('select_quotes_keywords').selectedIndex = '0';
});
$('select_quotes_keywords').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_quotes_keywords', 'quotes_keywords' );
	}
});


$('toolbar-Link').observe('click',function(event)
{
	Event.stop(event);
	$('quotes_hidden_locations' ).value = locations.compact().uniq().join(',');		
	$('adminForm').submit();
});

$('adminForm').select('.item_save').each(function(el){
	el.observe('click',function(event)
	{
//		Event.stop(event);
		$('quotes_hidden_locations' ).value = locations.compact().uniq().join(',');
	});
});

$('adminForm').select('.clear_form').each(function(el){
	el.observe('click',function(event)
	{
		Event.stop(event);
		$('adminForm').reset();
		location_populate();
	});
});

$('adminForm').observe('submit', function(event) {
//	Event.stop(event);
	$('quotes_hidden_locations' ).value = locations.compact().uniq().join(',');	
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
        inputField     :    "quotes_published_range_from",     // id of the input field
        ifFormat       :    "%e %b %Y",      // format of the input field
        align          :    "Bl",           // alignment (defaults to "Bl")
        min: 19700101,
	    button         : "quotes_published_range_from_trigger",
		singleClick	: true
    });    
	Calendar.setup({
        inputField     :    "quotes_published_range_to",     // id of the input field
        ifFormat       :    "%e %b %Y",      // format of the input field
        align          :    "Bl",           // alignment (defaults to "Bl")
	    button         : "quotes_published_range_to_trigger",
		singleClick	: true
    });    
   	id = tab_order.first();
	$(id).focus();
	},false);
//]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#quotes_published_range_from" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
     
     jQuery( function() {
    jQuery( "#quotes_published_range_to" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>