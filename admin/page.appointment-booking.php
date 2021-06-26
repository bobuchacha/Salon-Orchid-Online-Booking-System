<?php
$cssFolder =  plugin_dir_url(__DIR__ . '../') . 'public/css';
$jsFolder =  plugin_dir_url(__DIR__ . '../') . 'public/js';

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<div id="page" class="site">
	<div class="site-content-contain">
		<div id="content" class="site-content">
			<div class="wrap">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
				<?php
				while ( have_posts() ) : the_post();
					?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<!--header class="entry-header">
								<?php
									global $post;
//
//									// print the title of the page
									echo '<h1>' . __( 'ONLINE BOOKING', 'smappointmentbooker' ) . '</h1>';
									echo '<h1 class="page-title">' . get_the_title($post->post_parent) . '</h1>';

								?>
								<div class="page-description" style="position:relative;">
									<div style="position:absolute">
										<i class="fas fa-globe-americas fa-2x"></i>
									</div>

									<div class="address" style="">
										<p>
											<?php
											global $post;

											$address_line_1 =  get_post_meta( $post->post_parent, 'salon_address_1', true );
											if (!$address_line_1) $address_line_1 = get_post_meta( $post->ID, 'salon_address_1', true );
											$address_line_2 =  get_post_meta( $post->post_parent, 'salon_address_2', true );
											if (!$address_line_2) $address_line_2 = get_post_meta( $post->ID, 'salon_address_2', true );
											$phone =  get_post_meta( $post->post_parent, 'salon_phone' , true);
											if (!$phone) $phone = get_post_meta( $post->ID, 'salon_phone' , true);

											echo "{$address_line_1}<br/>$address_line_2<br/>$phone";
											?>
										</p>
									</div>
								</div>
							</header--><!-- .entry-header -->

							<div class="entry-content">
								<?php
								the_content();

								?>
							</div><!-- .entry-content -->
						</article><!-- #post-## -->

					<?php
					// If comments are open or we have at least one comment, load up the comment template.
//					if ( comments_open() || get_comments_number() ) :
//						comments_template();
//					endif;

				endwhile; // End of the loop.
				?>




				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .wrap -->

<?php //get_footer();
