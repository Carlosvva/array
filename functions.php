<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package WordPress
 * @subpackage MOVEME
 * @since MOVEME 1.0
 */

if (!defined("MOVEME_THEME_DIR")) define("MOVEME_THEME_DIR", trailingslashit( get_template_directory() ));
if (!defined("MOVEME_CHILD_DIR")) define("MOVEME_CHILD_DIR", trailingslashit( get_stylesheet_directory() ));

// Theme storage
$MOVEME_STORAGE = array(
	// Theme required plugin's slugs
	'required_plugins' => array(

		// Required plugins
		// DON'T COMMENT OR REMOVE NEXT LINES!
		'trx_addons',

		// Recommended (supported) plugins
		// If plugin not need - comment (or remove) it
		'contact-form-7',
		'essential-grid',
		'js_composer',
		'revslider',
		'vc-extensions-bundle',
		'woocommerce'
		)
);


//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

// Theme init priorities:
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
add_filter( 'wpsl_listing_template', 'custom_listing_template' );

function custom_listing_template() {
	$terms = wp_get_post_terms( $queried_object->ID, 'wpsl_store_category', '' );

if ( $terms && !is_wp_error( $terms ) ) {
    if ( count( $terms ) > 1 ) {
        $location_terms = array();

        foreach ( $terms as $term) {
            $location_terms[] = '<a href="' . get_term_link( $term->term_id, 'wpsl_store_category' ) . '">' . esc_html( $term->name ) . '</a>';
        }

        $term_list = implode( ', ', $location_terms );
    } else {
        $term_list = '<a href="' . get_term_link( $terms[0]->term_id, 'wpsl_store_category' ) . '">' . esc_html( $terms[0]->name ) . '</a>';
    }

    echo __( 'Categories:', 'wpsl' ) . ' ' .  $term_list;
}

    global $wpsl, $wpsl_settings;

    // phone
    $listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
    $listing_template .= "\t\t" . '<div>' . "\r\n";
    $listing_template .= "\t\t\t" . '<p><%= thumb %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . wpsl_store_header_template( 'listing' ) . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
    $listing_template .= "\t\t\t" . '</p>' . "\r\n";
   
    
    $listing_template .= "\t\t" . '</div>' . "\r\n";

    // Check if we need to show the distance.
    if ( !$wpsl_settings['hide_distance'] ) {
        $listing_template .= "\t\t" . '<%= distance %> ' . esc_html( $wpsl_settings['distance_unit'] ) . '' . "\r\n";
    }
 
    $listing_template .= "\t\t" . '<%= createDirectionUrl() %>' . "\r\n"; 
    $listing_template .= "\t" . '</li>' . "\r\n";
    // end phone
    
    $listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
    $listing_template .= "\t\t" . '<div class="wpsl-store-location">' . "\r\n";
    $listing_template .= "\t\t\t" . '<p><%= thumb %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . wpsl_store_header_template( 'listing' ) . "\r\n"; // Check which header format we use
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
    $listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; // Use the correct address format

    if ( !$wpsl_settings['hide_country'] ) {
        $listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
    }

    $listing_template .= "\t\t\t" . '</p>' . "\r\n";
    
     // Include the category names.
    $listing_template .= "\t\t\t" . '<% if ( terms ) { %>' . "\r\n";
    $listing_template .= "\t\t\t" . '<p>' . __( 'Categoría:', 'wpsl' ) . ' <%= terms %></p>' . "\r\n";
    $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";

    // Show the phone, fax or email data if they exist.
    if ( $wpsl_settings['show_contact_details'] ) {
        $listing_template .= "\t\t\t" . '<p class="wpsl-contact-details">' . "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'phone_label', __( 'Phone', 'wpsl' ) ) ) . '</strong>: <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( email ) { %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'email_label', __( 'Email', 'wpsl' ) ) ) . '</strong>: <%= email %></span>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . '</p>' . "\r\n";
    }

    $listing_template .= "\t\t\t" . wpsl_more_info_template() . "\r\n"; // Check if we need to show the 'More Info' link and info
    $listing_template .= "\t\t" . '</div>' . "\r\n";
    $listing_template .= "\t\t" . '<div class="wpsl-direction-wrap">' . "\r\n";

    if ( !$wpsl_settings['hide_distance'] ) {
        $listing_template .= "\t\t\t" . '<%= distance %> ' . esc_html( $wpsl_settings['distance_unit'] ) . '' . "\r\n";
    }

    $listing_template .= "\t\t\t" . '<%= createDirectionUrl() %>' . "\r\n"; 
    $listing_template .= "\t\t" . '</div>' . "\r\n";
    $listing_template .= "\t" . '</li>';

    return $listing_template;
}
add_filter( 'wpsl_info_window_template', 'custom_info_window_template' );

