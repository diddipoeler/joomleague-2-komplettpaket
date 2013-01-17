<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- person data START -->
<?php if ($this->referee) { ?>
<h2><?php	echo JText::_( 'COM_JOOMLEAGUE_PERSON_PERSONAL_DATA' );	?></h2>
<table class="plgeneralinfo">
	<tr>
		<?php
		if ( $this->config['show_photo'] == 1 )
		{
			?>
			<td class="picture">
				<?php
				$picturetext=JText::_( 'COM_JOOMLEAGUE_PERSON_PICTURE' );
				$imgTitle = JText::sprintf( $picturetext, JoomleagueHelper::formatName(null, $this->referee->firstname, $this->referee->nickname, $this->referee->lastname, $this->config["name_format"]) );
				$picture = $this->referee->picture;
				if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player")  ))
				{
					$picture = $this->person->picture;
				}
				if ( !file_exists( $picture ) )
				{
					$picture = JoomleagueHelper::getDefaultPlaceholder("player") ;
				}
				echo JoomleagueHelper::getPictureThumb($picture, 
														$imgTitle, 
														$this->config['picture_width'],
														$this->config['picture_height']);
				?>
			</td>
			<?php
		}
		?>
		<td class="info">
			<table class="plinfo">
				<?php
				if(!empty($this->person->country) && ($this->config["show_nationality"] == 1))
				{
				?>
				<tr>
					<td class="label"><?php echo JText::_( 'COM_JOOMLEAGUE_PERSON_NATIONALITY' ); ?>
					</td>
					<td class="data">
					<?php
						echo Countries::getCountryFlag( $this->person->country ) . " " .
						JText::_( Countries::getCountryName($this->person->country));
						?>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td class="label">

							<?php
							echo JText::_( 'COM_JOOMLEAGUE_PERSON_NAME' );
							?>

					</td>
					<td class="data">
						<?php
						$outputName = JText::sprintf( '%1$s %2$s', $this->referee->firstname, $this->referee->lastname);
						if ( $this->referee->user_id )
						{
							switch ( $this->config['show_user_profile'] )
							{
								case 1:	 // Link to Joomla Contact Page
											$link = JoomleagueHelperRoute::getContactRoute( $this->referee->user_id );
											$outputName = JHTML::link( $link, $outputName );
											break;

								case 2:	 // Link to CBE User Page with support for JoomLeague Tab
											$link = JoomleagueHelperRoute::getUserProfileRouteCBE(	$this->referee->user_id,
																									$this->project->id,
																									$this->referee->id );
											$outputName = JHTML::link( $link, $outputName );
											break;

								default:	break;
							}
						}
						echo $outputName;
						?>
					</td>
				</tr>
				<?php
						if ( ! empty( $this->referee->nickname ) )
						{
							?>
							<tr>
								<td class="label">

										<?php
										echo JText::_( 'COM_JOOMLEAGUE_PERSON_NICKNAME' );
										?>

								</td>
								<td class="data">
									<?php
									echo $this->referee->nickname;
									?>
								</td>
							</tr>
							<?php
						}
				if (	( $this->config[ 'show_birthday' ] > 0 ) &&
						( $this->config[ 'show_birthday' ] < 5 ) &&
						( $this->referee->birthday != '0000-00-00' ) )
				{
					#$this->config['show_birthday'] = 4;
					?>
					<tr>
						<td class="label">

								<?php
								switch ( $this->config['show_birthday'] )
								{
									case 	1:			// show Birthday and Age
														$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY_AGE';
														break;

									case 	2:			// show Only Birthday
														$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY';
														break;

									case 	3:			// show Only Age
														$outputStr = 'COM_JOOMLEAGUE_PERSON_AGE';
														break;

									case 	4:			// show Only Year of birth
														$outputStr = 'COM_JOOMLEAGUE_PERSON_YEAR_OF_BIRTH';
														break;
								}
								echo JText::_( $outputStr );
								?>

						</td>
						<td class="data">
							<?php
							#$this->assignRef( 'playerage', $model->getAge( $this->player->birthday, $this->project->start_date ) );
							switch ( $this->config['show_birthday'] )
							{
								case 1:	 // show Birthday and Age
											$birthdateStr =	$this->referee->birthday != "0000-00-00" ?
															JHTML::date( $this->referee->birthday, JText::_( 'COM_JOOMLEAGUE_GLOBAL_DAYDATE' ) ) : "-";
											$birthdateStr .= "&nbsp;(" . JoomleagueHelper::getAge( $this->referee->birthday,$this->referee->deathday ) . ")";
											break;

								case 2:	 // show Only Birthday
											$birthdateStr =	$this->referee->birthday != "0000-00-00" ?
															JHTML::date( $this->referee->birthday, JText::_( 'COM_JOOMLEAGUE_GLOBAL_DAYDATE' ) ) : "-";
											break;

								case 3:	 // show Only Age
											$birthdateStr = JoomleagueHelper::getAge( $this->referee->birthday,$this->referee->deathday );
											break;

								case 4:	 // show Only Year of birth
											$birthdateStr =	$this->referee->birthday != "0000-00-00" ?
															JHTML::date( $this->referee->birthday, JText::_( '%Y' ) ) : "-";
											break;

								default:	$birthdateStr = "";
											break;
							}
							echo $birthdateStr;
							?>
						</td>
					</tr>
					<?php
				}

				if (( $this->referee->address != "" ) && ( $this->config[ 'show_person_address' ] ==1  ))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_ADDRESS' );
								?>

						</td>
						<td class="data">
							<?php
							echo Countries::convertAddressString(	'',
																	$this->referee->address,
																	$this->referee->state,
																	$this->referee->zipcode,
																	$this->referee->location,
																	$this->referee->address_country,
																	'COM_JOOMLEAGUE_PERSON_ADDRESS_FORM' );

							?>
						</td>
					</tr>
					<?php
				}

				if (( $this->referee->phone != "" ) && ( $this->config[ 'show_person_phone' ] ==1  ))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_PHONE' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->referee->phone;
							?>
						</td>
					</tr>
					<?php
				}

				if (( $this->referee->mobile != "" ) && ( $this->config[ 'show_person_mobile' ] ==1  ))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_MOBILE' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->referee->mobile;
							?>
						</td>
					</tr>
					<?php
				}

			if (($this->config['show_person_email'] == 1) && ( $this->referee->email != "" ))
			{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_EMAIL' );
								?>

						</td>
						<td class="data">
							<?php
							$user = JFactory::getUser();
							if ( ( $user->id ) || ( ! $this->overallconfig['nospam_email'] ) )
							{
								?>
								<a href="mailto: <?php echo $this->referee->email; ?>">
									<?php
									echo $this->club->email;
									?>
								</a>
								<?php
							}
							else
							{
								echo JHTML::_('email.cloak', $this->referee->email );
							}
							?>
						</td>
					</tr>
					<?php
			}

				if (( $this->referee->website != "" ) && ($this->config['show_person_website'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_WEBSITE' );
								?>

						</td>
						<td class="data">
							<?php
							echo JHTML::_(	'link',
											$this->referee->website,
											$this->referee->website,
											array( 'target' => '_blank' ) );
							?>
						</td>
					</tr>
					<?php
				}

				if(( $this->referee->height > 0 ) && ($this->config['show_person_height'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_HEIGHT' );
								?>

						</td>
						<td class="data">
							<?php
							echo str_replace( "%HEIGHT%", $this->referee->height, JText::_( 'COM_JOOMLEAGUE_PERSON_HEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
				}
				if (( $this->referee->weight > 0 ) && ($this->config['show_person_weight'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_WEIGHT' );
								?>

						</td>
						<td class="data">
							<?php
							echo str_replace( "%WEIGHT%", $this->referee->weight, JText::_( 'COM_JOOMLEAGUE_PERSON_WEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
				}
				if ( $this->referee->position_name != "" )
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_POSITION' );
								?>

						</td>
						<td class="data">
							<?php
							echo JText::_( $this->referee->position_name );
							?>
							</td>
					</tr>
					<?php
				}
				if (( ! empty( $this->referee->knvbnr ) ) && ($this->config['show_person_regnr'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_REGISTRATIONNR' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->referee->knvbnr;
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
	</tr>
</table>
<br />
<?php } ?>
