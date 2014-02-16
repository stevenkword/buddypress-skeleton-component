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
				} elseif( bp_loggedin_user_id() && bp_loggedin_user_id() == bp_displayed_user_id() ) {

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
					'author'    => bp_displayed_user_id(),
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
				} elseif ( bp_loggedin_user_id() && bp_loggedin_user_id() == bp_displayed_user_id() ) {
					// no posts found
					$link = add_query_arg( 'post_type', 'presentations', home_url( '/wp-admin/post-new.php' ) );
					?>
					<br/>
					<h3>It's looks like you're new here.</h3>
					<p>Here are some links to help you get started:</p>
					<ul>
						<li><a href="<?php echo esc_url( $link ); ?>">Create a new presentation</a></li>
						<li><a href="<?php echo esc_url( network_home_url( 'presenters' ) ); ?>">Explore the presenters directory</a></li>
					</ul>
					<?php
				} else {
					?>
					<br/>
					<h3>It's looks like <?php bp_displayed_user_fullname(); ?> is new here.</h3>
					<?php
				}

				/* Restore original Post Data */
				wp_reset_postdata();
				?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>