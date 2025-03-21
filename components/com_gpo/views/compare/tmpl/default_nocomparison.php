<?php
defined('_JEXEC') or die('Restricted Access');
?>

<?php
if( 'fr' == $this->currentLangCode ) {
    $factsClass = 'frFacts';
}elseif( 'es' == $this->currentLangCode ) {
    $factsClass = 'esFacts';
}else {
    $factsClass = 'enFacts';
}
?>

<div class="introbox facts">
    <div class="introlabel" style="background-position:top;" class="<?php echo $factsClass;?>">
        <h4 style="margin-top: 60px">Sorry, No comparison available for this record!</h4>
    </div>
    <div class="clear"></div>
</div>