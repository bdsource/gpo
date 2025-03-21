<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$front_end = str_replace( "administrator/",'',JURI::base());

$document->addScript( '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript( $front_end . 'media/system/js/jeditable/jquery.jeditable.js');

$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet('/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');

$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');

$mootools = JURI::root(true).'/media/system/js/mootools.js';
if( isset( $document->_scripts[$mootools]))
{
	unset( $document->_scripts[$mootools]);
}

$options['types'] = array(
				'country'=>'Country',
				'jurisdiction'=>'Jurisdiction',
				'region'=>'Region',
				'subregion'=>'Sub Region'				
				);
$options['display'] = array(
				'0'=>'No',
				'1'=>'Yes'
				);
?>

<style type="text/css">
.error_warning {
	color: #ff0000;
}

.hidden {
	display: none;
}

#message_box a {
	display: block;
}

#adminForm {
	padding: 10px;
}


.input_field {
	width: 370px;
}

.clear {
	float: none;
	clear: both;
}

#adminForm p {
	margin: 1px auto;
	line-height: 15px;
	padding: 1px;
	font-size: 8px;
}

#edit_column td{
    padding: 5px 10px;
}
#edit_column th{
    font-weight: bold;
    font-size: large;
    padding: 10px;
}

tr.l0 label{
   font-weight:bold;
   font-size:15px;
}
td.tlo {
   height: 40px;
}
.editable{
   width: 150px !important;
   float: none;
   text-align: center;
   margin: 5px 5px 5px 0;
   cursor: auto;
   letter-spacing: normal;
   word-spacing: normal;
   text-transform: none;
   text-indent: 0px;
   text-shadow: none;
   display: inline-block;
   padding: 1px;
}
.editable textarea {
   width: 150px !important;
   height: 20px !important;
}
.editable button {
   margin-left: 4px;
}
.prefix .editable textarea {
   width: 100px !important;
   height: 20px !important;
}
.prefix {
    border: 1px solid #8dbdd8;
    display: inline-block;
    min-height: 20px;
    min-width: 70px;
}

#adminList select, input, textarea {
   font-size: 10px !important;
}
#adminList td, tr, label, span {
    word-wrap: break-word;
}

/* color schemes */
.l0 {
    color: #000;
    font-weight: bold;
}
.l1 {
    color: green;
}
</style>

<div id="message_box"></div>

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


<?php if( count($this->rows) > 0 ): ?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=edit_columns'); ?>" id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php');?>
<fieldset>
<legend><span style="font-size:14px;"> <?php echo JText::_('Translate Location Names');?></span></legend>

<p style="font-size:12px;">
☞ &nbsp;Click on any Location Name or Prefix to edit it, then be sure to click ‘OK’ to update the value in Database.
<br>
☞ &nbsp;To discard the changes click 'Cancel'
</p>

<input type="hidden" name="option" value="com_gpo">
<input type="hidden" id="adminForm_task" name="task" value="" >
<input type="hidden" name="controller" value="locations">
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" >

<div id="dataset" class="dataset">
    <table class="adminlist table-striped table-hover loc-list-edit" id="adminList" style="table-layout:fixed;" cellspacing="0px" cellpadding="2px" width="">
          <thead>
               <tr>
                  <th width="4%">
                      Loc #ID
                  </th>
                  <th width="5%"> 
                      <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('en');?>" />
                      <?php echo JText::_('Prefix (EN)');?>
                  </th>
                  <th width="20%"> 
                      <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('en');?>" />
                      <?php echo JText::_('Location Name (English)');?>
                  </th>
                  <th width="5%"> 
                      <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('es');?>" />
                      <?php echo JText::_('Prefix (ES)');?>
                  </th>
                  <th width="24%"> 
                       <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('es');?>" />
                      <?php echo JText::_('Location Name (') . getLanguageName('es') . ')';?> 
                  </th>
                  <th width="5%"> 
                      <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('fr');?>" />
                      <?php echo JText::_('Prefix (FR)');?>
                  </th>
                  <th width="24%"> 
                      <img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('fr');?>" />
                      <?php echo JText::_('Location Name (') . getLanguageName('fr') . ')';?> 
                  </th>
                  <th width="8%"> <?php echo JText::_('Type');?> </th>
                  <th width="5%"> <?php echo JText::_('Show');?> </th>
               </tr>
           </thead>
           
           <tbody>
    
    <?php
    $i = 0;
    foreach( $this->rows as $key => $row ):
        $elementId   = $row['id'] . '_en';
        $elementIdEs = $row['id'] . '_es';
        $elementIdFr = $row['id'] . '_fr';
        $i++;
    ?>
        <tr>
        <td class="l0">
            <div> <?php echo $row['id'];?></div>
        </td>
        <td class="l0">
            <span class="edit_area prefix" id="<?php echo $elementId . '_prefix';?>"><?php echo $row['prefix'];?></span>
        </td>
        <td class="l0">
            <span class="edit_area" id="<?php echo $elementId . '_name';?>"><?php echo $row['name'];?></span>
        </td>
        <td class="l0">
            <span class="edit_area prefix" id="<?php echo $elementIdEs . '_prefix';?>"><?php echo $row['prefix_es'];?></span>
        </td>
        <td class="l0">
            <span class="edit_area" id="<?php echo $elementIdEs . '_name';?>"><?php echo $row['name_es'];?></span>
        </td>
        <td class="l0">
            <span class="edit_area prefix" id="<?php echo $elementIdFr . '_prefix';?>"><?php echo $row['prefix_fr'];?></span>
        </td>
        <td class="l0">
            <span class="edit_area" id="<?php echo $elementIdFr . '_name';?>"><?php echo $row['name_fr'];?></span>
        </td>
        <td class="l0">
            <div><?php echo  $options['types'][ $row['type'] ];?></div>
        </td>
        <td class="l0">
            <div><?php echo  $options['display'][ $row['display'] ];?></div>
        </td>
        </tr>
    <?php
    endforeach;
    ?>  
    </tbody>
    </table>

    </div>

<div class="clear"></div>
</fieldset>
<?php include_once('submenus_endblock.php');?>
</form>
<?php endif; ?>

<script>
//<![CDATA[	
/*
var options_type = new Hash( {"country":"Country","jurisdiction":"Jurisdiction","region":"Region","subregion":"Sub Region"} );
var options_display = new Hash( { "0":"No","1":"Yes" } );
*/

/*
 * For language Switching 
 * 
 */
var currentLang = '<?php echo $this->currentLanguage;?>';
jQuery = $.noConflict();

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

/* inline edit */
jQuery(document).ready(function() {
     var imgDIR  = '<?php echo $front_end;?>'+'media/system/js/jeditable/';
     var postURL = '<?php echo JRoute::_('index.php?option=com_gpo&controller=locations&task=location_translate&lang='.$this->currentLanguage,false);?>';
     
     jQuery('.edit').editable(postURL, {
         indicator   : 'Saving...',
         tooltip     : 'Click to edit...',
         placeholder : ''

     });
     jQuery('.edit_area').editable(postURL, {
         data: function(value, settings) {
               var retval = value.trim();
               return retval;
         },
         select    : true,
         type      : 'textarea',
         cancel    : 'Cancel',
         submit    : 'OK',
         indicator : '<img src="'+imgDIR+'img/indicator.gif">',
         tooltip   : 'Click to edit...',
         id        : 'locationId',
         name      : 'locationNewName',
         cssclass  : 'editable',
         placeholder: ''

     });
     
 });

//]]>
</script>