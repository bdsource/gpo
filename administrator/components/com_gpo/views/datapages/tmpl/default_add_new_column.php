<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$front_end = str_replace( "administrator",'',JURI::base(true));
$columnTitles = getDPColumnTitles();
?>

<style>
<!--
#h1column {
width:267px;
height: 120px;

}
#h1column option {
width:200px;
}

#h2column, #h3column {
width:290px;
height: 120px;
}
#h2column option, #h3column option {
width:280px;
}
-->
</style>


<?php
$document = &JFactory::getDocument();
$front_end = str_replace( "administrator/",'',JURI::base());

unset($document->_scripts[JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js']);
$document->addScript( '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet(JURI::root().'/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

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

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=add_new_column', false); ?>" 
 id="adminForm" name="adminForm">
<?php include_once('submenus_startblock.php'); ?>

<h3>Add New DP Category</h3>
<table border="0" cellpadding="0" cellspacing="0" >
<tr><td>

<input type="hidden" name="id" value="<?php echo $this->id;?>" /> 
<input type="hidden" name="location" value="<?php echo $this->location;?>" />
<input type="hidden" name="ParentId" id="ParentId" value="0" />
<table width="100%" border="0" align="left" cellpadding="1" cellspacing="0">    
    <tr>
       <td align="left" colspan="3">
       <strong>
               Category Type: 
           <a id="popup_category_type" class="help_popup" href="javascript:;" title="Help on Category Type">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
            <br />
            <label><input type="radio" name="column_type" id="column_type_0" value="0" checked="checked" />Add New DP Category</label>
            <label><input type="radio" name="column_type" id="column_type_1" value="1" />Add New Link Category</label>
       </strong>
        &nbsp;
           
       </td>
    </tr>
    <tr>
       <td align="left" colspan="3"><br /></td>
    </tr>
    <tr>
       <td align="left" colspan="3">
       <strong>
               Category Title: <br />
               (The category display name: can be longer than its alias)
               &nbsp;
               <a id="popup_category_title" class="help_popup" href="javascript:;" title="Help on Category Title">
                  <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
               </a>
       </strong>
       </td>
    </tr>
    
    <tr>
       <td align="left" colspan="3">
          <div>
            <textarea name="column_title" id="column_title" cols="58" rows="2" ></textarea>
	      </div>
       </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
	<td align="left" colspan="3" style="display:none;" id="external_hyperlink_name">
	       <strong>Name of Source Site: <br />
               <input type="text" name="external_hyperlink_name" value="" size="85" />
 	       </strong>
           &nbsp;
           <a id="popup_name_of_source_site" class="help_popup" href="javascript:;" title="Help on Name of Source Site">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
       </td>
    </tr>
    <tr>
    <tr>
	<td align="left" colspan="3" style="display:none;" id="external_hyperlink">
	       <strong>External Hyperlink: <br />
               <input type="text" name="external_hyperlink" value="" size="85" />
 	       </strong>
           &nbsp;
           <a id="popup_external_hyperlink" class="help_popup" href="javascript:;" title="Help on External Hyperlink">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
    </td>
    </tr>
   
    <tr id="gateway_category">
        <td colspan="3"><b>Gateway Category (No Data)?
            <a id="popup_gateway_category" class="help_popup" href="javascript:;" title="Help on Gateway Category">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
            </b>
            <label><input type="radio" name="is_gateway" value="1" />Yes</label>
            <label><input type="radio" name="is_gateway" value="0" checked />No</label>
            &nbsp;
        </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
       <td align="left" colspan="3">
       <strong>Category Alias: <br />
               (Max 61 characters, using only lower-case A-Z, 0-9 and underscores: identical to the display name if length allows)
       </strong>
       </td>
    </tr>
    
    <tr>
       <td align="left" colspan="3">
          <div>
             <input type="text" maxlength="61" size="85" name="column_name" id="column_name">
             &nbsp;
             <a id="popup_category_alias" class="help_popup" href="javascript:;" title="Help on Category Alias">
                <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
             </a>
             <span id="aliasloader" style="display:none;">Loading...</span>
	      </div>
       </td>
    </tr>   
    
   
   <tr>
      <td colspan="3">&nbsp;</td>
   </tr>
    
   <tr>
       <td align="left" colspan="3" id="default_preamble_level">
           <strong>Default Preamble Value (If any):</strong>
           &nbsp;
           <a id="popup_default_preamble_value" class="help_popup" href="javascript:;" title="Help on Default Preamble Value">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
       </td>
   </tr>
    <tr>
       <td align="left" colspan="3">
          <div>
            <textarea name="default_preamble" id="default_preamble" cols="58" rows="2" ></textarea>
            <INPUT TYPE=CHECKBOX NAME="checkjs" id="checkjs" value="1">Apply to all Locations
            <INPUT TYPE=CHECKBOX NAME="usjs" id="usjs" value="194">US Jurisdictions
            <INPUT TYPE=CHECKBOX NAME="selectedjs" id="selectedjs" value="1">Apply to Selected Locations
	      </div>
       </td>
    </tr>
    
    <?php
      $totalLocations = count($this->allLocations);
    ?>
    <tr>
       <td>
          <div style="padding-top:10px;display:none;" id="location_list_1" style="padding-top:10px;">
                 <span style="margin-right:80px;"><b>Select Locations:<b><br/>
                        (Multiple Locations can be selected)</span><br />
                        <select style="width:200px;height:200px;" name="locations[]" id="locations"  multiple="multiple">
                        <?php for( $lcounter=0; $lcounter<100; $lcounter++ ) //$this->allLocations as $key=>$val)
                          {
                          	$val = $this->allLocations[$lcounter];
                          	if( empty($val) ){ continue;}
                        ?>
                           <option title="<?php echo $val['name'];?>" value="<?php echo $val['id'];?>"> <?php echo $val['name'];?> </option>
                        <?php
                          }
                        ?>
                        </select>
	      </div>
       </td>
               
       
       <td>
          <div style="padding-top:10px;display:none;" id="location_list_2">  
                 <span style="margin-right:80px;"><b>Select Locations:<b><br/>
                        (Multiple Locations can be selected)</span><br />
                        <select style="width:200px;height:200px;" name="locations2[]" id="locations2"  multiple="multiple">
                        <?php for( ;$lcounter<200; $lcounter++ )
                          {
                           $val = $this->allLocations[$lcounter];
                           if( empty($val) ){ continue;}
                        ?>
                           <option title="<?php echo $val['name'];?>" value="<?php echo $val['id'];?>"> <?php echo $val['name'];?> </option>
                        <?php
                          }
                        ?>
                        </select>
	      </div>
       </td>
       
       <td>
          <div style="padding-top:10px;display:none;" id="location_list_3">  
                 <span style="margin-right:80px;"><b>Select Locations:<b><br/>
                        (Multiple Locations can be selected)</span><br />
                        <select style="width:200px;height:200px;" name="locations3[]" id="locations3"  multiple="multiple">
                        <?php for( ;$lcounter<=$totalLocations; $lcounter++) //$this->allLocations as $key=>$val)
                          {
                          $val = $this->allLocations[$lcounter];
                          if( empty($val) ){ continue;}
                        ?>
                           <option title="<?php echo $val['name'];?>" value="<?php echo $val['id'];?>"> <?php echo $val['name'];?> </option>
                        <?php
                          }
                        ?>
                        </select>
	      </div>
       </td>       
       
    </tr>
   
   <tr>
      <td colspan="3">&nbsp;</td>
   </tr>
   
   <!--Switch value-->
    <tr>
       <td align="left" colspan="3" id="default_switch_level">
           <strong>Default Switch  Value (If any):</strong>
           &nbsp;
           <a id="popup_default_switch_value" class="help_popup" href="javascript:;" title="Help on Default Switch Value">
               <img style="vertical-align:middle;" src="<?php echo $front_end;?>templates/gunpolicy/images/help_onebit.png">
           </a>
       </td>
    </tr>    
    <tr>
       <td align="left" colspan="3">
          <div>
              <textarea name="default_switch" id="default_switch" cols="58" rows="2" ></textarea>
	      </div>
       </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
   </table>
   </td>
   </tr>

<tr>
    <td colspan="3">
  
    <table cellpadding="2" cellspacing="2">
    <tr valign="top">
	<td >
	<b>Select Category:</b><br />
	<select name="h1column" id="h1column"  multiple="multiple">
	<option value="0"> --Select top header-- </option>
	<?php foreach( $this->topLevelHeaders as $key=>$val)
	  {
	?>
	   <option title="<?php echo $columnTitles[$val];?>" value="<?php echo $key;?>"> <?php echo $columnTitles[$val];?> </option>
	<?php
	  }
	?>
	</select>
	</td>
	
	<td>
	<br /><div style="border:2px; background-color:#CCCCFF;width: 110px;"> &gt;&gt; 
	</div> 
        <img id="h2loader" src="<?php echo JURI::root(true).'/media/system/images/move-spinner.gif';?>" style="display:none;"/>
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
	  <img id="h3loader" src="<?php echo JURI::root(true).'/media/system/images/move-spinner.gif';?>" style="display:none;"/>
	</td>
	
	<td>
	<br />
	<select name="h3column" id="h3column" multiple="multiple">
	<option value="0"> --Select level-3 category-- </option>
	</select>
	</td>
	</tr>
 	   
   
   <tr>
      <td colspan="2">
         <p>
           <b>Select Category Position:</b><br />
          <input type="radio" name="insert_position" id="insert_position_a" value="selected_after" checked="checked">
          After Selected Item
          <input type="radio" name="insert_position" id="insert_position_b" value="selected_before">
          Before Selected Item
          <input type="radio" name="insert_position" id="insert_position_c" value="under_item">
          <span id="insert_position_c_label">Add Sub-Category<span>
          <!--<label id="insert_position_c_label">Add Sub-Category</label>-->
          <br><br>
        </p>
      </td>
</tr>

      
    
    <tr>
       <td>
         <input type="submit" name="submit" id="submit" value="Add New Category">
       </td>
    </tr>
    </table>

</td></tr></table>

<?php include_once('submenus_endblock.php'); ?>
</form>

<!-- Help POPUP Contents --> 
<div id="help_category_type" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Category Type </h1>
       A DP Category sources data internally, from Data Pages on this site. 
       A Link Category opens a URL at an external web site.
    </p>
</div>

<div id="help_category_title" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Category Title </h1>
       The Title of the Category as it displays to visitors on each Data Page. 
       Although the Title can be edited later, the initial auto-filled Category Alias cannot be changed.
    </p>
</div>

<div id="help_gateway_category" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Gateway Category </h1>
       A ‘header only’ category which contains no data, and generates no charts. 
       Under this can be nested one or more ‘active’ Categories which display data.
    </p>
</div>

<div id="help_category_alias" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Category Alias </h1>
       The Category Alias is auto-generated from the Category Title, and cannot be changed after saving.
    </p>
</div>

<div id="help_default_preamble_value" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Default Preamble Value </h1>
       A Default Preamble is propagated to all, or selected DPs. 
       It usually includes auto-fill syntax such as ‘#’ for Location and ‘~’ for Data.
    </p>
</div>

<div id="help_default_switch_value" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Default Switch Value </h1>
       If a common Switch Value will be used, enter it here.
    </p>
</div>

<div id="help_external_hyperlink" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Link Category: External Hyperlink </h1>
       If entering a AVRMonitor.org or similar page or category anchor which relies on the ‘gpo_com’ Locations component, 
       enter [location] in place of the data page or country page alias. 
       For example: http://www.avrmonitor.org/firearms/region/[location]#total_number_of_gun_deaths
       <br /><br />
       URLs can also be manually entered in the Data Field of any DP to create an individual link to an external web page. 
       A URL entered in this way will override any string entered in the External Hyperlink field.
    </p>
</div>

<div id="help_name_of_source_site" style="display:none;">
    <p style="word-wrap:break-word;">
       <h1> Link Category: Name of Source Site </h1>    
       The brand name of the target external site, inserted in the Preamble for each Category.
    </p>
</div>

<!-- PopUp contents done -->



<script type="text/javascript">
    
/* help tooltip */
jQuery.noConflict();
jQuery(document).ready(function() {
   jQuery(".help_popup").on("click",function(event){
        var helpId = jQuery(this).attr('id');
        var helpId = helpId.replace('popup_','');
        var helpContentId = helpId.replace('_',' ').toUpperCase();
        var helpHtml = jQuery('#'+'help_'+helpId).html();
	    new Messi(helpHtml, {title: 'Help on '+helpContentId, modal: true, titleClass: 'anim'});
   });
   
   jQuery('#h1column').on('change',function(event){
       var URL = '<?php echo JRoute::_("index.php?option=com_gpo&controller=datapages&task=frt&action=getcol", false); ?>';
       URL += '&pid=' + jQuery(this).val() + '&level=2';
       jQuery('#h2loader').show(); 
       jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h2column").html(result);
                       jQuery('#h2loader').hide();    
                      }});
   jQuery('#h3column').text('<option value="0">' + '-- select 3rd level Category --' + '</option>');
   jQuery('#ParentId').val( jQuery('#h1column').val() );
   }); 
   
   jQuery('#h2column').on('change',function(event)
   {
   var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=frt&action=getcol",false ); ?>';
   URL += '&pid=' + jQuery(this).val() + '&level=3';
   jQuery('#h3loader').show();
   jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery("#h3column").html(result);
                       jQuery('#h3loader').hide();    
                      }});
                  
    if(jQuery('#insert_position_c').is(":checked")){
      if(jQuery('#h2column').val()!=0){
        jQuery('#ParentId').val(jQuery('#h2column').val());
      }
    }else{
      jQuery('#ParentId').val(jQuery('#h1column').val());
    }
});

