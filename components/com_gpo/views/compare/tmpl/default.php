<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
jimport( 'joomla.html.parameter' );

$jView = new JViewLegacy();
?>

<div class="introbox facts">
  <div class="introlabel">
    
    <?php
    $_fp_intro_content = null;
    $_fp_cond = 'compare-button-intro';
    
    /* get the desired custom_mod module for the home page intro html */
    $_fp_modules = JModuleHelper::getModules( 'insert-custom' );
    foreach ( $_fp_modules as $_module ) {
    	$_fp_params = new JParameter( $_module->params );
        if ( strpos( $_fp_params->get('moduleclass_sfx'), $_fp_cond ) !== false ) {
           // first match wins
           $_fp_intro_content = clone($_module);
           break;
        }
    }
    
    /* now print the intro content */
    if ( is_object( $_fp_intro_content ) ) {
        $_options = array( 'style' => 'xhtml' );
    	echo JModuleHelper::renderModule( $_fp_intro_content, $_options );
    }
    ?>
  
  <div style="padding-top:20px; padding-bottom:90px;">
       <p>
         <img src="<?php JURI::base()?>templates/gunpolicy/images/compare_chart_demo.gif" 
              width="520" border="0" 
              alt="Compare Alpers Data" title="Data Comparision From Alpers.org Facts" />
       </p>
  </div>
  <p> &nbsp; </p>
  
  </div>  
</div>


<p>&nbsp;</p>
