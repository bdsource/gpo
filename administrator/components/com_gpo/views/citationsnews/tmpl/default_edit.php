<?php
//citationsnews
defined( '_JEXEC' ) or die( 'Restricted Access' );
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

//$filename = JPATH_BASE . '/components/com_gpo/cache/source.txt';
//$select_data['source'] = explode("\r\n",trim( file_get_contents_to_utf8( $filename ) ));
//$filename = JPATH_BASE . '/components/com_gpo/cache/categories.txt';
//$select_data['categories'] = explode("\r\n",trim(file_get_contents( $filename )));

//echo 'iiii';print_r($this->oCitation->published); die();

$select_data['source'] = GpoGetHtmlForType( 'source' );
$select_data['category'] = GpoGetHtmlForType( 'category' );
$date_output = 'j F Y';
//Set display output

//if( $this->oCitation->published === '0000-00-00 00:00:00' || empty( $this->oCitation->published ) ){ $this->oCitation->published=GpoDefaultPublishTime();}
if( $this->oCitation->published === '0000-00-00 00:00:00' || empty( $this->oCitation->published ) ){ $this->oCitation->published=date('j M Y');}
else{ 
   
    }    
    $date = new DateTime($this->oCitation->published);
    $this->oCitation->published = $date->format("j M Y");
    
foreach( $this->oCitation as $key => $value )
{
	if( is_string( $value ) )
	{
            $this->oCitation->$key = htmlspecialchars( $value, ENT_QUOTES );
	}
}
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
	width:120px;
}

.input120{
	width:120px;
}
.clear{
	clear:both;
}

#citations_txt_locations a{padding-left:5px;}
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:13px;}
.location_txt{font-size:13px;}

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
<div style="text-align:center;"><h1 style="font-weight:bold"><?php echo ( $this->isNew ) ? "Create new NCite" : "Edit this NCite"; ?></h1></div>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&format=raw', false ); ?>" id="adminForm" name="adminForm">
    
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="citations" />
<input type="hidden" name="type" value="news" />
<input type="hidden" id="form_id" name="citations[id]" value="<?php echo $this->oCitation->id; ?>" />
<input type="hidden" id="new_record" name="new_record" value="0" />

<div class="row">
	<div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
		<p>
			<label style="display:block;" title="ID number cloned from original News record: cannot be altered. When Published, a unique NCite ID number is added.  Deleted ID numbers cannot be re-used.">News ID*</label>
			<input id="ext_id" class="not-editable" style="width:40px;" type="text" value="<?php echo $this->oCitation->ext_id; ?>" disabled="true" />
		</p>		
	</div>		
	<div class="cell">
		<p>
			<label style="display:block;" for="citations_published" title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Published*</label>
			<input class="input_field published" readonly type="text" id="citations_published" name="citations[published]" value="<?php echo $this->oCitation->published; ?>" />
		</p>
		</div>
</div>

<div class="clear"></div>

<div class="row">
	<div class="cell">
