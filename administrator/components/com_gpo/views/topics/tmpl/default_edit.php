<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();

$this->topic->meta = json_decode( $this->topic->meta );
?>

<style>
#topic-tbl td{
	vertical-align:top;
}
.row{
width:600px;
}
.leftcell{
	width: 200px;
	text-align:left;
}
</style>





<div width="80%" style="margin-left:20px;">
	<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm">
	<input type="hidden" name="option" value="com_gpo" />
	<input type="hidden" id="adminForm_task" name="task" value="a_save" />	
	<input type="hidden" name="controller" value="topics" />
	<input type="hidden" id="form_id" name="t[id]" value="<?php echo $this->topic->id; ?>" />
	<input type="hidden" id="t_search" name="t[search]" value="<?php echo $jView->escape( $this->topic->search ); ?>" />
	<input type="hidden" name="t[search_hash]" value="<?php echo $this->topic->search_hash; ?>" />
	<?php include_once('submenus_startblock.php'); ?>
		<table width="100%" border="0" id="topic-tbl">
				<tr>		
					<td class="leftcell">&nbsp;</td>
					<td>
						<div id="message-box"></div>
					</td>
				</tr>
				<tr>		
					<td class="leftcell">
						Topic Name
					</td>
					<td>					
						<input id="t_topic_name" class="row" name="t[topic_name]" value="<?php echo $jView->escape( $this->topic->topic_name ); ?>"> <a href="#" id="quick_fill" >( Populate the form based on this )</a>
					</td>
				</tr>
				
					
				<tr>		
					<td class="leftcell">
						URL Suffix
					</td>
					<td>
<?php if( empty( $this->topic->id ) ): ?>					
						<input id="t_seo" name="t[seo]" value="firearms/topic/" class="row" title="Please check the seo field, &quot;spaces&quot;, &quot;:&quot; and &quot;-&quot; are not allowed, consider using &quot;_&quot; this is a joomla limitation"> <span>( Add Topic name )</span>
<?php else: ?>
						<input id="t_seo" name="t[seo]" value="<?php echo $jView->escape( $this->topic->seo ); ?>" class="row" title="Please check the seo field, &quot;spaces&quot;, &quot;:&quot; and &quot;-&quot; are not allowed, consider using &quot;_&quot; this is a joomla limitation"> <span>( Add Topic name )</span>
<?php endif; ?>
					</td>
				</tr>
				<tr>		
					<td>
						Window Title
					</td>
					<td>
						<input id="t_window_title" name="t[window_title]" value="<?php echo $jView->escape( $this->topic->window_title ); ?>" class="row">
					</td>
				</tr>
				<tr>		
					<td>
						Page Headline
					</td>
					<td>
						<input id="t_page_headline" name="t[page_headline]" value="<?php echo $jView->escape( $this->topic->page_headline ); ?>" class="row">
					</td>
				</tr>
				<tr>		
					<td>
						Sub Headline
					</td>
					<td>
						<input id="t_page_headline_sub" name="t[page_headline_sub]" value="<?php echo $jView->escape( $this->topic->page_headline_sub ); ?>" class="row">
					</td>
				</tr>
				<tr>
					<td>Search Criteria</td>
					<td>
						<p>
						
						<?php if( $this->topic->id !== 0 ): ?>
						<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=topics&task=editsearch&id=' . $this->topic->id ); ?>" id="editSearch">Click to edit your search query.</a>
						<?php else: ?>
						Once saved you will be able to edit the search criteria for this topic by using News search.
						<?php endif; ?>
						( Save first!, if you have made any changes )
						</p>
					</td>
				</tr>
				<tr>		
					<td>
						Spider Bait
					</td>
					<td>
						<textarea id="t_spiderbait" name="t[spiderbait]" rows="4" class="row"><?php echo $jView->escape( $this->topic->spiderbait ); ?></textarea>
					</td>
				</tr>
				
				<tr>		
					<td>
						Metadata Author
					</td>
					<td>
						<textarea id="t_meta_author" name="t[meta][author]" rows="1" class="row"><?php echo $jView->escape( $this->topic->meta->author ); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						Metadata Keyword
					</td>
					<td>
						<textarea id="t_meta_keywords" name="t[meta][keywords]" rows="4" class="row"><?php echo $jView->escape( $this->topic->meta->keywords ); ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						Metadata Description
					</td>
					<td>
						<textarea id="t_meta_description" name="t[meta][description]" rows="4" class="row"><?php echo $jView->escape( $this->topic->meta->description ); ?></textarea>
					</td>
				</tr>	
				
				
