<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$users = $this->users;
$logs = $this->logs;
$u = array();
if( count( $users ) == 0 ) { ?>
<?php include_once('submenus_startblock.php'); ?>
<p>No non-admin Members have logged into the front end in the past 7 days. This log excludes Super Administrators.</p>
<?php 
	return;
}
echo '
<h1>Admin Members active in the last 7 days</h1>
<ul>';
foreach( $users as $user )
{
	echo '<li><a href="#logs-for-', $user['user_id'], '">', $user['user_name'], '</a></li>';
	$u[$user['user_id']]=$user['user_name'];
}
echo '</ul>';

echo '<style type="text/css">
 .user_logs td{
    padding: 5px;
    font-size: 12px;
 }
 .user_logs th{
    padding: 5px;
    font-weight: bold;
    font-size: 16px;
  }
  .user_logs .center{
    text-align:center;
  }
</style>
';

$config = &JFactory::getConfig();
$baseurl =$config->get('live_site');
echo '<h1>Breakdown of Admin Member activity</h1>';

foreach($users as $user){

    echo '<a name="logs-for-'.$user['user_id'].'"><h2>Activity Log of '.$user['user_username'].'</h2></a><br/>';
    echo '<table class="user_logs" border="1px" width="100%" cellspacing="0px" cellpadding="0px">
        <thead>
        <tr>
            <th>URL</th>
            <th width="200px">Page Title</th>
            <th class="center" width="120px">When</th>
            <th class="center" width="100px">IP</th>
        </tr></thead><tbody>';
    foreach($logs[$user['user_id']] as $user_log){
        echo '<tr>
            <td><a href="'.$baseurl.$user_log['request_uri'].'" target="_blank">'.substr($user_log['request_uri'],0,100).'</a></td>
            <td>'.$user_log['title'].'</td>
            <td class="center">'.$user_log['when'].'</td>
            <td class="center">'.$user_log['remote_addr'].'</td>
            </tr>';
    }
    echo '</tbody></table>';
}
?>
<?php include_once('submenus_endblock.php'); ?>

