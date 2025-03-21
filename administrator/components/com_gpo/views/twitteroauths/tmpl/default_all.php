<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

echo "<h1>Twitter Settings</h1>";
?>

<style>
    .gpo-row td{
        vertical-align:top;
    }
    .gpo-row td.id{ width:15px; }
    .gpo-row td.published{ width:80px; text-align:center; }
    .gpo-row td.gpnheader{text-align:left; }
    .gpo-row td.access{ width:30px; text-align:center; }
    .gpo-row td.action{ width:80px; text-align:center; }

</style>
<div class="responsive">
<table class="adminlist table-striped table-hover table-bordered">
    <thead>
    <tr>
        <th><?php echo JText::_( 'Owner' ); ?></th>
        <th><?php echo JText::_( 'Client' ); ?></th>
        <th><?php echo JText::_( 'Consumer Key' ); ?></th>
        <th><?php echo JText::_( 'Consumer Secret' ); ?></th>
        <th><?php echo JText::_( 'User Token' ); ?></th>
        <th><?php echo JText::_( 'User Secret' ); ?></th>
        <th><?php echo JText::_( 'Action' ); ?></th>
    </tr>
    </thead>
    <tbody>

    <?php foreach($this->items as $item) :?>
    <tr class="gpo-row">
        <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['owner']; ?></a></td>
        <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['client']; ?></a></td>
         <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['consumer_key']; ?></a></td>
         <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['consumer_secret']; ?></a></td>
         <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['user_token']; ?></a></td>
         <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>"><?php echo $item['user_secret']; ?></a></td>
         <td><a href="index.php?option=com_gpo&controller=twitteroauths&task=delete&id=<?php echo $item['id'] ?>">Delete</a> | <a href="index.php?option=com_gpo&controller=twitteroauths&task=edit&id=<?php echo $item['id'] ?>">Edit</a></td>
     </tr>
    <?php endforeach ?>
    </tbody>

</table>
</div>