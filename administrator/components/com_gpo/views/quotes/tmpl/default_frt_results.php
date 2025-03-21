<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$jView = new JViewLegacy();
$from = $this->options['from'];  //$jView->escape( $this->options['from'] );
$to = $this->options['to']; //$jView->escape( $this->options['to'] );
$total = $this->total;
$where = $this->options['column_name'] . ' & Table: ' . $this->options['table_name'];
//$action = ( $this->action == 'add' ) ? 'replace' : $this->action;
$isCaseSensitive = ( $this->options['case_sensitive'] ) ? 'true' : 'false';
$searchPattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($from,'/') . "/") : ("/" . preg_quote($from,'/') . "/i");
$replacePattern = ( $isCaseSensitive == 'true' ) ? ("/" . preg_quote($to,'/') . "/") : ("/" . preg_quote($to,'/') . "/i");
?>

<?php
$document = &JFactory::getDocument();
//$document->addScript( JURI::root(true).'/media/system/js/jquery.tablesorter.min.js');
$document->addStyleSheet('/media/system/js/table_sorter_theme/blue/style.css', 'text/css', 'print, projection, screen');
$document->addScript( JURI::root(true).'/media/system/js/messi.min.js');
$document->addStyleSheet('/media/system/js/messi.min.css', 'text/css', 'print, projection, screen');
?>
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
}
.find {
    text-align:left;
}

.pink {
  /* background-color: #F96 !important; */
  background-color: #F96;
}

#adminForm {
  background-color: #F96 !important;
  padding: 10px;
}

