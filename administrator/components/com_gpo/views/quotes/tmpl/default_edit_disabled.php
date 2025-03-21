<?php
defined('_JEXEC') or die('Restricted Access');
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');
//$filename = JPATH_BASE . '/components/com_gpo/cache/quotes_keywords.txt';
//$select_data['keywords'] = explode("\r\n",trim(file_get_contents( $filename )));
//$select_data['cities'] = GpoGetHtmlOptionCities();

$select_data['keywords'] = GpoGetHtmlForType('keywords');
$select_data['city'] = GpoGetHtmlForType('city');


$filename = JPATH_BASE . '/components/com_gpo/cache/admin_region.txt';
if (!file_exists($filename)) {
    echo 'Error: Remember to create your region list';
    return;
}
$select_data['region'] = explode("\n", trim(file_get_contents($filename)));

$filename = JPATH_BASE . '/components/com_gpo/cache/admin_country.txt';
if (!file_exists($filename)) {
    echo 'Error: Remember to create your country list';
    return;
}
$data = trim(file_get_contents($filename));
$select_data['country'] = explode("\n", $data);


$date_output = 'j F Y';
//Set display output
if ($this->oQuote->published === '0000-00-00 00:00:00' || empty($this->oQuote->published)) {
    $this->oQuote->published = $_SERVER['REQUEST_TIME'];
}
else {
    $this->oQuote->published = strtotime($this->oQuote->published);
}
$this->oQuote->published = date($date_output, $this->oQuote->published);

foreach ($this->oQuote as $key => $value)
{
    if ($key !== 'locations' && is_string($value)) {
        $this->oQuote->$key = htmlspecialchars($value, ENT_QUOTES);
    }
}
?>

<script>
    Calendar._DN = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    Calendar._SDN = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
    Calendar._FD = 0;
    Calendar._MN = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    Calendar._SMN = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    Calendar._TT = {};
    Calendar._TT["INFO"] = "About the Calendar";
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

    Calendar._TT["PREV_YEAR"] = "Click to move to the previous year. Click and hold for a list of years.";
    Calendar._TT["PREV_MONTH"] = "Click to move to the previous month. Click and hold for a list of the months.";
    Calendar._TT["GO_TODAY"] = "Go to today";
    Calendar._TT["NEXT_MONTH"] = "Click to move to the next month. Click and hold for a list of the months.";
    Calendar._TT["NEXT_YEAR"] = "Click to move to the next year. Click and hold for a list of years.";
    Calendar._TT["SEL_DATE"] = "Select a date.";
    Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
    Calendar._TT["PART_TODAY"] = " (Today)";
    Calendar._TT["DAY_FIRST"] = "Display %s first";
    Calendar._TT["WEEKEND"] = "0,6";
    Calendar._TT["CLOSE"] = "Close";
    Calendar._TT["TODAY"] = "Today";
    Calendar._TT["TIME_PART"] = "(Shift-)Click or Drag to change the value.";
    Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%D";
    Calendar._TT["TT_DATE_FORMAT"] = "%A, %B %e";
    Calendar._TT["WK"] = "wk";
    Calendar._TT["TIME"] = "Time:";
</script>

<style>
    .error_warning {
        color: #ff0000;
    }

    #message_box a {
        display: block;
    }

    #adminForm {
        background-color: #FF9966;
        padding: 10px;
    }

        /*#adminForm p label{display:block;padding-right:10px;}*/
    .input_field {
        width: 370px;
    }

    .clear {
        float: none;
    }

    .row {
        clear: both;
        display: block;
        padding: 0px;
        margin: 0px auto;
    }

    .cell {
        float: left;
        padding: 0px;
        margin: 0px auto;
    }

    .cell label {
        padding: 0px;
        margin: 0px auto;
        display: inline;
    }

    .published {
        width: 100px;
    }

    .input120 {
        width: 120px;
    }

    .clear {
        clear: both;
    }

    .not-editable {
        background-color: #e9e9e9;
    }

    #quotes_txt_locations a {
        padding-left: 5px;
    }

    #adminForm p {
        margin: 1px auto;
        line-height: 15px;
        padding: 1px;
        font-size: 8px;
    }

    .location_txt {
        font-size: larger;
    }

    #quotes_published {
        text-align: center;
    }

    #tool-tip-box {
        width: 250px;
        border: 1px solid #cccccc;
        background-color: #ccff99;
        color: #000000;
    }
