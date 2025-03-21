<?php defined( '_JEXEC' ) or die( 'Restricted Access' );
$jinput = JFactory::getApplication()->input;

$newItem = $jinput->getVar('newItem',false);
if(isset($this->twitter_flag_message)){
    echo $this->twitter_flag_message;
}

switch( $this->can_publish )
{
	case true:
		$publish_text = "Publish Now!";
		break;
	default:
		$publish_text = "Place in the publish queue.";	
		break;
}

include_once('submenus_startblock.php');

if( $this->testMode === true )
{
	echo '<p style="color:#ff0000;">Test Mode</p>';	
}
?>

<h1>Publish</h1>
<div id="message_box"></div>

<h3><?php echo $this->oNews->title; ?></h3>

<p>
GPNHeader: <?php echo $this->oNews->gpnheader; ?>
</p>
<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo',false ); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="controller" value="news" />
<input type="hidden" name="task" value="publish" />
<input type="hidden" name="id" value="<?php echo $this->oNews->id; ?>" />
<input type="hidden" name="publish[approve]" value="1" />

<?php if( $this->can_publish ): ?>

<?php if( $this->oNews->share === 1 ): 
//if( '1' === '1' ):
$member_checked='';
?>

<!--
<p>
	Public <label><input type="radio" name="publish[mail]" value="public-post" checked="checked" />Publish immediately to the Public web site, RSS feeds and Members-only E-mail lists, then queue for posting to Public E-mail digests</label><br />
	<label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="global-interest"/>Publish to Public site and RSS, send to Member' lists, then queue for ALL Public E-mail lists as a 'global interest' item</label><br />	
	<label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-post-only"/>Publish to Public site and RSS, queue for Public digests, but don't publish to Members' list</label><br />
	<label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-archive"/>Publish to Public site and RSS; don’t post to any E-mail groups</label><br />
</p>
-->
    <?php if( !empty($newItem) ){ ?>
        <p>
            Public <label><input type="radio" name="publish[mail]" value="public-post" checked="checked" />Publish immediately to the Public web site, RSS feeds, Twitter (if filled) and Members-only E-mail list, then queue for posting to Public E-mail digests</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="global-interest"/>Publish to Public site, RSS and Twitter (if filled), send to Members' list, then queue for ALL Public E-mail lists as a 'global interest' item</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-post-only"/>Publish to Public site, RSS and Twitter (if filled), queue for Public digests, but don't publish to the Members' list</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-archive"/>Publish to Public site, RSS and Twitter (if filled), but don't re-post to any E-mail groups</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-archive-no-tweet"/>Publish to Public site and RSS, but don't re-post to any E-mail groups, or to Twitter (even if filled)
        </p>
    <?php }else{ ?>
        <p>
            Public <label><input type="radio" name="publish[mail]" value="public-post" checked="checked" />Publish immediately to the Public web site, RSS feeds, Twitter (if filled) and Members-only E-mail list, then queue for posting to Public E-mail digests</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="global-interest"/>Publish to Public site, RSS and Twitter (if filled), send to Members' list, then queue for ALL Public E-mail lists as a 'global interest' item</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-post-only"/>Publish to Public site, RSS and Twitter (if filled), queue for Public digests, but don't publish to the Members' list</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-archive"/>Publish to Public site, RSS and Twitter (if filled), but don't re-post to any E-mail groups</label><br />
            <label style="margin-left:45px;"><input type="radio" name="publish[mail]" value="public-archive-no-tweet"/>Publish to Public site and RSS, but don't re-post to any E-mail groups, or to Twitter (even if filled)
        </p>
    <?php } ?>

<?php else: 
$member_checked = 'checked="checked"';
?>
<?php endif; ?>

<p>
	Members <label><input type="radio" name="publish[mail]" value="members-post" <?php echo $member_checked; ?>/>Publish only to Members, and post immediately to Members' E-mail groups</label><br />
	<label><input type="radio" name="publish[mail]" value="members-post-only"/>Publish only to Members, but don’t post to Members' E-mail groups</label>
</p>
<?php endif; ?>
	
<p>
	<input type="submit" value="<?php echo $publish_text; ?>" />
</p>
</form>

<?php if( isset( $this->mailHistory['0'] ) ): 
echo '<h3 style="color:#ff0000;">This article has already been published. To save it again without re-posting to E-mail lists, select a &quot;don\'t send&quot; option.</h3>';
echo '<table><tbody>';
foreach( $this->mailHistory as $history )
{
	echo '<tr><td>' . $history['type'] . '</td><td>' . date( 'j F Y \a\t H:i:s' , strtotime($history['when']) ) . '</td></tr>';
}
echo '</tbody></table>';

else:
echo '<p>No mail history</p>';
endif; ?>

<script type="text/javascript"> 
//<![CDATA[
document.observe("keydown",function(event){
	
	if( event.keyCode === Event.KEY_RETURN )
	{
		Event.stop(event);
		$('adminForm').submit();
	}
});
//]]> 
</script>
<?php include_once('submenus_endblock.php'); ?>