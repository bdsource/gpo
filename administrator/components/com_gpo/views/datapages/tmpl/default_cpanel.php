
<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$document = &JFactory::getDocument();

$base_link='index.php?option=com_gpo&controller=locations';
?>
<dl>
	<dt>
		<h1>Location Links + Information</h1>
	</dt>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=location_new' ); ?>">Add a Location</a>
	</dd>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=location_list' ); ?>">Edit Location List</a>
	</dd>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=location_delete' ); ?>">Delete a Location permanently</a>
	</dd>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=admin_location_links' ); ?>">Create or delete Links between Locations</a>
	</dd>

	<dd>
		<a href="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=keywords'); ?>">Keywords Errors</a>
	</dd>
</dl>
<p>
	
</p>



<dl>
	<dt>
		<h1>Front-end Location Lists</h1>
	</dt>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=public_region_list' ); ?>">Region List</a>
	</dd>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=public_country_list' ); ?>">Country List</a>
	</dd>
</dl>


<dl>
	<dt>
		<h1>Back-end Location Lists</h1>
	</dt>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=admin_region_list' ); ?>">Region List</a>
	</dd>
	<dd>
		<a href="<?php echo JRoute::_( $base_link . '&task=admin_country_list' ); ?>">Country List</a>
	</dd>
</dl>

<!-- 
<dl>
	<dt>
		<h1>Members</h1>
	</dt>
	<dd>
		<p>Todo</p>
	</dd>
</dl>
-->
