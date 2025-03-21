<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
unset($document->_scripts[JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js']);
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');

$jView = new JViewLegacy();
$from = $this->options['from'];  //$jView->escape( $this->options['from'] );
$to = $this->options['to']; //$jView->escape( $this->options['to'] );
$total  = $this->total;
$where  = $this->options['all_categories'] ? 'All Categories'.' & Table: ' . $this->options['table_name']:$this->options['column_name'] . ' & Table: ' . $this->options['table_name'];
$action = ( $this->action == 'add' ) ? 'replace' : $this->action;
$isCaseSensitive = ( $this->options['case_sensitive'] ) ? 'true' : 'false';
if(isset($_POST['swap']['all_categories']) && $_POST['swap']['all_categories']==1) {
    
    $searchPattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($from,'/') . "/") : ("/" . preg_quote($from,'/') . "/i");
    $replacePattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($to,'/') . "/") : ("/" . preg_quote($to,'/') . "/i");
 }else {
    $searchPattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($from,'/') . "/") : ("/" . preg_quote($from,'/') . "/i");
    $replacePattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($to,'/') . "/") : ("/" . preg_quote($to,'/') . "/i");
}

if( isset($_POST['swap']['correction']['manual']) && $_POST['swap']['all_categories']==1 ) {
    $from = 'manual_correction - Pattern: /[0-9]{5,}/';
    $to = 'manual_correction - Pattern: /[0-9]{5,}/';
}

$isPreamble = (strpos($this->options['table_name'],'preamble') !== false) ? TRUE : FALSE;
$inputRows  = ($isPreamble) ? 5 : 2;
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

<!--  Show the Propagate Button -->
<div id="propagate" class="col" style="width: 100%;">
    
<div class="pane-sliders" id="menu-pane">
<div class="panel">
    <h3 id="info-propagate" class="jpane-toggler title">
        <span title="Copy this value to all of the edit boxes below for replacement">Propagate</span>
    </h3>
    <div class="jpane-slider content">
        <textarea rows="2" cols="70" name="propagate_val" id="propagate_val" title="Copy this value to all of the edit boxes below for replacement"></textarea>
        &nbsp; &nbsp; &nbsp;
        <input style="padding:2px;" type="button" value="Propagate" id="propagate_bt" name="propagate_bt" 
               title="Copy this value to all of the edit boxes below for replacement" >
    </div>
</div>
</div>

</div>
<div class="clr"></div>

<!-- Preview Find-Replace Result -->
<h3>
	Find <strong><i> <pre class='searchterm'>"<?php echo $from;?>"</pre></i></strong>, 
	then replace with <strong><i><pre class='replaceterm'>"<?php echo $to;?>"</pre> </i></strong> 
	in the Category: <strong><i> "<?php echo $where;?>" </i></strong>
    
    <?php 
        if(!empty($this->USJurisdictionIDs)) {
           echo "<br>Searched only in US Jurisdictions <br>"; 
        }
    ?>
</h3>

<h3>
  Total Match Found: <strong><i> "<?php echo $total;?>" </i></strong>
  <span style="color:red;">
  &nbsp;&nbsp;
  [
      <?php echo ( $isCaseSensitive == 'true' ) ? 'Case sensitive' : ' Case insensitive';?>
  ]
  </span>
</h3>
<br />

<style>
<!--
#propagate .content {
  margin: 10px;
}
pre{
	padding:1px;
	margin:1px;
	display:inline;
}
.adminlist td a{
	display:inline;
}
.center{
	text-align:center;
}
.searchterm {
    background:none repeat scroll 0 0 #00FF00;
}
.replaceterm {
    background:none repeat scroll 0 0 #CCFFCC;
}
.replace {
    text-align:left;
/*
    white-space:pre-wrap;
    white-space:-moz-pre-wrap;
    white-space:-pre-wrap;
    white-space:-o-pre-wrap;
    word-wrap:break-word;
*/
}
.find {
    text-align:left;
/*
    white-space:pre-wrap;
    white-space:-moz-pre-wrap;
    white-space:-pre-wrap;
    white-space:-o-pre-wrap;
    word-wrap:break-word;
*/
}
-->
</style>


<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_gpo" />
    <input type="hidden" name="controller" value="datapages" />
    <input type="hidden" name="task" value="<?php echo $this->task;?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="action" value="<?php echo $action;?>" />
    <input type="hidden" name="search_options" value="<?php echo urlencode( serialize($this->options) );?>" />
    <input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>" />

