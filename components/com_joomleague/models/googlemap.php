<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

include_once JPATH_COMPONENT . DS . 'helpers' . DS .'easygooglemap.php';

class JoomleagueModelGoogleMap extends JModel
{
    function __construct( )
    {
        parent::__construct( );
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

    function getGoogleApiKey( )
    {
        return 'ABQIAAAAUb3FT_Hqpkax9LNDx4V1YBQfi9dertITqLwkvJfu7PIjzmG5ahQbXN7oji_69-FZZFwiADO1n5i5XA';
    }
}

?>