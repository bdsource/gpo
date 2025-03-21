<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$this->access ='1';

if ($this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) :
?>
<table class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
<?php
	//show_pdf_icon
	//show_pdf_icon
	//show_print_icon
?>

		<?php if ($this->params->get('show_email_icon')) : ?>
		<td align="right" width="100%" class="buttonheading">
		<?php echo JHTML::_('icon.email',  $this->article, $this->params, $this->access); ?>
		</td>
		<?php endif; ?>

</tr>
</table>
<?php endif; ?>

<table class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?>">

<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
<tr>
	<td valign="top">
		<span class="small">
			<?php JText::printf( 'Written by', ($this->article->created_by_alias ? $this->article->created_by_alias : $this->article->author) ); ?>
		</span>
		&nbsp;&nbsp;
	</td>
</tr>
<?php endif; ?>


<?php if ($this->params->get('show_create_date')) : ?>
<tr>
	<td valign="top" class="createdate">
		<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?>
	</td>
</tr>
<?php endif; ?>


<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
<tr>
	<td valign="top">
		<a href="//<?php echo $this->article->urls ; ?>" target="_blank">
			<?php echo $this->article->urls; ?></a>
	</td>
</tr>
<?php endif; ?>


<tr>
<td valign="top">
<?php echo $this->article->text; ?>
</td>
</tr>


<?php if ( intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
<tr>
	<td class="modifydate" style="padding-top:20px;">
			<?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2'))); ?>
	</td>
</tr>
<?php endif; ?>

</table>