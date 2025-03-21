<?php
defined('_JEXEC') or die('Restricted Access');

$jView = new JViewLegacy();
$metaDescription = JText::_('COM_GPO_POPUP_METADATA_DESCRIPTION');

/*
  < Author fullstop >
  < Published {year only} fullstop  >
  < Title {in ‘single quotes’ with a fullstop inside the final apostrophe} >
  < Source {in italics, with no fullstop here} >
  < semi-colon space Volume >
  < space (Issue) { in round brackets} >
  < comma space Page > fullstop space < City colon space >
  < Publisher fullstop >
  < space Published {day and month only} fullstop >
 */
$parts = array();

if (!empty($this->citation->author)){
    $str = GpoEndWith(".", $this->citation->author);
    $parts['author'] = $str . ' ';
}

if( $this->citation->currentdate == 1 ) {
	$str = date('Y');
	$parts['published_year'] = $str.' ';
	$str = ' '.date("j F");
	$parts['published_daymonth'] = $str;	
}elseif (!empty($this->citation->published)) {
    
    $date = new DateTime($this->citation->published);
    
    $str = $date->format('Y');
    //$str = date('Y', strtotime($this->citation->published)) . '. ';
    $parts['published_year'] = $str.' ';

    //$str = ' ' . date('j F', strtotime($this->citation->published)) . '. ';
    $str = ' '.$date->format("j F");
    $parts['published_daymonth'] = $str;
}else { }
//< Title {in ‘single quotes’ with a fullstop inside the final apostrophe} >
if (!empty($this->citation->title)) {
    $str = GpoEndWith(".", $this->citation->title);
    $parts['title'] = '&lsquo;' . $str . '&rsquo; ';
}
//< Source {in italics, with no fullstop here} >
if (!empty($this->citation->source)) {
    $str = trim($this->citation->source);
    if (substr($str, -1) === '.') {
        $str = substr($str, 0, strlen($str) - 1);
    }
    $parts['source'] = '<em>' . $str . '</em>';
}
//< semi-colon space Volume > 
if (!empty($this->citation->volume)) {
    $str = "; " . $this->citation->volume;
    $parts['volume'] = $str;
}
//< space (Issue) { in round brackets} > 
if (!empty($this->citation->issue)) {
    $parts['issue'] = ' (' . $this->citation->issue . ')';
}
//< comma space Page >
if (!empty($this->citation->page)) {
    $str = ', ' . $this->citation->page;
    $parts['page'] = $str;
}
//< City colon space >
if (!empty($this->citation->city)) {
    $str = trim($this->citation->city) . ': ';
    $parts['city'] = $str;
}
//< Publisher fullstop > 
if (!empty($this->citation->publisher)) {
    $str = GpoEndWith(".", $this->citation->publisher);
    $parts['publisher'] = $str;
}

$html  = $parts['author'];
$html .= $parts['published_year'];
$html .= $parts['title'];
$html .= $parts['source'];
$html .= $parts['volume'];
$html .= $parts['issue'];
////< comma space Page > fullstop space < City colon space >
$html .= $parts['page'] . '. ' . $parts['city'];
$html .= $parts['publisher'];
$html .= $parts['published_daymonth'];

//echo '<!-- ' . print_r( $parts, true ) . ' -->';

$html_content = '';
$html_content = str_replace("\r\n", "\n", $this->citation->content);
$html_content = str_replace("\n", "<br />", $html_content);
$u = & Joomla\CMS\Uri\Uri::getInstance();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="description" content="<?php echo $metaDescription;?>" />
        <title><?php echo $this->citation->title; ?> – Alpers.org</title>
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
        <!--
<?php //ftp_debug( $parts, 'parts', true, false ); 
echo "ygyg";
?>
        -->
        <div id="wrapper">
            <div id="header">
                <img src="<?php echo $u->base(); ?>templates/gunpolicy/images/gpo_logo_cit_l.gif" width="531" height="83" alt="alpers.org popup logo"/>
            </div>
	    
            <div id="content">
		<?php
		//show the breadcrumb only if it come from citation page
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
                <h3 style="color:#048DD4;">Citation(s) from the <span style="color: #E8921E;">Alpers.org</span> literature library</h3>
<?php if (!empty($this->citation->id)): ?>

                    <div class="buttonbar">
                        <a class="buttonlink print" href="javascript: window.print();">Print</a>
                        <a class="buttonlink close" href="javascript: self.close();">Close</a>
                    </div>

                    <div id="citation"><h1><?php echo $html; ?></h1></div>



<?php if (!empty($html_content)): ?>
                <div id="content">
                    <h3>Relevant contents</h3>
                    <p><?php echo $html_content; ?></p>
                </div>
<?php
    endif;
?>


<?php if (!empty($this->citation->websource)): ?>
                <div id="citation-link">
                    <p>
<?php echo "Last accessed at:<br />" . autoCorrectionHTTPLink($this->citation->websource); ?>

                    </p>
                </div>
                <?php endif; ?>


<?php else: ?>
                    <p>The citation you are looking for seems to have gone.</p>
<?php endif; ?>
                    <div id="citation-id">
                        <span>ID: Q<?php echo $this->citation->id; ?></span>
                    </div>
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
        <!-- a -->
    </body>
</html>