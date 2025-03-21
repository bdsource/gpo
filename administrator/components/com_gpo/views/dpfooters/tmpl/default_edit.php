<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
?>

<form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
<input type="hidden" name="option" value="com_gpo" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="dpfooters" />

<input type="hidden" name="sb[id]" value="<?php echo $this->dpfooters->id; ?>" />
<input type="hidden" name="sb[url_hash]" value="<?php echo $this->dpfooters->url; ?>" />
<p>
Enter front url: ( Address in your browser window )<br />

<textarea class="inputbox"  name="sb[url]" id="sb[url]" rows="3" cols="70"><?php echo $this->dpfooters->url; ?></textarea>
</p>



<p>
<div id="queryboxcontainer" style="background:#CCCCCC">
<fieldset id="querybox">
<legend>Select authors from the drop-down list, adjust the format to ‘first author: last name first,’ then add a full stop after the last name: </legend>
<div id="queryfieldscontainer">
<div id="sqlquerycontainer">
<textarea dir="ltr" rows="14" cols="40" id="footer" name="sb[footer_credit]"><?php echo $this->dpfooters->footer_credit; ?></textarea>
</div>
<div id="tablefieldscontainer">
<label>Authors: (Double Click on the name)</label>
<select name="authorList" ondblclick="insertValueQuery()" multiple="multiple" size="10" id="authorList"> 
<?php 
foreach ( $this->allUsers as $_user ):
?>
<option value="<?php echo $_user->name;?> ">
<?php echo $_user->name; ?>
</option>
<?php
endforeach;
?>
</select>


<div id="tablefieldinsertbuttoncontainer">
<input type="button" title="Insert" onclick="insertValueQuery()" value="&lt;&lt;" name="insert">
</div>
</div>
<div class="clearfloat"></div>
</div>
<div class="clearfloat"></div>
</fieldset>
</div>
</p>

<p>
Published: 
<input type="radio" id="published_yes" name="sb[is_published]" value="1" 
<?php if($this->dpfooters->is_published == 1){echo 'checked="checked"';}?>>Yes
&nbsp;&nbsp;
<input type="radio" id="published_no" name="sb[is_published]" value="0"
<?php if($this->dpfooters->is_published == 0){echo 'checked="checked"';}?>>No"
</p>


<p>
Enter comment [optional] <br />
<textarea class="inputbox" name="sb[comment]" id="sb[comment]" rows="3" cols="70"><?php echo $this->dpfooters->comment; ?></textarea>
</p>

</form>

<style>
<!--
/* querybox */

div#sqlquerycontainer {
    float: left;
    width: 69%;
    /* height: 15em; */
}

div#tablefieldscontainer {
    float: right;
    width: 29%;
    /* height: 15em; */
}

div#tablefieldscontainer select {
    width: 100%;
    /* height: 12em; */
}

textarea#footer {
    width: 100%;
    /* height: 100%; */
}

div#queryboxcontainer div#bookmarkoptions {
    margin-top: 0.5em;
}
/* end querybox */

-->
</style>
<script type="text/javascript">
<!--
/**
 * Inserts multiple fields.
 *
 */
function insertValueQuery() {
   var myQuery = document.adminForm.footer;
   var myListBox = document.adminForm.authorList;

   if(myListBox.options.length > 0) {
       sql_box_locked = true;
       var chaineAj = "";
       var NbSelect = 0;
       for(var i=0; i<myListBox.options.length; i++) {
           if (myListBox.options[i].selected){
               NbSelect++;
               if (NbSelect > 1)
                   chaineAj += ", ";
               chaineAj += myListBox.options[i].value;
           }
       }

       //IE support
       if (document.selection) {
           myQuery.focus();
           sel = document.selection.createRange();
           sel.text = chaineAj;
           document.adminForm.insert.focus();
       }
       //MOZILLA/NETSCAPE support
       else if (document.adminForm.footer.selectionStart || document.adminForm.footer.selectionStart == "0") {
           var startPos = document.adminForm.footer.selectionStart;
           var endPos = document.adminForm.footer.selectionEnd;
           var chaineSql = document.adminForm.footer.value;

           myQuery.value = chaineSql.substring(0, startPos) + chaineAj + chaineSql.substring(endPos, chaineSql.length);
       } else {
           myQuery.value += chaineAj;
       }
       sql_box_locked = false;
   }
}

//-->
</script>

