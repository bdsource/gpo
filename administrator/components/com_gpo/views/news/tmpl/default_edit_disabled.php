<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');

//$filename = JPATH_BASE . '/components/com_gpo/cache/source.txt';
//$select_data['source'] = explode("\r\n",trim(file_get_contents( $filename )));
//$filename = JPATH_BASE . '/components/com_gpo/cache/keywords.txt';
//$select_data['keywords'] = explode("\r\n",trim(file_get_contents( $filename )));
//$filename = JPATH_BASE . '/components/com_gpo/cache/categories.txt';
//$select_data['categories'] = explode("\r\n",trim(file_get_contents( $filename )));


$select_data['source'] = GpoGetHtmlForType( 'source' );
$select_data['keywords'] = GpoGetHtmlForType( 'keywords' );
$select_data['category'] = GpoGetHtmlForType( 'category' );
$select_data['hashtags'] = GpoGetHtmlForType('hashtags');

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
if( $this->oNews->published === '0000-00-00 00:00:00' || empty( $this->oNews->published ) ){ $this->oNews->published=$_SERVER['REQUEST_TIME'] - ( 60*60*24 );}
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
</style>
<div id="message_box"></div>

<?php include_once('submenus_startblock.php'); ?>

<span style="font-size:90%;color:#ff0000;">Changes made to this &quot;Lookup&quot; version of the record cannot be saved. Select &quot;Edit&quot; to alter the record, or &quot;Citation&quot; to copy its data into a new reference record.</span>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="unpublished" />
<input type="hidden" name="controller" value="news" />

<div class="row">
	<div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
		<p>
			<label style="display:block;" title="Unique auto-inserted record number: cannot be altered. A new record shows zero until Published. When a record is deleted, its ID number cannot be re-used">News ID*</label>
			<input style="background-color:#ffffff;width:50px;" type="text" value="<?php echo $this->oNews->id; ?>" disabled="true" />
		</p>		
	</div>	
	<div class="cell">
		<p>
			<label style="display:block;" for="news_published" title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Published</label>
			<input class="input_field published" type="text" readonly="readonly" id="news_published" name="news[published]" value="<?php echo $this->oNews->published; ?>" /><img class="calendar" src="<?php echo JURI::root(true); ?>/templates/system/images/calendar.png" alt="calendar" id="news_published_img" />

			<label title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Location*:</label>
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
			
			<span id="news_txt_locations" style="width:350px;"></span>
		</p>
	</div>
</div>

<div class="clear"></div>
<input type="hidden" id="news_hidden_locations" name="news[locations]" value="" />

<p>
	<label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Source*</label>
	<select id="select_news_source">
    <option value="">Source</option>
	  <?php foreach($select_data['source'] as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select> 
	<input class="input_field" type="text" id="news_source" name="news[source]" style="width:745px;" value="<?php
	if( !empty( $this->oNews->source ) )
	{
		echo $this->oNews->source;
	}
	?>" />
</p>

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
	</select>
        <br/>
        <textarea id="news_keywords" name="news[keywords]" rows="3" cols="50" style="width:370px;"><?php echo $this->oNews->keywords;?></textarea>
</p>

<p>
	<label style="display:block;" title="Original, main heading of article, broadcast segment, correspondence, etc., in Title Case. OR: English translation of original foreign-language heading">Title*</label>
	<input class="input_field" type="text" id="news_title" name="news[title]" value="<?php echo $this->oNews->title; ?>" />
</p>

<p>
	<label style="display:block;" title="Any sub title(s), in Sentence case, multiples separated by a colon. OR: foreign-language heading where Title is the English translation of that heading" >Sub Title</label>
	<input class="input_field" type="text" id="news_sub_title" name="news[subtitle]" value="<?php echo $this->oNews->subtitle; ?>" />
</p>

<p>
	<label style="display:block;" title="First, then last name of the principal author(s), multiples separated with a comma, and/or 'and.' No periods after initials, usually no rank, title, etc. ">Byline</label>
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
	<label style="display:block;" title="Explanatory GPN headline as posted to Gun Policy News, in Title Case">GpnHeader*</label>
	<input class="input_field" style="width:370px;" type="text" id="news_gpnheader" name="news[gpnheader]" value="<?php echo $this->oNews->gpnheader; ?>" />
</p>


<div class="row">
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
			<label title="Publish to GPO open web site, post to ‘public’ E-mail lists and RSS feeds (default)">Public</label> <input type="radio" id="news_share_public" name="news[share]" value="0" <?php echo $share_public; ?>/>
			<label title="Restrict to Members-only web pages, E-mail lists and RSS feeds">Members</label> <input type="radio" id="news_share_member" name="news[share]" value="1" <?php echo $share_member; ?> />
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

        <!-- Start Twitter Field Show -->
        <p>
            <label style="display:block;">Twitter Text </label>
            <input class="input_field" type="text" id="news_twitter_text" name="news[twitter_text]" value="<?php echo $this->oNews->twitter_text; ?>" />
        </p>
        <p>
            <label style="display:block;">Twitter URL</label>
            <input class="input_field" type="text" id="news_twitter_url" name="news[twitter_url]" value="<?php echo $this->oNews->twitter_url; ?>" />
        </p>

        <div class="row">
            <div class="cell">
                <p>
                    <label style="display:block;" title="Name of publication or medium in which the text appeared, or the news agency credited. Rewrites may note primary source, as in 'Chicago Tribune / AP'">Hashtags</label>


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
                    <input class="input_field" style="width:147px;" type="text" id="news_hashtags" name="news[twitter_hashtag]" value="<?php
                    if( !empty( $this->oNews->twitter_hashtag ) )
                    {
                        echo $this->oNews->twitter_hashtag;
                    }else{
                        foreach($select_data['hashtags'] as $hashtags){
                            if(preg_match('/.*\*$/i', $hashtags)){
                                echo str_replace("*",'',$hashtags). ' ';
                            }
                        }
                    }
                    ?>" />
                </p>
            </div>
        </div>
        <div class="clear"></div>
        <!-- End Twitter Field Show -->

<p>
    <label  title="The NCite number of any pop-up Citations created from this News record is automatically entered here">NCite(s)
	<input type="text" id="ncite" style="width:314px" class="not-editable" name="news[citation]" value="<?php echo $this->news_cited; ?>" readonly />
    </label>
</p>
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

	<div class="cell" style="padding-left:15px;">
	<p>
	<label title=" Verbatim text extracts, any edits marked by an ellipsis […]. Enter Editors' notes in square brackets, as in [sic.], [Includes link to video], etc.">Content</label><br />
	<textarea id="news_content" name="news[content]" style="width:565px;height:790px;"><?php
	if( !empty( $this->oNews->content ) )
	{
		echo $this->oNews->content;
	}
	?></textarea>
	</p>
	
	</div>
</div>
<div class="clear"></div>
	


</form>

<?php include_once('submenus_endblock.php'); ?>

<script type="text/javascript">
//<![CDATA[	

var tab_order_str="news_content,news_published,select_news_country,select_news_region,select_news_source,news_source,select_news_keywords,news_keywords,news_title,news_sub_title,news_byline,news_category,news_websource,news_gpnheader,news_notes,btn_save_news,btn_clear_news";
var locations = [];
<?php if( count( $this->oNews->locations ) > 0 ): ?>
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
	

$('adminForm').select("a").each(function(el){
	el.observe('click',function(event)
	{
		Event.stop(event);
		alert( 'Edit is disabled');
	})
});

//]]>
</script>
