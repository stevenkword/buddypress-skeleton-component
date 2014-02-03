<?php get_header() ?>
	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

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
					echo '<ul>';
					while ( $the_query->have_posts() ) {

						$the_query->the_post();
						?>
						<li>
							<div>
								<!-- Display the Title as a link to the Post's permalink. -->
								<h3>
									<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
								</h3>
								<p>Last Update 7 Mins ago</p>
							</div>
						</li>
						<?php
					}
					echo '</ul>';
				} else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>