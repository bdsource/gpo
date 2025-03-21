<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();

$base_link='index.php?option=com_gpo&controller=sitemap';
?>
<style type="text/css">
    #sitemapform p label{
        font-size:12pt;
    }
    #sitemapform p textarea{
        font-size:8pt;
    }
</style>
<form id="sitemapform" action="<?php echo JRoute::_('?option=com_gpo&controller=sitemap&task=writesitemap');?>" method="post">
<?php include_once('submenus_startblock.php'); ?>
    <div class="row">   
		<div class="cell">
			<p> 	
				<label>Total URLs: <?php echo $this->totalURLs;?></label><br/>
			</p>
            <p> 	
				<label>Total English (En) URLs: <?php echo $this->totalENURLs;?></label><br/>
			</p>
            <p> 	
				<label>Total Français (FR) URLs: <?php echo $this->totalFRURLs;?></label><br/>
			</p>
            <p> 	
				<label>Total Español (ES) URLs: <?php echo $this->totalESURLs;?></label><br/>
			</p>
		</div>
	</div>
    
    <div class="row">   
		<div class="cell">
			<p> 	
                    <h1>To see the Sitemap, click on the link below, opens in a new window: </h1>
                    
                    <h1>
                         <a href="/sitemap_en.xml" title="English Sitemap, non-compressed" target="_blank"> Sitemap English </a> 
                    </h1>
                    
                    <h1>
                         <a href="/sitemap_es.xml" title="Español Sitemap, non-compressed" target="_blank"> Sitemap Español </a> <br />
                    </h1>
                    
                    <h1>
                         <a href="/sitemap_fr.xml" title="Français Sitemap, non-compressed" target="_blank"> Sitemap Français </a> <br />
			        </h1>
                    
                    <label>The following contents has been written in the 
                           <a href="/sitemap.xml" title="Sitemap Indedx file" target="_blank"> Sitemap index file </a></label><br/>
                    <textarea rows="20" cols="100"><?php echo $this->sitemap_contents;?></textarea>
            </p>
		</div>
	</div>
<?php include_once('submenus_endblock.php'); ?>	
</form>
