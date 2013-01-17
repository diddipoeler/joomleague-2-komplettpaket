<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );
include_once JPATH_COMPONENT . DS . 'helpers' . DS . 'easygooglemap.php';

class JoomleagueModelPlayground extends JoomleagueModelProject
{
    var $playgroundid = 0;
    var $playground = null;

    function __construct( )
    {
        parent::__construct( );

        $this->projectid = JRequest::getInt( "p", 0 );
        $this->playgroundid = JRequest::getInt( "pgid", 0 );
    }

    function getPlayground( )
    {
        if ( is_null( $this->playground ) )
        {
            $pgid = JRequest::getInt( "pgid", 0 );
            if ( $pgid > 0 )
            {
                $this->playground = & $this->getTable( 'Playground', 'Table' );
                $this->playground->load( $pgid );
            }
        }
        return $this->playground;
    }

    function getAddressString( )
    {
        $playground = $this->getPlayground();
        $address_string = $playground->address.", ".$playground->zipcode ." ".$playground->city;
        return $address_string;
    }

    function getTeams( )
    {
        $teams = array();

        $playground = $this->getPlayground( );
        if ( $playground->id > 0 )
        {
            $query = "SELECT id, team_id, project_id
                      FROM #__joomleague_project_team
                      WHERE standard_playground = ".(int)$playground->id;
            $this->_db->setQuery( $query );
            $rows = $this->_db->loadObjectList();
			
            foreach ( $rows as $row )
            {
                $teams[$row->id]->project_team[] = $row;

                $query = "SELECT name, short_name, notes
                          FROM #__joomleague_team
                          WHERE id=".(int)$row->team_id;
                $this->_db->setQuery( $query );
                $teams[ $row->id ]->teaminfo[] = $this->_db->loadObjectList();

                $query= "SELECT name
                         FROM #__joomleague_project
                         WHERE id=".(int)$row->project_id;
                $this->_db->setQuery( $query );
            	$teams[ $row->id ]->project = $this->_db->loadResult();
            }
        }
        return $teams;
    }

    function getNextGames( $project = 0 )
    {
        $result = array();

        $playground = $this->getPlayground( );
        if ( $playground->id > 0 )
        {
            $query = "SELECT m.*, DATE_FORMAT(m.time_present, '%H:%i') time_present,
                             p.name AS project_name, tj.team_id team1, tj2.team_id team2
                      FROM #__joomleague_match AS m
                      INNER JOIN #__joomleague_project_team tj ON tj.id = m.projectteam1_id 
                      INNER JOIN #__joomleague_project_team tj2 ON tj2.id = m.projectteam2_id 
                      INNER JOIN #__joomleague_project AS p ON p.id=tj.project_id
                      INNER JOIN #__joomleague_team t ON t.id = tj.team_id
                      INNER JOIN #__joomleague_club c ON c.id = t.club_id
                      WHERE (m.playground_id= " . (int)$playground->id . "
                          OR (tj.standard_playground = " . (int)$playground->id . " AND m.playground_id IS NULL)
                          OR (c.standard_playground = " . (int)$playground->id . " AND m.playground_id IS NULL))
                      AND m.match_date > NOW()
                      AND m.published = 1
                      AND p.published = 1";
            if ( $project )
            {
                $query .= " AND project_id= " . (int)$project;
            }
            $query .= " GROUP BY m.id ORDER BY match_date ASC";
            $this->_db->setQuery( $query );
            $result = $this->_db->loadObjectList();
        }
        return $result;
    }

    function getTeamLogo($team_id)
    {
        $query = "
            SELECT c.logo_small,
                   c.country
            FROM #__joomleague_team t
            LEFT JOIN #__joomleague_club c ON c.id = t.club_id
            WHERE t.id = ".$team_id."
        ";
        $this->_db->setQuery( $query );
        $result = $this->_db->loadObjectList();

        return $result;
    }

    function getTeamsFromMatches( & $games )
    {
        $teams = Array();

        if ( !count( $games ) )
        {
            return $teams;
        }

        foreach ( $games as $m )
        {
            $teamsId[] = $m->team1;
            $teamsId[] = $m->team2;
        }
        $listTeamId = implode( ",", array_unique( $teamsId ) );

        $query = "SELECT t.id, t.name
                 FROM #__joomleague_team t
                 WHERE t.id IN (".$listTeamId.")";
        $this->_db->setQuery( $query );
        $result = $this->_db->loadObjectList();

        foreach ( $result as $r )
        {
            $teams[$r->id] = $r;
        }

        return $teams;
    }

    function getGoogleApiKey( )
    {
    	$params =& JComponentHelper::getParams('com_joomleague');
    	$apikey=$params->get('cfg_google_api_key');

      return $apikey;
    }



    function getGoogleMap( $mapconfig, $address_string = "" )
    {
        $gm = null;

        $google_api_key = $this->getGoogleApiKey();
        if ( ( trim( $google_api_key ) != "" ) &&
             ( trim( $address_string ) != "" ) )
        {
            $gm = new EasyGoogleMap( $google_api_key, "jl_pg_map" );

            $width = ( is_int( $mapconfig['width'] ) ) ? $mapconfig['width'].'px' : $mapconfig['width'];

            $gm->SetMapWidth( $mapconfig['width'] );
            $gm->SetMapHeight( $mapconfig['height'] );
            $gm->SetMapControl( $mapconfig['map_control'] );
            $gm->SetMapDefaultType( $mapconfig['default_map_type'] );

            if ( intval( $mapconfig['map_zoom'] ) > 0 )
            {
                $gm->SetMapZoom( intval( $mapconfig['map_zoom'] ) );
            }

            $gm->mScale = ( intval( $mapconfig['map_scale'] ) > 0 ) ? TRUE : FALSE;
            $gm->mMapType = ( intval( $mapconfig['map_type_select']) > 0 ) ? TRUE : FALSE;
            $gm->mContinuousZoom = ( intval( $mapconfig['cont_zoom']) > 0 ) ? TRUE : FALSE;
            $gm->mDoubleClickZoom = ( intval( $mapconfig['dblclick_zoom']) > 0 ) ? TRUE : FALSE;
            $gm->mInset = ( intval( $mapconfig['map_inset'] ) > 0 ) ? TRUE : FALSE;
            $gm->mShowMarker = ( intval( $mapconfig['show_marker'] ) > 0 ) ? TRUE : FALSE;
            $gm->SetMarkerIconStyle( $mapconfig['map_icon_style'] );
            $gm->SetMarkerIconColor( $mapconfig['map_icon_color'] );
            $gm->SetAddress( $address_string );
        }
        return $gm;
    }

}
?>