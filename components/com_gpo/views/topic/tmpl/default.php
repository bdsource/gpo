<?php
defined('_JEXEC') or die('Restricted Access');
$jView = new JViewLegacy();

?>
<h1><?php //echo $jView->escape( $this->topic->get('page_headline' ) ); ?></h1>

<h1 class="componentheading">Alpers News</h1>
<h3><?php echo $jView->escape($this->topic->get('page_headline_sub')); ?><div class="optionbar">
                <a id="btnprint" class="btn print" title="<?php echo JText::_('COM_GPO_NEWS_PRINT_ICON_TITLE');?>" onclick="window.print();"></a>
            </div></h3>

<?php
if (count($this->results)) :
    define('NEWS_LIST', 1);

    foreach ($this->results as $article):

        include(JPATH_COMPONENT . DS . 'views' . DS . 'search' . DS . 'tmpl' . DS . 'default_abstract.php');

    endforeach;
    
    ##footnote
    echo '<br>
          <h5> 
              For earlier articles, use the Search News feature above
          </h5>';

else:
//#fix
    echo '<p>This topic is yet to be populated with data from the real world.</p>';
endif;
?>