function custom_info_window_template() {
   
    $info_window_template = '<div data-store-id="<%= id %>" class="wpsl-info-window">' . "\r\n";
    $info_window_template .= "\t\t" . '<p>' . "\r\n";
    $info_window_template .= "\t\t\t" .  wpsl_store_header_template() . "\r\n";  
    $info_window_template .= "\t\t\t" . '<span><%= address %></span>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<span><%= address2 %></span>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $info_window_template .= "\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n";
    $info_window_template .= "\t\t" . '</p>' . "\r\n";
    
    // Include the category names.
    $info_window_template .= "\t\t" . '<% if ( terms ) { %>' . "\r\n";
    $info_window_template .= "\t\t" . '<p>' . __( 'Categoría:', 'wpsl' ) . ' <%= terms %></p>' . "\r\n";
    $info_window_template .= "\t\t" . '<% } %>' . "\r\n";
    
    $info_window_template .= "\t\t" . '<%= createInfoWindowActions( id ) %>' . "\r\n";
    $info_window_template .= "\t" . '</div>' . "\r\n";
    
    return $info_window_template;
}

add_filter( 'wpsl_store_meta', 'custom_store_meta', 10, 2 );

function custom_store_meta( $store_meta, $store_id ) {

    if ( function_exists( 'z_taxonomy_image_url' ) ) {
        $terms = wp_get_post_terms( $store_id, 'wpsl_store_category' );
        $store_meta['terms'] = '';
    
    if ( $terms ) {
        if ( !is_wp_error( $terms ) ) {
            if ( count( $terms ) > 1 ) {
                $location_terms = array();

                foreach ( $terms as $term ) {
                    $location_terms[] = $term->name;
                }

                $store_meta['terms'] = implode( ', ', $location_terms );
            } else {
                $store_meta['terms'] = $terms[0]->name;    
            }
        }
    }
        if ( $terms ) {
            if ( !is_wp_error( $terms ) ) {
                if ( isset( $_GET['filter'] ) && $_GET['filter'] ) {
                    $filter_ids = explode( ',', $_GET['filter'] );

                    foreach ( $terms as $term ) {
                        if ( in_array( $term->term_id, $filter_ids ) ) {
                            $cat_marker = z_taxonomy_image_url( $term->term_id );

                            if ( $cat_marker ) {
                                $store_meta['categoryMarkerUrl'] = $cat_marker;
                            }
                        }
                    }
                } else {
                    $store_meta['categoryMarkerUrl'] = z_taxonomy_image_url( $terms[0]->term_id );
                }
            }
        }
    }

    return $store_meta;
}


add_filter( 'wpsl_cpt_info_window_meta_fields', 'custom_cpt_info_window_meta_fields', 10, 2 );

