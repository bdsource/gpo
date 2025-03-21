<?php
defined('_JEXEC') or die('Restricted Access');
$document = JFactory::getDocument();
$document->addScript('components/com_gpo/assets/jquery-ui.min.js');
//$filename = JPATH_BASE . '/components/com_gpo/cache/quotes_keywords.txt';
//$select_data['keywords'] = explode("\r\n",trim(file_get_contents( $filename )));
//$select_data['cities'] = GpoGetHtmlOptionCities();


$select_data['keywords'] = GpoGetHtmlForType('keywords');
$select_data['city']     = GpoGetHtmlForType('city');


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


$document = &JFactory::getDocument();
$document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');

$document->addScript(JURI::root(true) . '/administrator/templates/bluestork/js/quotes_location.js');

//$document->addStyleSheet(JURI::root(true) . '/media/system/css/calendar-jos.css', 'text/css', 'all', array('title' => 'green'));

$document->addScript(JURI::root(true) . '/media/system/js/calendar.js');
$document->addScript(JURI::root(true) . '/media/system/js/calendar-setup.js');

$mootools = JURI::root(true) . '/media/system/js/mootools.js';
if (isset($document->_scripts[$mootools])) {
    unset($document->_scripts[$mootools]);
}

$date_output = 'j F Y';
//Set display output
if ($this->oQuote->published === '0000-00-00 00:00:00' || empty($this->oQuote->published)) {
    $this->oQuote->published = GpoDefaultPublishTime();
} else {
    $this->oQuote->published = strtotime($this->oQuote->published);
}
$this->oQuote->published = date($date_output, $this->oQuote->published);