<!--	
				<tr>		
					<td>
						Map variations ( .50 => 50caliber )
					</td>
					<td>
						<p>
						Find:<br />
						<textarea id="t_exceptions_from" name="t[exceptions][from]" class="row"><?php echo $jView->escape( $this->topic->sphinx_exceptions->from ); ?></textarea>
						</p>
						<p>
						To:<br />
						<input id="t_exceptions_to" name="t[exceptions][to]" value="<?php echo $jView->escape( $this->topic->sphinx_exceptions->to ); ?>" class="row">
						</p>
					</td>
				</tr>
-->
		</table>
<?php 
//#fix
//figure out a plan for this then delete or use it.
?>
<input name="t[exceptions][from]" value="" type="hidden">
<input name="t[exceptions][to]" value="" type="hidden">
<?php include_once('submenus_endblock.php'); ?>	
	</form>		
</div>
<script>
//search
var check = new  Hash();
check.set( 'search', false );
check.set( 'seo', false );

document.observe( "topic:populate", function( event )
{
	Event.stop( event );
	var word = $( "t_topic_name" ).getValue();
	if( word.empty() )
	{
		alert( 'Enter something' );
		return false;
	}
	seo_url = word.toLowerCase();
	seo_url = seo_url.replace(/ /g,"_" );
	
	$( 't_seo' ).value = "firearms/topic/" + seo_url;
	$( 't_window_title' ).value = word + " - Daily Bulletin from Gun Policy News";
	$( 't_page_headline' ).value = word + " - News Feed";
	$( 't_spiderbait' ).value = "Updated daily, this news feed points to mass-media articles on a single aspect of gun policy. For earlier articles, other topics or regions, use Search.";
	$( 't_meta_author' ).value = "Philip Alpers";
	$( 't_meta_keywords' ).value = word.toLowerCase() + ", gun news, gun control, gun policy, small arms news, small arms policy, small arms proliferation, firearm news, firearm violence, firearm law, public health, Philip Alpers";
	$( 't_meta_description' ).value = word + " - Daily Bulletin from Gun Policy News";
});


$( "quick_fill" ).observe( 'click', function( event ){
	Event.stop( event );
	this.fire( "topic:populate" );
});

$('item_save').observe( "click", function( event ){
	Event.stop( event );
	if( check_generic() === false )
	{
		return false;
	}

	
	new Ajax.Updater( 'message-box', $('adminForm').action,{
		parameters :  $('adminForm').serialize(true),
		evalScripts : true
	});
    return false;
	$("adminForm").submit();
});

$('editSearch').observe( "click", function( event ){
	Event.stop( event );
	var data = $("t_search").getValue();
	
	var href = this.readAttribute('href');
	
	href += '&d=';
	href += encodeURIComponent( data );
	window.location = href;
});


function check_generic()
{
	if( check_seo() === false )
	{
		return false;
	}
		
	if( check_search() === false )
	{
		return false;
	}
	return true;
}

function check_search()
{
	if( check.get('search') === true )
	{
		return true;
	}
	check.set('search',true);
	var v = $('t_search').getValue();
	if( v.empty() )
	{		
		alert( 'You are creating a topic without a search, you will need to add this before the topic works' );
		return false;
	}
	return true;
}

function check_seo()
{
	var v = $('t_seo').getValue();
	if( v === 'firearms/topic/' || v.empty() )
	{
		alert( 'You have not set the SEO of the topic' );
		return false;
	}	
	check.set('city',true);
	return true;
}
</script>