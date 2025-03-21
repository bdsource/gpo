
<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();

$base_link='index.php?option=com_gpo&controller=sitemap';
?>
<style type="text/css">
    #sitemapform{
        font-size:14pt;
}
</style>
<form id="sitemapform" action="<?php echo JRoute::_('?option=com_gpo&controller=sitemap&task=saveconfigs');?>" method="post">
<?php include_once('submenus_startblock.php'); ?>
    <p>Include the following sections in Sitemap</p>
    <label>Region Data <input type="checkbox" name="region_data"  checked="checked"/></label><br/>
    <label>Citations Data <input type="checkbox" name="citation_data" checked="checked" /></label><br/>
    <label>Topics Data <input type="checkbox" name="topics_data"  checked="checked"/></label><br/>
    <label>News Data <input type="checkbox" name="news_data"  checked="checked" /></label><br/>
    <label>Miscellaneous Data <input type="checkbox" name="misc_data"  checked="checked" /></label><br/>
    <br/><br/>  <hr/>
    <label>Compress Sitemap <input type="checkbox" name="compress" checked /></label><br/>
    <br/><br/>
    <input type="submit" value="Regenerate Sitemap" />
	<?php include_once('submenus_endblock.php'); ?>
</form>
