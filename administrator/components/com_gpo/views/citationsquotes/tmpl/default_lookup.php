<?php
//citationsquotes
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

//$select_data['cities'] = GpoGetHtmlOptionCities();
$select_data['city'] = GpoGetHtmlForType( 'city' );

$document = &JFactory::getDocument();
$document->addScript( JURI::root(true).'/media/system/js/prototype-1.6.0.2.js');

$document->addScript( JURI::root(true).'/administrator/templates/bluestork/js/citations_location.js');

$document->addStyleSheet( JURI::root(true).'/media/system/css/calendar-jos.css', 'text/css', 'all', array('title'=>'green'));

$document->addScript( JURI::root(true).'/media/system/js/calendar.js');
$document->addScript( JURI::root(true).'/media/system/js/calendar-setup.js');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

foreach( $this->oCitation as $key => $value )
{
	if( is_string( $value ) )
	{
		$this->oCitation->$key = htmlspecialchars( $value, ENT_QUOTES );
	}
}

$date_output = 'j M Y';
//Set display output
if( $this->oCitation->published === '0000-00-00 00:00:00' || empty( $this->oCitation->published ) ){ $this->oCitation->published=GpoDefaultPublishTime();}
else{ 
    
}
    $date = new DateTime($this->oCitation->published);
    $this->oCitation->published = $date->format("j M Y"); 
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
	background-color:#ccffcc;
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

#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:8px;}
.location_txt{font-size:larger;}

#citations_published{text-align:center;}

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
<span style="font-size:90%;color:#ff0000;">Changes made to this &quot;Lookup&quot; version of the record cannot be saved. Select &quot;Edit&quot; to alter the record.</span>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="quotes" />
<input type="hidden" id="form_id" name="citations[id]" value="<?php echo $this->oCitation->id; ?>" />
<input type="hidden" id="new_record" name="new_record" value="0" />
<div class="row">
	<div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
		<p>
			<label style="display:block;" title="ID number cloned from original Quote record: cannot be altered. When Published, a unique QCite ID number is added. Deleted ID numbers cannot be re-used.">Quote ID*</label>
			<input id="ext_id" class="not-editable" type="text" value="<?php echo $this->oCitation->ext_id; ?>" disabled="true" />
		</p>		
	</div>	
	<div class="cell">
		<p>
			<label style="display:block;" for="citations_published" title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Published*</label>
			<input class="input_field published" type="text" readonly id="citations_published" name="citations[published]" value="<?php echo $this->oCitation->published; ?>" />
		</p>
	</div>
	<div style="margin-left:150px;">
		<p>
			<label style="display:block;" title="If checked then current date (Today) will be shown in the citation content in the frontend instead of showing the published date.">Show Current Date</label>			
			<input class="input_field" type="checkbox" readonly id="currentdate" name="citations[currentdate]" <?php echo $this->oCitation->currentdate ? "checked='checked'" : ""; ?> />		
		</p>
	</div>	
</div>

<div class="clear"></div>

