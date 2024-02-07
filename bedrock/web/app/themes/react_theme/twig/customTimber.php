<?php

use Timber\Timber;
/**
 * Initialize all functions of Timber here
 */
class customTimber {

    public function __construct() {
        add_action('init', function () {
            $this->action_init();
        });
    }

    /**
     * Fires after WordPress has finished loading but before any headers are sent.
     */

    public function action_init() {
        // Use the 'timber/twig/environment/options' filter to set autoescape
        add_filter('timber/twig/environment/options', function ($options) {
            $options['autoescape'] = false;
            return $options;
        });

        add_filter('timber/twig', function (\Twig\Environment $twig) {
            $twig->addFunction(new \Twig\TwigFunction('get_breadcrumbs', function(){
                global $post;
                $breadcrumbs = '';

                // Home link
                $breadcrumbs .= '<a href="' . home_url() . '">' . __('Home', 'your-text-domain') . '</a>';

                // Separator
                $separator = '<span class="separator"> / </span>';

                // Check if it's a single post
                if (is_single()) {
                    // Get post categories
                    $categories = get_the_category();
                    if ($categories) {
                        foreach ($categories as $category) {
                            $breadcrumbs .= $separator . '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
                        }
                    }
                    // Current post title
                    $breadcrumbs .= $separator . get_the_title();
                }
                // Check if it's a page
                elseif (is_page()) {
                    // Get ancestors of the current page
                    $ancestors = get_post_ancestors($post->ID);
                    if ($ancestors) {
                        $ancestors = array_reverse($ancestors);
                        foreach ($ancestors as $ancestor) {
                            $breadcrumbs .= $separator . '<a href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a>';
                        }
                    }
                    // Current page title
                    $breadcrumbs .= $separator . get_the_title();
                }
                // Return breadcrumbs
                return $breadcrumbs;
            }));
            return $twig;
        });

    }

    //function render 
    public function timber_option($query_args = null,$location) {

        $context = Timber::context();

        if (!is_null($query_args)){
              $context['post_query'] = Timber::get_posts($query_args);
        }
        
        Timber::render($location, $context);
    }
}


$customTimberInstance = new customTimber();