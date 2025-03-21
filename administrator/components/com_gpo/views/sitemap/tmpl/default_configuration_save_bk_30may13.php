
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
<form id="sitemapform" action="<?php echo JRoute::_('?option=com_gpo&controller=sitemap&task=writesitemap');?>" method="post">
    The following contents has been written<br/>
    <textarea rows="25" cols="150"><?php echo $this->sitemap_contents;?></textarea>
</form>
