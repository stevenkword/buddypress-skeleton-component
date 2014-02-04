<?php get_header() ?>
	<div id="content">

		<div id="section-title"><?php bp_displayed_user_fullname(); ?>'s Presentations</div>

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

		<header class="entry-header">

			<div class="entry-byline">
				<time <?php hybrid_attr( 'entry-published' ); ?>><?php echo get_the_date(); ?></time>
				<?php comments_popup_link( number_format_i18n( 0 ), number_format_i18n( 1 ), '%', 'comments-link', '' ); ?>
				<?php edit_post_link(); ?>
			</div><!-- .entry-byline -->

		</header><!-- .entry-header -->

		<div <?php hybrid_attr( 'entry-summary' ); ?>>
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->




							</div>
						</li>
						<?php
					}
					echo '</ul>';
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