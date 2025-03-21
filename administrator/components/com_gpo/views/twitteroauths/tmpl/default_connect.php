<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
$jView = new JViewLegacy();

$type = Joomla\CMS\Factory::getApplication()->getInput()->get( 'type' );
?>

<style>
    #topic-tbl td{
        vertical-align:top;
    }
    .row{
        width:600px;
    }
    .leftcell{
        width: 200px;
        text-align:left;
    }
</style>


<div width="80%" style="margin-left:20px;">
    <?php if($this->item->client == 'twitter' || $type =='twitter'){ ?>
        <form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths'); ?>" id="adminForm" name="adminForm">
            <input type="hidden" name="option" value="com_gpo" />
            <input type="hidden" name="controller" value="twitteroauths" />
            <input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
            <input type="hidden" name="task" value="save" />
            <table width="100%" border="0" id="topic-tbl">
                <tr>
                    <td class="leftcell">&nbsp;</td>
                    <td>
                        <div id="message-box"></div>
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        API Owner
                    </td>
                    <td>
                        <input id="owner" class="row" name="owner" value="<?php echo $jView->escape( $this->item->owner ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Client
                    </td>
                    <td>
                        <select id="client_name" name="client_name">
                            <option value="">--Select--</option>
                            <option value="twitter">Twitter</option>
                            <option value="bitly">Bitly</option>
                            <?php if($this->item){
                                echo "<option selected=selected  value=".$this->item->client.">".$this->item->client."</option>";
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Consumer Key
                    </td>
                    <td>
                        <input id="consumer_key" class="row" name="consumer_key" value="<?php echo $jView->escape( $this->item->consumer_key ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Consumer Secret
                    </td>
                    <td>
                        <input id="consumer_secret" class="row" name="consumer_secret" value="<?php echo $jView->escape( $this->item->consumer_secret ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        User Token
                    </td>
                    <td>
                        <input id="user_token" class="row" name="user_token" value="<?php echo $jView->escape( $this->item->user_token ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        User Secret
                    </td>
                    <td>
                        <input id="user_secret" class="row" name="user_secret" value="<?php echo $jView->escape( $this->item->user_secret ); ?>">
                    </td>
                </tr>
            </table>

            <div class="clear"></div>

            <div id="tool-tip-box"></div>
        </form>
    <?php }elseif($type =='bitly') { ?>
        <form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths'); ?>" id="adminForm" name="adminForm">
            <input type="hidden" name="option" value="com_gpo" />
            <input type="hidden" name="controller" value="twitteroauths" />
            <input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
            <input type="hidden" name="task" value="save" />
            <table width="100%" border="0" id="topic-tbl">
                <tr>
                    <td class="leftcell">&nbsp;</td>
                    <td>
                        <div id="message-box"></div>
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        API Owner
                    </td>
                    <td>
                        <input id="owner" class="row" name="owner" value="<?php echo $jView->escape( $this->item->owner ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Client
                    </td>
                    <td>
                        <select id="client_name" name="client_name">
                            <option value="">--Select--</option>
                            <option value="twitter">Twitter</option>
                            <option value="bitly">Bitly</option>
                            <?php if($this->item){
                                echo "<option selected=selected  value=".$this->item->client.">".$this->item->client."</option>";
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Bitly Username
                    </td>
                    <td>
                        <input id="consumer_key" class="row" name="consumer_key" value="<?php echo $jView->escape( $this->item->consumer_key ); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="leftcell">
                        Bitly API Key
                    </td>
                    <td>
                        <input id="consumer_secret" class="row" name="consumer_secret" value="<?php echo $jView->escape( $this->item->consumer_secret ); ?>">
                    </td>
                </tr>
            </table>

            <div class="clear"></div>

            <div id="tool-tip-box"></div>
        </form>
    <?php }else { ?>
        <form method="post" action="<?php echo JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths'); ?>" id="adminForm" name="adminForm">
                <input type="hidden" name="option" value="com_gpo" />
                <input type="hidden" name="controller" value="twitteroauths" />
                <input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
                <input type="hidden" name="task" value="save" />
                <table width="100%" border="0" id="topic-tbl">
                    <tr>
                        <td class="leftcell">&nbsp;</td>
                        <td>
                            <div id="message-box"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="leftcell">
        API Owner
        </td>
                        <td>
                            <input id="owner" class="row" name="owner" value="<?php echo $jView->escape( $this->item->owner ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="leftcell">
        Client
                        </td>
                        <td>
                            <select id="client_name" name="client_name">
                                <option value="">--Select--</option>
                                <option value="twitter">Twitter</option>
                                <option value="bitly">Bitly</option>
                                <?php if($this->item){
            echo "<option selected=selected  value=".$this->item->client.">".$this->item->client."</option>";
        }

                                ?>
        </select>
        </td>
        </tr>
        <tr>
            <td class="leftcell">
                Bitly Username
            </td>
            <td>
                <input id="consumer_key" class="row" name="consumer_key" value="<?php echo $jView->escape( $this->item->consumer_key ); ?>">
            </td>
        </tr>
        <tr>
            <td class="leftcell">
               Bitly API Key
            </td>
            <td>
                <input id="consumer_secret" class="row" name="consumer_secret" value="<?php echo $jView->escape( $this->item->consumer_secret ); ?>">
            </td>
        </tr>
        </table>

        <div class="clear"></div>

        <div id="tool-tip-box"></div>
        </form>

    <?php } ?>
</div>
