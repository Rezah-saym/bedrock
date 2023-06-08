<?php
/**
 * The theme header.
 * 
 * @package bootstrap-basic4
 */

$container_class = apply_filters('bootstrap_basic4_container_class', 'container');
if (!is_scalar($container_class) || empty($container_class)) {
    $container_class = 'container';
}
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <link  rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">

        <!--WordPress head-->
        <?php wp_head(); ?> 
        <!--end WordPress head-->
    </head>
    <body <?php body_class(); ?>>
        <?php
        if (function_exists('wp_body_open')) {
            wp_body_open();
        }
        ?> 
    <header class="topbar">
    <nav class="navbar navbar-expand-lg navbar-dark mx-background-top-linear">
        <div class="container">
        <a class="navbar-brand" rel="nofollow"href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home" style="text-transform: uppercase;"><?php bloginfo('name'); ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">

                            <?php 
                                    wp_nav_menu(
                                        [
                                            'depth' => '2',
                                            'theme_location' => 'primary', 
                                            'container' => false, 
                                            'menu_id' => 'bb4-primary-menu',
                                            'menu_class' => 'navbar-nav ml-auto', 
                                            'walker' => new \BootstrapBasic4\BootstrapBasic4WalkerNavMenu()
                                        ]
                                    ); 
                            ?> 
        </div>
        </div>
    </nav>
    </header>

<div class="<?php echo $container_class; ?> page-container">
    <div id="content" class="site-content row row-with-vspace">