<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="Name of publication or medium in which the text appeared.">Source*</label>
	<input type="text" id="citations_source" name="citations[source]" style="width:620px;" value="<?php
	if( !empty( $this->oCitation->source ) )
	{
		echo $this->oCitation->source;
	}
	?>" />

	</p>
	</div>
	
	<div class="cell">
	<p>
	<label style="display:block;" title="Place of publication. Usually a single city name suitable for use in a citation, such as Canberra or Geneva" id="citations_city_label">City*</label>

	<select id="select_citations_city">
		 <option value="">Select</option>
	  <?php foreach($select_data['city'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select>

	<input type="text" id="citations_city" name="citations[city]" style="width:108px;" value="<?php
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
	<label style="display:block;" title="Original title of article, chapter, subtitle, paragraph title, etc.">Title*</label>
	<input class="input_field" type="text" id="citations_title" name="citations[title]" value="<?php echo $this->oCitation->title; ?>" />
</p>

<p>
	<label style="display:block;" title="Last, first name of first author, then first, last names of subsequent author(s), (use comma, ‘and’). Authorless articles: ‘PNG’, ‘UNIDIR’, ‘Australia’, ‘SIPRI’, etc.">Author*</label>
	<input class="input_field" type="text" id="citations_author" name="citations[author]" value="<?php echo $this->oCitation->author; ?>" />
</p>

<!--
<p>
	<label style="display:block;" title="Redundant Affiliation data from Quotes. Must be copied to another field, or deleted">Affiliation</label>
	<input class="input_field not-editable" type="text" id="citations_affiliation" name="citations[affiliation]" value="<?php echo $this->oCitation->affiliation; ?>" />
</p>
-->

<p>
	<label style="display:block;" title="Institution or individual responsible for publication. If possible, should not duplicate Author or Source.">Publisher</label>
	<input class="input_field" type="text" id="citations_publisher" name="citations[publisher]" value="<?php echo $this->oCitation->publisher; ?>" />
</p>



<div class="row">
	<div class="cell">
	<p>
		<label style="display:block;" title="Volume or chapter in which the cited quotation appeared">Volume</label>
		<input class="input_field input120" type="text" id="citations_volume" name="citations[volume]" value="<?php echo $this->oCitation->volume; ?>" />
	</p>
	</div>
	<div class="cell">
	<p>
		<label style="display:block;" title="Journal or magazine issue; number, month or local season (June, June/July, Summer, Autumn, Fall, etc.)">Issue</label>
		<input class="input_field input120" type="text" id="citations_issue" name="citations[issue]" value="<?php echo $this->oCitation->issue; ?>" />
	</p>
	</div>
	<div class="cell">
	<p>
		<label style="display:block;" title="All page numbers and ranges should be prefixed, as in 'p. 3', 'pp. 243-45', 'pp. 1, 4, 7-8', etc.">Page</label>
		<input class="input_field input120" type="text" style="width:97px;" id="citations_page" name="citations[page]" value="<?php echo $this->oCitation->page; ?>" />
	</p>
	</div>
</div>

<div class="clear"></div>

<p>
	<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
	<input class="input_field" type="text" id="citations_websource" name="citations[websource]" value="<?php echo $this->oCitation->websource; ?>" />
</p>


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
	<label title="Publish this Citation to the open web site (default).">Public</label> <input type="radio" id="citations_share_public" name="citations[share]" value="1" <?php echo $share_public; ?>/>
	<label title="Restrict this Citation to Members-only web pages.">Members</label> <input type="radio" id="citations_share_member" name="citations[share]" value="0" <?php echo $share_member; ?>/>
</p>
	</div>
<?php if ( !empty( $this->oCitation->modified ) && $this->oCitation->modified !== "0000-00-00 00:00:00" ):?>	
	<div class="cell" style="padding-left:15px;">
	<p style="padding-top:7px; font-size: 12px">
		<span title="Date last modified (server date). Updates when saved.">Modified</span> <span><?php echo date( 'j F Y', strtotime( $this->oCitation->modified ) ); ?></span>
	</p>
	</div>
<?php endif; ?>	
</div>
<div class="clear"></div>


<p>
	<label style="display:block;" title="Administrative notes for this record. Never published, usually empty">Notes</label>
	<textarea id="citations_notes" name="citations[notes]" style="width:370px;height:50px;"><?php
	if( !empty( $this->oCitation->notes ) )
	{
		echo $this->oCitation->notes;
	}
	?></textarea>
</p>

<p>
	<label style="display:block;" title="Source document file name. Will not be visible in citations.">Sourcedoc</label>
	<input class="input_field" type="text" id="citations_sourcedoc" name="citations[sourcedoc]" value="<?php echo $this->oCitation->sourcedoc; ?>" />
</p>

	</div>

    <div class="cell" style="padding-left:15px;">
	<p>
	<label title="Verbatim text extracts, any edits marked by an ellipsis […]. Enter Editors’ notes in square brackets, as in [sic.], [Includes link to video], etc.">Content*</label> <a id="content_remove_html" href="#" title="clicking this will remove all html from the content">Remove Html</a><br />
	<textarea id="citations_content" name="citations[content]" style="width:565px;height:427px;"><?php
	if( !empty( $this->oCitation->content ) )
	{
		echo $this->oCitation->content;
	}
	?></textarea>
	</p>

	</div>
</div>
<div class="clear"></div>
	

<div id="tool-tip-box"></div>
<?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
//<![CDATA[	
var check = new  Hash();
check.set( 'city', false );
check.set( 'source', false );
check.set( 'websource', false );
check.set( 'publisher', false );
check.set( 'generic',false );

var tab_order_str="citations_content,citations_published,citations_source,select_citations_city,citations_city,citations_title,citations_author,citations_affiliation,citations_publisher,citations_volume,citations_issue,citations_page,citations_websource,citations_notes,citations_sourcedoc";
	
Event.observe(window,'load',function(){
		
dd = document.body;
Element.extend( dd );


//Allow the left click of the mouse to trigger a new item
$('select_citations_city').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_citations_city', 'citations_city' );
	}
});
//this is required to reset the list options
$('citations_city').observe('focus',function(event){
	$('select_citations_city').selectedIndex = '0';
});

$('select_citations_city').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_citations_city', 'citations_city' );
	}
});