function custom_cpt_info_window_meta_fields( $meta_fields, $store_id ) {

    $terms = wp_get_post_terms( $store_id, 'wpsl_store_category' );

    if ( $terms ) {
        if ( !is_wp_error( $terms ) ) {
            if ( function_exists( 'z_taxonomy_image_url' ) ) {
                $meta_fields['categoryMarkerUrl'] = z_taxonomy_image_url( $terms[0]->term_id );
            }
        }
    }

    return $meta_fields;
}
if ( !function_exists('moveme_theme_setup1') ) {
	add_action( 'after_setup_theme', 'moveme_theme_setup1', 1 );
	function moveme_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		// Attention! Translations must be loaded before first call any translation functions!
		load_theme_textdomain( 'moveme', get_template_directory() . '/languages' );

		// Set theme content width
		$GLOBALS['content_width'] = apply_filters( 'moveme_filter_content_width', 1170 );
	}
}

if ( !function_exists('moveme_theme_setup') ) {
	add_action( 'after_setup_theme', 'moveme_theme_setup' );
	function moveme_theme_setup() {

		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		
		// Custom header setup
		add_theme_support( 'custom-header', array(
			'header-text'=>false,
			'video' => true
			)
		);

		// Custom backgrounds setup
		add_theme_support( 'custom-background', array()	);
		
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') ); 
 
 		// Autogenerate title tag
		add_theme_support('title-tag');
 		
		// Add theme menus
		add_theme_support('nav-menus');
		
		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption') );
		
		// Editor custom stylesheet - for user
		add_editor_style( array_merge(
			array(
				'css/editor-style.css',
				moveme_get_file_url('css/fontello/css/fontello-embedded.css')
			),
			moveme_theme_fonts_for_editor()
			)
		);	
	
		// Register navigation menu
		register_nav_menus(array(
			'menu_main' => esc_html__('Main Menu', 'moveme'),
			'menu_mobile' => esc_html__('Mobile Menu', 'moveme'),
			'menu_footer' => esc_html__('Footer Menu', 'moveme')
			)
		);

		// Excerpt filters
		add_filter( 'excerpt_length',						'moveme_excerpt_length' );
		add_filter( 'excerpt_more',							'moveme_excerpt_more' );

        add_filter( 'wpcf7_support_html5_fallback', '__return_true' );

		// Add required meta tags in the head
		add_action('wp_head',		 						'moveme_wp_head', 0);
		
		// Load current page/post customization (if present)
		add_action('wp_footer',		 						'moveme_wp_footer');
		add_action('admin_footer',	 						'moveme_wp_footer');

		// Enqueue scripts and styles for frontend
		add_action('wp_enqueue_scripts', 					'moveme_wp_scripts', 1000);			// priority 1000 - load styles
																									// before the plugin's support custom styles
																									// (with priority 1100)
		add_action('wp_enqueue_scripts', 					'moveme_wp_scripts_child', 1200);		// priority 1200 - load styles
																									// after the plugin's support custom styles
																									// (with priority 1100)
		add_action('wp_enqueue_scripts', 					'moveme_wp_scripts_responsive', 2000);	// priority 2000 - load responsive
																									// after all other styles
		add_action('wp_footer',		 						'moveme_localize_scripts');
		
		// Add body classes
		add_filter( 'body_class',							'moveme_add_body_classes' );

		// Register sidebars
		add_action('widgets_init',							'moveme_register_sidebars');

		// Set options for importer (before other plugins)
		add_filter( 'trx_addons_filter_importer_options',	'moveme_importer_set_options', 9 );
	}

}


//-------------------------------------------------------
//-- Theme scripts and styles
//-------------------------------------------------------

