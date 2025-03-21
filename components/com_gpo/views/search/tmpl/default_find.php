<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$u =& JFactory::getURI();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $this->article->title; ?></title>
		<link rel="stylesheet" href="<?php echo $u->base();?>templates/gunpolicy/css/citation-quotes.css" type="text/css" />
	</head>
	<body>

<div id="wrapper">
<div id="header">
	
<img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit.gif" width="531" height="83" alt="gunpolicy.org popup logo"/>
</div>

<div id="content">
<h1>Find Facts Help</h1>

<p>Select either the country or region you are interested in - <em> You cannot select both region and country at the same time.</em></p>
<p>Then click on the Find Button to be redirected to that page.</p>
<p>&nbsp;</p>

</div>

<div class="clear"></div>
<div id="footer">
  <p>
  <span style="color: #E8921E;">Alpers.org</span> provides evidence-based, public health-oriented information on<br />
  gun violence, small arms policy and firearm-related injury around the world.<br />
 &copy; alpers.org <?php echo date("Y"); ?>
  </p>
</div>

</div>
</body>
</html>