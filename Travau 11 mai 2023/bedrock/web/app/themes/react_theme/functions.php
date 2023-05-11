<?php
/*
* Generated By Orbisius Child Theme Creator - your favorite plugin for Child Theme creation and editing :)
* https://orbisius.com/products/wordpress-plugins/orbisius-child-theme-creator
*
* Unlike style.css, the functions.php of a child theme does not override its counterpart from the parent.
* Instead, it is loaded in addition to the parent’s functions.php. (Specifically, it is loaded right before the parent theme's functions.php).
* Source: https://codex.wordpress.org/Child_Themes#Using_functions.php
*
* When copying functions from the parent theme make sure you use function_exists( 'put_the_function_name_here' ) calls.
* Otherwise having functions with the same name will crash the site.
* Also when adding new functions do put prefix before the function names to ensure uniqueness.
*/

/**
 * Loads parent theme's style first and then child theme's style.css so you can override parent styles
 */
function orbisius_ct_react_theme_child_theme_enqueue_styles() {
	global $wp_styles;

    $parent_style = 'orbisius_ct_react_theme_parent_style';
    $parent_base_dir = 'bootstrap-basic4';
	$child_dir_id = basename(get_stylesheet_directory());

	// WP enqueues the child style automatically. We want to dequeue it so we can load parent first and child theme's css later
	// We'll also append the last modified times for versions and for better cache clean up.
	if ( ! empty( $wp_styles->queue ) ) {
		$srch_arr = [
			$parent_base_dir,
			$parent_base_dir . '-style',
			$parent_base_dir . '_style',
			$child_dir_id,
			$child_dir_id . '-style',
			$child_dir_id . '_style',
		];

		foreach ( $wp_styles->queue as $registered_style_id ) {
			if ( in_array( $registered_style_id, $srch_arr ) ) {
				wp_dequeue_style( $registered_style_id );
				wp_deregister_style( $registered_style_id );
			}
		}
	}

    // We use last modified as version as it's a reliable way to tell when the file was modified so
    // the browser can load the new file if necessary and not use the cached version.
    $parent_ver = time();
    $parent_style_css_file = get_template_directory() . '/style.css';

    if (file_exists($parent_style_css_file)) {
    	$parent_ver = filemtime($parent_style_css_file);
    } else {
    	$v = wp_get_theme( $parent_base_dir )->get('Version');

    	if (!empty($v)) {
		    $parent_ver = $v;
	    }
    }

    wp_enqueue_style( $parent_style,
        get_template_directory_uri() . '/style.css',
        array(),
	    $parent_ver
    );

    wp_enqueue_style( $parent_style . '_child_style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}

add_action( 'wp_enqueue_scripts', 'orbisius_ct_react_theme_child_theme_enqueue_styles', 25 );

function orbisius_ct_react_theme_example_function() {
    // put some code here
}