<?php 
/* Submenus starting HTML 
 *
 * Include this file just after the opening form tag 
 *
 *
*/ 

if (!empty($this->sidebar)): 
?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
    <?php else : ?>
		<div id="j-main-container">
<?php 
endif; 
?>