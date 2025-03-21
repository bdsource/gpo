<?php
defined('_JEXEC') or die('Restricted Access');

$jView = new JViewLegacy();
//$u =& JFactory::getURI();
$u = \Joomla\CMS\Uri\Uri::getInstance();

$metaDescription = JText::_('COM_GPO_POPUP_METADATA_DESCRIPTION');
$metaTitle       = JText::_('COM_GPO_POPUP_METADATA_TITLE');
//$metaTitle       = str_replace('~', $this->glossary->title, $metaTitle);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="description" content="<?php echo $metaDescription;?>" />
        <title>Find Citations from the Alpers.org Literature Library - Home</title>
        <link rel="stylesheet" href="<?php echo $u->base(); ?>templates/gunpolicy/css/citation-quotes.css?v=0.6" type="text/css" />
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
		</style>
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit_l.gif" width="531" height="83" alt="alpers.org popup logo"/>
            </div>

            <div id="content" style="">
		<div id="nav"><br/>
		    <ul>
			<li><a href="/">Home</a></li>
			<li><a href="/firearms/citation">Citation</a></li>
		    </ul>
		</div>
                <h3 style="color:#048DD4;">Citations from the <span style="color: #E8921E;">Alpers.org</span> literature and news libraries</h3>

                <ul>
		    <p>Search our library of mass media (News) articles, sorted by Title:</p>
		    <?php echo generateAlphabetLinks('news'); ?>
		    <p>&nbsp;</p>
		    <p>Search our gun policy literature (Quotes) library, sorted by Title:</p>
		    <?php echo generateAlphabetLinks('quotes'); ?>
		    
                </ul><p></p>
                <p>&nbsp;</p>
            </div>

            <div class="clear"></div>
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