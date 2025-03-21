<?php

defined('_JEXEC') or die('Restricted Access');

$filter_order = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order', '');
$filter_order_Dir = Joomla\CMS\Factory::getApplication()->getInput()->get('filter_order_Dir', '');

$document = JFactory::getDocument();
$document->addScript(JURI::root(true)  . '/media/system/js/mootools.js');
$document->addScript( JURI::root(true) . '/includes/js/joomla.javascript.js');

// Load the tooltip behavior.
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('behavior.modal');
?>

<div id="message_box"></div>
<form method="get" action="<?php echo JRoute::_('index.php?option=com_gpo'); ?>" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_gpo"/>
    <input type="hidden" name="controller" value="quotes"/>
    <?php include_once('submenus_startblock.php'); ?>
    <?php
    if(isset($this->poaim)){
        echo '<input type="hidden" id="adminForm_task" name="task" value="poaimsearch"/>';
    } else {
        echo '<input type="hidden" id="adminForm_task" name="task" value="search"/>';
    }
    ?>
    <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
<?php
foreach ($this->oQuote as $key => $value)
{
    if (is_string($value)) {
        $this->oQuote->$key = htmlspecialchars($value, ENT_QUOTES);
    }
}
    foreach ($_GET["quotes"] as $key => $value)
    {
        if (is_string($value)) {
            $_GET["quotes"][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    }
   
echo '
<input type="hidden" name="quotes[locations]" value="' . $_GET["quotes"]["locations"] . '" />
<input type="hidden" name="quotes[source]" value="' . $_GET["quotes"]["source"] . '" />
<input type="hidden" name="quotes[city]" value="' . $_GET["quotes"]["city"] . '" />
<input type="hidden" name="quotes[title]" value="' . $_GET["quotes"]["title"] . '" />
<input type="hidden" name="quotes[author]" value="' . $_GET["quotes"]["author"] . '" />
<input type="hidden" name="quotes[publisher]" value="' . $_GET["quotes"]["publisher"] . '" />
<input type="hidden" name="quotes[volume]" value="' . $_GET["quotes"]["volume"] . '" />
<input type="hidden" name="quotes[issue]" value="' . $_GET["quotes"]["issue"] . '" />
<input type="hidden" name="quotes[page]" value="' . $_GET["quotes"]["page"] . '" />
<input type="hidden" name="quotes[keywords]" value="' . $_GET["quotes"]["keywords"] . '" />
<input type="hidden" name="quotes[websource]" value="' . $_GET["quotes"]["websource"] . '" />
<input type="hidden" name="quotes[sourcedoc]" value="' . $_GET["quotes"]["sourcedoc"] . '" />
<input type="hidden" name="quotes[share]" value="' . $_GET["quotes"]["share"] . '" />
<input type="hidden" name="quotes[notes]" value="' . $_GET["quotes"]["notes"] . '" />
<input type="hidden" name="quotes[content]" value="' . $_GET["quotes"]["content"] . '" />
<input type="hidden" name="quotes[id_range][to]" value="' . $_GET["quotes"]["id_range"]["to"] . '" />
<input type="hidden" name="quotes[staff]" value="' . $_GET["quotes"]["staff"] . '" />
<input type="hidden" name="quotes[id_range][from]" value="' . $_GET["quotes"]["id_range"]["from"] . '" />
<input type="hidden" name="quotes[published_range][to]" value="' . $_GET["quotes"]["published_range"]["to"] . '" />
<input type="hidden" name="quotes[published_range][from]" value="' . $_GET["quotes"]["published_range"]["from"] . '" />
<input type="hidden" name="revise" value="" />
<input type="hidden" name="filter_order" id="filter_order" value="' . (!empty($filter_order) ? $filter_order : 'id') . '" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="' . (!empty($filter_order_Dir) ? $filter_order_Dir : 'desc') . '" />
<input type="hidden" name="quotes[poaim]" value="' . $_GET['quotes']['poaim'] . '" />
';
?>
    <p><b>Found:</b> <?php echo $this->totalFound; ?> results.
    <?php
    $shareArray = [ 0 => 'Members', 1 => 'Public', 2 => 'All'];
    if( !empty($_GET["quotes"]["published_range"]["from"]) ) {
        $searchedIn .= "From Date: " . "<b>" . str_replace('/','-',$_GET["quotes"]["published_range"]["from"]) . "</b>, ";
        $searchedIn .= "To Date: "   . "<b>";
        $searchedIn .= !empty($_GET["quotes"]["published_range"]["to"]) ? str_replace('/','-',$_GET["quotes"]["published_range"]["to"]) : date('d-m-Y');
        $searchedIn .= "</b>, ";
    }
    if( !empty($_GET["quotes"]["id_range"]["from"]) || !empty($_GET["quotes"]["id_range"]["to"]) ) {
        $searchedIn .= "ID From: " . "<b>" . !empty($_GET["quotes"]["id_range"]["from"]) ? $_GET["quotes"]["id_range"]["from"] : 1 . "</b>, ";
        $searchedIn .= "ID To: <b>" . !empty($_GET["quotes"]["id_range"]["to"]) ? $_GET["quotes"]["id_range"]["to"] : 1000000;
        $searchedIn .= "</b>, ";
    }
    if( isset($_GET["quotes"]["share"]) ) {
        $searchedIn .= "Share: " . "<b>" . $shareArray[$_GET["quotes"]["share"]] . "</b>, ";
    }
    
    foreach( $_GET["quotes"] as $key => $val ) {
        if( in_array($key,array('published_range','id_range','share')) ) {
            continue;
        }
        $searchedIn .= empty($val) ? '' : ($key . ": " . "<b>$val</b>, ");
    }
    echo !empty($searchedIn) ? "<br> <b>Searched In:</b> $searchedIn</p>" : '';
    ?>

    <style>
        .gpo-row td {
            vertical-align: top;
        }

            /* ID, Published, Location and Create */
        .gpo-row td.id {
            width: 15px;
        }

        .gpo-row td.published {
            width: 80px;
        }

        .gpo-row td.location {
            width: 120px;
        }

        .gpo-row td.create {
            width: 20px;
            vertical-align: middle;
        }

        .gpo-row td.publisher {
            width: 120px
        }

        .gpo-row td.author {
            width: 100px
        }

            /*
            .gpo-row td.author{ width:10% }
            .gpo-row td.title{ width:30% }
            .gpo-row td.source{ width:15% }

            */
    </style>
    <div class="responsive dejans22">
    <table class="adminlist table-striped table-hover">
        <thead>
        <tr>
            <th>
                <input type="checkbox" name="toggle" value=""
                       onclick="checkAll(<?php echo count($this->rows); ?>,'cb');"/>
            </th>
            <th><?php
//						echo JText::_( 'Id' ); 
                echo JHTML::_('grid.sort', 'Id', 'id', $filter_order_Dir, $filter_order);
                ?></th>
            <th><?php
//						echo JText::_( 'Published' ); 
//	function sort( $title, $order, $direction = 'asc', $selected = 0, $task=NULL )

                echo JHTML::_('grid.sort', 'Published', 'published', $filter_order_Dir, $filter_order);
                ?></th>
            <th><?php
                echo JText::_('Author');
                ?></th>
            <th><?php echo JText::_('Title'); ?></th>
            <th><?php echo JText::_('Source'); ?></th>
            <th><?php echo JText::_('Location'); ?></th>
            <th><?php echo JText::_('Publisher'); ?></th>
            <th><?php echo JText::_('Create'); ?></th>
        </tr>
        </thead>
        <tbody>
<?php
$item = 0;
foreach ($this->rows as $pos => $row) {

    $link_edit = JRoute::_('index.php?option=com_gpo&controller=quotes&task=edit&live_id=' . $row['id'], false);
    $link_lookup = JRoute::_('index.php?option=com_gpo&controller=quotes&task=lookup&id=' . $row['id'], false);
    $link_citation = JRoute::_('index.php?option=com_gpo&controller=quotes&task=createcitation&id=' . $row['id'], false);
    ?>
<tr class="gpo-row" id="gpo-row-<?php echo $pos; ?>-<?php echo $row['id']; ?>">
    <td>
        <?php echo JHTML::_('grid.id', $item, $row['id']); ?>
    </td>
    <td class="id"><a style="color:green" class="track" href="<?php echo $link_lookup;?>" title="Open this Quote in Lookup view"><?php echo $row['id']; ?></a></td>
    <td class="published"><?php echo date("j M Y", strtotime($row['published'])); ?></td>
    <td class="author"><?php echo $row['author']; ?></td>
    <td class="title"><a href="<?php echo $link_edit;?>" title="Edit this Quote"><?php echo $row['title']; ?></a></td>
    <td class="source"><?php echo $row['source']; ?></td>
    <td class="location"><?php
        $locations = explode(",", $row['locations']);
        if (isset($locations['6'])) {
            $lo_size = count($locations);
            for ($i = 6; $i < $lo_size; $i++)
            {
                unset($locations[$i]);
            }
            $locations['5'] .= '...';
        }
        echo implode(", ", $locations);

        ?></td>
    <td class="publisher"><?php echo $row['publisher']; ?></td>
    <td class="create">
        <a href="<?php echo $link_citation;?>" title="Create a Citation">Citation</a>
    </td>
</tr>
    <?php
    $item++;
}
?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="8">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    </div>
	<?php include_once('submenus_endblock.php'); ?>
</form>
<?php $document->addScript(JURI::root(true) . '/media/system/js/prototype-1.6.0.2.js');?>
<script type="text/javascript">
    //<![CDATA[

    $('toolbar-Export').observe('click', function(event) {
        var chked = $('boxchecked').value;
        if(chked<1){
            alert("You must select at least one quote to export!");
            return false;
        }
        $('adminForm_task').value = 'exporttxt';
        $('adminForm').setAttribute('method', 'post');
        $('adminForm').submit();
    });

    $('toolbar-Export-Csv').observe('click', function(event) {
        var chked = $('boxchecked').value;
        if(chked<1){
            alert("You must select at least one quote to export!");
            return false;
        }
        $('adminForm_task').value = 'exportcsv';
        $('adminForm').setAttribute('method', 'post');
        $('adminForm').submit();
    });

    $('toolbar-Export-Csv2').observe('click', function(event) {
        var chked = $('boxchecked').value;
        if(chked<1){
            alert("You must select at least one quote to export!");
            return false;
        }
        $('adminForm_task').value = 'exportcsv2';
        $('adminForm').setAttribute('method', 'post');
        $('adminForm').submit();
    });

    function tableOrdering(order, dir, task) {
        //alert(order, dir, task);
        var form = document.adminForm;
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        submitform(task);
    }

    function reviseSearch() {
        var form = document.adminForm;
        form.revise.value = 1;
        form.submit();
    }

    $$("a.track").each(function(el) {
        el.observe("click", function(event) {
            Event.stop(event);
            var ids = this.up("tr").readAttribute("id").replace("gpo-row-", "");
            ids = ids.split("-");
            pos = ids[0];
            id = ids[1];

            var url = "<?php echo JURI::base(true) . JRoute::_('/index.php?option=com_gpo&controller=quotes&task=searchresult_clicked', false); ?>";
            var data = new Hash();
            data.set("id", id);
            data.set("pos", pos);

            new Ajax.Updater('message_box', url, {
                parameters :  data,
                evalScripts : true
            });

        });
    });
    //]]>
</script>