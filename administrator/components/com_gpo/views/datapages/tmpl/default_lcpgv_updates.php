<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
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
$front_end = str_replace( "administrator",'',JURI::base(true));
$columnTitles = getDPColumnTitles();

$file = 'select';
//$path = JPath::find(JHTML::addIncludePath(), strtolower($file).'.php');
$path = JPATH_LIBRARIES."/cms/html/".$file.'.php';
require_once( $path );

$shared_functions = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_gpo' . DS .'helper' . DS . 'shared_functions.php';
require_once( $shared_functions );


$options_country = '';
$options_region = '';
$options_group = '';
 
$db = JFactory::getDBO();
$query = "SELECT `id`,`name` FROM `#__gpo_groups`;";
$db->setQuery( $query );
$location_group = $db->loadAssocList('id');
foreach($location_group as $grp){
  $options_group.= '<option value="'.$grp['id'].'">'.$grp['name'].'</option>';
}

$data = GpoGetTypeFromCache( 'public_country' );

if( $data !== false )
{
	$items = explode("\n", $data );
	foreach( $items as $v )
	{
		$value = str_replace("&nbsp;","",$v);
		$options_country .= '<option value="' . $value . '">' . ucwords( $v ) . '</option>';
	}
}

//$data = GpoGetTypeFromCache( 'conflict_affected_country' );
if( $this->USJurisdictions !== false )
{
	foreach( $this->USJurisdictions as $k=>$v )
	{
		$value = str_replace("&nbsp;","",$v);
		$options_region .= '<option value="' . $value . '">' . ucwords( $v ) . '</option>';
	}
    }
?>

<form id="adminForm" name="adminForm" method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=datapages&task=lcpgv_updates', false); ?>">

<?php include_once('submenus_startblock.php'); ?>
<table border="0" cellpadding="0" cellspacing="0">

<?php
   if(!empty($this->errorMessage)) {
       echo "<tr border='1' style='color:red'><td>".$this->errorMessage."</td></tr>";
   }
?>
    
<tr>
<td>
<h3> Select US Jurisdiction and/or write Websource: </h3>

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="lcpgv_updates" />
<input type="hidden" name="ParentId" id="ParentId" value="0" />
<input type="hidden" name="isSubmitted" id="isSubmitted" value="1" /> 
<table width="100%" border="0" align="left" cellpadding="1" cellspacing="0">    
    
    <!-- add Region/Group/Location Dropdown -->
    
    <!--
    <select id="find_country" name="country" title="Find armed violence reduction facts by country " class="inputboxselect" style="width:150px;">
        <option value="">By Country</option>
        <?php // echo $options_country; ?>
    </select>
    -->
    
    <select style="margin-bottom:0px;" id="find_region" name="region"  title="Select the US Jurisdiction to find the Citation." class="inputboxselect" style="width:150px;">
        
         <option value="">Select by US Jurisdictions</option>
         <option value="">  </option>
         <option value="showall">  Show All  </option>
         <option value="">  </option>
        <?php echo $options_region; ?>
    </select>   
    <input style="margin-bottom:0px;margin-left:10px;" value="smartgunlaws.org" id="find_websource" name="websource"  title="Enter the URL [websource] to find the Last Modified Date." class="text" style="width:150px;">
    <!-- select box done -->       
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>    
    </table>
    </td>
   </tr>

<tr>
    <td colspan="3">
<!--
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
	<img id="h2loader" src="/media/system/images/move-spinner.gif" style="display:none;"/>
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
	  <img id="h3loader" src="/media/system/images/move-spinner.gif" style="display:none;"/>
	</td>
	
	<td>
	<br />
	<select name="h3column" id="h3column" multiple="multiple">
	<option value="0"> --Select level-3 category-- </option>
	</select>
	</td>
	</tr>
 	      
    <tr>
       <td>
           &nbsp;
       </td>
    </tr>
    </table>
-->

   </td></tr></table>
   <?php include_once('submenus_endblock.php'); ?>
</form>

<script type="text/javascript">
Event.observe(window,'load',function(){
    
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
});//end load
</script>