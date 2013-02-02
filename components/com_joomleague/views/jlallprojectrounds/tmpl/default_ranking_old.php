<?php defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<!-- Main START -->
<a name="jl_top" id="jl_top"></a>


<!-- content -->
<?php
    # var_dump( $this->currentRanking );
    foreach ( $this->currentRanking as $division => $cu_rk )
    {
        if ($division)
        {
            ?>
            <table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="contentheading">
                        <?php
                        //JoomleagueHTML::showRankingDivisionTitle( $division );
                        ?>
                    </td>
                </tr>
            </table>
            <?php
        }
        ?>
        <div style="width:99%;height:auto;overflow:auto;text-align:center;" align="center">
            <table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
                <?php
//		print_r($this->division);
/*		$style='style="background-color: #c0c0c0; border: 0px solid white; font-weight: bold; font-size: 8pt; width: 75px; font-family: verdana; text-align: center;"';
$space="&nbsp;";

$path='http://localhost/~val/101/onwhite/';
//<img src="" alt="|" width="16" height="18px">
$treedl='treedl.gif';
$treeul='treeul.gif';
$treecl='treecl.gif';

$treedr='treedr.gif';
$treeur='treeur.gif';
$treecr='treecr.gif';

$treep='treep.gif';
$treeh='treeh.gif';

$dl='<img src="'.$path.$treedl.'" alt="|" width="16" height="18px">';
$ul='<img src="'.$path.$treeul.'" alt="|" width="16" height="18px">';
$cl='<img src="'.$path.$treecl.'" alt="|-" width="16" height="18px">';
$dr='<img src="'.$path.$treedr.'" alt="|" width="16" height="18px">';
$ur='<img src="'.$path.$treeur.'" alt="|" width="16" height="18px">';
$cr='<img src="'.$path.$treecr.'" alt="-|" width="16" height="18px">';
$p='<img src="'.$path.$treep.'" alt="|" width="16" height="18px">';
$h='<img src="'.$path.$treeh.'" alt="-" width="16" height="18px">';

*/?>

<?php
								echo $this->loadTemplate('rankingheading');
								$this->division = $division;
								$this->current  = &$cu_rk;
								echo $this->loadTemplate('rankingrows');
                ?>
            </table>
        </div>
        <br/>
        <?php
    }
?>
<!-- ranking END -->



