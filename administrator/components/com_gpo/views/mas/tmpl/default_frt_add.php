<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<style>
<!--
#adminForm {
  background-color: #FFF880 !important;
  padding: 5px;
}
-->
</style>
<?php include_once('submenus_startblock.php'); ?>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=mas'); ?>" class="mas_FR" id="adminForm" name="adminForm">

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

$fieldNames = array( 
                     'state_province' => 'State/Province',
                     'city' => 'City',
                     'primary_venue' => 'Primary venue',
                     'venue_type' => 'Venue type',
                     'shooting_type' => 'Shooting type',
                     'longitude' => 'Longitude',
                     'latitude' => 'Latitude',
                     'victims_shot_dead' => 'Victims shot dead',
                     'victims_killed_other_means' => 'Victims killed other means',
                     'victims_killed_total' => 'Victims killed total',
                     'victims_wounded' => 'Victims wounded',
                     'perpetrators_killed_others' => 'Perpetrators killed others',
                     'perpetrators_killed_suicide' => 'Perpetrators killed suicide',
                     'perpetrators_captured_escaped' => 'Perpetrators captured escaped',
                     'primary_perpetrator_name' => 'Primary perpetrator name',
                     'perpetrators_gender' => 'Perpetrators gender',
                     'perpetrators_age' => 'Perpetrators age',
                     'perpetrators_previous_illness' => 'Perpetrators previous illness',
                     'perpetrators_previous_violence' => 'Perpetrators previous violence',
                     'primary_firearm_type' => 'Primary firearm type',
                     'primary_firearm_action' => 'Primary firearm action',
                     'primary_firearm_make' => 'Primary firearm make',
                     'primary_firearm_obtained_legally' => 'Primary firearm obtained legally',
                     'secondary_firearm_type' => 'Secondary firearm type',
                     'secondary_firearm_action' => 'Secondary firearm action',
                     'secondary_firearm_make' => 'Secondary firearm make',
                     'secondary_firearm_obtained_legally' => 'Secondary firearm obtained legally',
                     'citizen_armed_intervention' => 'Citizen armed intervention',
                     'narrative' => 'Narrative',
                     'share' => 'Share',
                     'staff' => 'Staff'                    
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
<div class="responsive">
<table id="search_input" cellspacing="2" class="table-hover">
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
</div>
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
	   <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
	<?php
	  }
	?>
	</select>
	</td>
	
   </tr>
  </table>	
</p>


</form>
<?php include_once('submenus_endblock.php'); ?>

<script>
$("#action-save").click( function( event ) {
	event.stopPropagation();
	$("#adminForm").submit();
});
</script>