</style>
<div id="message_box"></div>
<?php include_once('submenus_startblock.php'); ?>
<span style="font-size:90%;color:#ff0000;">Changes made to this &quot;Lookup&quot; version of the record cannot be saved. Select &quot;Edit&quot; to alter the record, or &quot;Citation&quot; to copy its data into a new reference record.</span>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo"/>
<input type="hidden" id="adminForm_task" name="task" value=""/>
<input type="hidden" name="controller" value="quotes"/>
<input type="hidden" name="quotes[id]" value="<?php echo $this->oQuote->id; ?>"/>
<input type="hidden" id="new_record" name="new_record" value="0"/>

<div class="row">
    <div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
        <p>
            <label style="display:block;" title="Unique Quotes record number. Cannot be altered.">Quote ID*</label>
            <input class="not-editable" type="text" value="<?php echo $this->oQuote->id; ?>"
                   disabled="true"/>
        </p>
    </div>
    <div class="cell">
        <p>
            <label style="display:block;" for="quotes_published"
                   title="Date of publication. Book, 2008: 1/1/08. Aug/Sep 2008 Issue: 1/8/08. Early copy: date received, plus 'June issue,' 'Summer issue,' etc. in Notes">Published*</label>
            <input class="input_field published" type="text" readonly id="quotes_published" name="quotes[published]"
                   value="<?php echo $this->oQuote->published; ?>"/>
        </p>
    </div>

    <div class="cell">
        <p>
            <label id="quotes_locations_label" style="display:block;"
                   title="Country and/or region name(s) from the drop-down list, as in: 'Fiji' or 'Fiji, Oceania' or 'Brazil, Uruguay, United Nations, World'">Location*</label>
            <select id="select_quotes_country">
                <option value="">Country</option>
                <?php foreach ($select_data['country'] as $cat) :
                //to deal with the format the text file is in
	  	$value = str_replace("&nbsp;", '', $cat);
	 	echo <<<EOB
      <option value="{$value}">{$cat}</option>
EOB;
		endforeach;
                ?>
            </select>
            <select id="select_quotes_region">
                <option value="">Region</option>
                <?php foreach ($select_data['region'] as $cat) :
                //to deal with the format the text file is in
	  	$value = str_replace("&nbsp;", '', $cat);
	 	echo <<<EOB
      <option value="{$value}">{$cat}</option>
EOB;
		endforeach;
                ?>
            </select>

            <span id="quotes_txt_locations"></span>
            <input type="hidden" id="quotes_hidden_locations" name="quotes[locations]" value=""/>
        </p>
    </div>
</div>

<div class="clear"></div>

<div class="row">
    <div class="cell">
        <p>
            <label style="display:block;"
                   title="Name of book, journal, publication or series title, as in: 'Small Arms Survey 2004: Rights at Risk', 'British Medical Journal', or 'SIPRI Backgound Paper'.">Source</label>
            <input type="text" id="quotes_source" name="quotes[source]" style="width:620px;" value="<?php
    if (!empty($this->oQuote->source)) {
                echo $this->oQuote->source;
            }
            ?>"/>

        </p>
    </div>

    <div class="cell">
        <p>
            <label style="display:block;"
                   title="Place of publication. Usually a single city name suitable for use in a citation, such as Canberra or Geneva">City</label>
            <select id="select_quotes_city">
                <option value="">Select</option>
<?php
                foreach ($select_data['city'] as $cat)
            {
                if ($cat !== $this->oQuote->city) {
                    $selected = '';
                } else {
                    $selected = 'selected="selected"';
                }
                echo <<<EOB
      <option value="$cat" $selected>$cat</option>
EOB;

            }
                ?>
            </select>
            <input type="text" id="quotes_city" name="quotes[city]" style="width:108px;" value="<?php
    if (!empty($this->oQuote->city)) {
                echo $this->oQuote->city;
            }
            ?>"/>
        </p>
    </div>
