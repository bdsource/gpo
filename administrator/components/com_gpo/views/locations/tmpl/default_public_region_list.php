<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>

<style>
.error_warning{color:#ff0000;}
#message_box a{display:block;}
#adminForm{
	padding:10px;
}
/*#adminForm p label{display:block;padding-right:10px;}*/
.input_field{width:370px;}

.clear{float:none;}


.row{
	clear:both;
	display:block;
	padding:0px;
	margin:0px auto;
}
.cell{
	float:left;
	padding:0px;
	margin:0px auto;
}
.cell label{
	padding:0px;
	margin:0px auto;
	display:inline;
}

.published{
	width:100px;
}

.input120{
	width:120px;
}
.clear{
	clear:both;
}

#quotes_txt_locations a{padding-left:5px;}
#adminForm p{margin:1px auto;line-height:15px;padding:1px;font-size:8px;}
.location_txt{font-size:larger;}

#quotes_published{text-align:center;}

#tool-tip-box{
	width:250px;
	border:1px solid #cccccc;
	background-color: #ccff99;
	color:#000000;
}

.location_menu a{
padding-right:5px;
text-decoration:underline;
}
</style>
<div id="message_box"></div>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" id="adminForm_task" name="task" value="" />
<input type="hidden" name="controller" value="locations" />
<input type="hidden" id="order" name="order" value="" />
<?php include_once('submenus_startblock.php');?>

<h1>Drop-down List of Regions ('Public' Front-end Display)</h1>

<p>
	Current: Displays this drop-down list as it currently appears<br />
<select id="select_region">
	  <?php foreach( $this->current_order as $cat) echo <<<EOB
      <option value="{$cat}">{$cat}</option>
EOB;
?>
	</select> 
</p>

<p>
	<a href="#" id="reset_order">Reset list to include all possible regions (any non-alphabetical list order must be manually rebuilt)</a>
</p>


<ul id="original_order">
<?php foreach( $this->location_links as $region_id => $links ): ?>
<li><span class="location_name"><?php echo $this->location_names[ $region_id ]['name']; ?></span> <span class="location_menu"></span></li>
	<?php if( is_array( $links ) ): ?>
		<?php foreach( $links as $subregion_id ): ?>
			<li><span class="location_name">---<?php echo $this->location_names[ $subregion_id ]['name']; ?></span> <span class="location_menu"></span></li>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
 
<ul id="new_order">
<?php foreach( $this->new_order as $location ): ?>
<li><span class="location_name"><?php echo $location; ?></span> <span class="location_menu"></span></li>
<?php endforeach; ?>
</ul>
<?php include_once('submenus_endblock.php');?>
</form>

<script type="text/javascript">
//<![CDATA[	

Event.observe(window,'load',function(){

$('original_order').hide();

$('toolbar-Link').observe('click',function(event)
{
	Event.stop(event);
	$('adminForm_task').value ='a_save_public_region';
	locations = $('new_order').select('.location_name').pluck('innerHTML');
	$('order').value = locations.toJSON();
		new Ajax.Updater( 'message_box', $('adminForm').action,{
		parameters :  $('adminForm').serialize(true),
		evalScripts : true
		});
      	return false;
});


$('new_order').select('.location_menu').each(function(oSpan){

	location_menu( oSpan );
});

$('reset_order').observe( 'click', function( event ){
	$('new_order').update('');
	$('new_order').update( $('original_order').innerHTML );
	
	$('new_order').select('.location_menu').each(function(oSpan){
		location_menu( oSpan );
	});
});

});//end load


function location_menu( oSpan )
{
	oA = new Element('a',{
		'href':'#'
						}).update('Move Up 1');
	oA.observe('click',function(event){
		Event.stop(event);
		oLi = this.up('li');
		move_up_one( oLi );
	});
	oSpan.insert({bottom:oA});
	
	oA = new Element('a',{
		'href':'#'
						}).update('Move Down 1');
	oA.observe('click',function(event){
		Event.stop(event);
		oLi = this.up('li');
		move_down_one( oLi );
	});
	oSpan.insert({bottom:oA});
	
	oA = new Element('a',{
		'href':'#'
						}).update('Copy to Top');
	oA.observe('click',function(event){
		Event.stop(event);
		oLi = this.up('li');
		copy_to_top( oLi );
	});
	oSpan.insert({bottom:oA});
	
	
	oA = new Element('a',{
		'href':'#'
						}).update('Remove');
	oA.observe('click',function(event){
		Event.stop(event);
		oLi = this.up('li');
		location_delete( oLi );
	});
	oSpan.insert({bottom:oA});
	
	oA = new Element('a',{
		'href':'#'
						}).update('Insert Blank line');
	oA.observe('click',function(event){
		Event.stop(event);
		oLi = this.up('li');
		insert_blank( oLi );
	});
	oSpan.insert({bottom:oA});
}



function move_up_one( el )
{
	oSib = el.previous('li');
	if( Object.isElement( oSib ) )
	{
	 	oSib.insert({before:el});
	}
}

function move_down_one( el )
{
	oSib = el.next('li');
	if( Object.isElement( oSib ) )
	{
	 	oSib.insert({after:el});
	}
}



function insert_blank(el)
{
	var t = '<li><span class="location_name">#{name}</span> <span class="location_menu"></span></li>';
	temp = new Template( t );
	
	data = new Hash();
	data.set('name','' );
	
	html = temp.evaluate( data );
	el.insert({before:html})	
	oSpan = el.previous('li').down('span',1);
	location_menu( oSpan );
}



function copy_to_top(el)
{
	var t = '<li><span class="location_name">#{name}</span><span class="location_menu"></span></li>';
	temp = new Template( t );
	
	name = el.down('span').innerHTML;
	data = new Hash();
	data.set('name',name );
	
	html = temp.evaluate( data );
	el.up('ul').down(0).insert({before:html});
	oSpan = el.up('ul').down(0).down('span',1);
	location_menu( oSpan );
}

function location_delete( el )
{
	el.remove();
}
//]]>
</script>
