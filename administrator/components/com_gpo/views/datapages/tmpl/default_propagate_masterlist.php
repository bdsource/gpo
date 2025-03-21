<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

<script>
    jQuery.noConflict();
</script>

<style>
pre{
	padding:1px;
	margin:1px;
	display:inline;
}
.adminlist td a{
	display:inline;
}
.center{
	text-align:center;
}
#adminList {
    line-height: 45px !important;
}
</style>

<!-- Language Switching Panel -->
<div class="langFloatBar" title="DP Language: <?php echo getLanguageName($this->currentLanguage);?>">
   <a href="#switchLang">
   <span class="title"><?php echo strtoupper($this->currentLanguage);?></span>
   <br />
   <img border="0" src="<?php echo getLanguageFlag($this->currentLanguage);?>"
        alt="<?php echo getLanguageName($this->currentLanguage);?>"
   />
   </a>
</div>

<div class="langPanel">
      <a name="switchLang"></a>
      <div id="langOptionsWrapper">
           <?php echo getLanguageOptionsHTML($this->currentLanguage);?>
      </div>
</div>
<div class="clr"></div>
<br />
<!-- Language Switching panel done -->


<?php
   if( 'en' == $this->currentLanguage ) {
       echo "<h2> Sorry, this propagate masterlist is only for the Es/Fr DPs, 
                  you don't need it for En Dps.
                  
                  To run it for Es/Fr, change the language from the left side
             </h2>";
   }
   else if( !in_array($this->currentLanguage, array('es','fr')) ) {
       echo "<h2> 
                  Sorry, the language code is unknown
                  Change the language from the left side
             </h2>";
   } else {
       echo "
              <p> 
                  <h1>
                  If you want to propagate all the preambles from 
                  the Preambles Master list ($this->currentLanguage - " . getLanguageName($this->currentLanguage) . ") 
                  to the $this->currentLanguage DPs Preambles, then press the propagate 
                  button above. 
                  
                  <br /> <br />
                  Please note that, this action will overwrite the existing preambles of all 
                  $this->currentLanguage DPs.
                      
                  <br /> <br />
                  Only Super Admin can access this panel 
                  $this->currentLanguage DPs.
                  </h1>
              </p>
            ";
   }
?>
<form action="index.php?option=com_gpo&amp;controller=datapages" method="post" name="adminForm" id="adminForm">
            
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="controller" value="datapages" />
<input type="hidden" name="task" value="<?php echo $this->task;?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="action" value="<?php echo $action;?>" />
<?php include_once('submenus_startblock.php'); ?>
<?php include_once('submenus_endblock.php'); ?>

</form>

<script type="text/javascript">
/*
 * For language Switching 
 * 
 */
var currentLang = '<?php echo $this->currentLanguage;?>';
jQuery(document).ready(function() {
    jQuery('#languageDropdown').ddslick({
        width: 200,
        onSelected: function (data) {
           var selectedLang = data.selectedData.value;
           if (currentLang == selectedLang ) 
           {
              return true;   
           } 
           else {
              var newLangURIPart = '&lang=' + selectedLang;
              var newLangURI = '<?php echo $this->currentURI;?>'+newLangURIPart;
             // similar behavior as an HTTP redirect
             window.location.replace(newLangURI);
           }
        }
    });
});
</script>