// Load frontend scripts
if ( !function_exists( 'moveme_wp_scripts' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'moveme_wp_scripts', 1000);
	function moveme_wp_scripts() {
		
		// Enqueue styles
		//------------------------
		
		// Links to selected fonts
		$links = moveme_theme_fonts_links();
		if (count($links) > 0) {
			foreach ($links as $slug => $link) {
				wp_enqueue_style( sprintf('moveme-font-%s', $slug), $link );
			}
		}
		
		// Fontello styles must be loaded before main stylesheet
		// This style NEED the theme prefix, because style 'fontello' in some plugin contain different set of characters
		// and can't be used instead this style!
		wp_enqueue_style( 'moveme-fontello',  moveme_get_file_url('css/fontello/css/fontello-embedded.css') );

		// Load main stylesheet
		$main_stylesheet = get_template_directory_uri() . '/style.css';
		wp_enqueue_style( 'moveme-main', $main_stylesheet, array(), null );

		// Add custom bg image for the body_style == 'boxed'
		if ( moveme_get_theme_option('body_style') == 'boxed' && ($bg_image = moveme_get_theme_option('boxed_bg_image')) != '' )
			wp_add_inline_style( 'moveme-main', '.body_style_boxed { background-image:url('.esc_url($bg_image).') }' );

		// Merged styles
		if ( moveme_is_off(moveme_get_theme_option('debug_mode')) )
			wp_enqueue_style( 'moveme-styles', moveme_get_file_url('css/__styles.css') );

		// Custom colors
		if ( !is_customize_preview() && !isset($_GET['color_scheme']) && moveme_is_off(moveme_get_theme_option('debug_mode')) )
			wp_enqueue_style( 'moveme-colors', moveme_get_file_url('css/__colors.css') );
		else
			wp_add_inline_style( 'moveme-main', moveme_customizer_get_css() );

		// Add post nav background
		moveme_add_bg_in_post_nav();

		// Disable loading JQuery UI CSS
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//------------------------
		
		// Modernizr will load in head before other scripts and styles
		if ( in_array(substr(moveme_get_theme_option('blog_style'), 0, 7), array('gallery', 'portfol', 'masonry')) )
			wp_enqueue_script( 'modernizr', moveme_get_file_url('js/theme.gallery/modernizr.min.js'), array(), null, false );

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', moveme_get_file_url('js/superfish.js'), array('jquery'), null, true );
		
		// Merged scripts
		if ( moveme_is_off(moveme_get_theme_option('debug_mode')) )
			wp_enqueue_script( 'moveme-init', moveme_get_file_url('js/__scripts.js'), array('jquery'), null, true );
		else {
			// Skip link focus
			wp_enqueue_script( 'skip-link-focus-fix', moveme_get_file_url('js/skip-link-focus-fix.js'), null, true );
			// Background video
			$header_video = moveme_get_header_video();
			if (!empty($header_video) && !moveme_is_inherit($header_video)) {
				if (moveme_is_youtube_url($header_video))
					wp_enqueue_script( 'tubular', moveme_get_file_url('js/jquery.tubular.js'), array('jquery'), null, true );
				else
					wp_enqueue_script( 'bideo', moveme_get_file_url('js/bideo.js'), array(), null, true );
			}
			// Theme scripts
			wp_enqueue_script( 'moveme-utils', moveme_get_file_url('js/_utils.js'), array('jquery'), null, true );
			wp_enqueue_script( 'moveme-init', moveme_get_file_url('js/_init.js'), array('jquery'), null, true );	
		}
		
		// Comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library	
		if (moveme_get_theme_setting('use_mediaelements')) {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}

// Load child-theme stylesheet (if different) after all styles (with priorities 1000 and 1100)
if ( !function_exists( 'moveme_wp_scripts_child' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'moveme_wp_scripts_child', 1200);
	function moveme_wp_scripts_child() {
		$main_stylesheet = get_template_directory_uri() . '/style.css';
		$child_stylesheet = get_stylesheet_directory_uri() . '/style.css';
		if ($child_stylesheet != $main_stylesheet) {
			wp_enqueue_style( 'moveme-child', $child_stylesheet, array('moveme-main'), null );
		}
	}
}

// Add variables to the scripts in the frontend
if ( !function_exists( 'moveme_localize_scripts' ) ) {
	//Handler of the add_action('wp_footer', 'moveme_localize_scripts');
	function moveme_localize_scripts() {

		$video = moveme_get_header_video();

		wp_localize_script( 'moveme-init', 'MOVEME_STORAGE', apply_filters( 'moveme_filter_localize_script', array(
			// AJAX parameters
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'ajax_nonce' => esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))),
			
			// Site base url
			'site_url' => get_site_url(),
			'theme_url' => get_template_directory_uri(),
						
			// Site color scheme
			'site_scheme' => sprintf('scheme_%s', moveme_get_theme_option('color_scheme')),
			
			// User logged in
			'user_logged_in' => is_user_logged_in() ? true : false,
			
			// Window width to switch the site header to the mobile layout
			'mobile_layout_width' => 767,
			'mobile_device' => wp_is_mobile(),
						
			// Sidemenu options
			'menu_side_stretch' => moveme_get_theme_option('menu_side_stretch') > 0 ? true : false,
			'menu_side_icons' => moveme_get_theme_option('menu_side_icons') > 0 ? true : false,

			// Video background
			'background_video' => moveme_is_from_uploads($video) ? $video : '',

			// Video and Audio tag wrapper
			'use_mediaelements' => moveme_get_theme_setting('use_mediaelements') ? true : false,

			// Messages max length
			'comment_maxlength'	=> intval(moveme_get_theme_setting('comment_maxlength')),

			
			// Internal vars - do not change it!
			
			// Flag for review mechanism
			'admin_mode' => false,

			// E-mail mask
			'email_mask' => '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$',
			
			// Strings for translation
			'strings' => array(
					'ajax_error'		=> esc_html__('Invalid server answer!', 'moveme'),
					'error_global'		=> esc_html__('Error data validation!', 'moveme'),
					'name_empty' 		=> esc_html__("The name can't be empty", 'moveme'),
					'name_long'			=> esc_html__('Too long name', 'moveme'),
					'email_empty'		=> esc_html__('Too short (or empty) email address', 'moveme'),
					'email_long'		=> esc_html__('Too long email address', 'moveme'),
					'email_not_valid'	=> esc_html__('Invalid email address', 'moveme'),
					'text_empty'		=> esc_html__("The message text can't be empty", 'moveme'),
					'text_long'			=> esc_html__('Too long message text', 'moveme')
					)
			))
		);
	}
}

