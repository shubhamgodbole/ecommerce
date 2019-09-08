<?php
/**
 * Set up the content width value based on the theme's design.
 *
 * @see tmpmela_content_width()
 *
 * @since TemplateMela 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1400;
}
function tmpmela_setup() {
	/*
	* Makes Templatemela available for translation.
	*
	* Translations can be added to the /languages/ directory.
	* If you're building a theme based on tm, use a find and
	* replace to change 'firezy' to the name of your theme in all
	* template files.
	*/
	load_theme_textdomain( 'firezy', get_template_directory() . '/languages' );
	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/font-awesome.css', '/fonts/css/font-awesome.css', tmpmela_fonts_url() ) );
	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );
	/*
	 * Switches default core markup for search form, comment form,
	 * and comments to output valid HTML5.
	 */
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
	/*
	 * This theme supports all available post formats by default.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',
	) );
	global $wp_version;
	if ( version_compare( $wp_version, '3.4', '>=' ) ) {
		add_theme_support( 'custom-background' ); 
	}
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		 'primary'   => esc_html__( 'TM Header Navigation', 'firezy' ),
		 'header-menu'   => esc_html__( 'TM Header Top Links', 'firezy' ),
		 'footer-menu'   => esc_html__( 'TM Footer Navigation', 'firezy' ),
	) );
	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270, true );
	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
add_action( 'after_setup_theme', 'tmpmela_setup' );
/********************************************************
**************** TEMPLATE MELA CONTENT WIDTH ******************
********************************************************/
function tmpmela_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'tmpmela_content_width', 895 );
}
add_action( 'after_setup_theme', 'tmpmela_content_width', 0 );
/**
 * Getter function for Featured Content Plugin.
 *
 * @since TemplateMela 1.0
 *
 * @return array An array of WP_Post objects.
 */
function tmpmela_get_featured_posts() {
	/**
	 * Filter the featured posts to return in TemplateMela.
	 * @param array|bool $posts Array of featured posts, otherwise false.
	 */
	return apply_filters( 'tmpmela_get_featured_posts', array() );
}
/**
 * A helper conditional function that returns a boolean value.
 * @return bool Whether there are featured posts.
 */