</div>

<div class="clear"></div>


<div class="row">
    <div class="cell">

        <p>
            <label style="display:block;"
                   title="Original, main heading of article, broadcast segment, correspondence, etc., in Title Case. OR: English translation of original foreign-language heading">Title*</label>
            <input class="input_field" type="text" id="quotes_title" name="quotes[title]"
                   value="<?php echo $this->oQuote->title; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Last, then first name of the principal author; subsequent author(s), separated with a semi-colon. No periods after initials, usually no rank, title, etc.">Author*</label>
            <input class="input_field" type="text" id="quotes_author" name="quotes[author]"
                   value="<?php echo $this->oQuote->author; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Redundant Affiliation data from Quotes. Must be copied to another field, or deleted.">Affiliation</label>
            <input class="input_field not-editable" type="text" id="quotes_affiliation" name="quotes[affiliation]"
                   value="<?php echo $this->oQuote->affiliation; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Institution or individual responsible for publication. Should not duplicate Author or Source">Publisher</label>
            <input class="input_field" type="text" id="quotes_publisher" name="quotes[publisher]"
                   value="<?php echo $this->oQuote->publisher; ?>"/>
        </p>


        <div class="row">
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Volume or chapter in which the cited quotation appeared">Volume</label>
                    <input class="input_field input120" type="text" id="quotes_volume" name="quotes[volume]"
                           value="<?php echo $this->oQuote->volume; ?>"/>
                </p>
            </div>
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Journal or magazine issue; number, month or local season (June, June/July, Summer, Autumn, Fall, etc.)">Issue</label>
                    <input class="input_field input120" type="text" id="quotes_issue" name="quotes[issue]"
                           value="<?php echo $this->oQuote->issue; ?>"/>
                </p>
            </div>
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Page number(s) or page range(s): 142-45; 8, 12, 32-38">Page</label>
                    <input class="input_field input120" type="text" id="quotes_page" name="quotes[page]"
                           value="<?php echo $this->oQuote->page; ?>"style="width:96px"/>
                </p>
            </div>
        </div>

        <div class="clear"></div>
        <div class="row">
            <div class="cell">
                <p>
                    <label
                           title="Descriptive keywords chosen from GPO thesaurus drop-down list, separated by commas, in Title Case">Keywords*</label><br/>
                    <select id="select_quotes_keywords">
                        <option value="">Select</option>
                        <?php foreach ($select_data['keywords'] as $cat) echo <<<EOB
      <option value="$cat">$cat</option>
EOB;
                        ?>
                    </select>
                </p>
            </div>
            <div class="cell">
                <p>
                    <label for="quotes_poaim"
                           title="Enter any relevant clause number(s) from the PoAIM protocol, comma separated.">PoAIM</label><br/>
                    <input type="text" id="quotes_poaim" name="quotes[poaim]" value="<?php echo $this->oQuote->poaim;?>"
                           style="width:146px"/>
                    <br/>


                </p>
            </div>

        </div>
        <div class="row">
            <textarea id="quotes_keywords" name="quotes[keywords]" rows="3" cols="50"
                      style="width:370px;"><?php echo $this->oQuote->keywords;?></textarea>
        </div>
        <p>
            <label style="display:block;"
                   title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
            <input class="input_field" type="text" id="quotes_websource" name="quotes[websource]"
                   value="<?php echo $this->oQuote->websource; ?>"/>
        </p>

         <p>
            <label style="display:block;"
                   title=" Source document file name. Will not be saved to Citations.">SourceDoc</label>
            <input class="input_field" type="text" id="quotes_sourcedoc" name="quotes[sourcedoc]"
                   value="<?php echo $this->oQuote->sourcedoc; ?>"/>
        </p>

        <div class="row">
            <div class="cell">
