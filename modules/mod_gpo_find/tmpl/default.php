<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
use Joomla\CMS\Language\Text;


<div class="gpo_find" >
<script type="text/javascript">

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
		alert("<?php echo Text::_('MOD_GPO_FIND_ALERT');?>");
		return false;
	}
	return true;
}
</script>

<form method="get" action="<?php echo JRoute::_('index.php?option=com_gpo&task=find_facts', true );?>" id="find-gun-policy-facts">
    <h3>
        <?php
        $headerTwo = Text::_('MOD_GPO_FIND_HEADER_TWO');
        $title =  str_replace($headerTwo,'<span title="'. Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG') . 
                              '" style="color:#E38303;">'. $headerTwo .'</span>',Text::_('MOD_GPO_FIND_HEADER_ONE'));
        echo '<span title="'. Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG') .'">'.$title.'</span>';
        ?>
    </h3>
<!-- <h3><span  title="<?php echo Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG'); ?>" ><?php echo Text::_('MOD_GPO_FIND_HEADER_ONE'); ?> </span><span title="<?php echo Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG'); ?>" style="color:#E38303;" ><?php echo Text::_('MOD_GPO_FIND_HEADER_TWO'); ?></span></h3> -->
<p style="color:#048DD4"><span  title="<?php echo Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG'); ?>" ><?php echo Text::_('MOD_GPO_FIND_DESCRIPTION'); ?></span></p>

<select id="find_country" name="country" title="<?php echo Text::_('MOD_GPO_FIND_COUNTRY_TITLE_TAG'); ?>" class="inputboxselect" style="width:150px;" onchange='this.form.submit()' onfocus="reset_select('find_region' );">
	<option value=""><?php echo Text::_('MOD_GPO_FIND_FACTS_BY_COUNTRY'); ?></option>
	<?php echo $options_country; ?>
</select>
<!-- select id="find_region" name="region"  title="<?php echo Text::_('MOD_GPO_FIND_REGION_TITLE_TAG'); ?>" class="inputboxselect" style="width:150px;" onchange='this.form.submit()' onfocus="reset_select( 'find_country' );">
	<option value=""><?php echo Text::_('MOD_GPO_FIND_FACTS_BY_REGION'); ?></option>
	<?php echo $options_region; ?>
		
</select -->


<input title="<?php echo Text::_('MOD_GPO_FIND_SUBMIT_TITLE_TAG'); ?>" type="submit" value="<?php echo Text::_('MOD_GPO_FIND_SUBMIT_VALUE'); ?>" class="button" />
   <input type="hidden" name="option" value="com_gpo" />
   <input type="hidden" name="task" value="find_facts" />
</form>
</div>