<?php
defined('_JEXEC') or die('Restricted Access');

$jView = new JViewLegacy();
$u = & Joomla\CMS\Uri\Uri::getInstance();

$metaDescription = JText::_('COM_GPO_POPUP_METADATA_DESCRIPTION');
$metaTitle = JText::_('COM_GPO_POPUP_METADATA_TITLE');
$metaTitle = str_replace('~', $this->glossary->title, $metaTitle);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
    <meta name="description" content="<?php echo $metaDescription;?>" />
    <title><?php echo $metaTitle;?></title>
    <link rel="stylesheet" href="<?php echo $u->base(); ?>templates/gunpolicy/css/glossary.css?v=0.6"
          type="text/css"/>
    <link rel="stylesheet" href="<?php echo $u->base(); ?>/templates/gunpolicy/css/print_popup.css" type="text/css"
          media="Print"/>
    <style type="text/css">
        #nav ul {
            margin: 0px;
        }

        #nav li {
            display: inline;
            padding: 8px 12px;
            background: url("/images/M_images/arrow.png") no-repeat scroll 0 12px #FFFFFF;
            list-style-type: none;
        }

        #nav a {
            color: #E8921E;
            text-decoration: none;
        }

        #nav a:hover {
            color: #048DD4;
        }
        sup a.incontent-citation{
            text-decoration: none;
        }

    </style>
</head>
<body>
<!--
<?php //ftp_debug( $parts, 'parts', true, false );  ?>
        -->
<div id="wrapper">
    <div id="header">
        <img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit_l.gif" width="531" height="83"
             alt="gunpolicy.org popup logo"/>
    </div>
 <div id="content">
        <h3 style="color:#048DD4;">Glossary, Definitions and Help - <span style="color: #E8921E;">Alpers.org</span> literature library</h3>
        <?php if (!empty($this->glossary->id)): ?>
        <div id="title"><?php echo $this->glossary->title;?></div>
        <?php if (!empty($this->glossary->subtitle)) { ?>
            <div id="subtitle"><?php echo $this->glossary->subtitle;?></div>
            <?php } ?>

        <div id="glossary"><h1><?php echo nl2br($this->glossary->content); ?></h1></div>


        <?php if (!empty($this->glossary->websource)): ?>
            <div id="citation-link">
                <p>
                    Last accessed at: <?php echo addHTTP($this->glossary->websource); ?>

                </p>
            </div>
            <?php endif; ?>

        <div id="glossary-id">
            <span>ID: G<?php echo $this->glossary->id; ?></span>
        </div>
        <?php else: ?>
        <p>The glossary you are looking for seems to have gone.</p>
        <?php endif; ?>

    </div>


    <div class="clear"></div>
    <div id="footer">
        <p>
            <span style="color: #E8921E;">Alpers.org</span> provides evidence-based, public health-oriented
            information on<br/>
            gun violence, small arms policy and firearm-related injury around the world.<br/>
            &copy; Alpers.org <?php echo date("Y"); ?>
        </p>
    </div>

</div>
<!-- a -->
</body>
</html>