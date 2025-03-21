<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
unset($document->_scripts[JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js']);
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

<script>
    jQuery.noConflict();
</script>


<style>
#h1column {
width:250px;
height: 120px;
}


#h1column select {
width:240px;
}

#h2column, #h3column {
width:440px;
height: 120px;
}


#h2column select, #h3column select {
width:3000px;
}


select option {
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0 5px 0 5px;
    word-break: normal;
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
<?php include_once('submenus_startblock.php'); ?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<input type="hidden" name="action" value="<?php echo $this->action; ?>" />
<input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

<?php
$languageSuffix = '';
if( !empty($this->currentLanguage) && 'en' != $this->currentLanguage ) {
    $languageSuffix = '_' . $this->currentLanguage;
}

$allowedTables = array( 'DP Preambles'   => 'gpo_datapage_preamble_values' . $languageSuffix, 
                        'DP Data Values' => 'gpo_datapages' . $languageSuffix 
			     );
$searchOptions = array( 0 => 'Case sensitive',
                        1 => 'Regular expressions'
				 );
?>
<div class="responsive">
<table id="search_input" cellspacing="2">
<tr>
	<td width="307">
	   Find:<br />
	   <textarea id="swap_from" name="swap[from]" cols="70" rows="2"></textarea>	
	</td>
	<td width="405">
	   <div align="left" style="padding-left: 20px;">
	       <span style="margin-left: 5px;">Search Options:</span> <br /> 
	       <input type="checkbox" name="swap[case_sensitive]" value="1" />
         <?php echo $searchOptions[0];?>
	       <input type="checkbox" name="swap[regular_expression]" value="1" disabled /> 
	       <?php echo $searchOptions[1];?>
	       <input id="search_all_cat" type="checkbox" name="swap[all_categories]" value="1" /> 
	       <?php echo 'Search All Categories'?>
      </div>
	</td>
</tr>

<tr>
    <td>
	   Replace With:<br />
	   <textarea id="swap_to" name="swap[to]" cols="70" rows="2"></textarea>	
	</td>
	<td><div align="left" style="padding-left: 20px;">
  <span style="margin-left: 5px;">Error Correction Options:</span><br />
  <input type="checkbox" name="swap[correction][manual]" id="correction_manual" value="manual" />
  Manual Correction
  <input type="checkbox" name="swap[correction][auto]" id="correction_auto" value="auto" />
  Automatic Correction
  <br />  <br />
 Both queries search Data Values only, in All Categories.
 <br />  <br />
            <span style="margin-left: 5px;">Location:</span><br />
            <input type="checkbox" name="swap[us_jurisdictions]" id="search_only_us_jurisdictions" value="search_only_us_jurisdictions" />
            Search only in US Jurisdictions
  </div> </td>
</tr>
<tr>
      <td colspan="2">
         <p>
	       Select Table:<br />
	       <?php foreach( $allowedTables as $key => $val)
	       { 
	       ?>
	       <input id="<?php echo str_replace(' ','_',$key); ?>" type="radio" name="swap[table_name]" value="<?php echo $val;?>"  
	       <?php if( $key == 'DP Preambles' ){ echo 'checked';}?> />
	       <?php
	       echo $key . '&nbsp;&nbsp;';  
	       }
	       ?>
        </p>
	  </td>
</tr>
</table>
<?php 
$columnTitles = getDPColumnTitles();
$loaderImg = JURI::root(true) . '/media/system/images/move-spinner.gif';
?>
<p>
  <table cellpadding="2" cellspacing="2">
    <tr valign="top">
	<td>
	Select Category:<br />
	<select name="h1column" id="h1column"  multiple="multiple">
	<option value="0"> --Select top header-- </option>
	<?php foreach( $this->topLevelHeaders as $key=>$val)
	  {
	?>
	   <option title="<?php echo $columnTitles[$val];?>" value="<?php echo $key;?>"><?php echo $columnTitles[$val];?></option>
	<?php
	  }
	?>
        
        <option title="Separator, used for aesthetic purpose" value=""> --- --- --- --- --- </option>
        <option title="Location Subheader" value="location_subheader">Location Subheader</option>
	</select>
	</td>
	
	<td>
	<br /><div style="border:2px; background-color:#CCCCFF"> &gt;&gt; 
	</div> 
	<img id="h2loader" src="<?php echo $loaderImg;?>" style="display:none;"/>
	</td>
	 
	<td>
	<br />
	<select name="h2column" id="h2column" multiple="multiple">
	<option value="0"> --Select level-2 category-- </option>
	</select>
	</td>
	
    <td>
	  <br /> <div style="border:2px; background-color:#CCCCFF;"> &gt;&gt; 
	  </div>
	  <img id="h3loader" src="<?php echo $loaderImg;?>" style="display:none;"/>
	</td>
	
	<td>
	<br />
	<select name="h3column" id="h3column" multiple="multiple">
	<option value="0"> --Select level-3 category-- </option>
	</select>
	</td>
	</tr>
  </table>	
</p>

</div>
</form>
<?php include_once('submenus_endblock.php'); ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
                jQuery('#correction_manual').on('change',function(){
    if(!jQuery('#correction_auto').is(":checked")){
    if(jQuery(this).is(":checked")){
      jQuery('#DP_Data_Values').prop('checked', true);
      jQuery('#search_all_cat').prop('checked', true);
      jQuery('#swap_from').prop('disabled', true);
      jQuery('#swap_to').prop('disabled', true);
    }else{
      jQuery('#DP_Preambles').prop('checked', true);
      jQuery('#search_all_cat').prop('checked', false);
      jQuery('#swap_from').removeAttr("disabled");
      jQuery('#swap_to').removeAttr("disabled");
    }
  }else{
    jQuery('#correction_auto').prop('checked',false);
  }
  });
  
  jQuery('#correction_auto').on('change',function(){
    if(!jQuery('#correction_manual').is(":checked")){
    if(jQuery(this).is(":checked")){
      jQuery('#DP_Data_Values').prop('checked',true);
      jQuery('#search_all_cat').prop('checked',true);
      jQuery('#swap_from').prop('disabled', true);
      jQuery('#swap_to').prop('disabled', true);
    }else{
      jQuery('#DP_Preambles').prop('checked',true);
      jQuery('#search_all_cat').prop('checked',false);
      jQuery('#swap_from').removeAttr("disabled");
      jQuery('#swap_to').removeAttr("disabled");
    }
  }else{
    jQuery('#correction_manual').prop('checked',false);
  }
  });

jQuery('#h1column').on('change',function(event)
{
   var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
   URL += '&pid=' + jQuery('#h1column').val() + '&level=2';
   jQuery('#h2loader').show();
   jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h2column").html(result);
                       jQuery('#h2loader').hide();    
                      }});
   jQuery('#h3column').text('<option value="0">' + '-- select 3rd level category --' + '</option>');
});

    /**
     * This code for location subheader select options
     **/
    jQuery('h1column').on('change',function(event)
    {
        var value = jQuery('#h1column').val();
        //location subheader is a data value, so search in data values
        if(value == 'location_subheader'){
            jQuery('#DP_Data_Values').attr('checked',true);
        }
    });

