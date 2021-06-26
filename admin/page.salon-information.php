
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>

    <header id="masthead" class="site-header" role="banner">

        <div class="custom-header">
            <?php if ( ( is_single() || ( is_page() && ! twentyseventeen_is_frontpage() ) ) && has_post_thumbnail( get_queried_object_id() ) ) : ?>
            <div class="custom-header-media">
                <div id="wp-custom-header" class="wp-custom-header">
                    <?php

                        echo '<div class="single-featured-image-header">';
                        echo get_the_post_thumbnail( get_queried_object_id(), 'twentyseventeen-featured-image' );
                        echo '</div><!-- .single-featured-image-header -->';

                        ?>
                </div>
            </div>
            <?php else:
                get_custom_header();
            endif;
            ?>

            <div class="site-branding">
                <div class="wrap">
                    <div class="site-branding-text">
                        <h1 class="site-title"><?php the_title() ?></h1>
                        <p class="site-subtitle"><?php echo get_post_meta( get_the_ID(), 'salon_slogan' )[0]; ?></p>
                        <hr size="50%">
                        <div class="site-description" style="position:relative;">
                            <div style="position:absolute">
                                <i class="fas fa-globe-americas fa-2x"></i>
                            </div>
                            <div class="address" style="margin-left:3rem;">
                                <p>
                                    <?php echo get_post_meta( get_the_ID(), 'salon_address_1' )[0]; ?><br/>
                                    <?php echo get_post_meta( get_the_ID(), 'salon_address_2' )[0]; ?><br>
                                    <?php echo get_post_meta( get_the_ID(), 'salon_address_3' )[0]; ?>
                                </p>
                            </div>

                        </div>
                    </div><!-- .site-branding-text -->

                    <?php //if ( ( twentyseventeen_is_frontpage() || ( is_home() && is_front_page() ) ) && ! has_nav_menu( 'top' ) ) : ?>
                        <a href="#content" class="menu-scroll-down"><?php echo twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ); ?><span class="screen-reader-text"><?php _e( 'Scroll down to content', 'twentyseventeen' ); ?></span></a>
                    <?php //endif; ?>

                </div><!-- .wrap -->
            </div><!-- .site-branding -->

        </div><!-- .custom-header -->

        <?php if ( has_nav_menu( 'top' ) ) : ?>
            <div class="navigation-top">
                <div class="wrap">
                    <?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
                </div><!-- .wrap -->
            </div><!-- .navigation-top -->
        <?php endif; ?>

    </header><!-- #masthead -->

    <div class="site-content-contain">
        <div id="content" class="site-content">

                <div class="wrap">
                    <div id="primary" class="content-area">
                        <main id="main" class="site-main" role="main" style="padding-top:100px">
                        <?php
                        while ( have_posts() ) : the_post();
                            ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                                    <div class="entry-content">
                                        <?php
                                        the_content();

                                        wp_link_pages( array(
                                            'before' => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
                                            'after'  => '</div>',
                                        ) );
                                        ?>
                                    </div><!-- .entry-content -->
                                </article><!-- #post-## -->

                            <?php
                            // If comments are open or we have at least one comment, load up the comment template.
                            if ( comments_open() || get_comments_number() ) :
                                comments_template();
                            endif;

                        endwhile; // End of the loop.
                        ?>




                        </main><!-- #main -->
                    </div><!-- #primary -->
                </div><!-- .wrap -->

        </div><!-- #content -->

        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="wrap">
                <?php
                get_template_part( 'template-parts/footer/footer', 'widgets' );

                if ( has_nav_menu( 'social' ) ) : ?>
                    <nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'twentyseventeen' ); ?>">
                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'social',
                            'menu_class'     => 'social-links-menu',
                            'depth'          => 1,
                            'link_before'    => '<span class="screen-reader-text">',
                            'link_after'     => '</span>' . twentyseventeen_get_svg( array( 'icon' => 'chain' ) ),
                        ) );
                        ?>
                    </nav><!-- .social-navigation -->
                <?php endif;

                get_template_part( 'template-parts/footer/site', 'info' );
                ?>
            </div><!-- .wrap -->
        </footer><!-- #colophon -->
    </div><!-- .site-content-contain -->
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>

