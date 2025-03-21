<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jinput = JFactory::getApplication()->input;
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

//$filename = JPATH_BASE . '/components/com_gpo/cache/source.txt';
//$select_data['source'] = explode("\r\n",trim( file_get_contents_to_utf8( $filename ) ));


//$filename = JPATH_BASE . '/components/com_gpo/cache/keywords.txt';
//$select_data['keywords'] = explode("\r\n",trim(file_get_contents( $filename )));


//$filename = JPATH_BASE . '/components/com_gpo/cache/categories.txt';
//$select_data['category'] = explode("\r\n",trim(file_get_contents( $filename )));

$select_data['source']   = GpoGetHtmlForType( 'source' );
$select_data['hashtags'] = GpoGetHtmlForType('hashtags');
$select_data['keywords'] = GpoGetHtmlForType('keywords');
$select_data['category'] = GpoGetHtmlForType('category');

$createTask  = $jinput->getVar('task');
$contentRows = 43;

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


$date_output = 'j F Y';
//Set display output
if( $this->oNews->published === '0000-00-00 00:00:00' || empty( $this->oNews->published ) ){ $this->oNews->published=GpoDefaultPublishTime();}
else{ $this->oNews->published = strtotime( $this->oNews->published ); }
$this->oNews->published = date( $date_output, $this->oNews->published );

foreach( $this->oNews as $key => $value )
{
	if( $key !== 'locations' && is_string( $value ) )
	{
		$this->oNews->$key = htmlspecialchars( $value, ENT_QUOTES );
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

// Twitter Total Post Character Checking
function updateDetails() {
    var total1 = document.getElementById('news_twitter_text').value.length;
    var total2 = document.getElementById('news_twitter_url').value.length;
    var total3 = document.getElementById('news_hashtags').value.length;
    var twitterUrlPlaceholder = document.getElementById('news_twitter_url').placeholder.length;

    <?php if($createTask == 'create'){ ?>
        if(!total2) {
            total2 = 15;
        }
        var grandTotal = total1+total2+total3+2+1;
    <?php } else { ?>
        var grandTotal = total1+total2+total3+2+1; // 2 = space beetween 3 sentence; 1 = twitter required for bitly url
    <?php } ?>

    if (grandTotal < 140) {
        document.getElementById('twitter_text_check').innerHTML = '<span style="color: #20851e;"><strong>Total : '+grandTotal+' characters (max 140)</strong></span>';
    } else {
        document.getElementById('twitter_text_check').innerHTML = '<span style="color: #fa0023; font-weight: bold;"><strong>Total : '+grandTotal+' characters (max 140)</strong></span>';
    }
}

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
	width:120px;
}

.clear{
	clear:both;
}
#news_txt_locations a{padding-left:5px;}
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:13px;}
.location_txt{font-size:13px;}

#news_published{text-align:center;}

.not-editable{
	background-color: #e9e9e9;
}
#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}

</style>
<div id="message_box"></div>
<div style="text-align:center;"><h1 id="form-header" style="font-weight:bold"><?php 	echo ( $this->isNew ) ? "Create new News Item" : "Edit this News Item"; ?></h1></div>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="news" />
<input type="hidden" id="form_id" name="news[id]" value="<?php echo $this->oNews->id; ?>" />
<input type="hidden" id="news_live_id" name="news[live_id]" value="<?php echo $this->oNews->live_id; ?>" />
<input type="hidden" id="new_record" name="new_record" value="0" />


