<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * @package gpo
 * @author Murshed Ahmmad Khan
 * @link http://www.usamurai.com
 * @license GPL, This script does not come with any expressed or implied warranties! Use at your own risks!
 */
function generateAlphabetLinks($type='quotes') {
    $chars = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';
    $chars = explode(' ', $chars);
    $links = array();
    foreach ($chars as $char) {
        $links[] = '<a href="/firearms/citation/' . $type . '/' . $char . '">' . $char . '</a>';
    }
    return '<p style="text-align:center;padding:0 50px;">' . implode(' :: ', $links) . '</center></p>';
}

function citation_formater(&$citation, $type='q') {
    //var_dump($citation);
    switch ($type) {
        case 'n':  //news


            $parts = array();

            if (!empty($citation->byline)) {
                $str = GpoEndWith(".", $citation->byline);
                $parts['byline'] = '<span class="author" title="byline">' . $str . '</span>';
            }

            if (!empty($citation->published)) {
                $str = date('Y', strtotime($citation->published)) . '.';
                $parts['published_year'] = '<span class="published" title="published year">' . $str . '</span>';

                $str = date('j F', strtotime($citation->published)) . '.';
                $parts['published_daymonth'] = '<span class="published" title="published day month">' . $str . '</span>';
            }

            if (!empty($citation->title)) {
                $str = GpoEndWith(".", $citation->title);
                $parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
            }

            if (!empty($citation->source)) {
                $str = GpoEndWith(".", $citation->source);
                $parts['source'] = '<span class="source" title="source">' . $str . '</span>';
            }


            if (!empty($citation->id)) {
                $parts['id'] = '<span class="id" title="ID">(' . ucfirst($type) . $citation->id . ')</span>';
            }

            if (!empty($citation->title)) {
                $link_info = "Show citation: &lsquo;" . GpoEndWith(".", $citation->title) . "&rsquo;";
            } else {
                $link_info = "Show citation: &lsquo;" . GpoEndWith(".", $citation->source) . "&rsquo;";
            }

            $parts['link'] = '<a class="link" href="javascript:popup=window.open(\'' . JRoute::_('index.php?option=com_gpo&task=citation&type=' . $citation->type_long . '&id=' . $citation->id, false) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . $link_info . '">Full Citation</a>';


            if (!empty($citation->byline)) {
                $order = explode(",", "byline,published_year,title,source,published_daymonth,id");
            } else {
                $order = explode(",", "source,published_year,title,published_daymonth,id");
            }
            break;

        case 'q':
            $parts = array();

            if (!empty($citation->author)) {
                $str = GpoEndWith(".", $citation->author);
                $parts['author'] = '<span class="author" title="author">' . $str . '</span>';
            }

            if (!empty($citation->published)) {
                $str = date('Y', strtotime($citation->published)) . '.';
                $parts['published_year'] = '<span class="published" title="published year">' . $str . '</span>';

                $str = date('j F', strtotime($citation->published)) . '.';
                $parts['published_daymonth'] = '<span class="published" title="published day month">' . $str . '</span>';
            }

            if (!empty($citation->title)) {
                $str = GpoEndWith(".", $citation->title);
                $parts['title'] = '<span class="title" title="title">&lsquo;' . $str . '&rsquo; </span>';
            }

            if (!empty($citation->source)) {
                $str = GpoEndWith(".", $citation->source);
                $parts['source'] = '<span class="source" title="source">' . $str . '</span>';
            }

            if (!empty($citation->city)) {
                $parts['city'] = '<span class="city" title="city">' . $citation->city . ':</span>';
            }
            if (!empty($citation->publisher)) {
                $str = GpoEndWith(",", $citation->publisher);
                $parts['publisher'] = '<span class="publisher" title="publisher">' . $str . '</span>';
            }

            if (!empty($citation->id)) {
                $parts['id'] = '<span class="id" title="ID"> (' . ucfirst($type) . $citation->id . ')</span>';
            }


            if (!empty($citation->title)) {
                $link_info = "Show citation: " . GpoEndWith(". ", $citation->author) . '&lsquo;' . GpoEndWith('.&rsquo;', $citation->title) . GpoEndWith('. ', $citation->publisher) . GpoEndWith(')', '(' . strtoupper($type) . $citation->id);
            } else {
                $link_info = "Show citation: " . GpoEndWith(". ", $citation->author) . GpoEndWith('. ', $citation->source) . GpoEndWith('. ', $citation->publisher) . GpoEndWith(')', ' (' . strtoupper($type) . $citation->id);
            }

            $parts['link'] = '<a class="link" href="javascript:popup=window.open(\'' . JRoute::_('index.php?option=com_gpo&task=citation&type=' . $citation->type_long . '&id=' . $citation->id, false) . '\',\'GunPolicyCitation\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600\'); popup.focus();" title="' . $link_info . '">Full Citation</a>';
            if (!empty($citation->author)) {
                $order = explode(",", "author,published_year,title,source,city,publisher,published_daymonth,id");
            } else {
                $order = explode(",", "publisher,published_year,title,source,city,published_daymonth,id");
            }
            break;
    }

    //format it
    $html = '';
    foreach ($order as $item) {
        $html .= ( isset($parts[$item]) ) ? $parts[$item] : '';
    }
    return $html;
}