<?php
                if (!isset($this->oQuote->share)
                    || $this->oQuote->share === '1'
                    || $this->oQuote->share === ""
) {
    $share_public = 'checked="true"';
    $share_member = '';
} else {
    $share_public = '';
    $share_member = 'checked="true"';
}
    ?>
    <p>
        <label title="Publish to GPO open web site." for="quotes_share_public">Public</label><input
            type="radio" id="quotes_share_public" name="quotes[share]"
            value="1" <?php echo $share_public; ?>/>
        <label title="Restrict to Members-only web pages (default)."
               for="quotes_share_member">Members</label><input type="radio" id="quotes_share_member"
                                                               name="quotes[share]"
                                                               value="0" <?php echo $share_member; ?>/>
    </p>
            </div>
            <?php if (!empty($this->oQuote->modified) && $this->oQuote->modified !== "0000-00-00 00:00:00"): ?>
            <div class="cell" style="padding-left:15px;">
                <p style="padding-top:7px;">
                    <span title="Date last modified (server date). Updates when saved.">Modified*</span>
                    <span><?php echo date('j F Y', strtotime($this->oQuote->modified)); ?></span>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
        <p>
            <label title="Select Senior Editor to review this after you">Staff
                <input type="text" id="staffs" name="quotes[staff]" value="<?php echo $this->oQuote->staff; ?>"
                       style="width:340px"/>
            </label>
        </p>

        <p>
            <label title="The QCite number of any pop-up Citations created from this Quote record is automatically entered here">QCite(s)
                <input type="text" id="qcite" style="width:315px" class="not-editable" name="quotes[citation]"
                       value="<?php echo $this->quotes_cited; ?>" readonly/>
            </label>
        </p>

        <p>
            <label style="display:block;" title="Administrative notes for this record. Never published, usually empty">Notes</label>
            <textarea id="quotes_notes" name="quotes[notes]" style="width:370px;height:30px;"><?php
    if (!empty($this->oQuote->notes)) {
                echo $this->oQuote->notes;
            }
                ?></textarea>
        </p>
    </div>

    <div class="cell" style="padding-left:15px;">
        <p>
            <label title="Verbatim body text of the article, omitting text entered in other fields. Mark edits with ellipsis ( … ). Notes in square brackets, e.g. [Table in the original]">Content*</label><br/>
            <textarea id="quotes_content" name="quotes[content]" style="width:565px;height:699px;"><?php
    if (!empty($this->oQuote->content)) {
                echo $this->oQuote->content;
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

    var tab_order_str = "quotes_content,quotes_published,select_quotes_country,select_quotes_region,quotes_source,select_quotes_city,quotes_city,quotes_title,quotes_author,quotes_affiliation,quotes_publisher,quotes_volume,quotes_issue,quotes_page,select_quotes_keywords,quotes_keywords,quotes_websource,quotes_sourcedoc,quotes_notes";
    var locations = [];
    <?php if (count($this->oQuote->locations) > 0): ?>
    var current_locations = '<?php
//tidy up for the json 
        $data = json_encode($this->oQuote->locations);
        $data = str_replace("'", "\'", $data);
        echo $data;
        ?>';
        <?php else: ?>
    var current_locations = null;
        <?php
             endif;
    ?>
    location_populate();
    $('tool-tip-box').hide();
    $('adminForm').select("a").each(function(el) {
        el.observe('click', function(event) {
            Event.stop(event);
            alert('Edit is disabled');
        })
    });
    //$('adminForm').getInputs('text').invoke('disable');

    function display_tip(el) {
        pos = $(el).viewportOffset();
        $('tool-tip-box').hide();
        title = el.readAttribute('title');
        $('tool-tip-box').update(title);
        $('tool-tip-box').setStyle({
            'position':'fixed',
            'top': pos.top + 'px',
            'left': pos.left + 'px',
            'z-index':100
        });
        $('tool-tip-box').show();
        new PeriodicalExecuter(function(pe) {
            $('tool-tip-box').hide();
            pe.stop();
        }, 2);
    }
    $('adminForm').select('label').each(function(s) {
        str = s.readAttribute('title');
        if (str == null || str.length == 0) {
            return;
        }
        s.observe('click', function(event) {
            Event.stop(event);
            display_tip(s);
        });
        s.observe('focus', function(event) {
            Event.stop(event);
        });
        s.observe('mouseover', function(event) {
            Event.stop(event);
        });
    });
    //]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#quotes_published" ).datepicker({ dateFormat: 'dd MM yy'});
  } );
     
    
</script>
