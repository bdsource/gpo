<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<style>
    #links {
        font-size: 14px;
        font-weight: bold;
    }
    
    #links li {
        line-height: 24px;
        list-style-type: circle;
    }
</style>
<?php include_once('submenus_startblock.php'); ?>
<div>
    <h2>The Following Queries are Executed:</h2>
    <p><strong>Quotes Query:</strong> &nbsp;<?php echo $this->results['quotes_query']?></p>
    <p><strong>Quotes Updated Rows:</strong>&nbsp;<?php echo $this->results['quotes_affected_rows']?></p>
    <p><strong>Citation Query:</strong>&nbsp;<?php echo $this->results['quotes_query']?></p>
    <p><strong>Citation Updated Rows</strong>:&nbsp;<?php echo $this->results['citation_affected_rows']?></p>
</div>
<?php include_once('submenus_endblock.php'); ?>