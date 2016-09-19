<?php
/**
 * (C) 2016, Normit, Events-Manager
 * 
 * Group documents, show the documents belonging to the group
 *
 *  original copied from events-manager plugin
 */

	global $bp;//, $EM_Notices;
	/*echo $EM_Notices;
	$url = $bp->events->link . 'my-events/'; //url to this page
	$order = ( !empty($_REQUEST ['order']) ) ? $_REQUEST ['order']:'ASC';
	$limit = ( !empty($_REQUEST['limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
	$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
	$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
	$EM_Events = EM_Events::get( array('group'=>'my','scope'=>'future', 'limit' => 0, 'order' => $order) );
	$events_count = count ( $EM_Events );
	$future_count = EM_Events::count( array('status'=>1, 'owner' =>get_current_user_id(), 'scope' => 'future'));
	$pending_count = EM_Events::count( array('status'=>0, 'owner' =>get_current_user_id(), 'scope' => 'all') );
	$use_events_end = get_option('dbem_use_event_end');
	echo $EM_Notices;*/
	
	$documents = get_posts(array('post_type'=>'document'));
	?>
	<div class="tablenav">
		<?php
		if ( $events_count >= $limit ) {
			//$events_nav = em_admin_paginate( $events_count, $limit, $page);
			//echo $events_nav;
		}
		?>
		<br class="clear" />
	</div>
		
	<?php
	if (empty ( $documents )) {
		// TODO localize
		echo "<p>". __( 'No Documents found','my-aia') ."</p>";
	} else {
	?>
			
	<table class="widefat events-table">
		<thead>
			<tr>
				<?php /* 
				<th class='manage-column column-cb check-column' scope='col'>
					<input class='select-all' type="checkbox" value='1' />
				</th>
				*/ ?>
				<th><?php _e ( 'Name', 'my-aia'); ?></th>
				<th>&nbsp;</th>
				<th><?php _e ( 'Omschrijving', 'events-manager'); ?></th>
				<th><?php _e ( 'Link', 'events-manager'); ?></th>
				<th colspan="2"><?php _e ( 'Date and time', 'events-manager'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$rowno = 0;
			$document_count = 0;
			foreach ( $documents as $document ) {
				/* @var $event EM_Event */
				if( ($rowno < $limit || empty($limit)) && ($document_count >= $offset || $offset === 0) ) {
					$rowno++;
					$class = ($rowno % 2) ? 'alternate' : '';
					// FIXME set to american
					//$localised_start_date = date_i18n(get_option('dbem_date_format'), $event->start);
					//$localised_end_date = date_i18n(get_option('dbem_date_format'), $event->end);
					$style = "";
					//$today = current_time('timestamp');
					//$location_summary = "<b>" . $event->get_location()->name . "</b><br/>" . $event->get_location()->address . " - " . $event->get_location()->town;
					
					if ($event->start < $today && $event->end < $today){
						$class .= " past";
					}
					//Check pending approval events
					if ( !$event->status ){
						$class .= " pending";
					}					
					?>
					<tr class="event <?php echo trim($class); ?>" <?php echo $style; ?> id="event_<?php echo $event->event_id ?>">
						<?php /*
						<td>
							<input type='checkbox' class='row-selector' value='<?php echo $event->event_id; ?>' name='events[]' />
						</td>
						*/ ?>
						<td>
							<strong>
								<a class="row-title" href="<?php echo $event->get_edit_url(); ?>"><?php echo ($event->event_name); ?></a>
							</strong>
							<?php 
							/*if( $event->can_manage('manage_bookings','manage_others_bookings') && get_option('dbem_rsvp_enabled') == 1 && $event->event_rsvp == 1 ){
								?>
								<br/>
								<a href="<?php echo esc_url($event->get_bookings_url()); ?>"><?php echo __("Bookings",'events-manager'); ?></a> &ndash;
								<?php _e("Booked",'events-manager'); ?>: <?php echo $event->get_bookings()->get_booked_spaces()."/".$event->get_spaces(); ?>
								<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
									| <?php _e("Pending",'events-manager') ?>: <?php echo $event->get_bookings()->get_pending_spaces(); ?>
								<?php endif;
							}*/
							?>
							<div class="row-actions">
								<?php if( current_user_can('delete_events')) : ?>
								<span class="trash"><a href="<?php echo $url ?>?action=event_delete&amp;event_id=<?php echo $event->event_id ?>" class="em-event-delete"><?php _e('Delete','events-manager'); ?></a></span>
								<?php endif; ?>
							</div>
						</td>
						<td>
							<a href="<?php echo $url ?>edit/?action=event_duplicate&amp;event_id=<?php echo $event->event_id ?>" title="<?php _e ( 'Duplicate this event', 'events-manager'); ?>">
								<strong>+</strong>
							</a>
						</td>
						<td>
							<?php echo $location_summary; ?>
							<?php if( is_object($category) && !empty($category->name) ) : ?>
							<br/><span class="category"><strong><?php _e( 'Category', 'events-manager'); ?>: </strong><?php echo $category->name ?></span>
							<?php endif; ?>
						</td>
				
						<td>
							<?php echo $localised_start_date; ?>
							<?php echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'' ?>
							<br />
							<?php
								if(!$event->event_all_day){
									echo date_i18n(get_option('time_format'), $event->start) . " - " . date_i18n(get_option('time_format'), $event->end);
								}else{
									echo get_option('dbem_event_all_day_message');
								}
							?>
						</td>
						<td>
							
						</td>
					</tr>
					<?php
				}
				$document_count++;
			}
			?>
		</tbody>
	</table>  
	<?php
	} // end of table
	?>
	<div class='tablenav'>
		<div class="alignleft actions">
		<br class='clear' />
		</div>
		<?php if ( $events_count >= $limit ) : ?>
		<div class="tablenav-pages">
			<?php
			echo $events_nav;
			?>
		</div>
		<?php endif; ?>
		<br class='clear' />
	</div>