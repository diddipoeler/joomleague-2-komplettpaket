<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

    <!-- START: Contentheading -->
<table class="contentpaneopen" width="100%">
    <tr>
        <td class="contentheading">
            <?php
            echo $this->leaguename;;
            ?>
        </td>
    </tr>
    <tr>
        <td class="contentheading">
            <?php
            echo JText::sprintf('JL_RANKINGALLTIME_PAGE_TITLE', $this->alltimepoints );
            ?>
        </td>
    </tr>
    <tr>
        <td align="left">
            <?php
            /*
            if ( $this->tableconfig['show_ranking_dropdown'] == 1 )
            {
                echo JoomleagueHTML::rankingnav(
                                                                                $this->rounds,
                                                                                $this->type,
                                                                                $this->from,
                                                                                $this->to,
                                                                                $this->divLevel,
                                                                                $this->tableconfig );
            }
            */
            ?>
        </td>
    </tr>
</table>
<br />
    <!-- END: Contentheading -->