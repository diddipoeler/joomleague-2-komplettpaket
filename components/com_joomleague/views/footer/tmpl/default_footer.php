<?php defined('_JEXEC') or die('Restricted access');

if (JComponentHelper::getParams('com_joomleague')->get('show_footer',0))
{
?>
	<br />
		<div align='center' style='font-size:9px;float:none;clear:both; '>
			<?php
			echo ' :: Powered by ';
			echo JHTML::link('http://www.joomleague.net','JoomLeague',array('target' => '_blank'));
			echo ' - ';
			echo JHTML::link('index.php?option=com_joomleague&amp;view=about',sprintf('Version %1$s',JoomleagueHelper::getVersion()));
			echo ' :: ';
			?>
		</div>
<?php
}
?>