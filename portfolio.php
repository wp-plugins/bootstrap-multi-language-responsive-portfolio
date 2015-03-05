<?php
/*

Plugin Name: Multi-language Responsive Portfolio

Plugin URI: http://www.augustinfotech.com

Description: Multi-language Responsive Portfolio is a simple WordPress plugin to showcase your portfolio on your website.

Version: 1.0

Text Domain: wpt

Author: August Infotech

Author URI: http://www.augustinfotech.com

*/

define( 'WPT_PLUGIN_URL', 			plugin_dir_url( __FILE__) );
define( 'WPT_PLUGIN_PATH',			plugin_dir_path( __FILE__ ) );
define( 'WPT_PLUGIN_BASENAME', 		plugin_basename( __FILE__ ) );
define( 'WPT_PLUGIN_VERSION', 	    '1.0' );


/* ---------------------------------------------------------------------------
 * Load the plugin required files
 * --------------------------------------------------------------------------- */
 
add_action( 'plugins_loaded','portfolio_plugin_load_function' );

if ( ! function_exists( 'portfolio_plugin_load_function' ) ) :
function portfolio_plugin_load_function(){
	
	// Add required Files for portfolio Plugin and filters for template

	require_once( 'wpt-posttype-portfolio.php' );
	require_once( 'portfolio-shortcode.php' );
	require_once( 'widget-featured-recent-posts.php' );
   
    // Add a filter to the template include to determine if the page has our 
	// template assigned and return it's path
    add_filter(
		'template_include', 
		'view_project_template'
	);
}
endif; // portfolio_plugin_load_function


/* ---------------------------------------------------------------------------
 * Activate Hook Plugin
 * --------------------------------------------------------------------------- */

register_activation_hook(__FILE__,'wpt_portfolio_plugin_enabled');

if ( ! function_exists( 'wpt_portfolio_plugin_enabled' ) ) :
function wpt_portfolio_plugin_enabled() {	
		 
	//Add Default Options for Portfolio Plugin
	 
	add_option('portfolio-page', 'Project','','yes');
	add_option('portfolio_title','Portfolio','', 'yes');
	add_option('portfolio_content','Lorem ipsum dolor sit amet, consectetur adipiscing elit.','', 'yes');
	add_option('portfolio_post_count','8','', 'yes');
	add_option('portfolio_layout','2col','', 'yes');	
	add_option('portfolio_order_by','title','', 'yes');
	add_option('portfolio_order', 'ASC', '', 'yes');
    
}
endif; // wpt_portfolio_plugin_enabled



/* ---------------------------------------------------------------------------
 * Uninstall Hook Plugin
 * --------------------------------------------------------------------------- */

if ( function_exists('register_uninstall_hook') )
 register_uninstall_hook(__FILE__,'wpt_portfolio_plugin_droped'); 

if ( ! function_exists( 'wpt_portfolio_plugin_droped' ) ) :
function wpt_portfolio_plugin_droped() { 

	delete_option( 'portfolio_title' );
	delete_option( 'portfolio_content' );
	delete_option( 'portfolio_post_count' );
	delete_option( 'portfolio_layout' );	
	delete_option( 'portfolio_order_by' );
	delete_option( 'portfolio_order' );
	delete_option( 'portfolio_order' );
	delete_option( 'portfolio-page' );
}
endif; // wpt_portfolio_plugin_droped



/* ---------------------------------------------------------------------------
 * Required fucntions and hooks for Plugin
 * --------------------------------------------------------------------------- */
 
if ( ! function_exists( 'call_custom_taxonomy_template' ) ) : 
function call_custom_taxonomy_template( $template_path ){

    //Get template name
    $template = basename($template_path);
    //Check if template is taxonomy-portfolio_category.php
    //Check if template is taxonomy-portfolio_category-{term-slug}.php
    if( 1 == preg_match('/^taxonomy-portfolio-types((-(\S*))?).php/',$template) )
         return true;

    return false;
}
endif; // call_custom_taxonomy_template

if ( ! function_exists( 'view_project_template' ) ) : 
/**
 * Checks if the template is assigned to the page
 */
 function view_project_template( $template ) {

        global $post,$wp_query; 
        //check if the query is for that specific taxonomy page otherwise it goes to particular template page. 
        if( $wp_query->query_vars['taxonomy'] == 'portfolio-types' && !call_custom_taxonomy_template($template))
         $filename = 'taxonomy-portfolio-types.php'; 
        else if( $post->ID == get_option('portfolio-page') )
          $filename = 'template-portfolio.php'; 
         
        if( !empty($filename) ){
          $file = plugin_dir_path(__FILE__). $filename;
          
	      // Just to be safe, we check if the file exist first
	      if( file_exists( $file ) ) {
	           return $file;
	      } 
        }
        
        return $template;

} 
endif; // view_project_template

// Single Page redirect Tempalte hook
add_filter( 'single_template', 'get_portfolio_post_type_single_template' );

if ( ! function_exists( 'get_portfolio_post_type_single_template' ) ) : 
function get_portfolio_post_type_single_template($single_template) {
     global $post;

     if ($post->post_type == 'portfolio') {
          $single_template = plugin_dir_path(__FILE__). 'single-portfolio.php';
     }
     return $single_template;
}
endif; // get_portfolio_post_type_single_template

//filter for custom posts per page
add_filter( 'option_posts_per_page', 'portfolio_tax_filter_posts_per_page' );

if ( ! function_exists( 'portfolio_tax_filter_posts_per_page' ) ) : 
function portfolio_tax_filter_posts_per_page( $value ) {
  return (is_tax('portfolio-types')) ? 1 : $value;
}
endif; // portfolio_tax_filter_posts_per_page


if ( ! function_exists( 'wpt_portfolio_pagination' ) ):  
function wpt_portfolio_pagination() {
   
	global $wp_query;

	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;  
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	if( empty( $paged ) ) $paged = 1;
	$prev = $paged - 1;							
	$next = $paged + 1;
	
	$end_size = 1;
	$mid_size = 2;
	$show_all = true;
	$dots = false;	
	if( ! $total = $wp_query->max_num_pages ) $total = 1;
	
	if( $total > 1 )
	{
		
		if( $paged >1 ){
			echo '<li><a class="prev_page" href="'. get_pagenum_link($current-1) .'">'. __('&lsaquo;','wpt') .'</a></li>';
		}

		for( $i=1; $i <= $total; $i++ ){
			if ( $i == $current ){
				echo '<li class="active">';
					echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				echo '</li>';
				$dots = true;
			} else {
				if ( $show_all || ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total - $end_size ) ){
					echo '<li>';
					   echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				    echo '</li>';
					$dots = true;
				} elseif ( $dots && ! $show_all ) {
					echo '<span class="page">...</span>&nbsp;';
					$dots = false;
				}
			}
		}
		
		if( $paged < $total ){
			echo '<li><a class="next_page" href="'. get_pagenum_link($page+1) .'">'. __('&rsaquo;','wpt') .'</a></li>';
		}

	}	
}
endif; // wpt_portfolio_pagination
?>