function tmpmela_has_featured_posts() {
	return ! is_paged() && (bool) tmpmela_get_featured_posts();
}
/********************************************************
**************** TEMPLATE MELA SIDEBAR ******************
********************************************************/
function tmpmela_widgets_init() {
	register_sidebar( array(
		'name' => esc_html__( 'Main Sidebar', 'firezy' ),
		'id' => 'sidebar-1',
		'description' => esc_html__( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'firezy' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
}
add_action( 'widgets_init', 'tmpmela_widgets_init' );
/********************************************************
**************** TEMPLATE MELA FONT SETTING ******************
********************************************************/
function tmpmela_fonts_url() {
	$fonts_url = '';
	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'firezy' );
	/* Translators: If there are characters in your language that are not
	 * supported by Bitter, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$bitter = _x( 'on', 'Bitter font: on or off', 'firezy' );
	if ( 'off' !== $source_sans_pro || 'off' !== $bitter ) {
		$font_families = array();
		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source Sans Pro:300,400,600,300italic,400italic,600italic';
		if ( 'off' !== $bitter )
			$font_families[] = 'Bitter:400,600';
		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = esc_url( add_query_arg( $query_args, "//fonts.googleapis.com/css" ));
	}
	return $fonts_url;
}
/********************************************************
************ TEMPLATE MELA SCRIPT SETTING ***************
********************************************************/
function tmpmela_scripts_styles() {
	// Add Poppins fonts, used in the main stylesheet.
	wp_enqueue_style( 'tmpmela-fonts', tmpmela_fonts_url(), array(), null );
	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/fonts/css/font-awesome.css', array(), '4.7.0' );
	// Loads our main stylesheet.
	wp_enqueue_style( 'tmpmela-style', get_stylesheet_uri(), array(), '1.0' );
	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	// Loads JavaScript file with functionality specific to Templatemela.
	wp_enqueue_script( 'tmpmela-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '2014-02-01', true );	
}
add_action( 'wp_enqueue_scripts', 'tmpmela_scripts_styles' );
/********************************************************
************ TEMPLATE MELA IMAGE ATTACHMENT ***************
********************************************************/
if ( ! function_exists( 'tmpmela_the_attached_image' ) ) :
/**
 * Print the attached image with a link to the next attached image.
 * @return void
 */
function tmpmela_the_attached_image() {
	/**
	 * Filter the image attachment size to use.
	 *
	 * @since Templatemela 1.0
	 *
	 * @param array $size {
	 *     @type int The attachment height in pixels.
	 *     @type int The attachment width in pixels.
	 * }
	 */
	$attachment_size     = apply_filters( 'tmpmela_attachment_size', array( 1400, 1100 ) );
	$next_attachment_url = wp_get_attachment_url();
	$post                = get_post();
	/*
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );
	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}
		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );
		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}
	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;
/********************************************************
************ TEMPLATE MELA GET URL **********************
********************************************************/
function tmpmela_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );
	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
/********************************************************
************ TEMPLATE MELA LIST AUTHOR SETTING**************
********************************************************/
if ( ! function_exists( 'tmpmela_list_authors' ) ) :
/**
 * Print a list of all site contributors who published at least one post.
 * @return void
 */
function tmpmela_list_authors() {
	$contributor_ids = get_users( array(
		'fields'  => 'ID',
		'orderby' => 'post_count',
		'order'   => 'DESC',
		'who'     => 'authors',
	) );
	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );
		// Move on if user has not published a post (yet).
		if ( ! $post_count ) {
			continue;
		}
	?>
<div class="contributor">
  <div class="contributor-info">
    <div class="contributor-avatar"><?php echo esc_attr(get_avatar( $contributor_id, 132 )); ?></div>
    <div class="contributor-summary">
      <h2 class="contributor-name"><?php echo esc_attr(get_the_author_meta( 'display_name', $contributor_id )); ?></h2>
      <p class="contributor-bio"> <?php echo esc_attr(get_the_author_meta( 'description', $contributor_id )); ?> </p>
      <a class="contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>"> <?php printf( _n( '%d Article', '%d Articles', $post_count, 'firezy' ), $post_count ); ?> </a> </div>
    <!-- .contributor-summary -->
  </div><!-- .contributor-info -->
</div><!-- .contributor -->
<?php
	endforeach;
}
endif;
/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Presence of header image.
 * 3. Index views.
 * 4. Full-width content layout.
 * 5. Presence of footer widgets.
 * 6. Single views.
 * 7. Featured content layout.
 *
 * @since TemplateMela 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function tmpmela_body_classes( $classes ) {
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	if ( get_header_image() ) {
		$classes[] = 'header-image';
	} else {
		$classes[] = 'masthead-fixed';
	}
	if ( is_archive() || is_search() || is_home() ) {
		$classes[] = 'list-view';
	}
	if ( ( ! is_active_sidebar( 'sidebar-2' ) )
		|| is_page_template( 'page-templates/full-width.php' )
		|| is_page_template( 'page-templates/contributors.php' )
		|| is_attachment() ) {
	}
	if ( is_singular() && ! is_front_page() ) {
		$classes[] = 'singular';
	}
	if ( is_front_page() && 'slider' == get_theme_mod( 'tmpmela_Featured_Content_layout' ) ) {
		$classes[] = 'slider';
	} elseif ( is_front_page() ) {
		$classes[] = 'grid';
	}
	return $classes;
}
add_filter( 'body_class', 'tmpmela_body_classes' );
/**
 * Extend the default WordPress post classes.
 *
 * Adds a post class to denote:
 * Non-password protected page with a post thumbnail.
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
function tmpmela_post_classes( $classes ) {
	if ( ! post_password_required() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
	}
	return $classes;
}
add_filter( 'post_class', 'tmpmela_post_classes' );
/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function tmpmela_wp_title( $title, $sep ) {
	global $paged, $page;
	if ( is_feed() ) {
		return $title;
	}
	// Add the site name.
	$title .= get_bloginfo( 'name' );
	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}
	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'firezy' ), max( $paged, $page ) );
	}
	return $title;
}
add_filter( 'wp_title', 'tmpmela_wp_title', 10, 2 );
// Implement Custom Header features.
get_template_part('inc/custom-header' );
// Custom template tags for this theme.
get_template_part('inc/template-tags' );
// Add Theme Customizer functionality.
get_template_part( 'inc/customizer' );
/*
 * Add Featured Content functionality.
 *
 * To overwrite in a plugin, define your own tmpmela_Featured_Content class on or
 * before the 'setup_theme' hook.
*/
if ( ! class_exists( 'tmpmela_Featured_Content' ) && 'plugins.php' !== $GLOBALS['pagenow'] ) {	
	get_template_part('inc/featured-content' );
}
function tmpmela_title_tag() {
   add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'tmpmela_title_tag' );
/*Add Templatemela custom function */
get_template_part('templatemela/megnor-functions' );
/*Add Templatemela theme setting in menu */
get_template_part('templatemela/options' );
/*Add TGMPA library file */
get_template_part('/templatemela/tmpmela-plugins-install');
add_action( 'admin_menu', 'tmpmela_theme_setting_menu' );
function tmpmela_theme_settings_page() {
	$locale_file = get_template_part('templatemela/admin/theme-setting');
	if (is_readable( $locale_file ))		
		get_template_part('templatemela/admin/theme-setting' );
}
function tmpmela_hook_manage_page() {
	$locale_file = get_template_part('templatemela/admin/theme-hook') ;
	if (is_readable( $locale_file ))		
			get_template_part('templatemela/admin/theme-hook' );
}
/* Control Panel Tags Function Includes */
get_template_part('templatemela/controlpanel/tmpmela_control_panel' );
get_template_part('templatemela/admin/hook-functions' );
get_template_part('mr-image-resize' );
/* Adds woocommerce functions if active */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
	get_template_part('templatemela/woocommerce-functions');
endif;
/*for single post related page */
if ( ! function_exists( 'tmpmela_related_posts' ) ) :
function tmpmela_related_posts() {
global $post;
	$orig_post = $post;
$categories = get_the_category($post->ID);
if ($categories) {
$category_ids = array();
foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
$args=array(
'category__in' => $category_ids,
'post__not_in' => array($post->ID),
'posts_per_page'=> 12, // Number of related posts that will be shown.
'ignore_sticky_posts'=>1
);
$my_query = new wp_query( $args );
if( $my_query->have_posts() ) {   ?>
<div class="related_posts_outer"><div class="shortcode-title center"><h1 class="normal-title"><?php echo esc_attr_e( 'Related Posts', 'firezy' ) ?></h1></div>
<div class="related_posts blog-list">
<?php while( $my_query->have_posts() ) {
$my_query->the_post();?>
<article class="post">
<div class="entry-main-content">
<div class="entry-thumbnail">
	<a href="<?php the_permalink()?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
</div>

 <?php $postImage = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );	?>
<div class="post-info <?php if(empty($postImage)): ?> non <?php endif; ?>">
<div class="entry-meta date <?php if(empty($postImage)): ?> non <?php endif; ?>">
	<?php tmpmela_post_entry_date(); ?>	 
	<?php tmpmela_comments_link(); ?>				
</div>
	
	<div class="entry-main-header ">			
		<header class="entry-header"><h3 class="entry-title">
		<a href="<?php the_permalink()?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title_attribute(); ?></a>
		</h3></header>
	</div>
	<div class="entry-content-inner">
				<div class="entry-meta-inner">
				  <div class="entry-meta">
					  <div class="entry-summary">
							<div class="excerpt"> <?php echo tmpmela_posts_short_description(); ?> </div>
					  </div><!-- .entry-summary -->
					</div> <!--  entry-meta -->
			</div><!--  entry-meta-inner -->
	 </div>
</div>	
</article>		
<?php
}
echo '</div></div>';
}
}
$post = $orig_post;
wp_reset_postdata(); 
}
endif;
/********************************************************
**************** One Click Import Data ******************
********************************************************/
if ( ! function_exists( 'sampledata_import_files' ) ) :
function sampledata_import_files() {
return array(
 array(
    'import_file_name'            => 'firezy',
    'local_import_file'           => trailingslashit( get_stylesheet_directory() ) . 'demo-content/default/firezy.wordpress.xml',
    'local_import_customizer_file'=> trailingslashit( get_stylesheet_directory() ) . 'demo-content/default/firezy_customizer_export.dat',
	'local_import_widget_file'    => trailingslashit( get_stylesheet_directory() ) . 'demo-content/default/firezy_widgets_settings.wie',
    'import_notice'               => esc_html__( 'Please waiting for a few minutes, do not close the window or refresh the page until the data is imported.', 'firezy' ),
),
);
}
add_filter( 'pt-ocdi/import_files', 'sampledata_import_files' );
endif;
if ( ! function_exists( 'sampledata_after_import' ) ) :
function sampledata_after_import($selected_import) {
         //Set Menu
        $header_menu = get_term_by('name', 'MainMenu', 'nav_menu');
        $top_menu = get_term_by('name', 'Header Top Links', 'nav_menu');
		$footer_menu = get_term_by('name', 'footer-menu', 'nav_menu');
        set_theme_mod( 'nav_menu_locations' , array( 
		 'primary'   => $header_menu->term_id,
		 'header-menu'   => $top_menu->term_id ,
		 'footer-menu'   => $footer_menu->term_id 
         ) 
        );
		
		//Set Front page and blog page
       $page = get_page_by_title( 'Home');
       if ( isset( $page->ID ) ) {
        update_option( 'page_on_front', $page->ID );
        update_option( 'show_on_front', 'page' );
       }
	   $post = get_page_by_title( 'Blog');
       if ( isset( $page->ID ) ) {
        update_option( 'page_for_posts', $post->ID );
        update_option( 'show_on_posts', 'post' );
       }
	   
	   //Import Revolution Slider
       if ( class_exists( 'RevSlider' ) ) {
           $slider_array = array(
              get_template_directory()."/demo-content/default/tmpmela_homeslider.zip",
			  );
           $slider = new RevSlider();
        
           foreach($slider_array as $filepath){
             $slider->importSliderFromPost(true,true,$filepath);  
           }
           echo esc_html__( 'Slider processed', 'firezy' );
      }
}
add_action( 'pt-ocdi/after_import', 'sampledata_after_import' );
endif;
function tmpmela_change_time_of_single_ajax_call() {
	return 10;
}
add_action( 'pt-ocdi/time_for_one_ajax_call', 'tmpmela_change_time_of_single_ajax_call' );
/* remove notice info*/
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' ); 
?>