<p>
	<label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Source*</label>
	<select id="select_news_source">
    <option value="">Source</option>
	  <?php foreach($select_data['source'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select> 
		
	<input type="text" id="citations_source" name="citations[source]" style="width:875px;" value="<?php
	if( !empty( $this->oCitation->source ) )
	{
		echo $this->oCitation->source;
	}
	?>" />
	</p>
	</div>
<!--	
	<div class="cell">
	<p>
	<label style="display:block;" title="Place of publication. Usually a single city name suitable for use in a citation, such as Canberra or Geneva">City</label>
	<select id="select_citations_city">
		 <option value="">Select</option>
 <?php 
  foreach($select_data['cities'] as $cat)
  {
  	if( $cat !== $this->oCitation->city )
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
	<input type="text" id="citations_city" name="citations[city]" style="width:90px;" value="<?php
	if( !empty( $this->oCitation->city ) )
	{
		echo $this->oCitation->city;
	}
	?>" />
</p>
	</div>
-->	
</div>

<div class="clear"></div>

	

<div class="row">
	<div class="cell">

<p>
	<label style="display:block;" title="Original, main heading of article, broadcast segment, correspondence, etc., in Title Case. OR: English translation of original foreign-language heading">Title*</label>
	<input class="input_field" type="text" id="citations_title" name="citations[title]" value="<?php echo $this->oCitation->title; ?>" />
</p>

<p>
	<label style="display:block;" title="Any sub title(s), in Sentence case, multiples separated by a colon. OR: foreign-language heading where Title is the English translation of that heading" >Sub Title</label>
	<input class="input_field" type="text" id="citations_subtitle" name="citations[subtitle]" value="<?php echo $this->oCitation->subtitle; ?>" />
</p>

<p>
	<label style="display:block;" title="First and last name of the principal author(s), multiples separated with a comma, and/or 'and'">Byline</label>
	<input class="input_field" type="text" id="citations_byline" name="citations[byline]" value="<?php echo $this->oCitation->byline; ?>" />
</p>

<?php
$selected = $this->oCitation->category;
?>
<p>
	<label style="display:block;" title="Type of article, from drop-down list. Opinion is signed, Editorial is collective, unsigned. Default is a printed news item (empty field)">Category</label>	
	<select id="citations_category" name="citations[category]">
		 <option value="">Select</option>
 <?php 
  foreach($select_data['category'] as $cat)
  {
  	if( $cat !== $this->oCitation->category )
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

<div class="clear"></div>

<p>
	<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource*</label>
	<input class="input_field" type="text" id="citations_websource" name="citations[websource]" value="<?php echo $this->oCitation->websource; ?>" />
</p>

<div class="row">
	<div class="cell">
<?php
if( !isset( $this->oCitation->share )
	|| $this->oCitation->share === '1'
	|| $this->oCitation->share === 1
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
		<label title="Publish to GPO open web site, post to 'public' E-mail lists and RSS feeds (default)." for="citations_share_public">Public</label><input type="radio" id="citations_share_public" name="citations[share]" value="1" <?php echo $share_public; ?>/>
		<label title="Restrict to Members-only web pages, E-mail lists and RSS feeds." for="citations_share_member">Members</label><input type="radio" id="citations_share_member" name="citations[share]" value="0" <?php echo $share_member; ?>/>
	</p>
	</div>
<?php if ( !empty( $this->oCitation->modified ) && $this->oCitation->modified !== "0000-00-00 00:00:00" ):?>	
	<div class="cell" style="padding-left:15px;">
	<p style="padding-top:7px;">
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

	</div>

	<div class="cell rightcol">
	<p>
	<label title="Verbatim text extracts, any edits marked by an ellipsis […]. Enter Editors’ notes in square brackets, as in [sic.], [Includes link to video], etc">Content*</label> <a id="content_remove_html" href="#" title="clicking this will remove all html from the content">Remove Html</a><br />
	<textarea id="citations_content" name="citations[content]" style="width:700px;height:366px;"><?php
	if( !empty( $this->oCitation->content ) )
	{
		echo $this->oCitation->content;
	}
	?></textarea>
	</p>
<!--	
	<p style="text-align:right;">
		<button id="btn_save_citations" class="citations_save">Save and Create Another</button> <button  id="btn_clear_citations" class="clear_form">Clear</button>
	</p>
-->
	</div>
</div>
<div class="clear"></div>
	

<div id="tool-tip-box"></div>
</form>

<script type="text/javascript">
//<![CDATA[	

var tab_order_str="citations_content,citations_published,citations_source,citations_title,citations_subtitle,citations_byline,citations_category,citations_websource,citations_notes,btn_save_citations,btn_clear_citations";
	
Event.observe(window,'load',function(){
	
//Allow the left click of the mouse to trigger a new item
$('select_news_source').observe( 'click', function(event){
	if( this.selectedIndex != '0' )
	{
		select_split( 'select_news_source', 'citations_source' );
	}
});
//this is required to reset the list options
$('citations_source').observe('focus',function(event){
	$('select_news_source').selectedIndex = '0';
});

$('select_news_source').observe( 'keypress', function(event){
	if( event.keyCode == Event.KEY_RETURN )
	{
		select_split( 'select_news_source', 'citations_source' );
	}
});


	
$('content_remove_html').observe('click',function(event)
{
	Event.stop(event);
	clean = $('citations_content' ).getValue().stripTags().stripScripts();
	$('citations_content').update( clean );
});

document.observe("adminFormNCite:clear", function(event) {				
	$('new_record').value ='0';	
	$("citations_notes").value = "";
	$("citations_content").value ="";
	$('adminForm').getInputs('text').each( function(i){ i.value=""; });
	$("ext_id").value = '0';
	$("form_id").value = '0';
	$("message_box").update("");	
//	check.each(function(c){
//		check.set(c.key, false );
//	});
});


$("save_create_another").observe("click",function(event){
	$('new_record').value ='1';
	Event.stop(event);
	citations_save();
});


if( Object.isElement( $("clear_form") ) )
{
	$("clear_form").observe("click",function(event){
			Event.stop(event);
			this.fire("adminFormNCite:clear");
	});	
}


if( Object.isElement( $("item_publish") ) )
{
	
	$('item_publish').observe('click',function(event)
	{
		//	alert('test8');

		Event.stop(event);
		$('adminForm_task').value ='save_publish';
	//	console.log($('adminForm').action);
	//	console.log($('adminForm').serialize( true ));
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize( true ),
		method: 'get',
		evalScripts : true
		});
      	return false;
	});
	
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
		
		$( document.body ).observe('keypress',function(event){
		
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