<div class="row">
	<div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
		<p>
			<label style="display:block;" title="Unique auto-inserted record number: cannot be altered. A new record shows zero until Published. When a record is deleted, its ID number cannot be re-used.">News ID*</label>
			<input class="not-editable" style="width:40px;" id="ext_id" type="text" value="<?php echo $this->oNews->live_id; ?>" disabled="true" />
		</p>		
	</div>
	
	<div class="cell">
		<p>
			<label style="display:block;" for="news_published" title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Published*</label>
			<input class="input_field published" type="text" readonly="readonly" id="news_published" name="news[published]" value="<?php echo $this->oNews->published; ?>" />
		</p>
	</div>
	
	<div class="cell">
		<p>
			<label style="display:block;" id="news_locations_label" title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Location*:</label>
			<select id="select_news_country" name="select_news_country">
			<option value="">Country</option>
			<?php foreach($select_data['country'] as $cat) :
//to deal with the format the text file is in
	  	$value = trim(str_replace("&nbsp;",'',$cat ));
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
	<label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Source*</label>
	

  <select id="select_news_source">
    <option value="">Source</option>
	  <?php foreach($select_data['source'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select> 
	<input class="input_field" type="text" id="news_source" name="news[source]" style="width:875px;" value="<?php
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
	<label style="display:block;" title="Descriptive keywords chosen from GPO thesaurus drop-down list, separated by commas, in Title Case">Keywords*</label>
	<select id="select_news_keywords" >
    <option value="">Select</option>
  <?php foreach($select_data['keywords'] as $cat) echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
?>
	</select><br/>
	<textarea id="news_keywords" name="news[keywords]" rows="3" cols="50" style="width:370px;"><?php echo @$this->oNews->keywords;?></textarea>
</p>

<p>
	<label style="display:block;" title="Original, main heading of article, broadcast segment, correspondence, etc., in Title Case. OR: English translation of original foreign-language heading">Title* <a href="#" id="title_ucwords" title="Change all text to Title Case">Title Case</a></label>
	<input class="input_field" type="text" id="news_title" name="news[title]" value="<?php echo $this->oNews->title; ?>" />
</p>

<p>
	<label style="display:block;" title="Any sub title(s), in Sentence case, multiples separated by a colon. OR: foreign-language heading where Title is the English translation of that heading" >Sub Title <a href="#" id="subtitle_ucwords" title="Change all text to Sentence case (Check for errors)">Sentence case</a></label>
	<input class="input_field" type="text" id="news_sub_title" name="news[subtitle]" value="<?php echo $this->oNews->subtitle; ?>" />
</p>

<p>
	<label style="display:block;" title="First, then last name of the principal author(s), multiples separated with a comma, and/or 'and.' No periods after initials, usually no rank, title, etc. ">Byline <a href="#" id="byline_ucwords" title="Change all text to Title Case">Title Case</a></label>
	<input class="input_field" type="text" id="news_byline" name="news[byline]" value="<?php echo $this->oNews->byline; ?>" />
</p>

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

<p>
	<label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource*</label>
	<input class="input_field" style="width:370px;" type="text" id="news_websource" name="news[websource]" value="<?php echo $this->oNews->websource; ?>" />
</p>

<p>
	<label style="display:block;" title="Explanatory GPN headline as posted to Gun Policy News, in Title Case. Header must not exceed length of text entry box.">GpnHeader* <a href="#" id="gpnheader_ucwords" title="Change all text to Title Case">Title Case</a></label>
	<input class="input_field" style="width:370px;" type="text" id="news_gpnheader" name="news[gpnheader]" value="<?php echo $this->oNews->gpnheader; ?>" />
</p>


<div class="row">
	<div class="cell">
<?php 
if( !isset( $this->oNews->share )
	|| $this->oNews->share === '1'
	|| $this->oNews->share === 1
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
			<label title="Publish to GPO open web site, post to ‘public’ E-mail lists and RSS feeds (default)">Public</label> <input type="radio" id="news_share_public" name="news[share]" value="1" <?php echo $share_public; ?>/>
			<label title="Restrict to Members-only web pages, E-mail lists and RSS feeds">Members</label> <input type="radio" id="news_share_member" name="news[share]" value="0" <?php echo $share_member; ?> />
		</p>
	</div>

<?php if ( !empty( $this->oNews->modified ) && $this->oNews->modified !== "0000-00-00 00:00:00" ):?>	
	<div class="cell" style="padding-left:15px;">
		<p style="padding-top:7px;">
			<span title="Date last modified (server date). Updates when saved.">Modified</span> <span><?php echo date( 'j F Y', strtotime( $this->oNews->modified ) ); ?></span>
		</p>
	</div>
<?php endif; ?>	
</div>
<div class="clear"></div>
<p>
    <label  title="The NCite number of any pop-up Citations created from this News record is automatically entered here">NCite(s)
	<input type="text" id="qcite" style="width:314px" class="not-editable" name="news[citation]" value="<?php if (isset ($this->news_cited)) echo $this->news_cited; ?>" readonly />
    </label>
</p>
        <!--
        <?php //if($createTask != 'create') {?>
        <script>
            var formType = '<?php //echo $createTask; ?>';
        </script>
-->
        <p>
            <label style="display:block;" title="When added to a URL and any hashtags, the entire Twitter post cannot exceed 140 characters, with spaces. If this field is left empty, no Tweet will be posted.">Twitter Text* <a href="#" id="twitter_text" title="Change all text to Title Case">Title Case  </a> <a href="#" id="twitter_text_check" title="Check If twitter text is not more than 140 characters."></a></label>
            <input onkeydown="updateDetails();" onkeyup="updateDetails();" class="input_field" type="text" id="news_twitter_text" name="news[twitter_text]" value="<?php echo $this->oNews->twitter_text; ?>" />
        </p>
        <p>
            <label style="display:block;" title="URL shortened with bit.ly or similar">Twitter URL</label>
            <input readonly="readonly" onkeydown="updateDetails();" onkeyup="updateDetails();" class="input_field" type="text" id="news_twitter_url" name="news[twitter_url]" value="<?php echo $twitter_url = ($this->oNews->twitter_url) ? $this->oNews->twitter_url : $this->news_bitly_url; ?>" />
        </p>

        <div class="row">
            <div class="cell">
                <p>
                    <!--<label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Hashtags</label>-->
                    <label style="display:block;" title="Edit, add or delete tags at  Components/Super Admin/Lists/Default Hashtags">Hashtags</label>

                    <select id="select_news_hashtags">
                        <option value="">Hashtags</option>
                        <?php foreach($select_data['hashtags'] as $hashtags) {
                            if(preg_match('/.*\*$/i', $hashtags)){
                                $hashtagsVal = str_replace("*",'',$hashtags). ' ';
                            }else {
                                $hashtagsVal = $hashtags;
                            }
                            echo <<<EOB
                                 <option value="{$hashtagsVal}">{$hashtags}</option>
EOB;
                        }
                        ?>
                    </select>
                    <input class="input_field" style="width:147px;" onkeydown="updateDetails();" onkeyup="updateDetails();" type="text" id="news_hashtags" name="news[twitter_hashtag]" style="width:300px;" value="<?php
                    if( !empty( $this->oNews->twitter_hashtag ) )
                    {
                        echo $this->oNews->twitter_hashtag;
                    }else{
                        $hash = '';
                        foreach($select_data['hashtags'] as $hashtags){
                            if(preg_match('/.*\*$/i', $hashtags)){
                                $hash .= str_replace("*",'',$hashtags). ' ';
                            }
                        }
                        echo trim($hash);
                    }
                    ?>" />
                </p>
            </div>
        </div>
        <div class="clear"></div>
        <?php if($createTask !='create'){?>
            <script>
                var formType = '<?php echo $createTask; ?>';
            </script>
        <?php }else { ?>
            <script>
                var formType = '<?php echo $createTask; ?>';
            </script>
            <input type="hidden" name="newItem" value="newItem" />
            <?php $contentRows = 25;?>
        <?php } ?>
<p>

	<label style="display:block;" title="Administrative notes for this record. Never published, usually empty">Notes</label>
	<textarea id="news_notes" name="news[notes]" style="width:370px;height:50px;"><?php
	if( !empty( $this->oNews->notes ) )
	{
		echo $this->oNews->notes;
	}
	?></textarea>
</p>
	</div>

	<div class="cell rightcol">
	<p>
	<label title="Verbatim text extracts, any edits marked by an ellipsis […]. Enter Editors' notes in square brackets, as in [sic.], [Includes link to video], etc.">Content*</label> 
<!--
<a id="content_remove_html" href="#" title="clicking this will remove all html from the content">Remove Html</a>
-->
	<br />
	<textarea id="news_content" name="news[content]" style="width:700px;height:790px;"><?php
	if( !empty( $this->oNews->content ) )
	{
		echo $this->oNews->content;
	}
	?></textarea>
	</p>

	<p style="text-align:right;">
<!--		
		<button id="btn_save_news" class="news_save">Save</button> 
-->
		<button  id="clear_form" class="clear_form">Clear</button>
	</p>

	</div>
</div>
<div class="clear"></div>

<div id="tool-tip-box"></div>
</form>
<script type="text/javascript">
	 jQuery( function() {
    jQuery( "#news_published" ).datepicker({ 
    	dateFormat: 'dd MM yy',
    	changeMonth: true, 
    	changeYear: true, 
    	yearRange: "-90:+00"
    });
  } );
</script>
<script type="text/javascript">
// userAllow for allow user to save data after warning twitter message.
var userAllow = 0;

//<![CDATA[	
var check = new  Hash();
var tab_order_str="news_content,news_published,select_news_country,select_news_region,select_news_source,news_source,select_news_keywords,news_keywords,news_title,news_sub_title,news_byline,news_category,news_websource,news_gpnheader,news_notes";
var locations = [];
<?php
    
    if( count( $this->oNews->locations ) > 0 ): ?>
var current_locations = '<?php
//tidy up for the json 
	$data = json_encode( $this->oNews->locations );
	$data = str_replace("'","\'",$data);
	echo $data;
?>';
<?php  else: ?>
var current_locations = null;	
<?php
	 endif;
?>

location_populate();
	
Event.observe(window,'load',function(){

dd = document.body;
Element.extend( dd );


//Allow the left click of the mouse to trigger a new item
$('select_news_source').observe( 'change', function(event){
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

//Allow the left click of the mouse to trigger a new item
//    if(formType !='create'){
        $('select_news_hashtags').observe( 'change', function(event){
            if( this.selectedIndex != '0' )
            {
                select_split_by_space( 'select_news_hashtags', 'news_hashtags' );
            }
        });
//    }
//this is required to reset the list options
//    if(formType !='create'){
        $('news_hashtags').observe('focus',function(event){
            $('select_news_hashtags').selectedIndex = '0';
        });
//    }
//    if(formType !='create'){
        $('select_news_hashtags').observe( 'keypress', function(event){
            if( event.keyCode == Event.KEY_RETURN )
            {
                select_split_by_space( 'select_news_hashtags', 'news_hashtags' );
            }
        });
//    }


    function checkTwitter(){

        twitter_text = $('news_twitter_text').getValue();
        twitter_url  = $('news_twitter_url').getValue();

        if(twitter_url.empty()){
            twitter_url_length = 15;
        }else{
            twitter_url_length = twitter_url.length;
        }

        twitter_hashtags = $('news_hashtags').getValue();

        tweet = (twitter_text.length + twitter_url_length  + twitter_hashtags.length + 2 + 1);

        if(tweet > 140){
            count_confirm = confirm("Tweet exceeds maximum length. Shorten text in one or more Twitter fields to total 140 characters or less, including spaces. You already entered " + tweet);
            return false;
        }

        userAllow +=1;
        if(userAllow>1){return true;}


        text_confirm = true;
        url_hashtag_confirm = true;
        count_confirm = true;

        if(formType !='create'){
            if(!(twitter_text.empty())){
                if( twitter_hashtags.empty() ){
                    url_hashtag_confirm = confirm('Please check, Twitter Hashtags field is empty!');
                    return url_hashtag_confirm;
                }
            }
        }
        return true;
    }


//country - mouse click & key = return
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
//region - mouse click & key = return
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

$('title_ucwords').observe('click',function(event)
{
	Event.stop(event);
	str = $('news_title').getValue();
	str = str.toLowerCase();
	str = (str+'').replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase( );} );
	$('news_title').value=str;
});

$('byline_ucwords').observe('click',function(event)
{
	Event.stop(event);
	str = $('news_byline').getValue();
	str = str.toLowerCase();
	str = (str+'').replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase( );} );
	$('news_byline').value=str;
});

$('subtitle_ucwords').observe('click',function(event)
{
	Event.stop(event);
	str = $('news_sub_title').getValue();
	str = str.toLowerCase();	
	str += '';
    var f = str.charAt(0).toUpperCase();
    nstr = f + str.substr(1);
	$('news_sub_title').value=nstr;
});

$('gpnheader_ucwords').observe('click',function(event)
{
	Event.stop(event);
	str = $('news_gpnheader').getValue();
	str = str.toLowerCase();
	str = (str+'').replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase( );} );
	$('news_gpnheader').value=str;
});

//$('content_remove_html').observe('click',function(event)
//{
//	Event.stop(event);
//	clean = $('news_content' ).getValue().stripTags().stripScripts();
//	$('news_content').update( clean );
//});

$("save_create_another").observe("click",function(event) {
	$('new_record').value ='1';
	Event.stop(event);
	news_save();
    $('news_twitter_url').value = ''; //clear the twitter url for cloned records
});

$("item_saveAndCloneToQuotes").observe("click",function(event) {
      //$('new_record').value ='1';
      Event.stop(event);
      filter_save_and_clone_news_to_quotes();
});

document.observe("adminFormNews:clone", function(event) {
	$("news_content").value ="";
	$('form-header').update('Create New News Item - <span style="color:#ff0000;">Cloned Copy</span>');
	$('form_id').value='0';
	
	$("ext_id").value = '0';	
	$("news_live_id").value = '0';
		
	$("message_box").update("Saved");
	$('new_record').value ='0';	
	check.each(function(c){
		check.set(c.key, false );
	});	
});

document.observe("adminFormNews:clear", function(event) {	
	$('new_record').value ='0';	
	$("news_txt_locations").update("");
	$("news_notes").value = "";
	$("news_content").value ="";
	$('news_hidden_locations' ).value = "";			
	$('adminForm').getInputs('text').each( function(i){ i.value=""; });

	$("news_category").selectedIndex = 0;	
	
	$("ext_id").value = '0';
	$("news_live_id").value = '0';
	$("form_id").value = '0';
	locations = [];
	$("form-header").update("Create New News Item");
	$("message_box").update("");
	

	check.each(function(c){
		check.set(c.key, false );
	});
});


if( Object.isElement( $("clear_form") ) )
{
	$("clear_form").observe("click",function(event){
		Event.stop(event);		
		this.fire("adminFormNews:clear");
	});	
}


if( Object.isElement( $('item_publish') ) )
{
	$('item_publish').observe('click',function(event)
	{
		Event.stop(event);
//        if(formType !='create'){
            if(checkTwitter() === false){
                return false;
            }
//        }
		$('news_hidden_locations' ).value = locations.compact().uniq().join(',');
		$('adminForm_task').value ='save_publish'; 
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize( true ),
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
    inputField     :    "news_published",     // id of the input field
    ifFormat       :    "%e %B %Y",      // format of the input field
    align          :    "Bl",           // alignment (defaults to "Bl")
    singleClick    :    true
});
id = tab_order.first();
$(id).focus();


},false);
//]]>
</script>
