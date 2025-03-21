<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );

$allModules = array(); $enModules = array();
$frModules = array(); $esModules = array();
foreach($this->allSponsors as $_module) {
    if($_module->language == '*') {
       $allModules[] = $_module;
    } else if (stripos($_module->language,'en') !== false) {
       $enModules[] = $_module; 
    } else if (stripos($_module->language,'fr') !== false) {
       $frModules[] = $_module; 
    } else if (stripos($_module->language,'es') !== false) {
       $esModules[] = $_module; 
    }
}
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="sponsors" />

<input type="hidden" name="sb[id]" value="<?php echo $this->sponsors->id; ?>" />
<input type="hidden" name="sb[url_hash]" value="<?php echo $this->sponsors->url; ?>" />
<p>
Enter front url: ( Address in your browser window )<br />

<textarea class="inputbox"  name="sb[url]" id="sb[url]" rows="3" cols="70"><?php echo $this->sponsors->url; ?></textarea>
</p>

<!-- English -->
<p>
<img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('en');?>" />
<span title="Select the Custom HTML module title that you have created from the mod_custom module earlier">
      For EN: Select the Sponsors Credit title 
</span><br />
<select name="sb[module_id]"> 
<option value="0"> Select Sponsors </option>

<?php
if( count($allModules)>0 ):
echo '<option value="-1"> ------ Lang: ALL ------ </option>';
foreach ( $allModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($enModules)>0 ):
echo '<option value="-2"> ------ Lang: EN ------ </option>';
foreach ( $enModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($frModules)>0 ):
echo '<option value="-3"> ------ Lang: FR ------ </option>';
foreach ( $frModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($esModules)>0 ):
echo '<option value="-4"> ------ Lang: ES ------ </option>';
foreach ( $esModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

</select>
</p>

<!-- French -->
<p>
<img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('fr');?>" />
<span title="Select the Custom HTML module title that you have created from the mod_custom module earlier">
      For FR: Select the Sponsors Credit title 
</span><br />
<select name="sb[module_id_fr]"> 
<option value="0"> Select Sponsors </option>


<?php
if( count($allModules)>0 ):
echo '<option value="-1"> ------ Lang: ALL ------ </option>';
foreach ( $allModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_fr){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($enModules)>0 ):
echo '<option value="-2"> ------ Lang: EN ------ </option>';
foreach ( $enModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_fr){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($frModules)>0 ):
echo '<option value="-3"> ------ Lang: FR ------ </option>';
foreach ( $frModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_fr){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($esModules)>0 ):
echo '<option value="-4"> ------ Lang: ES ------ </option>';
foreach ( $esModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_fr){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

</select>
</p>

<!-- Spanish -->
<p>
<img style="margin:0;float:none;" border="0" src="<?php echo getLanguageFlag('es');?>" />
<span title="Select the Custom HTML module title that you have created from the mod_custom module earlier">
      For ES: Select the Sponsors Credit title 
</span><br />
<select name="sb[module_id_es]"> 
<option value="0"> Select Sponsors </option>

<?php
if( count($allModules)>0 ):
echo '<option value="-1"> ------ Lang: ALL ------ </option>';
foreach ( $allModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_es){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($enModules)>0 ):
echo '<option value="-2"> ------ Lang: EN ------ </option>';
foreach ( $enModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_es){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($frModules)>0 ):
echo '<option value="-3"> ------ Lang: FR ------ </option>';
foreach ( $frModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_es){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

<?php
if( count($esModules)>0 ):
echo '<option value="-4"> ------ Lang: ES ------ </option>';
foreach ( $esModules as $_module ):
?>
<option value="<?php echo $_module->id;?>" <?php if($_module->id == $this->sponsors->module_id_es){ echo 'selected="selected"';}?>>  
<?php echo $_module->title; ?>
</option>
<?php
endforeach;
endif;
?>

</select>
</p>

<p>
Enter comment [optional] <br />
<textarea class="inputbox" name="sb[comment]" id="sb[comment]" rows="3" cols="70"><?php echo $this->sponsors->comment; ?></textarea>
</p>

</form>