-->
.sort_column_head{color: #025A8D !important;cursor: pointer;}
</style>

<?php if( $this->qcites_items > 0 ): ?>

<!--  Show the Qcites Finding -->
<div id="propagate" class="col" style="width: 100%;">

<div class="pane-sliders" id="menu-pane">
<div class="panel">
<h3 id="info-propagate" class="jpane-toggler title">
<span title="Mentions in Qcites">Mentions in QCites</span>
</h3>
<div class="jpane-slider content">
<?php 
$qCitesLink = 'index.php?option=com_gpo&controller=citations&type=quotes&task=frt&action=add'.
              '&swap[from]='.urlencode($from).'&swap[to]='.urlencode($to).
              '&swap[column_name]='.urlencode($this->options['column_name']).
              '&swap[case_sensitive]='.$this->options['case_sensitive'];
?>
  
  <span style="color:#FF0000; font-weight:bold;"> 
  <?php echo $this->qcites_items; ?> mentions of '<?php echo $from; ?>' were also found in the 
  '<?php echo $this->options['column_name'];?>' field of the Qcites table. 
  </span>
  To open the QCites search results in a new window, 
  <a href="<?php echo $qCitesLink?>" onClick="javascript: updateQciteStatus();"
     title='Click here to search in Qcites table: Opens in New Window' target="_blank">
    click here.
  </a>
</div>
</div>
</div>

</div>
<div class="clr"></div>

<?php endif; ?>


<!-- Preview Find-Replace Result -->
<p>
	You searched as:  &nbsp;
	Find: <strong><i> <span class='searchterm'>"<?php echo $from;?>"</span></i></strong>  &nbsp; » &nbsp;
	Replace With: <strong><i><span class='replaceterm'>"<?php echo $to;?>"</span> </i></strong>  &nbsp; » &nbsp;
	Column Name: <strong><i> "<?php echo $this->options['column_name'];?>" </i></strong>  &nbsp; » &nbsp;
	Table Name: <strong><i> "<?php echo $this->options['table_name'];?>" </i></strong>  &nbsp; » &nbsp;
	<span style="color:red;">
       &nbsp;
      [
        <?php echo ( $isCaseSensitive == 'true' ) ? 'Case Sensitive + Accent Sensitive' : ' Case Insensitive + Accent Insensitive';?>
      ]
   </span>
</p>

<h3>
  Total Match Found: <strong><i> "<?php echo $total;?>" </i></strong>
</h3>
<?php if( $total > 0 ):
echo 'TIP! To order results by both ID and Location, sort first by the primary column, 
      then hold down the Shift key to sort within those
      results using the secondary column.';
endif;
?>

<form action="index.php?option=com_gpo&controller=quotes" method="post" name="adminForm" id="adminForm">
<div class="responsive">
<table class="pink adminlist tablesorter table-striped table-hover table-bordered" id="adminlist" width="100%">

	<thead>
	<tr>
		<th sort="desc" class="sort_column_head" width="40" valign="top" align="center">
		    Quotes ID
		</th>
		<th width="10" valign="top" align="center">
		    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
		</th>
		<th sort="desc" class="sort_column_head" width="50" valign="top" align="center">
		   Location
		</th>
		<th width="205" valign="top" align="left">Found
	    </th>
		<th align="left" width="215" valign="top" align="left">
		   Replace With
		   <small>
		       <strong>(To EDIT, click any of the cells below)</strong>
		       <a id="edit_all" href="javascript:void(0);" title="click here to open all edit boxes of this column">Edit All</a> 
		   </small>
		</th>
	</tr>
	</thead>
	
	
	<tbody class="pink">
<?php
function highlight_search ( $content ) {
   return  '<span class="searchterm">'. $content[0] . "</span>";
}
function highlight_replace ( $content ) {
   return  '<span class="replaceterm">'. $content[0] . "</span>";
}

$i = 0;
foreach( $this->items as $item ):
	$content = $item->content; //$jView->escape( $item->content );	
	$checked 	= JHTML::_('grid.checkedout', $item, $i );
	$rowId = $item->id;
	
	$content = htmlspecialchars( $content, ENT_QUOTES );
	
	$nContent = ( !empty($from) ) ? 
	            preg_replace_callback( $searchPattern, "highlight_search", $content )
	            : $content;

?>
		<tr class="<?php echo "row$k"; ?>">
			<td width="40" valign="top" align="center"> 
			    <a style="color:green" target="_blank" 
			       href="index.php?option=com_gpo&controller=quotes&task=lookup&state=published&id=<?php echo $item->id;?>" 
			       title="Open this Quote in Lookup view in a New Window">
                    <?php echo $item->id;?>
                </a>
			</td>
			<td width="10" valign="top" align="center"> <?php echo $checked;?></td>
			<td width="50" valign="top" align="center"> 
			    <?php echo implode(', ', $this->quotesModel->getLocations( $item->id, true ) );?>
			</td>
			<td width="205" class="find" valign="top" align="left">
			   <div align="left"><?php echo nl2br($nContent); ?></div>
			</td>
			<td width="215" class="replace" valign="top" align="left">
			    <div align="left">
			       <?php 
			       if( !empty($to) ):
			          echo nl2br( preg_replace_callback( $replacePattern, "highlight_replace", htmlspecialchars( $this->replacedItems[$rowId], ENT_NOQUOTES ) ));
			       else:
			          echo nl2br( htmlspecialchars( $this->replacedItems[$rowId], ENT_NOQUOTES ) );                    
			       endif;
			       ?>
			    </div>
			   <textarea style="display:none;" name="replace<?php echo $item->id;?>" rows="2" cols="45"><?php echo $this->replacedItems[$rowId];?></textarea>
			</td>
		</tr>
<?php
$i++;
$k = 1 -$k;
endforeach;
?>
</tbody>

<?php if (count($this->items) > 0): ?>
	<tfoot>
		<tr>
			<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
<?php endif; ?>

</table>
</div>
    <input type="hidden" name="option" value="com_gpo" />
	<input type="hidden" name="controller" value="quotes" />
	<input type="hidden" name="task" value="<?php echo $this->task;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="action" value="<?php echo $this->action;?>" />
	<input type="hidden" name="search_options" value="<?php echo urlencode( serialize($this->options) );?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>"/>
    <input type="hidden" name="filter_order_dir" value="<?php echo $this->filter_order_dir; ?>"/>
<?php
$document->addScript( JURI::root(true).'/media/system/js/jquery.tablesorter.min.js');
?>
<script>

//use jquery tablesorter plugin to enable column sorting
jQuery.noConflict();
jQuery('.sort_column_head').click(function(){
    var sort_order = jQuery(this).attr('sort');
    var sort_image_asc = '<img class="sort_image_asc" alt="" src="<?php echo JURI::root() ?>media/system/images/sort_asc.png">';
    var sort_image_desc = '<img alt="" class="sort_image_desc" src="<?php echo JURI::root() ?>media/system/images/sort_desc.png">';
    if(sort_order=='desc'){ jQuery(this).attr('sort','asc');jQuery('.sort_image_desc,.sort_image_asc').remove(); jQuery(this).append(sort_image_asc);  }else{ jQuery('.sort_image_asc,.sort_image_desc').remove();jQuery(this).append(sort_image_desc);jQuery(this).attr('sort','desc');}
  });
jQuery(document).ready(function() {
	jQuery("#adminlist").tablesorter({ 
        // pass the headers argument and assing a object 
        headers: { 
            // assign the secound column (we start counting zero) 
            1: {
                // disable it by setting the property sorter to false 
                sorter: false 
            }, 
            // assign the third column (we start counting zero) 
            3: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            },
            // assign the third column (we start counting zero) 
            4: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            } 
        },
	    sortList: [[0,1]]
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
	    //alert(isCaseSensitive);
	    //alert(this.isCaseSensitive);
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
						     text = text.replace( new RegExp( RegExp.escape(term), "ig" ), "<span class='"+highlightClass+"'>" + term + "</span>")
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

//SearchUtil.searchHighlight( 'td.replace div', '<?php echo $to;?>', 'replaceterm' );
SearchUtil.onClickShowEditable( 'td.replace' );
</script>

<script>
var qCiteClickStatus = 0;
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

function updateQciteStatus() {
	qCiteClickStatus = 1;
	return true;
}
                              
/**
* 
* originally declared in joomla.javascript.js.  
* overriden by this component
* 
*/

Joomla.submitbutton = function(task){
    var submitStatus = giveFnRWarning();
   
    if( submitStatus ){
	   Joomla.submitform(task);
	}
   
    return false;
};


function giveFnRWarning( ) {

	var totalRes = '<?php echo $this->total;?>';
	var statusVar = 1;
	var qCitesWarning = "These strings have also been found in QCites. Before altering them in Quotes, ";
	qCitesWarning	 += "open the QCites search results in a new window to ensure you make the same changes there.";
	var boxedCheckedVal = document.adminForm.boxchecked.value;
<?php 
    if( $this->qcites_items > 0 ): 
?>
      if ( qCiteClickStatus == 0 ){
        //show warning for qcites mentions
        //alert(qCitesWarning);
        new Messi('<p>'+qCitesWarning+'</p>', {title: 'Match Found in Qcites also', modal: true});
        statusVar = 0;
      }
<?php
    endif;
?>
	
	if( boxedCheckedVal > 90 && totalRes > boxedCheckedVal && qCiteClickStatus !=0 ) {
        alert('You need to repeat the F&R operation for all pages');
	}

	//return true;

	if( 1 == statusVar ){
	   return true;
	}else{
       return false;
    }
}

jQuery("#edit_all").live("click",function(event){
    var currentHtml = jQuery(this).html(); //text().trim();
	if ( 'Edit All' == currentHtml ) {
  	    showAllEditBox();
	} else {
        hideAllEditBox();
	}
});
</script>
</form>
