<?php

/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @theme	aia
 * @copyright (c) 2016, Michiel Keijts
 */

// Extra arguments: the extra arguments
$data = array_merge(
		array(
			'title'			=>	!strlen(get_the_title()) > 2 ? get_the_title() : xprofile_get_field_data(1, get_current_user_id()),
			'current_title' => "&nbsp;&nbsp;<b>Great, you're in the team!</b>",
			'nav'			=>	my_aia_bp_get_displayed_user_nav(),
		),
		$extra_arguments
	);
?>
	<span class="buddypress">
		<nav class="buddypress_nav">
			<div class="columns-wrapper">
				<div class="text column-4-2 hidden-md">
					<?= $data['current_title']; ?>
				</div>

				<div class='bp-menu column-4-2  column-sm-1'>
					<div class='text'><?= $data['title']; ?></div>
					<a class="dropdown_menu" id='dropdown_menu' href="javascript:void(0);">
						<span class="select-button"></span>
					</a>

					<div id="item-nav" class="column-4-2 column-sm-1">
						<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
							<ul>

								<?= $data['nav']; ?>

								<?php

								/**
								 * Fires after the display of member options navigation.
								 *
								 * @since 1.2.4
								 */
								do_action( 'bp_member_options_nav' ); ?>

							</ul>
						</div>
					</div><!-- #item-nav -->
				</div>		
			</div>
		</nav>
	</span>