<div id="listings-jobs-job-dashboard">
	<p><?php _e( 'Your listings are shown in the table below.', 'listings-jobs' ); ?></p>
	<table class="listings-jobs-jobs">
		<thead>
			<tr>
				<?php foreach ( $restaurant_dashboard_columns as $key => $column ) : ?>
					<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $jobs ) : ?>
				<tr>
					<td colspan="6"><?php _e( 'You do not have any active listings.', 'listings-jobs' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $jobs as $job ) : ?>
					<tr>
						<?php foreach ( $restaurant_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ('restaurant_title' === $key ) : ?>
									<?php if ( $job->post_status == 'publish' ) : ?>
										<a href="<?php echo get_permalink( $job->ID ); ?>"><?php echo $job->post_title; ?></a>
									<?php else : ?>
										<?php echo $job->post_title; ?> <small>(<?php listings_restaurants_restaurant_status( $job ); ?>)</small>
									<?php endif; ?>
									<ul class="job-dashboard-actions">
										<?php
											$actions = array();

											switch ( $job->post_status ) {
												case 'publish' :
													$actions['edit'] = array( 'label' => __( 'Edit', 'listings-jobs' ), 'nonce' => false );

													if ( listings_restaurants_is_position_filled( $job ) ) {
														$actions['mark_not_filled'] = array( 'label' => __( 'Mark not filled', 'listings-jobs' ), 'nonce' => true );
													} else {
														$actions['mark_filled'] = array( 'label' => __( 'Mark filled', 'listings-jobs' ), 'nonce' => true );
													}

													$actions['duplicate'] = array( 'label' => __( 'Duplicate', 'listings-jobs' ), 'nonce' => true );
													break;
												case 'expired' :
													if ( listings_get_permalink( 'submit_restaurant_form' ) ) {
														$actions['relist'] = array( 'label' => __( 'Relist', 'listings-jobs' ), 'nonce' => true );
													}
													break;
												case 'pending_payment' :
												case 'pending' :
													if ( listings_user_can_edit_pending_submissions() ) {
														$actions['edit'] = array( 'label' => __( 'Edit', 'listings-jobs' ), 'nonce' => false );
													}
												break;
											}

											$actions['delete'] = array( 'label' => __( 'Delete', 'listings-jobs' ), 'nonce' => true );
											$actions           = apply_filters( 'listings_restaurants_my_restaurant_actions', $actions, $job );

											foreach ( $actions as $action => $value ) {
												$action_url = add_query_arg( array( 'action' => $action, 'restaurant_id' => $job->ID ) );
												if ( $value['nonce'] ) {
													$action_url = wp_nonce_url( $action_url, 'listings_restaurants_my_restaurant_actions' );
												}
												echo '<li><a href="' . esc_url( $action_url ) . '" class="job-dashboard-action-' . esc_attr( $action ) . '">' . esc_html( $value['label'] ) . '</a></li>';
											}
										?>
									</ul>
								<?php elseif ('date' === $key ) : ?>
									<?php echo date_i18n( get_option( 'date_format' ), strtotime( $job->post_date ) ); ?>
								<?php elseif ('expires' === $key ) : ?>
									<?php echo $job->_restaurant_expires ? date_i18n( get_option( 'date_format' ), strtotime( $job->_restaurant_expires ) ) : '&ndash;'; ?>
								<?php elseif ('filled' === $key ) : ?>
									<?php echo listings_restaurants_is_position_filled( $job ) ? '&#10004;' : '&ndash;'; ?>
								<?php else : ?>
									<?php do_action( 'listings_restaurants_restaurant_dashboard_column_' . $key, $job ); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php listings_get_template( 'restaurant-pagination.php', array( 'max_num_pages' => $max_num_pages ) ); ?>
</div>
