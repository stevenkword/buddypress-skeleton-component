<?php get_header() ?>
	<div id="content">
		<div id="section-buttons">

			<?php
			if ( function_exists( 'bp_follow_add_follow_button' ) ) :
				if ( bp_loggedin_user_id() && bp_loggedin_user_id() != bp_displayed_user_id() ) {
					echo '<button>';
					bp_follow_add_follow_button( array(
						'leader_id'   => bp_displayed_user_id(),
						'follower_id' => bp_loggedin_user_id()
					) );
					echo '</button>';
				} else {

					$link = add_query_arg( 'post_type', 'presentations', home_url( '/wp-admin/post-new.php' ) );

					echo '<button><a id="wpp-button-new" href="'. esc_url( $link ) .'""><span class="wpp-button-icon">New Presentation</span></a></button>';
				}
			endif;
			?>


		</div>
		<div id="section-title"><?php bp_displayed_user_fullname(); ?>'s Presentations</div>

		<div class="padder">

			<!--
			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>
		-->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php //bp_get_options_nav() ?>
					</ul>
				</div>
				<!---
				<header class="entry-header">
					<h2 <?php hybrid_attr( 'entry-title' ); ?>><?php bp_displayed_user_fullname(); ?>'s' Presentations</h2>
				</header><!-- .entry-header -->
				<?php
				// The Query
				$the_query = new WP_Query( array(
					'post_type' => 'presentations',
				 ) );

				// The Loop
				if ( $the_query->have_posts() ) {
					$i = 0;
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						if( 0 == $i % 2 ) {
							echo '<div class="wpp-row">';
						} else {
							echo '<div class="wpp-row alt">';
						}

						// The content
						get_template_part( 'content', 'presentations-list' );

						echo '</div>';
						$i++;
					}
					unset( $i );
				} else {
					// no posts found
					echo '<p>One is the lonlinest number. Let us help you get stared!</p>';
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>