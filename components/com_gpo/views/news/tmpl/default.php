<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$article = $this->oNews;
$article->locations = implode(",", $article->locations );
include( JPATH_COMPONENT.DS.'views'.DS.'search'.DS.'tmpl'.DS.'default_abstract.php' );
return;