foreach ($this->oQuote as $key => $value) {
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
        width: 120px;
    }

    .input120 {
        width: 113px;
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
        font-size: 13px;
    }

    .location_txt {
        font-size: 13px;
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
<div style="text-align:center;"><h1 id="form-header" style="font-weight:bold"><?php echo ($this->isNew)
        ? "Create new Quote" : "Edit this Quote"; ?></h1></div>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo"/>
<input type="hidden" id="adminForm_task" name="task" value=""/>
<input type="hidden" name="controller" value="quotes"/>
<input type="hidden" id="form_id" name="quotes[id]" value="<?php echo $this->oQuote->id; ?>"/>
<input type="hidden" id="quotes_live_id" name="quotes[live_id]" value="<?php echo $this->oQuote->live_id; ?>"/>
<input type="hidden" id="new_record" name="new_record" value="0"/>

<?php
if( stripos($this->oQuote->clonedFrom,'news') !== false ):
?>
<input type="hidden" id="clonedFrom" name="quotes[clonedFrom]" value="UpdatedCloneFromNews"/>
<?php
endif;
?>

<div class="row">
    <div style="float:right;padding:0px;margin:0px auto;padding-right:50px;">
        <p>
            <label style="display:block;"
                   title="Unique record number inserted automatically: cannot be altered. When a record is deleted, its ID number is not available for re-use.">Quote ID*</label>
            <!-- #live_id added -->
            <input class="not-editable" style="width:40px;" id="ext_id" type="text"
                   value="<?php echo $this->oQuote->live_id; ?>" disabled="true"/>
        </p>
    </div>
    <div class="cell">
        <p>
            <label style="display:block;" for="quotes_published"
                   title="Date of publication. Book, 2009: 1/1/09. Aug/Sep 2009 Issue: 1/8/09. Early copy: date received, plus 'Aug/Sep', 'Summer', etc. in Issue field.">Published*</label>
            <input class="input_field published" type="text" readonly id="quotes_published" name="quotes[published]"
                   value="<?php echo $this->oQuote->published; ?>"/>
        </p>
    </div>

    <div class="cell">
        <p>
            <label style="display:block;" id="quotes_locations_label"
                   title="Country and/or region name(s) mentioned, as in: 'Fiji', or 'Oceania, Fiji, Tonga, Tuvalu', or 'United Kingdom, Scotland', or 'Brazil, United Nations, World'.">Location*</label>
            <select id="select_quotes_country">
                <option value="">Country</option>
<?php
            foreach ($select_data['country'] as $cat) :
                //to deal with the format the text file is in
                $value = str_replace("&nbsp;", '', $cat);
                echo '<option value="' . $value . '">' . $cat . '</option>';
                EOB;
            endforeach;
                ?>
            </select>
            <select id="select_quotes_region">
                <option value="">Region</option>
<?php
            foreach ($select_data['region'] as $cat) :
                //to deal with the format the text file is in
                $value = str_replace("&nbsp;", '', $cat);
                echo '<option value="' . $value . '">' . $cat . '</option>';
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
                   title="Name of book, journal, publication or series title, as in: 'Small Arms Survey 2004: Rights at Risk', 'British Medical Journal', or 'SIPRI Backgound Paper'.">Source
                <a href="#" id="source_ucwords" title="Change all text to Title Case">Title Case</a></label>
            <input type="text" id="quotes_source" name="quotes[source]" style="width:706px;" value="<?php
            if (!empty($this->oQuote->source)) {
                echo $this->oQuote->source;
            }
            ?>"/>

        </p>
    </div>

    <div class="cell">
        <p>
            <label style="display:block;"
                   title="Place of publication. Use a single city name suitable for use in a citation, such as 'Canberra', 'Washington, DC' or 'Fairfax, VA'.">City</label>
            <select id="select_quotes_city">
                <option value="">Select</option>
        <?php
            foreach ($select_data['city'] as $cat){
                echo "<option value='".$cat."'>".$cat."</option>";
             }
            ?>
            </select>
            <input type="text" id="quotes_city" name="quotes[city]" style="width:153px;" value="<?php
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
                   title="Original title of article, chapter name, subtitle or paragraph title of article, subsection, broadcast segment, correspondence, etc., in Title Case.">Title*
                <a href="#" id="title_ucwords" title="Change all text to Title Case">Title Case</a></label>
            <input class="input_field" type="text" id="quotes_title" name="quotes[title]"
                   value="<?php echo $this->oQuote->title; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Last, first name of first author, then first, last names of subsequent author(s), (use comma, 'and'). Authorless articles: 'PNG', 'UNIDIR', 'Australia', 'SIPRI', etc.">Author*
                <a href="#" id="author_ucwords" title="Change all text to Title Case">Title Case</a></label>
            <input class="input_field" type="text" id="quotes_author" name="quotes[author]"
                   value="<?php echo $this->oQuote->author; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Redundant field. Author's institution or group, listed by initial if more than one. Delete all data, or move to other fields if needed. Never published.">Affiliation</label>
            <input class="input_field not-editable" type="text" id="quotes_affiliation" name="quotes[affiliation]"
                   value="<?php echo $this->oQuote->affiliation; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="Institution responsible for publication, as in: 'Penguin', 'Control Arms', 'Small Arms Survey', or 'Government of Fiji'.">Publisher
                <a href="#" id="publisher_ucwords" title="Change all text to Title Case">Title Case</a></label>
            <input class="input_field" type="text" id="quotes_publisher" name="quotes[publisher]"
                   value="<?php echo $this->oQuote->publisher; ?>"/>
        </p>


        <div class="row">
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Volume or description, as in: 'Vol. 6', 'Section 7', 'Resolution 42/9', 'Annex 3', Article 451', 'Table 6.2', 'Special Report No. 17' or 'Occasional Paper No. 8'.">Volume</label>
                    <input class="input_field input120" type="text" id="quotes_volume" name="quotes[volume]"
                           value="<?php echo $this->oQuote->volume; ?>"/>
                </p>
            </div>
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Issue number, month or local season, as in: '14', 'June', 'Oct/Nov', 'Summer', 'Winter/Spring'. Brackets are automatically added in Qcites; (14), (Oct/Nov).">Issue</label>
                    <input class="input_field input120" type="text" id="quotes_issue" name="quotes[issue]"
                           value="<?php echo $this->oQuote->issue; ?>"/>
                </p>
            </div>
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Page number(s) or page range(s), as in: 'p. 7', 'p. ix', 'pp. 142-45' or 'pp. iv, 8, 12, 32-38'.">Page</label>
                    <input class="input_field input120" type="text" id="quotes_page" name="quotes[page]"
                           value="<?php echo $this->oQuote->page; ?>"/>
                </p>
            </div>
        </div>

        <div class="clear"></div>

        <div class="row">
            <div class="cell">
                <p>
                    <label style="display:block;"
                           title="Descriptive keywords chosen from GPO thesaurus drop-down list, separated by commas, in Title Case. Search aid for staff: never published.">Keywords*</label>
                    <select id="select_quotes_keywords" style="vertical-align:top">
                        <option value="">Select</option>
             <?php
                foreach($select_data['keywords'] as $cat){
                        echo '<option value="'.$cat .'">'.$cat.'</option>';
                     } ?>
                    </select>
                 </p>
            </div>

            <div class="cell">
                <p>
                    <label for="quotes_poaim" title="Enter any relevant clause number(s) from the PoAIM protocol, comma separated.">PoAIM</label><br/>
                    <input type="text" name="quotes[poaim]" id=quotes_poaim" value="<?php echo $this->oQuote->poaim;?>"  style="width:148px"/>
                </p>
            </div>
        </div>
        <div class="row">
            <textarea id="quotes_keywords" name="quotes[keywords]" rows="3" cols="50"
                      style="width:370px;"><?php echo @$this->oQuote->keywords; ?></textarea>
        </div>
        <p>
            <label style="display:block;" title="Web link (URL) at which the article was located. If the URL is unknown, broken or not applicable, enter NoWebSource - without spaces.">WebSource</label>
            <input class="input_field" type="text" id="quotes_websource" name="quotes[websource]"
                   value="<?php echo $this->oQuote->websource; ?>"/>
        </p>

        <p>
            <label style="display:block;"
                   title="File name, location of any source document. If no file extension is shown, assume .doc. Staff only: often empty, never published.">SourceDoc</label>
            <input class="input_field" type="text" id="quotes_sourcedoc" name="quotes[sourcedoc]"
                   value="<?php echo $this->oQuote->sourcedoc; ?>"/>
        </p>


        <div class="row">
            <div class="cell">
<?php
                if (!isset($this->oQuote->share)
                    || $this->oQuote->share === '1'
                    || $this->oQuote->share === 1
                    || $this->oQuote->share === ""
) {
    $share_public = 'checked';
    $share_member = '';
} else {
    $share_public = '';
    $share_member = 'checked';
}
    ?>
    <p>
        <label title="Publish to GPO open web site (Default)." for="quotes_share_public">Public</label><input
            type="radio" id="quotes_share_public" name="quotes[share]" value="1" <?php echo $share_public; ?>/>
        <label title="Restrict to Members-Only web pages." for="quotes_share_member">Members</label><input type="radio"
                                                                                                           id="quotes_share_member"
                                                                                                           name="quotes[share]"
                                                                                                           value="0" <?php echo $share_member; ?>/>
    </p>
            </div>
            <?php if (!empty($this->oQuote->modified) && $this->oQuote->modified !== "0000-00-00 00:00:00"): ?>
            <div class="cell" style="padding-left:15px;">
                <p style="padding-top:7px;">
                    <span title="Date modified (local AET date). Auto-filled, cannot be altered on the Web site, but can be manually updated in Access.">Modified</span>
                    <span><?php echo date('j F Y', strtotime($this->oQuote->modified)); ?></span>


                </p>
            </div>
            <?php endif; ?>

        </div>

        <div class="clear"></div>
        <p>
            <span title="Select and enter your initials from the drop-down list." style="font-size:13px">Staff</span>
            <span><select name="quotes[staff_list]" id="quotes_staff_list" style="width:129px;">
                <option>Select</option>
              <?php
                foreach ($this->staffs as $staff) {
                echo '<option>' . $staff->initial . '</option>' . PHP_EOL;
                  }
                ?>
            </select>
			    <input type="text" id="staffs" name="quotes[staff]" value="<?php echo $this->oQuote->staff; ?>"
                       size="30"/>
	    </span>
        </p>
        <p>
            <label title="The QCite number of any pop-up Citations created from this Quote record is automatically entered here">QCite(s)
                <input type="text" id="qcite" style="width:314px" class="not-editable" name="quotes[citation]"
                       value="<?php echo $this->quotes_cited; ?>" readonly/>
            </label>
        </p>

        <p>
            <label style="display:block;" title="Staff only: often empty, never published.">Notes</label>
            <textarea id="quotes_notes" name="quotes[notes]" style="width:370px;height:30px;"><?php
                if (!empty($this->oQuote->notes)) {
                echo $this->oQuote->notes;
            }
                ?></textarea>
        </p>
    </div>

    <div class="cell rightcol">
        <p>
            <label title="Verbatim extracts, omitting text entered in other fields. Mark edits with ellipsis ' … '. Add notes in square brackets, e.g. [Table in the original].*">Content*</label>
            <a id="content_remove_html" href="#" title="clicking this will remove all html from the content">Remove
                Html</a><br/>
            <textarea id="quotes_content" name="quotes[content]" style="width:700px;height:696px;"><?php
                if (!empty($this->oQuote->content)) {
                echo $this->oQuote->content;
            }
                ?>
            </textarea>
        </p>
    </div>

</div>
<div class="clear"></div>

<div id="tool-tip-box"></div>
</form>

<script type="text/javascript">
//<![CDATA[
var check = new Hash();
check.set('city', false);
check.set('source', false);
check.set('websource', false);
check.set('publisher', false);
check.set('generic', false);

var tab_order_str = "quotes_content,quotes_published,select_quotes_country,select_quotes_region,quotes_source,select_quotes_city,quotes_city,quotes_title,quotes_author,quotes_affiliation,quotes_publisher,quotes_volume,quotes_issue,quotes_page,select_quotes_keywords,quotes_keywords,quotes_websource,quotes_sourcedoc,quotes_staff_list,quotes_notes";
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
Event.observe(window, 'load', function() {

    dd = document.body;
    Element.extend(dd);
    $('tool-tip-box').hide();

    /*
    //Allow the left click of the mouse to trigger a new item
    $('select_quotes_source').observe( 'click', function(event){
    if( this.selectedIndex != '0' )
    {
        select_split( 'select_quotes_source', 'quotes_source' );
    }
    });

    //this is required to reset the list options
    $('quotes_source').observe('focus',function(event){
    $('select_quotes_source').selectedIndex = '0';
    });

    $('select_quotes_source').observe( 'keypress', function(event){
    if( event.keyCode == Event.KEY_RETURN )
    {
        select_split( 'select_quotes_source', 'quotes_source' );
    }
    });
    */


//Allow the left click of the mouse to trigger a new item
    $('select_quotes_city').observe('click', function(event) {
        if (this.selectedIndex != '0') {
            select_split('select_quotes_city', 'quotes_city');
        }
    });
//this is required to reset the list options
    $('quotes_city').observe('focus', function(event) {
        $('select_quotes_city').selectedIndex = '0';
    });

    $('select_quotes_city').observe('keypress', function(event) {
        if (event.keyCode == Event.KEY_RETURN) {
            select_split('select_quotes_city', 'quotes_city');
        }
    });


//county - mouse click & key = return
    $('select_quotes_country').observe('click', function(event) {
        if (this.selectedIndex != '0') {
            location_select_add(this.readAttribute('id'));
        }
    });
    $('select_quotes_country').observe('keypress', function(event) {
        if (event.keyCode == Event.KEY_RETURN) {
            location_select_add(this.readAttribute('id'));
        }
    });


//region - mouse click & key = return
    $('select_quotes_region').observe('click', function(event) {
        if (this.selectedIndex != '0') {
            location_select_add(this.readAttribute('id'));
        }
    });
    $('select_quotes_region').observe('keypress', function(event) {
        if (event.keyCode == Event.KEY_RETURN) {
            location_select_add(this.readAttribute('id'));
        }
    });


    $('quotes_staff_list').observe('click', function(event) {
//alert('i m fired');
        if (this.selectedIndex != '0') {
            select_split('quotes_staff_list', 'staffs');
        }
    });


    $('select_quotes_keywords').observe('click', function(event) {
        if (this.selectedIndex != '0') {
            select_split('select_quotes_keywords', 'quotes_keywords');
        }
    });
//this is required to reset the list options
    $('quotes_keywords').observe('focus', function(event) {
        $('select_quotes_keywords').selectedIndex = '0';
    });
    $('select_quotes_keywords').observe('keypress', function(event) {
        if (event.keyCode == Event.KEY_RETURN) {
            select_split('select_quotes_keywords', 'quotes_keywords');
        }
    });

    $('content_remove_html').observe('click', function(event) {
        Event.stop(event);
        clean = $('quotes_content').getValue().stripTags().stripScripts();
        $('quotes_content').update(clean);
    });

    $("save_create_another").observe("click", function(event) {
        $('new_record').value = '1';
        Event.stop(event);
        if (check_generic() === false) {
            return;
        }
        /*
       if( check_city() === false )
       {
           return;
       }
       if( check_source() === false )
       {
           return;
       }
       if( check_websource() === false )
       {
           return;
       }
       if( check_publisher() === false )
       {
           return;
       }
        */
        quotes_save();
    });


    document.observe("adminFormQuotes:clone", function(event) {
        $("quotes_content").value = "";
        $('form-header').update('Create New Quote - <span style="color:#ff0000;">Cloned Copy</span>');
        $('form_id').value = '0';

        $("ext_id").value = '0';
        $("quotes_live_id").value = '0';

        $("message_box").update("Saved");
        $('new_record').value = '0';
        check.each(function(c) {
            check.set(c.key, false);
        });
    });

    document.observe("adminFormQuotes:clear", function(event) {
        $('new_record').value = '0';
        $("quotes_txt_locations").update("");
        $("quotes_notes").value = "";
        $("quotes_content").value = "";
        $('quotes_hidden_locations').value = "";
        $('adminForm').getInputs('text').each(function(i) {
            i.value = "";
        });

        $("select_quotes_city").selectedIndex = '0';
        $("ext_id").value = '0';
        $("quotes_live_id").value = '0';
        $("form_id").value = '0';
        locations = [];
        $("form-header").update("Create New Quote");
        $("message_box").update("");


        check.each(function(c) {
            check.set(c.key, false);
        });
    });


    if (Object.isElement($("clear_form"))) {
        $("clear_form").observe("click", function(event) {
            Event.stop(event);
            this.fire("adminFormQuotes:clear");
        });
    }


    if (Object.isElement($('item_publish'))) {
        $('item_publish').observe('click', function(event) {
            Event.stop(event);
            $('new_record').value = '0';
            if (check_generic() === false) {
                return;
            }
            /*
           if( check_city() === false )
           {
               return;
           }
           if( check_source() === false )
           {
               return;
           }
           if( check_websource() === false )
           {
               return;
           }
           if( check_publisher() === false )
           {
               return;
           }
            */
            $('quotes_hidden_locations').value = locations.compact().uniq().join(',');
            $('adminForm_task').value = 'save_publish';
            new Ajax.Updater('message_box', $('adminForm').action, {
                parameters :  $('adminForm').serialize(true),
                evalScripts : true
            });
            return false;
        });
    }


    function check_generic()
    {
        //Added for checking the validity of Author in News when it come from Quotes.
        <?php
        if('NewCloneFromNews' == $this->oQuote->clonedFrom):
        ?>
        if (check_authorchange() === false) {
            return false;
        }
        <?php
        endif;
        ?>

        if (check.get('generic') === true) {
            return true;
        }
        check.set('generic', true);

        if (check_city() === false) {
            return false;
        }
        if (check_source() === false) {
            return false;
        }
        if (check_websource() === false) {
            return false;
        }
        if (check_publisher() === false) {
            return false;
        }

        return true;
    }
    

    function check_city() {
        if (check.get('city') === true) {
            return true;
        }
        check.set('city', true);
        var v = $('quotes_city').getValue();
        if (v.empty()) {

            alert('You\'ve left one or more of these fields empty: Source, City, Publisher, or WebSource. If at all possible, please enter data in each of them, most importantly Source, Publisher and City.');
            return false;
        }
        return true;
    }

    function check_authorchange()
    {
        if ( authorFieldChanged != true ) {
            alert('No change has been made to the Author field. Remember to reverse the firstname/lastname ‘Byline’ order in News to fit the lastname/firstname ‘Author’ format used in Quotes.');
            return false;
        }

        return true;
    }

    function check_source() {
        if (check.get('source') === true) {
            return true;
        }
        check.set('source', true);
        var v = $('quotes_source').getValue();
        if (v.empty()) {
            alert('You\'ve left one or more of these fields empty: Source, City, Publisher, or WebSource. If at all possible, please enter data in each of them, most importantly Source, Publisher and City.');
            return false;
        }
        return true;
    }

    function check_websource() {
        if (check.get('websource') === true) {
            return true;
        }
        check.set('websource', true);
        var v = $('quotes_websource').getValue();
        if (v.empty()) {
            alert('You\'ve left one or more of these fields empty: Source, City, Publisher, or WebSource. If at all possible, please enter data in each of them, most importantly Source, Publisher and City.');
            return false;
        }
        return true;
    }

    function check_publisher() {
        if (check.get('publisher') === true) {
            return true;
        }
        check.set('publisher', true);
        var v = $('quotes_publisher').getValue();
        if (v.empty()) {
            alert('You\'ve left one or more of these fields empty: Source, City, Publisher, or WebSource. If at all possible, please enter data in each of them, most importantly Source, Publisher and City.');
            return false;
        }
        return true;
    }


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

    tab_order = tab_order_str.split(",");
    tab_order.each(function(s, i) {
        if ($(s)) {
            $(s).writeAttribute('tabindex', i + 1);
        }
    });
//this catches return
    $('adminForm').select('input').each(function(el) {

        if (el.readAttribute('type') != 'text') {
            return;
        }
        el.observe('keypress', function(event) {
            if (event.keyCode == Event.KEY_RETURN) {
                Event.stop(event);
                if (this.readAttribute('tabindex')) {
                    tabto = this.readAttribute('tabindex');
                    ++tabto;
                    el = $('adminForm').select('[tabindex="' + tabto + '"]').first();
                    if (Object.isElement(el)) {
                        el.focus();
                    }
                }
            }
        });
    });

    dd.observe('keypress', function(event) {

        if (event.keyCode == Event.KEY_TAB) {
            element = Event.element(event);
            if (element.readAttribute('tabindex') > 0) {
                tabto = parseInt(element.readAttribute('tabindex'));
                ++tabto;
            } else //if( current_tab >= tab_order.length )
            {
                tabto = 1;
            }
            el = this.select('[tabindex="' + tabto + '"]').first();
            if (Object.isUndefined(el)) {
                tabto = 1;
                el = this.select('[tabindex="' + tabto + '"]').first();
            }
            Event.stop(event);
            el.focus();
        }
    });

    $('title_ucwords').observe('click', function(event) {
        Event.stop(event);
        str = $('quotes_title').getValue();
        str = str.toLowerCase();
        str = (str + '').replace(/^(.)|\s(.)/g, function ($1) {
            return $1.toUpperCase();
        });
        $('quotes_title').value = str;
    });

    $('source_ucwords').observe('click', function(event) {
        Event.stop(event);
        str = $('quotes_source').getValue();
        str = str.toLowerCase();
        str = (str + '').replace(/^(.)|\s(.)/g, function ($1) {
            return $1.toUpperCase();
        });
        $('quotes_source').value = str;
    });

    $('author_ucwords').observe('click', function(event) {
        Event.stop(event);
        str = $('quotes_author').getValue();
        str = str.toLowerCase();
        str = (str + '').replace(/^(.)|\s(.)/g, function ($1) {
            return $1.toUpperCase();
        });
        $('quotes_author').value = str;
    });

    $('publisher_ucwords').observe('click', function(event) {
        Event.stop(event);
        str = $('quotes_publisher').getValue();
        str = str.toLowerCase();
        str = (str + '').replace(/^(.)|\s(.)/g, function ($1) {
            return $1.toUpperCase();
        });
        $('quotes_publisher').value = str;
    });
    
    //author field changed?
    var authorFieldChanged = false;
    $('quotes_author').observe( 'change', function(event){
	authorFieldChanged = true;
    });
    
    //Added for checking the validity of Author in News when it come from Quotes.
    <?php
    if('NewCloneFromNews' == $this->oQuote->clonedFrom):
    ?>
    $('quotes_author').setStyle({backgroundColor: '#1a3867',color: 'white'});
    <?php
    endif;
    ?>

    Calendar.setup({
        inputField     :    "quotes_published",     // id of the input field
        ifFormat       :    "%e %B %Y",      // format of the input field
        align          :    "Bl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });


    id = tab_order.first();
    $(id).focus();

}, false);
//]]>
</script>
<script type="text/javascript">
     jQuery( function() {
    jQuery( "#quotes_published" ).datepicker({ 
        dateFormat: 'dd MM yy',
        changeMonth: true, 
        changeYear: true, 
        yearRange: "-90:+00"
    });
  } );
</script>
