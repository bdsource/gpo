<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<style>
<!-- 
#adminForm {
  background-color: #9CF !important;
  padding: 5px;
}
-->
</style>
<?php include_once('submenus_startblock.php');?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">

<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="<?=$this->controller?>" />
<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<input type="hidden" name="action" value="<?php echo $this->action; ?>" />

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>"/>
<input type="hidden" name="filter_order_dir" value="<?php echo $this->filter_order_dir; ?>"/>
<?php
$searchOptions = array( 0 => 'Case sensitive',
                        1 => 'Case Insensitive'
                 );

$fieldNames = array( 'Title',
                     'Subtitle',
                     'Websource',
                     'Content',
                     'Modified',
					 'Share',
                     'Published'
               );

//get the default search values
$optionsArray = Joomla\CMS\Factory::getApplication()->getInput()->get('swap', false);
$optionsFrom = urldecode($optionsArray['from']);
$optionsTO = urldecode($optionsArray['to']);
$optionsColumn = urldecode($optionsArray['column_name']);
$optionsCase = urldecode($optionsArray['case_sensitive']);

if( 'case_sensitive' == $optionsCase ){
   $optionsCase = 1;
}
else if( 'case_insensitive' == $optionsCase ){
   $optionsCase = 0;
}
?>

<table id="search_input" cellspacing="2">
<tr>
	<td width="307">
	   Find:<br />
	   <textarea name="swap[from]" cols="70" rows="2"><?php echo $optionsFrom;?></textarea>	
	</td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>

<tr>
	<td>
	   <div align="left">
	        <span>Search Options:</span> <br />
		    <input type="radio" id="case_insensitive" name="swap[case_sensitive]" value="0" 
		    <?php if( isset($optionsCase) && 0 == $optionsCase ){ echo 'checked="checked"'; }
		            else if( !isset($optionsCase) ) { echo 'checked="checked"'; } 
		    ?>
		    /> 
		    Case <strong>In</strong>sensitive
	
	   <span align="left" style="padding-left: 5px;">
		    <input type="radio" id="case_sensitive" name="swap[case_sensitive]" value="1" 
		    <?php if( 1 == $optionsCase ) echo ' checked="checked" '; ?>
		    /> 
		    Case Sensitive
	   </span>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>

<tr>
    <td>
	   Replace With:<br />
	   <textarea name="swap[to]" cols="70" rows="2"><?php echo $optionsTO;?></textarea>	
	</td>
	<td>&nbsp;  </td>
</tr>
</table>

<p>
  
  <table cellpadding="2" cellspacing="2">
    <tr valign="top">
	
	<td>
	Find in Field:<br />
	<select name="column_name" id="column_name">
	<option value="0"> -- Select a Field to Search -- </option>
	<?php foreach( $fieldNames as $key=>$val)
	  {
	?>
	   <option title="<?php echo $val;?>" 
	           value="<?php if('Published (YYYY-MM-DD)' == $val ) { echo 'published'; }
	                        else{ echo strtolower($val); }?>"
	   <?php if( strtolower($val) == $optionsColumn ){ echo ' selected="selected" ';}?>
	   >
	           <?php echo $val;?>
	   </option>
	<?php
	  }
	?>
	</select>
	</td>
	
   </tr>
  </table>	
</p>

</form>
<?php include_once('submenus_endblock.php');?>

<script>
$("#action-save").click( function( event ) {
	event.stopPropagation();
	$("#adminForm").submit();
});
</script>