// Load responsive styles (priority 2000 - load it after main styles and plugins custom styles)
if ( !function_exists( 'moveme_wp_scripts_responsive' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'moveme_wp_scripts_responsive', 2000);
	function moveme_wp_scripts_responsive() {
		wp_enqueue_style( 'moveme-responsive', moveme_get_file_url('css/responsive.css') );
	}
}

//  Add meta tags and inline scripts in the header for frontend
if (!function_exists('moveme_wp_head')) {
	//Handler of the add_action('wp_head',	'moveme_wp_head', 1);
	function moveme_wp_head() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php
	}
}

// Add theme specified classes to the body
if ( !function_exists('moveme_add_body_classes') ) {
	//Handler of the add_filter( 'body_class', 'moveme_add_body_classes' );
	function moveme_add_body_classes( $classes ) {
		$classes[] = 'body_tag';	// Need for the .scheme_self
		$classes[] = 'scheme_' . esc_attr(moveme_get_theme_option('color_scheme'));

		$blog_mode = moveme_storage_get('blog_mode');
		$classes[] = 'blog_mode_' . esc_attr($blog_mode);
		$classes[] = 'body_style_' . esc_attr(moveme_get_theme_option('body_style'));

		if (in_array($blog_mode, array('post', 'page'))) {
			$classes[] = 'is_single';
		} else {
			$classes[] = ' is_stream';
			$classes[] = 'blog_style_'.esc_attr(moveme_get_theme_option('blog_style'));
			if (moveme_storage_get('blog_template') > 0)
				$classes[] = 'blog_template';
		}
		
		if (moveme_sidebar_present()) {
			$classes[] = 'sidebar_show sidebar_' . esc_attr(moveme_get_theme_option('sidebar_position')) ;
		} else {
			$classes[] = 'sidebar_hide';
			if (moveme_is_on(moveme_get_theme_option('expand_content')))
				 $classes[] = 'expand_content';
		}
		
		if (moveme_is_on(moveme_get_theme_option('remove_margins')))
			 $classes[] = 'remove_margins';

		$classes[] = 'header_style_' . esc_attr(moveme_get_theme_option("header_style"));
		$classes[] = 'header_position_' . esc_attr(moveme_get_theme_option("header_position"));

		$menu_style= moveme_get_theme_option("menu_style");
		$classes[] = 'menu_style_' . esc_attr($menu_style) . (in_array($menu_style, array('left', 'right'))	? ' menu_style_side' : '');
		$classes[] = 'no_layout';
		
		return $classes;
	}
}
	