jQuery('#insert_position_a').on('click',function(e){
  if(jQuery('#h3column').val()==0 || (jQuery('#h3column').val()=='') || (jQuery('#h3column').val()==null)){
    jQuery('#ParentId').val(jQuery('#h1column').val());
  }
});

jQuery('#insert_position_b').on('click',function(e){
  if(jQuery('#h3column').val()==0 || (jQuery('#h3column').val()=='') || (jQuery('#h3column').val()==null)){
    jQuery('#ParentId').val(jQuery('#h1column').val());
  }
});

jQuery('#insert_position_c').on('click',function(e){
  if(jQuery('#h2column').val()!=0){
    jQuery('#ParentId').val(jQuery('#h2column').val());
  }
});


jQuery('#h1column').on('click',function(event)
{
      jQuery('#insert_position_c').show();
      jQuery('#insert_position_c_label').show();
});

jQuery('#h2column').on('click',function(event)
{
      jQuery('#insert_position_c').show();
      jQuery('#insert_position_c_label').show();
});

jQuery('#h3column').on('click',function(event)
{
      jQuery('#insert_position_c').hide();
      jQuery('#insert_position_c_label').hide();
      jQuery('#insert_position_a').attr('checked',true);
  	  jQuery('#ParentId').val(jQuery('#h2column').val());
});

