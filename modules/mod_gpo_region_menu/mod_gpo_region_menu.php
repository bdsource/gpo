<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

// Include the region model (using forward slashes)
include_once(JPATH_BASE . '/components/com_gpo/models/region.php');

if (Gpo_allow_region('Public Backend') === false)
{
    $sql_add = ' AND `lo`.`display`=1 ';
}
else
{
    $sql_add = ' ';
}

$jinput       = Factory::getApplication()->input;
$article_id   = $jinput->get('id', false, 'INT');
$base_location = $jinput->get('base_location', false, 'INT');
$db           = Factory::getDbo();

// If no article id is provided but a base location is, try to get the article id
if (empty($article_id) && !empty($base_location))
{
    // Get Category ID from the location
    $query = 'SELECT lo.id, lo.type, lo.name, cat.id AS catid
              FROM #__gpo_location AS lo
              INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
              WHERE lo.id = ' . $db->quote($base_location);
    $db->setQuery($query);
    $locResult = $db->loadObject();
    $catId     = $locResult->catid;

    // Get Content/Article ID from the content table based on the category
    if (!empty($catId))
    {
        $query = 'SELECT a.id
                  FROM #__content AS a
                  LEFT JOIN #__categories AS cc ON cc.id = a.catid
                  WHERE a.catid = ' . $db->quote($catId) .
                 ' LIMIT 0,1';
        $db->setQuery($query);
        $articleObj = $db->loadObject();
        $article_id = !empty($articleObj) ? $articleObj->id : false;
    }
}

if (!empty($article_id))
{
    // Get information on location and the corresponding category
    $query = 'SELECT lo.id, lo.type, cat.id AS cat_id, lo.name AS name
              FROM #__gpo_location AS lo
              INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
              INNER JOIN #__content AS a ON cat.id = a.catid
              WHERE a.id = ' . $db->quote($article_id) . ' ' . $sql_add . ' LIMIT 0,1';
    $db->setQuery($query);
    $item = $db->loadObject();

    if (!isset($item->id))
    {
        return;
    }

    // Set category and location id
    $cat_id     = $item->cat_id;
    $location_id = $item->id;

    switch ($item->type)
    {
        case 'jurisdiction':
            $query = 'SELECT link.location_id, lo.name
                      FROM #__gpo_location_links AS link
                      INNER JOIN #__gpo_location AS lo ON lo.id = link.link_id
                      WHERE link.link_id = ' . $db->quote($location_id) . ' ' . $sql_add . ' LIMIT 0,1';
            $db->setQuery($query);
            $current_location_id = $location_id;
            $locResult = $db->loadObject();
            // (For debugging, you may echo the location_id, then remove the echo.)
            echo $location_id = $locResult->location_id;
            $regionModel = new GpoModelRegion();
            $parentObj   = $regionModel->getCatIdByLocationId($locResult->location_id);

            // If location id is empty, exit.
            if (empty($location_id)) {
                return;
            }
            $query = 'SELECT cat.id, cat.title AS title, lo.name_es AS name_es, lo.name_fr AS name_fr
                      FROM #__gpo_location_links AS link
                      INNER JOIN #__gpo_location AS lo ON lo.id = link.link_id
                      INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
                      WHERE link.location_id = ' . $db->quote($location_id) .
                      ' AND link.link_id != ' . $db->quote($current_location_id) . $sql_add .
                      ' ORDER BY cat.title ASC';
            $db->setQuery($query);
            $items = $db->loadObjectList();
            break;

        default:
            // Get Category Title and Id for default case
            $query = 'SELECT cat.id, cat.title AS title, lo.name_es AS name_es, lo.name_fr AS name_fr
                      FROM #__gpo_location_links AS link
                      INNER JOIN #__gpo_location AS lo ON lo.id = link.link_id
                      INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
                      WHERE link.location_id = ' . $db->quote($location_id) . $sql_add .
                      ' ORDER BY cat.title ASC';
            $db->setQuery($query);
            $items = $db->loadObjectList();

            if (empty($items))
            {
                // Get Parent Location Id to show all linked jurisdictions/countries
                $query = 'SELECT link.location_id, lo.name
                          FROM #__gpo_location_links AS link
                          INNER JOIN #__gpo_location AS lo ON lo.id = link.link_id
                          WHERE link.link_id = ' . $db->quote($location_id) . ' ' . $sql_add . ' LIMIT 0,1';
                $db->setQuery($query);
                $parentLocInfo = $db->loadObject();
                $parent_location_id = $parentLocInfo->location_id;

                if (empty($parent_location_id)) {
                    return;
                }

                $query = 'SELECT cat.id, cat.title AS title, lo.name_es AS name_es, lo.name_fr AS name_fr
                          FROM #__gpo_location_links AS link
                          INNER JOIN #__gpo_location AS lo ON lo.id = link.link_id
                          INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
                          WHERE link.location_id = ' . $db->quote($parent_location_id) .
                          ' AND link.link_id != ' . $db->quote($location_id) . $sql_add .
                          ' ORDER BY cat.title ASC';
                $db->setQuery($query);
                $items = $db->loadObjectList();
            }
            break;
    }

    // Get Articles
    $jnow     = Factory::getDate();
    $now      = $jnow->toSql();
    $nullDate = $db->getNullDate();

    $user = Factory::getUser();
    $aid = $user->get("aid", "0");

    if ($aid === 1)
    {
        $access = ' AND ( a.access = 1 OR a.access = 0 ) ';
    }
    else if ($aid === 2)
    {
        $access = '';
    }
    else
    {
        $access = ' AND a.access = 0 ';
    }

    $query = '
        SELECT a.id, a.alias, a.title
        FROM #__content AS a 
        LEFT JOIN #__categories AS cc ON cc.id = a.catid
        WHERE cc.id = ' . $db->quote($cat_id) . '
          AND a.id != ' . $db->quote($article_id) . '
          AND cc.published = 1
          AND a.state = 1
          ' . $access . '
          AND ( a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ' )
          AND ( a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ' )
    ';
    $db->setQuery($query);
    $articles = $db->loadObjectList();
}
else
{
    $query = 'SELECT cat.id, cat.title AS title, lo.name_es AS name_es, lo.name_fr AS name_fr
              FROM #__gpo_location AS lo
              INNER JOIN #__categories AS cat ON LOWER(lo.name) = LOWER(cat.title)
              WHERE lo.type = ' . $db->quote('region') . '
                AND lo.display = 1
              ORDER BY lo.name';
    $db->setQuery($query);
    $items = $db->loadObjectList();
}

// Uncomment the next line if you want to check for empty results:
// if (empty($items) && empty($articles)) return;

require(ModuleHelper::getLayoutPath('mod_gpo_region_menu'));
?>
