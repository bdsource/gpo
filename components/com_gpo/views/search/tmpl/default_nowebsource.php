<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$u =& JFactory::getURI();
$jView = new JViewLegacy();

$source = $_GET['source'];
$sourceInfo =" ";
if( !empty( $source ) )
{
	$sourceInfo .= "(" . $jView->escape( $source ) . ") ";
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Alpers.org | No Web Source</title>
		<link rel="stylesheet" href="<?php echo $u->base();?>templates/gunpolicy/css/citation-news.css" type="text/css" />
<style>

</style>	
	</head>
	<body>
<div id="wrapper">
<div id="header">
	<img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo.gif" width="260" height="73" alt="gunpolicy.org popup logo"/>
</div>

<div id="content">
<p>
	We regret that the original web link to this article is no longer available.
</p>
<p>
Please look for it on the publisher's<?php echo $sourceInfo ?>web site, or use your favourite search engine.
</p>	

</div>
<div class="clear"></div>
<div id="footer">
  <p>
  <span class="logo">Alpers.org</span> provides evidence-based, public health-oriented information on<br />
  gun violence, small arms policy and firearm-related injury around the world.<br />
  © Alpers.org <?php echo date("Y"); ?>
  </p>
</div>

</div>
</body>
</html>