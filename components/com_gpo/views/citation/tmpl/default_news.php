<?php
defined('_JEXEC') or die('Restricted Access');

$jView = new JViewLegacy();
$metaDescription = JText::_('COM_GPO_POPUP_METADATA_DESCRIPTION');

$parts = array();

if (!empty($this->citation->title)) {
    $parts['title'] = '<p class="titles" style="color:#048DD4;font-weight:bold;">' . $this->citation->title . '</p>';
}

if (!empty($this->citation->published)) {
    $str = date('j F Y', strtotime($this->citation->published));
    $parts['published'] = '<p class="titlesb">' . $str . '</p>';
}


if (!empty($this->citation->subtitle)) {
    $parts['subtitle'] = '<p class="titles" style="color:#777777;font-weight:bold;">' . $this->citation->subtitle . '</p>';
}

if (!empty($this->citation->source) || !empty($this->creation->category)) {
    $str = '';
    if (!empty($this->citation->source)) {

        $str .= '<p class="titlesb">' . $this->citation->source;
    }
    if (!empty($this->citation->category)) {
        //added comma with space before source, category
        $str .= ( (!empty($str) ) ? ", " : "<p>" ) . $this->citation->category . '</p>';
    } else {
        $str .= '</p>';
    }
    $parts['source_category'] = $str;
}


if (!empty($this->citation->byline)) {
    $parts['byline'] = '<span class="byline" title="byline"> By ' . $this->citation->byline . '</span>';
}

$order = explode(",", "title,subtitle,source_category,published,byline");
/*
  if( empty( $this->citation->byline ) )
  {
  $order = explode(",", "title,subtitle,source_category,byline" );
  }else{
  $order = explode(",", "title,subtitle,source_category,byline" );
  }

 */

$html = '';
foreach ($order as $item) {
//	$html .= ( isset( $parts[$item] )  )? $parts[$item] . "<br />": '';
    $html .= ( isset($parts[$item]) ) ? $parts[$item] . "" : '';
}

//$html_link = '';
//if ($this->citation->websource !== 'No Web Source') {
//    $html_link .= '<a href="' . $this->citation->websource . '" title="" target="_blank">' . $this->citation->websource . '</a>';
//} else {
//    $html_link .= "<a href=\"javascript:NewWindow=window.open('" . JRoute::_('index.php?option=com_gpo&task=search&view=nowebsource', false) . "','newWin','width=600,height=230,left=0,top=0,toolbar=yes,location=center,scrollbars=No,status=No,resizable=No,fullscreen=No');NewWindow.focus();void(0);\">No Web Source</a>";
//}