<div class="table-responsive">
<table class="adminList table-striped table-hover table-bordered" width="100%" id="adminList">
	<thead>
	<tr class="info">
		<th width="5%" valign="top" align="center">Id</th>
		<th width="5%" valign="top" align="center">
		    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
		</th>
		<th width="10%" valign="top" align="center">Location</th>
        <th width="10%" valign="top" align="center">Column</th>
		<th width="35%" valign="top" align="left">Find</th>
		<th width="35%" valign="top" align="left">
		   Replace 
		   <small>
		       <strong>(To EDIT, click any of the cells below)</strong>
		       <a id="edit_all" href="javascript:void(0);" title="click here to open all edit boxes of this category">Edit All</a> 
		   </small>
		</th>
	</tr>
	</thead>
	<tbody>
<?php
function highlight_search ( $content ) {
   return  '<span class="searchterm">'. $content[0] . "</span>";
}

function highlight_replace ( $content ) {
   return  '<span class="replaceterm">'. $content[0] . "</span>";
}

// Get Current Language Name
if( 'en' == $this->currentLanguage ) {
    $locationName = 'name';
}elseif( in_array($this->currentLanguage, array('es','fr')) ) {
    $locationName = 'name_' . strtolower($this->currentLanguage);
}else {
    $locationName = 'name';
}

$i = 0;
$k = 0;
foreach( $this->items as $key => $item ):
	$content = $item->content; //$jView->escape( $item->content );	
	$checked 	= JHTML::_('grid.checkedout', $item, $i );
	$rowId = $item->id;
	$content = htmlspecialchars( $content, ENT_QUOTES );
	$columnKey = ($isPreamble) ? substr($item->column,0,-2) : $item->column;
        $columnTitle = empty($this->columnInfo[$columnKey]->column_title) ? $item->column : $this->columnInfo[$columnKey]->column_title;
	
	$nContent = ( !empty($from) ) ? preg_replace_callback( $searchPattern, "highlight_search", $content ): $content;

?>
		<tr class="<?php echo "row$k"; ?>">
			<td width="5%" valign="top" align="center"> <?php echo $item->id;?></td>
			<td width="5%" valign="top" align="center"> <?php //echo $checked;?><input id="cb<?php echo $i ?>" type="checkbox" title="Checkbox for row <?php echo $i ?>" onclick="Joomla.isChecked(this.checked);" value="<?php echo $item->id;?>" name="cid[<?php echo $i ?>]"> </td>

			<td width="10%" valign="top" align="center" title="<?php echo $item->name;?>"> <?php echo $item->{$locationName};?></td>
			<td width="10%" valign="top" align="left">
			   <div align="left" title="<?php echo $item->column;?>">
                                <?php echo $columnTitle;?>
                           </div>
			</td>
                        <td width="35%" class="find" valign="top" align="left">
			   <div align="left"><?php echo nl2br($nContent); ?></div>
			</td>
			<td width="35%" class="replace" valign="top" align="left">
			    <div align="left">
                               <?php
			       if( !empty($to) ):
			          echo nl2br( preg_replace_callback( $replacePattern, "highlight_replace", htmlspecialchars( $this->replacedItems[$key]->content, ENT_NOQUOTES ) ));
			       else:
			          echo nl2br( htmlspecialchars( $this->replacedItems[$key]->content, ENT_NOQUOTES ) );
			       endif;
			       ?>
			    </div>
                           <input style="display:none" type="checkbox" name="column_name[<?php echo $i;?>]" value="<?php echo $item->column; ?>" class="column_name"  />
			   <textarea style="display:none;width:90%;" name="replace<?php echo $i;?>" rows="<?php echo $inputRows;?>"><?php echo $this->replacedItems[$key]->content;?></textarea>
			</td>
		</tr>
<?php
$i++;
$k = 1 - $k;
endforeach;
?>
</tbody>
</table>
</div>
</form>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery('input[name="toggle"]').click(function(){
        var chk = jQuery(this).is(':checked');
        jQuery('.column_name').each(function(){
          jQuery(this).prop('checked', chk);
          });
          
      });
jQuery('input[type="checkbox"]').click(function(){
    var chk = jQuery(this).is(':checked');
      jQuery(this).parent().parent().find('.column_name').prop('checked', chk);
  });
jQuery("#edit_all").click(function(event){
    var currentHtml = jQuery(this).html();
	if ( 'Edit All' == currentHtml ) {
  	    showAllEditBox();
	} else {
        hideAllEditBox();
	}
});