$('content_remove_html').observe('click',function(event)
{
	Event.stop(event);
	clean = $('citations_content' ).getValue().stripTags().stripScripts();
	$('citations_content').update( clean );
});

document.observe("adminFormQCite:clear", function(event) {				
	$('new_record').value ='0';	
	$("citations_notes").value = "";
	$("citations_content").value ="";
	$('adminForm').getInputs('text').each( function(i){ i.value=""; });
	$("select_citations_city").selectedIndex = '0';
	$("ext_id").value = '0';
	$("form_id").value = '0';
	$("message_box").update("");	
	check.each(function(c){
		check.set(c.key, false );
	});
});


$("save_create_another").observe("click",function(event){
	$('new_record').value ='1';
	Event.stop(event);
	if( check_generic() === false )
	{
		return;
	}
	citations_save();
});


if( Object.isElement( $("clear_form") ) )
{
	$("clear_form").observe("click",function(event){
			Event.stop(event);
			this.fire("adminFormQCite:clear");
	});	
}


if( Object.isElement( $("item_publish") ) )
{
	$('item_publish').observe('click',function(event)
	{
		Event.stop(event);
		if( check_generic() === false )
		{
			return;
		}
		$('adminForm_task').value ='save_publish';
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize( true ),
		evalScripts : true
		});
      	return false;
	});
}


function check_generic()
{
	if( check.get('generic') === true )
	{
		return true;
	}
	check.set('generic',true);
	if( check_websource() === false )
	{
		return false;
	}
	if( check_publisher() === false )
	{
		return false;
	}
	return true;
}


function check_websource()
{
	if( check.get('websource') === true )
	{
		return true;
	}
	check.set('websource',true);
	
	var v = $('citations_websource').getValue();
	if( v.empty() )
	{
		alert( 'You\'ve left one or more of these fields empty: Publisher and/or WebSource. If at all possible, please enter data in both of them, most importantly Publisher.' );
		return false;
	}
	return true;
}

function check_publisher()
{
	if( check.get('publisher') === true )
	{
		return true;
	}
	check.set('publisher',true);
	var v = $('citations_publisher').getValue();
	if( v.empty() )
	{
		alert( 'You\'ve left one or more of these fields empty: Publisher and/or WebSource. If at all possible, please enter data in both of them, most importantly Publisher.' );
		return false;
	}
	return true;	
}

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
        inputField     :    "citations_published",     // id of the input field
        ifFormat       :    "%e %B %Y",      // format of the input field
//        ifFormat       :    "%Y-%B-%d",      // format of the input field
//        button         :    "citations_published_img",  // trigger for the calendar (button ID)
        align          :    "Bl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
    
    
    	id = tab_order.first();
		$(id).focus();
	},false);
//]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#citations_published" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>