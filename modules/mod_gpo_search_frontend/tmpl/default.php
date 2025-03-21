<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="gpo_search_frontend">
<script>
function reset_select( id )
{
	set = false;
	if (document.getElementById)
	{
		if( document.getElementById(id).selectedIndex != 0 )
		{
			document.getElementById(id).selectedIndex = '0';
			set = true;
		}
	}
	else if (document.all)
	{
		if( document.all[id].selectedIndex != 0 )
		{
			document.all[id].selectedIndex = '0';
			set = true;
		}
	}
	else {
		return true;
	}

	if( set == true )
	{
		alert( "<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_ALERT');?>" );
		return false;
	}
	return true;
}

function copyValue(srcVal, targetId)
{
  document.getElementById(targetId).value = srcVal;
}
</script>

<?php
$user = JFactory::getUser();
$userId = $user->get('id');
?>
<?php
  jimport( 'joomla.application.module.helper' );
  $module = JModuleHelper::getModule( 'gpo_search_frontend' );
  $jinput = JFactory::getApplication()->input;
  ?>

<form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo&task=search'); ?>">

    <h3>
        <?php
        $headerTwo = JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_NEWS');
        $title =  str_replace($headerTwo,'<span title="'. JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG') .
                              '" style="color:#E38303;">'. $headerTwo .'</span>',$module->title);
        echo '<span title="'. JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG') .'">'.$title.'</span>';
        ?>
    </h3>

  <!-- <h3><span title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>"><?php echo $module->title;?> </span>
  <span style="color:#E38303;" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>"><?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_NEWS'); ?></span></h3> -->
<label ><?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_KEYWORD_PHRASE'); ?></label>
<input type="text" name="q" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>" value="<?php echo htmlentities($jinput->get('q','', 'string'),ENT_COMPAT,'UTF-8' ); ?>" class="inputbox" <?php if( !empty($userId) ){?> onChange="copyValue( this.value, 'contentadv' );" <?php }?> />

<select id="search_country" name="country" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>" class="inputboxselect"  <?php if( !empty($userId) ){?> onChange="copyValue( this.value, 'countryadv' );" <?php }else{ echo "onchange='this.form.submit();'"; }?> onfocus="reset_select( 'search_region' );">
	<option value=""><?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_OPTION_ONE'); ?></option>
	<?php echo $options_country; ?>
</select>
<select id="search_region" name="region" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>" class="inputboxselect" <?php if( !empty($userId) ){?>  onChange="copyValue( this.value, 'regionadv' );" <?php }else{ echo "onchange='this.form.submit();'";}?> onfocus="reset_select( 'search_country' );">
	<option value=""><?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_OPTION_TWO'); ?></option>
	<?php echo $options_region; ?>	
</select>


<?php if( !empty($userId) )
{
// for members only version 
?>
<input type="submit" value="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_SUBMIT_NEWS_VALUE');?>" class="button" style="width:115px;margin-left:38px;" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_SUBMIT_NEWS_TITLE_TAG'); ?>" />

<?php
} else {
// for visitors
?>
<a style="margin-left:3px;"  class="searchhelp" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HELP_TITLE_TAG'); ?>" 
   href="javascript:popup=window.open('<?php echo JRoute::_( 'index.php?option=com_gpo&task=search&view=help', false );?>','GunPolicySearchHelp','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600'); popup.focus();"> 
    <?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HELP'); ?> 
</a>

<input style="display:block;" type="submit" value="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_SUBMIT_VALUE'); ?>" class="button" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_HEADER_TITLE_TAG'); ?>" />
<?php
}
?>
<input type="hidden" name="option" value="com_gpo">
	<input type="hidden" name="task" value="search">
<!-- /index.php?option=com_gpo&task=search&lang=en -->
</form>


<?php
//now show the Members Advanced Search button for the logged in users only
if( !empty($userId) ) { ?>
   <form method="get" action="<?php echo JRoute::_( 'index.php?option=com_gpo&task=msearch'); ?>">
      <div>
         
	     <span style="margin-left:3px;" class="hasTip" title="Help::Members Advanced search help tips">
            <a href="javascript:popup=window.open('<?php echo JRoute::_( 'index.php?option=com_gpo&task=search&view=help', false );?>','GunPolicySearchHelp','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600'); popup.focus();"><img border="0" style="margin-right:4px;" align="absmiddle" src="templates/gunpolicy/images/help_icon.gif"></a>         
         </span>
		 
	     <input type="submit" value="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_SUBMIT_VALUE_ADVANCED'); ?>" class="button" style="width:115px;margin-left:4px;" title="<?php echo JText::_('MOD_GPO_SEARCH_FRONTEND_SUBMIT_TITLE_TAG'); ?>" />
         
      </div>	  
    <input type="hidden" name="countryadv" id="countryadv" value="">
	<input type="hidden" name="regionadv" id="regionadv" value="">
	<input type="hidden" name="option" value="com_gpo">
	<input type="hidden" name="task" value="msearch">
   </form>

<?php
}
?>

</div>