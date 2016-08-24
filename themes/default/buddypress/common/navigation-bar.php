<?php

/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @theme	aia
 * @copyright (c) 2016, Michiel Keijts
 */

// get BP Nav menu
ob_start();
bp_get_displayed_user_nav();
$bp_menu_list = ob_get_contents();
ob_end_clean();


// Extra arguments: the extra arguments
$data = array_merge(
		array(
			'title'			=>	the_title("","",FALSE),
			'current_title' => "<b>Great, you're in the team!</b>",
			'nav'			=>	$bp_menu_list,
		),
		$extra_arguments
	);
?>
	<span class="buddypress">
		<nav class="buddypress_nav">
			<div class="columns-wrapper">
				<div class="text column-4-3">
					<?= $data['current_title']; ?>
				</div>

				<div class='bp-menu column-4-1 column-mobile-1'>
					<div class='text'><?= $data['title']; ?></div>
					<a class="dropdown_menu" href="javascript:void(0);">
						<span class="select-button"></span>
					</a>

					<div id="item-nav" class="column-4-1">
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