$html_content = '';
$html_content = str_replace("\r\n", "\n", $this->citation->content);
$html_content = str_replace("\n", "<br />", $html_content);
$u = &Joomla\CMS\Uri\Uri::getInstance();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="<?php echo $metaDescription;?>" />
        <title><?php echo $this->citation->title; ?> – Alpers.org</title>
        <link rel="stylesheet" href="<?php echo $u->base(); ?>templates/gunpolicy/css/citation-news-popup.css?v=0.8" type="text/css" />
        <link rel="stylesheet" href="<?php echo $u->base(); ?>/templates/gunpolicy/css/print_popup.css" type="text/css" media="Print" />
		<link rel="stylesheet" href="<?php echo $u->base(); ?>/media/gpo/css/citation.css" type="text/css" media="Print" />
		<style type="text/css">
			#nav ul{
			margin:0px;
			}
			#nav a{
			color: #E8921E;
			text-decoration: none;
			}
			#nav a:hover{
			color: #048DD4;
			}
			#nav li{
			display:inline;
			padding: 8px 12px;
			background: url("/images/M_images/arrow.png") no-repeat scroll 0 12px #FFFFFF;
			list-style-type: none;
			}
			/* style at li */
			dl.references{
				display:block;
			}
			#content span{
				padding-right:2px;
			}
			#content li{
			    padding: 8px 15px;
			    background: url("/images/M_images/arrow.png") no-repeat scroll 0 12px #FFFFFF;
			    list-style-type: none;
			    margin-left: 0;
			}
			#content li a:hover span {
			    color: #E8921E;
			}
			#content span.title{
			    font-weight:bold;
			}
			#nav ul{
			    margin:0px;
			    padding:0px;
			}
			#nav a{
			    color: #E8921E;
			    text-decoration: none;
			}
			#nav a:hover{
			    color: #048DD4;
			}
			#nav li{
			    display:inline;
			    padding: 8px 12px;
			    background: url("/images/M_images/arrow.png") no-repeat scroll 0 12px #FFFFFF;
			    list-style-type: none;
			}
		</style>
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit_l.gif" width="531" height="83" alt="alpes.org popup logo"/>
            </div>

            <div id="content">
		<?php
		if(preg_match('%/firearms/citation%i', $_SERVER['HTTP_REFERER'])) {
		    ?>
		<div id="nav"><br/>
		    <ul>
			<li><a href="/">Home</a></li>
			<li><a href="/firearms/citation">Citation</a></li>
			<li><a href="/firearms/citation/<?php echo $this->type;?>/"><?php echo ucfirst($this->type);?></a></li>
			<?php if(!empty($this->alphabet)){ ?>
			<li><a href="/firearms/citation/<?php echo $this->type.'/'.strtoupper($this->alphabet);?>"><?php echo ucfirst($this->alphabet);?></a></li>
			<?php
			}
			?>
		    </ul>
		</div>
		<?php
		}
		?>
	    
                <h3 style="color:#048DD4;">Citation(s) from the <span style="color: #E8921E;">Alpers News</span> media archive</h3>

<?php if (!empty($this->citation->id)): ?>

                    <div class="buttonbar">
                        <a class="buttonlink print" href="javascript: window.print();">Print</a>
                        <a class="buttonlink close" href="javascript: self.close();">Close</a>
                    </div>

                    <div id="citation">
<?php echo $html; ?>
                    </div>

<?php if (!empty($html_content)): ?>
                <div id="content">
                    <h3>Relevant contents</h3>
                    <p><?php echo $html_content; ?></p>
                </div>
<?php
    endif;
?>

<?php
    if ($this->logged_in) {
        $href_web_source = JRoute::_('index.php?option=com_gpo&task=news&id=' . $this->citation->ext_id);
        $a_web_source = '<a href="javascript:NewsWindow=window.open(\''
                . JRoute::_('index.php?option=com_gpo&task=news&id=' . $this->citation->ext_id)
                . '\',\'newNewsWindow\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=540,height=600\');'
                . 'NewsWindow.focus();" title="'.JText::_('COM_GPO_READ_FULL_ARTICLE').'">'.JText::_('COM_GPO_READ_FULL_ARTICLE').'</a>';
?>

                <div id="read_more">
                    <p> <?php echo $a_web_source; ?> </p>
                </div>
                <?php
            }
                ?>

                <?php if (!empty($this->citation->websource)): ?>
                    <div id="citation-link">
                        <p><?php echo JText::_('COM_GPO_NEWS_PUBLISHERS_LINK');?>  <br /><?php echo addHTTP($this->citation->websource); ?></p>
                    </div>
<?php endif; ?>


                <?php else: ?>
                        <p>The citation you are looking for seems to have gone.</p>
<?php endif; ?>
                    </div>

                    <div id="citation-id">
                        <span>ID: N<?php echo $this->citation->id; ?></span>
                    </div>
                    <div class="clear"></div>
                    <div>
                        <p style="color:#4d4d4d; font-size:11px;"><em>
                                As many publishers change their links and archive their pages, the full-text version of this article may no longer be available from the original link. In this case, please go to the publisher's web site or use a search engine.</em></p>
                    </div>
                    <div class="clear"></div>


                    <div id="footer">
                        <p>
                            <span style="color: #E8921E;">Alpers.org</span> provides evidence-based, public health-oriented information on<br />
                            gun violence, small arms policy and firearm-related injury around the world.<br />
                            &copy; Alpers.org <?php echo date("Y"); ?>
                </p>
            </div>

        </div>
    </body>
</html>