jQuery('#h2column').on('change',function(event)
{
   var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
   URL += '&pid=' + jQuery('#h2column').val() + '&level=3';
   jQuery('#h3loader').show();
   
   jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h3column").html(result);
                       jQuery('#h3loader').hide();    
                      }});
});   
    });
/*Event.observe(window,'load',function(){

$('correction_manual').observe('change',function(){
    if($('correction_auto').checked==false){
    if($(this).checked==true){
      $('DP_Data_Values').set('checked',true);
      $('search_all_cat').set('checked',true);
      $('swap_from').set('disabled', true);
      $('swap_to').set('disabled', true);
    }else{
      $('DP_Preambles').set('checked',true);
      $('search_all_cat').set('checked',false);
      $('swap_from').set('disabled', false);
      $('swap_to').set('disabled', false);
    }
  }else{
    $('correction_auto').set('checked',false);
  }
  });
  
  $('correction_auto').observe('change',function(){
    if($('correction_manual').checked==false){
    if($(this).checked==true){
      $('DP_Data_Values').set('checked',true);
      $('search_all_cat').set('checked',true);
      $('swap_from').set('disabled', true);
      $('swap_to').set('disabled', true);
    }else{
      $('DP_Preambles').set('checked',true);
      $('search_all_cat').set('checked',false);
      $('swap_from').set('disabled', false);
      $('swap_to').set('disabled', false);
    }
  }else{
    $('correction_manual').set('checked',false);
  }
  });

$('h1column').observe('change',function(event)
{
   var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=frt&action=getcol",false ); ?>';
   URL += '&pid=' + $('h1column').value + '&level=2';
   $('h2loader').show(); 
   new Ajax.Updater( 'h2column', URL,{
		method: 'GET',
		onComplete: function(transport) {
	      $('h2loader').hide();	   
        }
   });
   $('h3column').update('<option value="0">' + '-- select 3rd level category --' + '</option>');
});

    /**
     * This code for location subheader select options
     
    $('h1column').observe('change',function(event)
    {
        var value = $('h1column').value;
        //location subheader is a data value, so search in data values
        if(value == 'location_subheader'){
            $('DP_Data_Values').set('checked',true);
        }
    });

$('h2column').observe('change',function(event)
{
   var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=frt&action=getcol",false ); ?>';
   URL += '&pid=' + $('h2column').value + '&level=3';
   $('h3loader').show();
   new Ajax.Updater( 'h3column', URL,{
		method: 'GET',
		onComplete: function(transport) {
	      $('h3loader').hide();
        }
   });
   
});


});//end load*/
</script>

<script>
/*
$("action-save").observe("click", function( event ){
	Event.stop(event);
	$("adminForm").submit();
});
*/

/*
$("#action-save").click( function( event ){
	event.stopPropagation();
	$("#adminForm").submit();
});
*/

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
    
    jQuery("#action-save").on("click", function( event ){
	event.preventDefault();
	jQuery("#adminForm").submit();
    });
    
    jQuery("#action-save").click( function( event ){
	event.stopPropagation();
	jQuery("#adminForm").submit();
});
});
</script>
