<?php
defined('_JEXEC') or die('Restricted Access');
?>

<style>
    #h1column, #h2column, #h3column {
        min-height: 220px;
    }
    #h3column {
        min-width: 350px;
    }
    #h2column {
        min-width: 350px;
    }
</style>

<?php
$front_end = str_replace("administrator", '', JURI::base(true));
$previewURLs  = array();
$previewLinks = '';

$replaceCount       = $this->replaceResult['total'];
$replacedLocIDs     = $this->replaceResult['updatedLocationIDs'];
$replacedLangs      = $this->replaceResult['affectedLanguages'];
$replacedCategories = $this->replaceResult['updatedCategories'];

$allGpoLocations      = $this->locModel->getAllLocationNames();
$updatedLocationNames = '';
$counter = 1;

if( 'by_category' == $this->replaceResult['importType'] ) {
    foreach ($replacedLocIDs as $key => $val) {
        $updatedLocationNames .= $allGpoLocations[$val]['name'] . ', ';
        $pURL = JURI::root() . 'preview_dp.php?location=' . urlencode($allGpoLocations[$val]['name']) . '&category=' . $this->search_options['selectedColumn'];
        $previewURLs[] = $pURL;
        $previewLinks .= $counter++ . '. <b>' . $allGpoLocations[$val]['name'] .
                         '</b> <a target="_blank" href="' . $pURL . '">' . $pURL . '</a>' . '<br>';
    }

    if ($replaceCount) {
        $previewSampleURL     = JURI::root() . 'preview_dp.php?location=Afghanistan&category=' . $this->search_options['selectedColumn'];
        $previewSampleURLLink = '<a target="_blank" href="' . $previewSampleURL . '">' . $previewSampleURL . '</a>';

        $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the column: <i>"
                . $this->search_options['selectedColumn'] . '</i>;<br/ > '
                . 'Updated location IDs: <i>'
                . implode(', ', $replacedLocIDs) . ' </i><br /> '
                . 'Location Names: <i>' . substr($updatedLocationNames, 0, -2) . '</i> <br>'
                . ' Affected Languages:  <i>' . implode(', ', array_unique($replacedLangs)) . '</i> <br>';

        $this->search_options['affected_locations'] = substr($updatedLocationNames, 0, -2);
        $this->search_options['affected_columns'] = implode(', ', $this->search_options['selectedColumn']);
        $this->frtModel->insertSearchHistory($this->search_options, $replaceCount);
    }
    
} else {
    $dpColumnsInfo = $this->DPModel->getDPColumnsInfo();
    foreach ($replacedCategories as $key => $val) {
        $updatedColumnNames .= $dpColumnsInfo[$val]->column_title . ', ';
        $pURL = JURI::root()  . 'preview_dp.php?location=' 
                              . urlencode( $allGpoLocations[$this->search_options['selectedLocation']]['name'] ) 
                              . '&category=' . $dpColumnsInfo[$val]->column_name;
        $previewURLs[] = $pURL;
        $previewLinks .= $counter++ . '. <b>' . $allGpoLocations[$this->search_options['selectedLocationID']]['name'] . ' - ' . $dpColumnsInfo[$val]->column_title 
                         . ' (' . $dpColumnsInfo[$val]->column_name . ') ' . 
                         '</b> <a target="_blank" href="' . $pURL . '">' . $pURL . '</a>' . '<br>';
    }
    
    if ($replaceCount) {
        $previewSampleURL = JURI::root() . 'preview_dp.php?location=Afghanistan&category=' . $this->search_options['selectedColumn'];
        $previewSampleURLLink = '<a target="_blank" href="' . $previewSampleURL . '">' . $previewSampleURL . '</a>';

        $responseMsg = "Total <i>$replaceCount</i> rows successfully updated in the Location: <i>"
                       . $allGpoLocations[$this->search_options['selectedLocation']]['name'] . '</i>;<br /> '
                       . 'Updated Category Aliases: <i>'
                       . implode(', ', $replacedCategories) . '</i> <br /> '
                       . 'Category Names: <i>' . substr($updatedColumnNames, 0, -2) . '</i> <br>'
                       . ' Affected Languages:  <i>' . implode(', ', array_unique($replacedLangs)) . '</i> <br>';

        $this->search_options['affected_locations'] = $this->search_options['selectedLocationID'];
        $this->search_options['affected_columns']   = substr($updatedColumnNames, 0, -2);
        $this->frtModel->insertSearchHistory($this->search_options, $replaceCount);
    }
    
}

if ($replaceCount <= 0 ) {
    $responseMsg = $this->errorMsg;
}
?>

<form id="adminForm" name="adminForm" method="post" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php');?>" >

    <?php include_once('submenus_startblock.php');?>

    <?php
        if (!empty($this->errorMessage)) {
            echo "<div border='1' style='color:red'>" . $this->errorMessage . "</div>";
        }
    ?>

    <div>
         <h2> DP-DATA Changes Update Result </h2>
        <?php 
            echo '<h4>' . $responseMsg . '</h4>';
            echo "<br><strong> DP FrontEnd Preview Link: </strong><br>";
            echo $previewLinks;
        ?>
    </div>

    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                    <input type="hidden" name="option" value="com_gpo" />
                    <input type="hidden" name="controller" value="datapages" />
                    <input type="hidden" name="task" value="dpdata_update_automation" />
                    <input type="hidden" name="ParentId" id="ParentId" value="0" />
                    <input type="hidden" name="isSubmitted" id="isSubmitted" value="1" />
            </td>
        </tr>
    </table>

    <?php include_once('submenus_endblock.php'); ?>
</form>