jQuery('#selectedjs').on('click',function(event)
{
   if( jQuery('#selectedjs').is(":checked") ) {
      jQuery('#location_list_1').show();
      jQuery('#location_list_2').show();
      jQuery('#location_list_3').show();
      jQuery('#checkjs').attr('checked', false);
      jQuery('#usjs').attr('checked', false);
   } else {
	  jQuery('#location_list_1').hide();
	  jQuery('#location_list_2').hide();
	  jQuery('#location_list_3').hide();
   }
});


jQuery('#checkjs').on('click',function(event)
{ 
   if( jQuery('#checkjs').is(":checked") ) {
	   jQuery('#selectedjs').attr('checked', false);
	   jQuery('#location_list_1').hide();
	   jQuery('#location_list_2').hide();
	   jQuery('#location_list_3').hide();
       jQuery('#usjs').attr('checked', false);
   }
});

    jQuery('#usjs').on('click',function(event)
    {
        if( jQuery('#usjs').is(":checked") ) {
            jQuery('#selectedjs').attr('checked', false);
            jQuery('#location_list_1').hide();
            jQuery('#location_list_2').hide();
            jQuery('#location_list_3').hide();
            jQuery('#checkjs').attr('checked', false);
        }
    });

jQuery('#column_title').on('change',function(event){
    var Text = jQuery(this).val();
    Text = Text.trim().toLowerCase().replace(/[^a-zA-Z0-9]+/g,'_').replace(/_+/g,'_').replace(/^_+/,"").replace(/\_+$/,'');
    jQuery('#column_name').val(Text);
    return;
})


