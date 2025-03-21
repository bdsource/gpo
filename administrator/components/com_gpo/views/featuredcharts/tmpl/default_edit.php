<?php
defined('_JEXEC') or die('Restricted Access');

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

<style type="text/css">
    .error_warning {
        color: #ff0000;
    }

    #message_box a {
        display: block;
    }

    #adminForm {
        background-color: #ccffcc;
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
    label{
        text-align:left;
    }
    ul li {
        list-style:none;
    }

</style>

<!-- Language Switching Panel -->
<div class="langFloatBar" title="DP Language: <?php echo getLanguageName($this->currentLanguage);?>">
   <a href="#switchLang">
   <span class="title"><?php echo strtoupper($this->currentLanguage);?></span>
   <br />
   <img border="0" src="<?php echo getLanguageFlag($this->currentLanguage);?>"
        alt="<?php echo getLanguageName($this->currentLanguage);?>"
   />
   </a>
</div>

<div class="langPanel">
      <a name="switchLang"></a>
      <div id="langOptionsWrapper">
           <?php echo getLanguageOptionsHTML($this->currentLanguage);?>
      </div>
</div>
<div class="clr"></div>
<br />
<!-- Language Switching panel done -->


<div id="message_box"></div>
<div style="text-align:center;"><h1
        style="font-weight:bold;padding-top:10px"><?php     echo ($this->isNew) ? "Add new chart" : "Edit chart"; ?></h1>
</div>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm" enctype="multipart/form-data">

    <input type="hidden" name="option" value="com_gpo"/>
    <input type="hidden" id="adminForm_task" name="task" value="save"/>
    <input type="hidden" name="controller" value="featuredcharts"/>
    <input type="hidden" id="id" name="id" value="<?php echo @$this->chart->id; ?>"/>
    <input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

    <label>Name <br/>
       <input type="text" name="chart_location" size="60" style="width:350px" value="<?php echo $this->chart->location;?>" />
    </label><br />

    <label>Description <br/><input type="text" name="chart_title" size="60" style="width:350px" value="<?php echo $this->chart->title;?>"/></label><br/>

    <input type="hidden" name="old_chart_image" id="old_chart_image" readonly="true" value="<?php echo $this->chart->image;?>"/>	
    <?php
	if($this->chart->image !='' ){
    ?>
	<br/><img src="<?php echo JURI::root().'/images/gpo/charts/'.$this->chart->image;?>" width="100" /><br/>	
    <?php
	}
    ?>	
    <label>Chart Image <br/><input type="file" name="chart_image" value="<?php echo $this->chart->image;?>" /></label><br/>
    <label>Chart Order <br/><input type="text" name="chart_order" size="4" value="<?php echo $this->chart->ordering;?>" /></label><br/>
    	
    <!--<div id="divStatus"></div>
    <div class="fieldset flash" id="fsUploadProgress">
        <span class="legend">Upload Queue</span>
    </div>

    <div id="uploadBox">
        <span id="spanButtonPlaceHolder"></span>
        <input id="btnCancel" type="button" value="Cancel Upload" onclick="swfu.cancelQueue();"
               disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;"/>
    </div>-->

    <div class="clear"></div>
</form>



<script type="text/javascript">

/*
 * For language Switching 
 * 
 */
var currentLang = '<?php echo $this->currentLanguage;?>';
jQuery(document).ready(function() {
    jQuery('#languageDropdown').ddslick({
        width: 200,
        onSelected: function (data) {
           var selectedLang = data.selectedData.value;
           if (currentLang == selectedLang ) 
           {
              return true;   
           } 
           else {
              var newLangURIPart = '&lang=' + selectedLang;
              var newLangURI = '<?php echo $this->currentURI;?>'+newLangURIPart;
             // similar behavior as an HTTP redirect
             window.location.replace(newLangURI);
           }
        }
    });
});
</script>