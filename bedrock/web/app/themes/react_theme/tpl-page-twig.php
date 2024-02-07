<?php

/**
 * Template Name: Page twig
 * Template for twig page
 * 
 * @package bootstrap-basic
 */

get_header(); 

?>
<div class="container-fluid page_top">
    <div id="twig_page" class="row">
        <?php 
            $query_args = array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            );
            $context = $customTimberInstance->timber_option($query_args, 'twig/templates-twig/pagetwig.twig');
        ?>
    </div>
</div>
<?php
get_footer();
?>