jQuery('#column_title').on('keyup',function(event)
{
    return; // this method is not used for time being. rather the method just above it used to generate alias using javascript
	var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=make_column_alias",false ); ?>';
	URL += '&column_title=' + jQuery('#column_title').value;
	jQuery('#aliasloader').show();
                  
                  jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       if (200 == result.status) {
	              jQuery('column_name').val(result.responseText.trim());
		      } 
	          jQuery('aliasloader').hide();
                      }});
});
jQuery("#column_type_0").on('click',function(event){
	jQuery('#default_preamble').show();
	jQuery('#default_preamble_level').show();
	jQuery('#default_switch').show();
	jQuery('#default_switch_level').show();
    jQuery('#gateway_category').show();
	jQuery('#external_hyperlink_name').hide();
	jQuery('#external_hyperlink').hide();
	getTopHeader(0);
});
jQuery("#column_type_1").on('click',function(event){
	jQuery('#default_switch').hide();
	jQuery('#default_switch_level').hide();
    jQuery('#gateway_category').hide();
	jQuery('#external_hyperlink_name').show();
	jQuery('#external_hyperlink').show();
	getTopHeader(0);
});

function getTopHeader(typ)
{
   //alert(typ);
   var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=parent_getcol",false ); ?>';
   URL += '&typ=' + typ;
   jQuery('#h2loader').show(); 
   
   jQuery.ajax({
                     url: URL, 
                     success: function(result){
                       jQuery('#h2loader').hide();	 
                      }});
}

