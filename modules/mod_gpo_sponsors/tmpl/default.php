<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$_m_counter = 0;
$_m_total = count( $mods );

if ( count($sponsors)>0 && $_m_total>0 ):
    
    /* now render the modules */
    foreach ($mods as $_mod ):
       if ( is_object( $_mod ) ) {
    	   $_options = array( 'style' => 'xhtml' );
    	   
    	   //check for moduleclass_sfx parameter
    	   $paramsdata = $_mod->params;
    	  // $_params = new JParameter( $paramsdata );
                     $_params = new JRegistry($paramsdata);
    	   $_moduleclass_sfx = $_params->get( 'moduleclass_sfx' );
           
           ## dp-central-sponsors and dp-right-sponsors suffix is for the central/right columns, so skip it
           if ($_moduleclass_sfx == 'dp-sponsors') {

                if (empty($_moduleclass_sfx)) {
                    $_mod->params .= 'moduleclass_sfx=gpcustom' . '\n';
                }

                echo JModuleHelper::renderModule($_mod, $_options);
                $_m_counter++;
            }
        }

       /*
        ##commented so that it do not show double border below the module block
        
        if ( $_m_counter>0 && $_m_counter<$_m_total ) {
       	   echo '<div class="mod_separator">' .
       	        '</div>';
        }
       */
    endforeach;
 
endif;
?>