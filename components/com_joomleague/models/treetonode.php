<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelTreetonode extends JoomleagueModelProject
{
	var $projectid=0;
	var $treetoid=0;

	function __construct( )
	{
		parent::__construct( );
		$this->projectid=JRequest::getInt('p',0);
		$this->treetoid=JRequest::getInt('tnid',0);
	}

	function getTreetonode()
	{
		if (!$this->projectid) {
			$this->setError(JText::_('Missing project id'));
			return false;
		}
		$query = 'SELECT ttn.* ';
		$query .=	' ,ttn.id AS ttnid';
		$query .=	' ,c.country AS country';
		$query .=	' ,c.logo_small AS logo_small';
		$query .=	' ,t.name AS team_name ';
		$query .=	' ,t.middle_name AS middle_name ';
		$query .=	' ,t.short_name AS short_name ';
		$query .=	' ,t.id AS tid ';
		$query .=	' ,ttn.title AS title ';
		$query .=	' ,ttn.content AS content ';
		$query .=	' ,tt.tree_i AS tree_i ';
		$query .=	' ,tt.hide AS hide ';
		$query .=	' FROM #__joomleague_treeto_node AS ttn ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt ON pt.id = ttn.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t ON t.id = pt.team_id ';
		$query .=	' LEFT JOIN #__joomleague_club AS c ON c.id = t.club_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto AS tt ON tt.id = ttn.treeto_id ';
		$query .=	' WHERE ttn.treeto_id = ' .  $this->_db->Quote($this->treetoid) ;
		$query .=	' ORDER BY ttn.row ';
		$query .=	';';
		$this->_db->setQuery( $query );
		$this->treetonode = $this->_db->loadObjectList();
		
		return $this->treetonode;
	}
	
	function getNodeMatches($ttnid=0)
	{
		$query = ' SELECT mc.id AS value ';
		$query .=	' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
	//	$query .=	' ,mc.id AS notes ';
	//	$query .=	' ,mc.id AS info ';
		$query .=	' FROM #__joomleague_match AS mc ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .=	' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto_match AS ttm ON mc.id = ttm.match_id ';
		$query .=	' WHERE  ttm.node_id = ' . (int) $ttnid ;
		$query .=	' ORDER BY mc.id ';
		$query .=	';';
		$this->_db->setQuery($query);
	//	if ( !$result = $this->_db->loadObjectList() )
	//	{
	//		$this->setError( $this->_db->getErrorMsg() );
	//		return false;
	//	}
	//	else
	//	{
			//return $result;
			return $this->_db->loadObjectList();
	//	}
	}
	
	function showNodeMatches(&$nodes)
	{
		//TODO
		$matches=$this->model->getNodeMatches($nodes);
		$lineinover='';
		foreach ($matches as $mat)
		{
			$lineinover .= $mat->text.'<br/>';
		}
		echo $lineinover;
	}
	
	function getRoundName()
	{
		$query = 'SELECT * '
			. ' FROM #__joomleague_round AS r '
			. ' WHERE r.project_id = ' .  $this->_db->Quote($this->projectid)
			. ' ORDER BY r.round_date_first, r.ordering '
			;
		$this->_db->setQuery( $query );
		$this->roundname = $this->_db->loadObjectList();

		return $this->roundname;
	}
}
?>