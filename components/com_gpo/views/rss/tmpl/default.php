<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

?>
<h1>Design Your Own <span style="color: #e8921e;">Alpers News</span> RSS Feed</h1>
<p><br />To receive automatic news updates, enter any combination of search topic, country or region in the form below. Create a custom web link, open it, or copy it to your browser or feed reader. As soon as a matching news item is posted, you'll receive it by RSS. You can also save our custom web links as Favourites/Bookmarks, then visit them for news updates whenever you choose.</p>
<hr />
<h5>Create Your Customised RSS Feed</h5>
<form class="create" style="text-align: center;" enctype="application/x-www-form-urlencoded" 
      method="post" action="/rss/design-your-own-updates"> 
<table style="width: 612px; height: 174px;" border="0" cellspacing="2">
<tbody>
<tr>
<td></td>
<td valign="top"></td>
<td></td>
</tr>
<tr>
<td>Keyword or phrase</td>
<td><input id="keyword" style="width: 150px;" name="keyword" type="text" /></td>
<td><span style="font-size: 10px;">There is no need to enter 'gun,' 'firearm' or 'small arm.' <br />One or more of these terms appears in every article.</span></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>Refine search<br /></td>
<td><select id="country" style="width: 150px;" name="country"> <option selected="selected" value="">By Country</option><?php echo $this->options_country; ?></select></td>
<td><select id="location" style="width: 205px;" name="location"> <option selected="selected" value="">Or by Region</option><?php echo $this->options_region; ?></select></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td><a href="javascript:popup=window.open('//www.gunpolicy.org/SearchHelp.html','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=550,height=600');popup.focus();"></a></td>
<td style="text-align: right;" valign="middle"><input class="button" style="margin-left:20px;" type="submit" value="Create" /><span style="background-color: #ffffff;"> </span></td>
<td valign="middle"><span style="background-color: #ffffff;"> </span><a href="javascript:popup=window.open('//www.gunpolicy.org/SearchHelp.html','MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=550,height=600');popup.focus();"><span style="font-size: 12px; font-weight: normal; text-decoration: none;padding-bottom:4px;">Help</span></a></td>
</tr>
</tbody>
</table>
</form>
<p><strong>To receive your custom RSS news feed, click this web link or add it to your browser:</strong>
    
<?php if( !empty( $this->url ) ): ?>
<a target="_blank" title="Your custom RSS news feed" 
   href="<?php echo $this->url; ?>">
       <?php echo $this->url; ?>
</a>
<br /><?php echo $this->url; ?>
<?php endif; ?>
</p>

<table style="border: #eeeeee 1px inset; width: 528px; height: 50px; padding:4px;" border="0">
<tbody>
<tr>
<td></td>
</tr>
</tbody>
</table>
<hr />
<p>RSS, or Really Simple Syndication, is immune to the disruptions which affect E-mail lists. RSS delivers updates automatically, at any interval you choose. There is no need to tell us your address. Feel free to display any Gun Policy News feed on your own web site or blog.</p>
<p>To receive any RSS feed you must run a recent browser version, or install a standalone feed reader program. Some feed readers are free: search the web for 'RSS Newsreader,' or visit the <a href="http://www.google.com/Top/Computers/Internet/On_the_Web/Syndication_and_Feeds/RSS/" target="_blank" title="Google RSS Directory">Google RSS Directory</a>.</p>