function autoCorrectionHTTPLink($websource){

    $vis_link = '';
    
    
    if (strlen($websource) < 98) {
        $vis_link = $websource;
    } else {
        $vis_a =
            substr_replace($websource, "", 98);
        $vis_b =
            substr_replace($websource, "", 0, 98);
        $vis_link = $vis_a . "<br />" . $vis_b;
    }


    $html_link = '';
    if( $article->websource !== 'NoWebSource' && $article->websource !== 'No Web Source') {
        $link_protocol = strtolower( substr($websource,0,5) );
        $link_url = ($link_protocol == 'http:' || $link_protocol == 'https') ? $websource : 'http://' . $websource;
        //$html_link = 'Last accessed at:<br /><a href="' . (substr($this->citation->websource,0,5)=='http:'||'https'?$this->citation->websource:'http://'.$this->citation->websource) . '" title="" target="_blank">' .
        $html_link = '<a href="' .
            $link_url .
            '" title="" target="_blank">' .
            $vis_link
            . '</a>';
    } else {
        $html_link .= "<a href=\"javascript:NewWindow=window.open('" . JRoute::_('index.php?option=com_gpo&task=search&view=nowebsource', false) . "','newWin','width=600,height=230,left=0,top=0,toolbar=yes,location=center,scrollbars=No,status=No,resizable=No,fullscreen=No');NewWindow.focus();void(0);\">No Web Source</a>";
    }

    return $html_link;

}

function addHTTP($websource){
    $html_link = '';
    if( $article->websource !== 'NoWebSource' && $article->websource !== 'No Web Source') {
        $link_protocol = strtolower( substr($websource,0,5) );
        $link_url = ($link_protocol == 'http:' || $link_protocol == 'https') ? $websource : 'http://' . $websource;
        //$html_link = 'Last accessed at:<br /><a href="' . (substr($this->citation->websource,0,5)=='http:'||'https'?$this->citation->websource:'http://'.$this->citation->websource) . '" title="" target="_blank">' .
        $html_link = '<a href="' .
            $link_url .
            '" title="" target="_blank">' .
            $websource
            . '</a>';
    } else {
        $html_link .= "<a href=\"javascript:NewWindow=window.open('" . JRoute::_('index.php?option=com_gpo&task=search&view=nowebsource', false) . "','newWin','width=600,height=230,left=0,top=0,toolbar=yes,location=center,scrollbars=No,status=No,resizable=No,fullscreen=No');NewWindow.focus();void(0);\">No Web Source</a>";
    }

    return $html_link;
}

function addOnlyHTTP($websource) {
    $html_link = '';
    if( $article->websource !== 'NoWebSource' && $article->websource !== 'No Web Source') {
        $link_protocol = strtolower( substr($websource,0,5) );
        $link_url = ($link_protocol == 'http:' || $link_protocol == 'https') ? $websource : 'http://' . $websource;
        $html_link = $link_url;
    } else {
        $html_link = $websource;
    }

    return $html_link;
}