getTopHeader(jQuery("#column_type_0").is(":checked")?0:1);

});
/* help tooltip done */


/*Event.observe(window,'load',function(){

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
  
   $('h3column').update('<option value="0">' + '-- select 3rd level Category --' + '</option>');
   $('ParentId').setValue( $('h1column').value );
 
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
    if($('insert_position_c').checked){
      if($('h2column').value!=0){
        $('ParentId').setValue($('h2column').value);
      }
    }else{
      $('ParentId').setValue($('h1column').value);
    }
});

$('insert_position_a').observe('click',function(e){
  if($('h3column').value==0 || ($('h3column').value=='')){
    $('ParentId').setValue($('h1column').value);
  }
});

$('insert_position_b').observe('click',function(e){
  if($('h3column').value==0 || ($('h3column').value=='')){
    $('ParentId').setValue($('h1column').value);
  }
});

$('insert_position_c').observe('click',function(e){
  if($('h2column').value!=0){
    $('ParentId').setValue($('h2column').value);
  }
});


$('h1column').observe('click',function(event)
{
      $('insert_position_c').show();
      $('insert_position_c_label').show();
});

$('h2column').observe('click',function(event)
{
      $('insert_position_c').show();
      $('insert_position_c_label').show();
});

$('h3column').observe('click',function(event)
{
      $('insert_position_c').hide();
      $('insert_position_c_label').hide();
      $('insert_position_a').set('checked',true);
  	  $('ParentId').setValue($('h2column').value);
});

$('selectedjs').observe('click',function(event)
{
   if( $('selectedjs').checked ) {
      $('location_list_1').show();
      $('location_list_2').show();
      $('location_list_3').show();
      $('checkjs').checked = false;
      $('usjs').checked = false;
   } else {
	  $('location_list_1').hide();
	  $('location_list_2').hide();
	  $('location_list_3').hide();
   }
});


$('checkjs').observe('click',function(event)
{ 
   if( $('checkjs').checked ) {
	   $('selectedjs').checked = false;
	   $('location_list_1').hide();
	   $('location_list_2').hide();
	   $('location_list_3').hide();
       $('usjs').checked = false;
   }
});

    $('usjs').observe('click',function(event)
    {
        if( $('usjs').checked ) {
            $('selectedjs').checked = false;
            $('location_list_1').hide();
            $('location_list_2').hide();
            $('location_list_3').hide();
            $('checkjs').checked = false;
        }
    });

$('column_title').observe('change',function(event){
    var Text = $(this).getValue('value');
    Text = Text.trim().toLowerCase().replace(/[^a-zA-Z0-9]+/g,'_').replace(/_+/g,'_').replace(/^_+/,"").replace(/\_+$/,'');
    $('column_name').setValue(Text);
    return;
})


$('column_title').observe('keyup',function(event)
{
    return; // this method is not used for time being. rather the method just above it used to generate alias using javascript
	var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=make_column_alias",false ); ?>';
	URL += '&column_title=' + $('column_title').value;
	$('aliasloader').show();
	new Ajax.Request( URL,{
			method: 'GET',
			onComplete: function(transport) {
		      if (200 == transport.status) {
	              $('column_name').setValue(transport.responseText.trim());
		      } 
	          $('aliasloader').hide();
	        }
    });
});
$("column_type_0").observe('click',function(event){
	$('default_preamble').show();
	$('default_preamble_level').show();
	$('default_switch').show();
	$('default_switch_level').show();
    $('gateway_category').show();
	$('external_hyperlink_name').hide();
	$('external_hyperlink').hide();
	getTopHeader(0);
});
$("column_type_1").observe('click',function(event){
	$('default_switch').hide();
	$('default_switch_level').hide();
    $('gateway_category').hide();
	$('external_hyperlink_name').show();
	$('external_hyperlink').show();
	getTopHeader(0);
});

function getTopHeader(typ)
{
   //alert(typ);
   var URL = '<?php echo JRoute::_( "index.php?option=com_gpo&controller=datapages&task=parent_getcol",false ); ?>';
   URL += '&typ=' + typ;
   $('h2loader').show(); 
   new Ajax.Updater( 'h1column', URL,{
		method: 'GET',
		onComplete: function(transport) {
	      $('h2loader').hide();	   
        }
   });
}

getTopHeader($("column_type_0").checked?0:1);

});//end load


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