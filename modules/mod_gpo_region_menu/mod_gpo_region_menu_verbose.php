<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

if (Gpo_allow_region('Public Backend') === false)
{
    $sql_add = ' AND `lo`.`display` = 1 ';
}
else
{
    $sql_add = ' ';
}

$input = Factory::getApplication()->input;
$article_id = $input->get('id', false, 'STRING'); // Adjust type as needed

$db = Factory::getDbo();

if (!empty($article_id))
{
    // Get information on location and category
    $query = "
SELECT `lo`.`id`, `lo`.`type`, `cat`.`id` AS `cat_id`, `lo`.`name` AS `name`
FROM `#__gpo_location` AS `lo`
INNER JOIN `#__categories` AS `cat` ON LOWER(`lo`.`name`) = LOWER(`cat`.`title`)
INNER JOIN `#__content` AS `a` ON cat.id = a.catid
WHERE `a`.`id` = " . $db->quote($article_id) . "
" . $sql_add . "
LIMIT 0,1
";
    echo "<!-- GOTO ITEM query: $query -->";
    $db->setQuery($query);
    $item = $db->loadObject();

    if (!isset($item->id))
    {
        return;
    }
    // Set category and location IDs
    $cat_id = $item->cat_id;
    $location_id = $item->id;

    switch ($item->type)
    {
        case 'jurisdiction':
            $query = "
SELECT `location_id`
FROM `#__gpo_location_links` AS `link`
INNER JOIN `#__gpo_location` AS `lo` ON `lo`.`id` = `link`.`link_id`
WHERE `link`.`link_id` = " . $db->quote($location_id) . "
" . $sql_add . "
LIMIT 0,1;";
            echo "<!-- GOTO Jurisdiction first query: $query -->";
            $db->setQuery($query);
            $location_id = $db->loadResult();

            // If not linked, exit.
            if (empty($location_id))
            {
                return;
            }
            $query = 'SELECT `cat`.`id`, `cat`.`title` AS `title`
FROM `#__gpo_location_links` AS `link`
INNER JOIN `#__gpo_location` AS `lo` ON `lo`.`id` = `link`.`link_id`
INNER JOIN `#__categories` AS `cat` ON LOWER(`lo`.`name`) = LOWER(`cat`.`title`)
WHERE `link`.`location_id` = ' . $db->quote($location_id) . $sql_add . '
ORDER BY `cat`.`title` ASC';
            $db->setQuery($query);
            echo "<!-- Jurisdiction query: $query -->";
            $items = $db->loadObjectList();
            break;

        default:
            // Get Category Title and Id for non-jurisdiction types
            $query = 'SELECT `cat`.`id`, `cat`.`title` AS `title`
FROM `#__gpo_location_links` AS `link`
INNER JOIN `#__gpo_location` AS `lo` ON `lo`.`id` = `link`.`link_id`
INNER JOIN `#__categories` AS `cat` ON LOWER(`lo`.`name`) = LOWER(`cat`.`title`)
WHERE `link`.`location_id` = ' . $db->quote($location_id) . $sql_add . '
ORDER BY `cat`.`title` ASC';
            $db->setQuery($query);
            echo "<!-- Default query: $query -->";
            $items = $db->loadObjectList();
            break;
    }

    // Get Articles
    $jnow = Factory::getDate();
    $now = $jnow->toMySQL();
    $nullDate = $db->getNullDate();

    $user = Factory::getUser();
    $aid = $user->get("aid", "0");

    if ($aid === "1")
    {
        $access = ' AND ( a.access = 1 OR a.access = 0 ) ';
    }
    else if ($aid === "2")
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
    $query = 'SELECT `cat`.`id`, `cat`.`title` AS `title`
FROM `#__gpo_location` AS `lo`
INNER JOIN `#__categories` AS `cat` ON LOWER(`lo`.`name`) = LOWER(`cat`.`title`)
WHERE `lo`.`type` = ' . $db->quote('region') . '
  AND `lo`.`display` = 1
ORDER BY `lo`.`name`';
    echo "<!-- GOTO default regions query: $query -->";
    $db->setQuery($query);
    $items = $db->loadObjectList();
}

require ModuleHelper::getLayoutPath('mod_gpo_region_menu');
