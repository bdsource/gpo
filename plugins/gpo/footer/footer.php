<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Module\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;

require_once JPATH_ROOT . '/components/com_gpo/helpers/footer_helper.php';

class plgGpoFooter extends CMSPlugin
{
    public function onAfterDisplayContent(&$article)
    {   
        $app = Factory::getApplication();
        $jinput = $app->input;

        $uri = $jinput->server->get('REQUEST_URI', '', 'string');
        if (strpos($uri, 'staff-notes') !== false) {
            return;
        }

        $now = time();
        $url = Uri::getInstance()->toString();
        $ts_modified = strtotime($article->modified);
        $title = empty($article->DPTitle) ? $article->title : $article->DPTitle;
       // $article_authors = footerhelper::getArticleAuthors();
	   $footerHelper = new footerhelper();
	   $article_authors = $footerHelper->getArticleAuthors();
	   
        ob_start();
        ?>
        <?php if ($article_authors === false) : ?>
            <h3 id="region-footer-head"></h3>
            <div id="region-footer"></div>
        <?php else : ?>
            <h3 id="region-footer-head"><?php echo Text::_('COM_GPO_PLG_CITATION_FOOTER_HEADER'); ?></h3>
            <div id="region-footer">
                <span><?php echo $article_authors . " " . date('Y.', $ts_modified) . ' <span class="footer-title">' . GpoEndWith(".", $title); ?></span></span>
                <span>Global Action on Gun Violence (GAGV), <a href="http://www.actiononguns.org">www.actiononguns.org</a>, <?php echo date('j F.', $ts_modified); ?></span>
                <span>Accessed <?php echo date('j F Y.', $now); ?> at: <?php echo $url; ?></span>
            </div>
        <?php endif; ?>

        <?php
        ## DP Sponsors Central Column HTML above Footer ##
        require_once JPATH_SITE . "/modules/mod_gpo_sponsors/helper.php";
      //  $DPCentralSponsors = modgposponsorshelper::getSponsors(); 
		$modSponsorsHelper = new modgposponsorshelper();
		$DPCentralSponsors = $modSponsorsHelper->getSponsors();
	  
	 	 $DPSPHTML = "";
        $db = Factory::getContainer()->get(DatabaseDriver::class);
        if (is_array($DPCentralSponsors)) {
            $mods = [];
            $DPSPHTML = "<br />";
            foreach ($DPCentralSponsors as $row) {
                if (empty($row->module_id)) {
                    continue;
                }

                // Query for specified module then render it
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__modules'))
                    ->where([
                        $db->quoteName('id') . ' = ' . (int) $row->module_id,
                        $db->quoteName('published') . ' = 1',
                        $db->quoteName('client_id') . ' = ' . (int) $app->getClientId()
                    ])
                    ->setLimit(1);

                $db->setQuery($query);
                $mods[] = $db->loadObject();
            }
            
            /* now render the modules */
            foreach ($mods as $_mod) {
                if (is_object($_mod)) {
                    $_options = ['style' => 'xhtml'];
                    // Check for moduleclass_sfx parameter
                    $paramsdata = $_mod->params;
                    $_params = new Joomla\Registry\Registry($paramsdata);
                    $_moduleclass_sfx = $_params->get('moduleclass_sfx');
                    if ($_moduleclass_sfx == 'dp-central-sponsors') {
                        $DPSPHTML .= ModuleHelper::renderModule($_mod, $_options);
                    }
                }
            }
        }

        $html = ob_get_contents();
        ob_end_clean();
        $article->footer = $html . $DPSPHTML;
    }
}
?>
