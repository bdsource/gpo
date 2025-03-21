<?php
defined('_JEXEC') or die('Restricted Access');
// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');

$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
$document->addScript(JURI::root().'templates/gunpolicy/javascript/jquery.ddslick.min.js');
$document->addStyleSheet(JURI::base().'templates/system/css/language.css');
?>

<script type="text/javascript">
    function confirmDel(url) {
        if (confirm('Are you sure to delete this featured chart? This will also delete the respective image from filesystem!')) {
            document.location = url;
        } else {
            return false;
        }
    }
</script>

<style type="text/css">
    .thumb{
        width:100px;
    }
    .modified{
        width:100px;
        text-align:center;
    }
    .action{
        width:100px;
        text-align:center;
    }
    .order{
        width:100px;
        text-align:center;
    }
    .location{
        width:200px;
        text-align:center;
    }
</style>


<!-- Language Switching Panel -->
<div class="langFloatBar" title="DP Language: <?php echo getLanguageName($this->currentLanguage);?>">
   <a href="#switchLang">
   <span class="title"><?php echo strtoupper($this->currentLanguage);?></span>
   <br />
   <img border="0" src="<?php echo getLanguageFlag($this->currentLanguage);?>"
        alt="<?php echo getLanguageName($this->currentLanguage);?>"
   />
   </a>
</div>

<div> &nbsp; </div>

<div class="langPanel">
      <a name="switchLang"></a>
      <div id="langOptionsWrapper">
           <?php echo getLanguageOptionsHTML($this->currentLanguage);?>
      </div>
</div>
<div class="clr"></div>
<br />
<!-- Language Switching panel done -->

<form method="post" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_gpo"/>
    <input type="hidden" id="task" name="task" value="published"/>
    <input type="hidden" name="controller" value="featuredcharts"/>
    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir; ?>"/>
    <input type="hidden" name="lang" value="<?php echo $this->currentLanguage;?>"/>
	<?php include_once('submenus_startblock.php'); ?>

<?php
    if (count($this->featuredcharts) < 1): ?>
    <p>
        <a href="<?php echo JRoute::_('index.php?option=com_gpo&controller=featuredcharts&task=create'); ?>">There are
            no
            featured chart. Click here to create one</a>
    </p>
    <?php else: ?>

    <style>

    </style>
    <div class="responsive">
    <table class="adminlist table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo JText::_('Thumb');?></th>
            <th><?php echo JHTML::_('grid.sort', 'Name', 'location', $this->filter_order_Dir, $this->filter_order); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Description', 'title', $this->filter_order_Dir, $this->filter_order); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'Ordering', 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th><?php echo JHTML::_('grid.sort', 'Modified', 'modified', $this->filter_order_Dir, $this->filter_order); ?></th>
            <th><?php echo JText::_('Action'); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                                if (count($this->featuredcharts) < 1) {
            ?>
        <tr>
            <td colspan="6"><p>Sorry, no match. Please try another search.</p></td>
        </tr>

            <?php

        } else {

            $i = 0;
            foreach ($this->featuredcharts AS $row):
                $i++;
                $link_edit = JRoute::_('index.php?option=com_gpo&controller=featuredcharts&task=edit&id=' . $row->id);
                $link_delete = JRoute::_('index.php?option=com_gpo&controller=featuredcharts&task=delete&id=' . $row->id);
                $published = '&nbsp;';
                ?>
            <tr class="gpo-row-citation">
                <td class="thumb"><a class="modal" rel="{handler: 'iframe', size: {x: 460, y: 390}}"
                                     href="<?php echo JURI::root() . '/images/gpo/charts/' . $row->image;?>"><img
                        src="<?php echo JURI::root() . '/images/gpo/charts/' . $row->image;?>" width="100px"
                        height="100px"/></a></td>
                <td class="location"><?php echo $row->location; ?></td>
                <td class="title"><?php echo $row->title; ?></td>
                <td class="order">
                    <span><?php //echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $this->ordering); ?></span>
                    <span><?php //echo $this->pagination->orderDownIcon($i, true, 'orderdown', 'Move Down', $this->ordering); ?></span>
                    <?php $disabled = $this->ordering ? '' : '"disabled=disabled"'; ?>
                    <input type="text" name="order[<?php echo $row->id;?>]" size="5"
                           value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area"
                           style="text-align: center"/>
                </td> 
                <td class="modified"><?php echo !empty($row->modified) ? date("j M Y", $row->modified) : 'N/A'; ?></td>
                <td class="action">
                    <a href="<?php echo $link_edit;?>" title="Edit this chart">Edit</a> |
                    <a href="#" onClick="confirmDel('<?php echo $link_delete;?>');" title="Delete this chart">Delete</a>

                </td>
            </tr>
                <?php
                                        endforeach;
        }
            ?>
        </tbody>
        <?php if (count($this->featuredcharts) > 0): ?>
        <tfoot>
        <tr>
            <td colspan="7">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <?php endif; ?>
    </table>
    </div>
    <?php endif; ?>
	<?php include_once('submenus_endblock.php'); ?>
</form>


<script type="text/javascript">

/*
 * For language Switching 
 * 
 */
var currentLang = '<?php echo $this->currentLanguage;?>';
jQuery(document).ready(function() {
    jQuery('#languageDropdown').ddslick({
        width: 200,
        onSelected: function (data) {
           var selectedLang = data.selectedData.value;
           if (currentLang == selectedLang ) 
           {
              return true;   
           } 
           else {
              var newLangURIPart = '&lang=' + selectedLang;
              var newLangURI = '<?php echo $this->currentURI;?>'+newLangURIPart;
             // similar behavior as an HTTP redirect
             window.location.replace(newLangURI);
           }
        }
    });
});
</script>