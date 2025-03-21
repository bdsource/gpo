<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$u =&Joomla\CMS\Uri\Uri::getInstance();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php if($this->article->title !=''){echo $this->article->title; }else{echo "GunPolicy News Search Help Tips";} ?></title>
		<link rel="stylesheet" href="<?php echo $u->base();?>templates/gunpolicy/css/help-popup.css" type="text/css" />
	</head>
	<body>

<div id="wrapper">
<div id="header">
	
<img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit.gif" width="531" height="83" alt="gunpolicy.org popup logo"/>
</div>

<div id="content">
   <div class="buttonbar">
       <a class="buttonlink close" href="javascript: self.close();">close</a>
   </div>
   <h2>Search Help</h2>

<p>Searches are not case sensitive, though the operators OR and NOT should be typed in upper case. The language of this web site is <a href="javascript:popup=window.open('//www.gunpolicy.org/firearms/glossary/139','GunPolicyGlossary','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=650,height=600');%20popup.focus();">International English</a>.</p>
<p>Operators can be combined in a single search, and words are located by their stems -- a search for guns will find gun, but a search for gun will not find handgun or begun.
</p>

<h3>Search Operators</h3>

<table class="widetable" cellspacing="2px">
<tr>
	<th>If you enter, for example:</th>
	<th>Your results will contain:</th>
</tr>
<tr>
	<td>two words <em>[law handgun]</em></td>
	<td>both words <em> ['law' and 'handgun']</em></td>
</tr>
<tr>
	<td>OR <em>[law OR legislation]</em></td>
	<td>either word <em>['law' OR 'legislation']</em></td>
</tr>
<tr>
	<td>inverted commas <em>["saturday night"]</em></td>
	<td>the exact phrase <em>['saturday night']</em></td>
</tr>
<tr>
	<td>NOT <em>[handgun NOT airgun]</em></td>
	<td>articles which contain the first, but not second word <em>['handgun' but not 'airgun']</em></td>
</tr>
<tr>
	<td>wildcard before <em>[*gun]</em></td>
	<td>words ending in ‘gun’ <em>[‘handgun,’ ‘begun’]</em></td>
</tr>
<tr>
	<td>wildcard after <em>[gun*]</em></td>
	<td>words beginning with ‘gun’ <em>[‘gunshot,’ ‘gunwale’]</em></td>
</tr>
<tr>
	<td>two wildcards <em>[*gun*]</em></td>
	<td>words containing ‘gun’ <em>[‘airgun,’ ‘gunwale,’ ‘burgundy’]</em></td>
</tr>
</table> 
<p>
Due to character differences, text strings can malfunction if clipped into Search from other applications.
</p>
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