// Load current page/post customization (if present)
if ( !function_exists( 'moveme_wp_footer' ) ) {
	//Handler of the add_action('wp_footer', 'moveme_wp_footer');
	//and add_action('admin_footer', 'moveme_wp_footer');
	function moveme_wp_footer() {
		if (($css = moveme_get_inline_css()) != '') {
			wp_enqueue_style(  'moveme-inline-styles',  moveme_get_file_url('css/__inline.css') );
			wp_add_inline_style( 'moveme-inline-styles', $css );
		}
	}
}


//-------------------------------------------------------
//-- Sidebars and widgets
//-------------------------------------------------------

// Register widgetized areas
if ( !function_exists('moveme_register_sidebars') ) {
	// Handler of the add_action('widgets_init', 'moveme_register_sidebars');
	function moveme_register_sidebars() {
		$sidebars = moveme_get_sidebars();
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$sb) {
				register_sidebar( array(
										'name'          => esc_html($sb['name']),
										'description'   => esc_html($sb['description']),
										'id'            => esc_attr($id),
										'before_widget' => '<aside id="%1$s" class="widget %2$s">',
										'after_widget'  => '</aside>',
										'before_title'  => '<h5 class="widget_title">',
										'after_title'   => '</h5>'
										)
								);
			}
		}
	}
}

