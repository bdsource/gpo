<?php
defined('_JEXEC') or die('Restricted Access');
?>
<style type="text/css">
    .error_warning {
        color: #ff0000;
    }

    #message_box a {
        display: block;
    }

    #adminForm{
    /* background color changed from green to blue 
       background-color: #ccffcc;
    */
    background-color: #99CCFF; 
	padding:10px;
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

    #adminForm p {
        margin: 1px auto;
        line-height: 15px;
        padding: 1px;
        font-size: 8px;
    }

    .location_txt {
        font-size: larger;
    }

    #citations_published {
        text-align: center;
    }

    #tool-tip-box {
        width: 250px;
        border: 1px solid #cccccc;
        background-color: #ccff99;
        color: #000000;
    }

    .not-editable {
        background-color: #e9e9e9;
    }
</style>
<div id="message_box"></div>
<div style="text-align:center;"><h1
        style="font-weight:bold"><?php     echo ($this->isNew) ? "Create new glossary" : "Edit this glossary"; ?></h1>
</div>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

    <input type="hidden" name="option" value="com_gpo"/>
    <input type="hidden" id="adminForm_task" name="task" value=""/>
    <input type="hidden" name="controller" value="glossary"/>
    <input type="hidden" id="id" name="id" value="<?php echo $this->glossary->id; ?>"/>

    <div class="row">
        <div style="float:right; padding:0px; margin:0px auto;padding-right:225px;">
            <p>
                <label style="display:block;" title="">Glossary Id*</label>
                <input id="ext_id" class="not-editable" type="text" value="<?php echo $this->glossary->id; ?>"
                       disabled="true"/>
            </p>
        </div>

    </div>
    <div class="cell">
        <p>
            <label style="display:block;" title="Ttle">Title*</label>
            <input type="text" id="glossary_title" name="glossary[title]" style="width:750px;" value="<?php
    if (!empty($this->glossary->title)) {
                echo $this->glossary->title;
            }
            ?>"/>

        </p>
    </div>
    <div class="clear"></div>

    <div class="row">
        <div class="cell">
            <p>
                <label style="display:block;" title="Ttle">Sub Title*</label>
                <input type="text" id="glossary_subtitle" name="glossary[subtitle]" style="width:750px;" value="<?php
    if (!empty($this->glossary->subtitle)) {
                    echo $this->glossary->subtitle;
                }
                ?>"/>

            </p>
        </div>

    </div>

    <div class="clear"></div>


    <div class="row">
        <div class="cell">

            <p>
                <label style="display:block;" title="Web Source">Web Source*</label>
                <input class="input_field" type="text" id="glossary_websource" name="glossary[websource]"
                       value="<?php echo $this->glossary->websource; ?>"/>
            </p>

            <p>
                <label style="display:block;" title="Last Modified">Modified</label>
                <input class="input_field not-editable" type="text" id="glossary_modified" name=""
                       value="<?php echo $this->glossary->modified; ?>"/>
            </p>

            <div class="row">
                <div class="cell">
<?php
if (!isset($this->glossary->share)
    || $this->glossary->share === '1'
    || $this->glossary->share === ""
) {
    $share_public = 'checked';
    $share_member = '';
} else {
    $share_public = '';
    $share_member = 'checked';
}
    ?>
    <p>
        <label title="Publish this glossary to the open web site (default).">Public</label> <input type="radio"
                                                                                                   id="glossary_share_public"
                                                                                                   name="glossary[share]"
                                                                                                   value="1" <?php echo $share_public; ?>/>
        <label title="Restrict this Citation to Members-only web pages.">Members</label> <input type="radio"
                                                                                                id="glossary_share_admin"
                                                                                                name="glossary[share]"
                                                                                                value="0" <?php echo $share_member; ?>/>
    </p>
                </div>
                <?php if (!empty($this->glossary->modified) && $this->glossary->modified !== "0000-00-00 00:00:00"): ?>
                <div class="cell" style="padding-left:15px;">
                    <p style="padding-top:7px;">
                        <span title="Date last modified (server date). Updates when saved.">Modified</span>
                        <span><?php echo date('j F Y', strtotime($this->glossary->modified)); ?></span>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <div class="clear"></div>

        </div>
        <div class="cell rightcol">
            <p>
                <label title="Verbatim text extracts, any edits marked by an ellipsis […]. Enter Editors’ notes in square brackets, as in [sic.], [Includes link to video], etc.">Content*</label>
               <br/>
                <textarea id="citations_content" name="glossary[content]" cols="80" rows="30"><?php
    if (!empty($this->glossary->content)) {
                    echo $this->glossary->content;
                }
                    ?></textarea>
            </p>

        </div>
    </div>
    <div class="clear"></div>

</form>

