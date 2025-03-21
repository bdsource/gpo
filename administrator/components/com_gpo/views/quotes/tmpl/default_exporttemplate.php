<?php
defined('_JEXEC') or die('Restricted Access');

?>
<style type="text/css">
    table td {
        padding-left: 20px;
    }
</style>
<form method="POST" action="<?php echo JRoute::_('index.php?option=com_gpo&action=update'); ?>" id="adminForm"
      name="adminForm">
    <input type="hidden" name="option" value="com_gpo"/>
    <input type="hidden" name="controller" value="quotes"/>
    <input type="hidden" id="adminForm_task" name="task" value="exportTemplate"/>
	<?php include_once('submenus_startblock.php'); ?>

    <h2>TXT Template</h2>
    <table id="txt_template">
        <tr>

            <td>
                <h3>Header</h3>
                <textarea name="txt_header" rows="2"
                          cols="80"><?php echo @$this->template->txt_template->header;?></textarea>

            </td>
        </tr>
        <tr>
            <td>
                <small>Format the template using the allowed tags. The tags will be replaced with actual data.</small>
                <br/>
                <textarea name="txt_template" rows="10"
                          cols="80"><?php echo @$this->template->txt_template->body;?></textarea>

                <p>
                    <strong>Allowed Tags:</strong>

                <div id="allowed_tags">
<?php
                            foreach ($this->valid_fields AS $field) {
    echo '{' . $field . '}, ';
}
    ?>
                </div>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <h3>Footer</h3>
                <textarea name="txt_footer" rows="2"
                          cols="80"><?php echo @$this->template->txt_template->footer;?></textarea>

            </td>
        </tr>
    </table>

    <h2>CSV Template</h2>
    <table>
        <tr>
            <td>
                <p>
                    <small>
                        <strong>Enter the field names in each line that you want to include in the export
                            file! </strong><br/>
                    </small>
                </p>
                <textarea name="csv_template" rows="10" cols="80"><?php echo $this->template->csv_template;?></textarea>

                <p>
                    <strong>Available Fields</strong><br/>
                    <?php echo implode(', ', $this->valid_fields);?>
                </p>
            </td>
        </tr>
    </table>
	<?php include_once('submenus_endblock.php'); ?>
</form>