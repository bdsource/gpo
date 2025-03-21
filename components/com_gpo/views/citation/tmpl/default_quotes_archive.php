<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$jView = new JViewLegacy();
$metaDescription = JText::_('COM_GPO_POPUP_METADATA_DESCRIPTION');

//$u =& JFactory::getURI();
$u = \Joomla\CMS\Uri\Uri::getInstance();

$citations = $this->citations;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="<?php echo $metaDescription;?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
		<title>Find Citations from the Alpers.org Literature Library</title>
		<link rel="stylesheet" href="<?php echo $u->base();?>templates/gunpolicy/css/citation-quotes.css?v=0.6" type="text/css" />
		<link rel="stylesheet" href="<?php echo $u->base(); ?>/templates/gunpolicy/css/print_popup.css" type="text/css" media="Print" />
		<link rel="stylesheet" href="<?php echo $u->base(); ?>/media/function_view/assets/css/citation.css" type="text/css" media="Print" />
		<style type="text/css">
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
	    <li><a href="/firearms/citation/<?php echo $this->type;?>/"><?php echo ucfirst($this->type);?></a></li>
	    <?php if(!empty($this->alphabet)){ ?>
	    <li><a href="/firearms/citation/<?php echo $this->type.'/'.strtoupper($this->alphabet);?>"><?php echo ucfirst($this->alphabet);?></a></li>
	    <?php
	    }
	    ?>
	</ul>
    </div>
    <h3 style="color:#048DD4;">Search our gun policy literature (Quotes) library articles, <span style="color: #E8921E;">sorted by Title:</span></h3>

    <?php
    echo generateAlphabetLinks();
    echo '<br/><br/>';
    if(!count($citations)){
        echo '<center>No citation titles begin with this letter!</center><br/>';
    } else {
    ?>
        <h2 style="margin-bottom:1px;color:#048DD4;margin-top: 1px;">Citations</h2>
	<p style="color:#048DD4;">Sorted by Title</p><p>&nbsp;</p>
        <ul>
        <?php
        foreach($citations as $citation){
            echo '<li class="cites"><a style="text-decoration:none; color: black;font-size:80%" href="/firearms/citation/quotes/'.$citation->id.'">'.citation_formater($citation, 'q').'</a></li>'.PHP_EOL;
        }
        ?>
        </ul>
        <?php } ?>
        <p></p>
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