// Return theme specific widgetized areas
if ( !function_exists('moveme_get_sidebars') ) {
	function moveme_get_sidebars() {
		$list = apply_filters('moveme_filter_list_sidebars', array(
			'sidebar_widgets'		=> array(
											'name' => esc_html__('Sidebar Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown on the main sidebar', 'moveme')
											),
			'header_widgets'		=> array(
											'name' => esc_html__('Header Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown at the top of the page (in the page header area)', 'moveme')
											),
			'above_page_widgets'	=> array(
											'name' => esc_html__('Top Page Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown below the header, but above the content and sidebar', 'moveme')
											),
			'above_content_widgets' => array(
											'name' => esc_html__('Above Content Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown above the content, near the sidebar', 'moveme')
											),
			'below_content_widgets' => array(
											'name' => esc_html__('Below Content Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown below the content, near the sidebar', 'moveme')
											),
			'below_page_widgets' 	=> array(
											'name' => esc_html__('Bottom Page Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown below the content and sidebar, but above the footer', 'moveme')
											),
			'footer_widgets'		=> array(
											'name' => esc_html__('Footer Widgets', 'moveme'),
											'description' => esc_html__('Widgets to be shown at the bottom of the page (in the page footer area)', 'moveme')
											)
			)
		);
		return $list;
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------

// Return links for all theme fonts
if ( !function_exists('moveme_theme_fonts_links') ) {
	function moveme_theme_fonts_links() {
		$links = array();
		
		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/
		$google_fonts_enabled = ( 'off' !== _x( 'on', 'Google fonts: on or off', 'moveme' ) );
		$custom_fonts_enabled = ( 'off' !== _x( 'on', 'Custom fonts (included in the theme): on or off', 'moveme' ) );
		
		if ( ($google_fonts_enabled || $custom_fonts_enabled) && !moveme_storage_empty('load_fonts') ) {
			$load_fonts = moveme_storage_get('load_fonts');
			if (count($load_fonts) > 0) {
				$google_fonts = '';
				foreach ($load_fonts as $font) {
					$slug = moveme_get_load_fonts_slug($font['name']);
					$url  = moveme_get_file_url( sprintf('css/font-face/%s/stylesheet.css', $slug));
					if ($url != '') {
						if ($custom_fonts_enabled) {
							$links[$slug] = $url;
						}
					} else {
						if ($google_fonts_enabled) {
							$google_fonts .= ($google_fonts ? '|' : '') 
											. str_replace(' ', '+', $font['name'])
											. ':' 
											. (empty($font['styles']) ? '400,400italic,700,700italic' : $font['styles']);
						}
					}
				}
				if ($google_fonts && $google_fonts_enabled) {
					$links['google_fonts'] = sprintf('%s://fonts.googleapis.com/css?family=%s&subset=%s', moveme_get_protocol(), $google_fonts, moveme_get_theme_option('load_fonts_subset'));
				}
			}
		}
		return $links;
	}
}

// Return links for WP Editor
if ( !function_exists('moveme_theme_fonts_for_editor') ) {
	function moveme_theme_fonts_for_editor() {
		$links = array_values(moveme_theme_fonts_links());
		if (is_array($links) && count($links) > 0) {
			for ($i=0; $i<count($links); $i++) {
				$links[$i] = str_replace(',', '%2C', $links[$i]);
			}
		}
		return $links;
	}
}


//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------
if ( !function_exists('moveme_excerpt_length') ) {
	function moveme_excerpt_length( $length ) {
		return max(1, moveme_get_theme_setting('max_excerpt_length'));
	}
}

if ( !function_exists('moveme_excerpt_more') ) {
	function moveme_excerpt_more( $more ) {
		return '&hellip;';
	}
}


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( !function_exists( 'moveme_importer_set_options' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_importer_options',	'moveme_importer_set_options', 9 );
	function moveme_importer_set_options($options=array()) {
		if (is_array($options)) {
			// Save or not installer's messages to the log-file
			$options['debug'] = false;
			// Prepare demo data
			$options['demo_url'] = esc_url(moveme_get_protocol() . '://moveme.ancorathemes.com/demo/');
			// Required plugins
			$options['required_plugins'] = moveme_storage_get('required_plugins');
			// Default demo
			$options['files']['default']['title'] = esc_html__('MoveMe Demo', 'moveme');
			$options['files']['default']['domain_dev'] = esc_url(moveme_get_protocol().'://moveme.dv.ancorathemes.com');		// Developers domain
			$options['files']['default']['domain_demo']= esc_url(moveme_get_protocol().'://moveme.ancorathemes.com');		// Demo-site domain
		}
		return $options;
	}
}



//-------------------------------------------------------
//-- Include theme (or child) PHP-files
//-------------------------------------------------------

require_once MOVEME_THEME_DIR . 'includes/utils.php';
require_once MOVEME_THEME_DIR . 'includes/storage.php';
require_once MOVEME_THEME_DIR . 'includes/lists.php';
require_once MOVEME_THEME_DIR . 'includes/wp.php';

if (is_admin()) {
	require_once MOVEME_THEME_DIR . 'includes/tgmpa/class-tgm-plugin-activation.php';
	require_once MOVEME_THEME_DIR . 'includes/admin.php';
}

require_once MOVEME_THEME_DIR . 'theme-options/theme.customizer.php';

require_once MOVEME_THEME_DIR . 'theme-specific/theme.tags.php';
require_once MOVEME_THEME_DIR . 'theme-specific/theme.hovers/theme.hovers.php';


// Plugins support
if (is_array($MOVEME_STORAGE['required_plugins']) && count($MOVEME_STORAGE['required_plugins']) > 0) {
	foreach ($MOVEME_STORAGE['required_plugins'] as $plugin_slug) {
		$plugin_slug = moveme_esc($plugin_slug);
		$plugin_path = MOVEME_THEME_DIR . sprintf('plugins/%s/%s.php', $plugin_slug, $plugin_slug);
		if (file_exists($plugin_path)) { require_once $plugin_path; }
	}
}
?>