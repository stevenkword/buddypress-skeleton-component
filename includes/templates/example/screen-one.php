<?php get_header() ?>
	<div id="content">
		<div style="float:right; display: inline-block; width:auto;"><button>New</button></div>
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

					while ( $the_query->have_posts() ) {

						$the_query->the_post();
						?>

						<?php hybrid_get_content_template(); // Loads the content/*.php template. ?>

						<?php
					}

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