jQuery("#propagate_bt").click(function(event){
    propagateToAll();
});

});
RegExp.escape = function(str) 
{
  var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g"); // .*+?|()[]{}\
  return str.replace(specials, "\\$&");
}

SearchUtil = {
/* highlight searched words */
    isCaseSensitive: '<?php echo $isCaseSensitive;?>', 
	
	searchHighlight: function(pSelector, pTerms, highlightClass) {
		var terms = pTerms.split(new RegExp("\\s"));
			$$(pSelector).each(function(e) {
				   var text = e.innerHTML;
				   
				if (text && text.length > 0) {
					for (var i = 0; i < terms.length; i++) {
						   var term = terms[i];
                           if ( "" == term ) { continue; }
						//if (text.indexOf(term, text) != -1) {
                          if ( this.isCaseSensitive == 'true') {
							 text = text.replace( new RegExp( RegExp.escape(term), "g" ), "<span class='"+highlightClass+"'>" + term + "</span>")
						  } else {
						     text = text.replace( new RegExp( RegExp.escape(term), "gi" ), "<span class='"+highlightClass+"'>" + term + "</span>")
						  }
                        //}
                    }
					e.innerHTML = text;
                }
		    });
	 },

/* onclick show the textarea for inline editing */
         /*
	 onClickShowEditable: function( pSelector) {
	    $$(pSelector).each( function(e) {
		   e.observe( "click", function(event) {
		      var el = Event.element(event);
		      if ('DIV' == el.tagName) {
		    	  el.next('textarea').show();
		      }
		      else if ('TD' == el.tagName) {
		    	  el.down('textarea').show();
		      }
		   });
		});
	 }
         */

       onClickShowEditable: function(pSelector) {
	   jQuery(pSelector).each( function(index) {
		   jQuery(this).click( function(event) {
		      var el = jQuery(this);
              var tagName = jQuery(this).prop('tagName');
		      if ('DIV' == tagName) {
		    	  jQuery(this).find('textarea').show();
		      }
		      else if ('TD' == tagName) {
		    	  jQuery(this).find('textarea').show();
		      }
		   });
		});
	 }
};

//SearchUtil.searchHighlight( 'td.find div', '<?php echo $from;?>', 'searchterm' );
//SearchUtil.searchHighlight( 'td.replace div', '<?php echo $to;?>', 'replaceterm' );
SearchUtil.onClickShowEditable( 'td.replace' );
</script>

<script>
/*
function showAllEditBox () {
	$$( "td.replace textarea" ).each(function(e){
	      e.show();	
	});
	changeLabel();
}

function hideAllEditBox () {
	$$( "td.replace textarea" ).each(function(e){
	      e.hide();	
	});
	changeLabel();
}

function changeLabel( newlabel ){ 
   var currentHtml = $("edit_all").innerHTML; 
   $("edit_all").innerHTML = ( 'Edit All' == currentHtml ) ? 'Close Edit All' : 'Edit All';
}

function propagateToAll () {
    var val = $('propagate_val').value;

    if ( val == "" ) {
        if ( !confirm( 'Are you sure you want to propagate an empty value?' ) ) {
             return false;
        }
    }
    
	$$( "td.replace textarea" ).each(function(e){
          e.value = val;
          e.show();
	});
	$("edit_all").innerHTML = 'Close Edit All';
}
*/

function showAllEditBox () {
	jQuery( "td.replace > textarea" ).each(function(index) {
	      jQuery(this).show();	
	});
	changeLabel();
}

function hideAllEditBox () {
	jQuery( "td.replace > textarea" ).each(function(e) {
	      jQuery(this).hide();
	});
	changeLabel();
}

function changeLabel( newlabel ) {
   var currentHtml = jQuery("#edit_all").html(); //text().trim(); 
   var newlabel = ('Edit All' == currentHtml) ? 'Close Edit All' : 'Edit All';
   jQuery("#edit_all").html(newlabel);
}

function propagateToAll () {
    var val = jQuery('#propagate_val').val();

    if (val == "") {
        if ( !confirm( 'Are you sure you want to propagate an empty value?' ) ) {
             return false;
        }
    }
    
	jQuery("td.replace > textarea").each(function(e){
          jQuery(this).val(val);
          jQuery(this).show();
	});
	jQuery("#edit_all").html('Close Edit All');
}


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

/*
$("edit_all").observe("click",function(event){
    var currentHtml = $("edit_all").innerHTML;     
	if ( 'Edit All' == currentHtml ) {
	   showAllEditBox();
	} else {
       hideAllEditBox();
	}
});

$("propagate_bt").observe("click",function(event){
    propagateToAll();
});
*/
</script>