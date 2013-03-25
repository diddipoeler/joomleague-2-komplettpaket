<?php 
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JHTML::_('behavior.mootools');
$modalheight = JComponentHelper::getParams('com_joomleague')->get('modal_popup_height', 600);
$modalwidth = JComponentHelper::getParams('com_joomleague')->get('modal_popup_width', 900);
?>

	<div class="contentpaneopen">
		<div class="contentheading">
			<?php
				echo JText::_( 'COM_JOOMLEAGUE_CLUBINFO_TITLE' ) . " " . $this->club->name;

	            if ( $this->showediticon )
	            {
	                /*
                    $link = JoomleagueHelperRoute::getClubInfoRoute( $this->project->id, $this->club->id, "club.edit" );
	                $desc = JHTML::image(
	                                      "media/com_joomleague/jl_images/edit.png",
	                                      JText::_( 'COM_JOOMLEAGUE_CLUBINFO_EDIT' ),
	                                      array( "title" => JText::_( "COM_JOOMLEAGUE_CLUBINFO_EDIT" ) )
	                                   );
	                echo " ";
	                echo JHTML::_('link', $link, $desc );
                    */
                 ?>   
	             <a	rel="{handler: 'iframe',size: {x: <?php echo $modalwidth; ?>,y: <?php echo $modalheight; ?>}}"
									href="index.php?option=com_joomleague&tmpl=component&view=editclub&cid=<?php echo $this->club->id; ?>"
									 class="modal">
									<?php
									echo JHTML::_(	'image','administrator/components/com_joomleague/assets/images/edit.png',
													JText::_('COM_JOOMLEAGUE_ADMIN_CLUBINFO_EDIT_DETAILS'),'title= "' .
													JText::_('COM_JOOMLEAGUE_ADMIN_CLUBINFO_EDIT_DETAILS').'"');
									?>
								</a>
                <?PHP
                
                
                }
			?>
		</div>
	</div>