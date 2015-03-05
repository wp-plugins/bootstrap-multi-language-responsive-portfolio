<?php

/**
 * Template Name: Portfolio
 * Description: A Page Template that display portfolio items.
 *
 * @package Portfolio
 * @author August Infotech
 */

get_header(); 
global $wp_query;
$post_id = $wp_query->get_queried_object_id();
$portfolio_post_count = get_option( 'portfolio_post_count' );
$post_order = get_option( 'portfolio_order' );
$post_orderby = get_option( 'portfolio_order_by' );

switch ( get_post_meta($post_id, 'Layout', true) ) {
	case 'left_sidebar':
		$class = 'left';
        break;
	case 'right_sidebar':
		$class = 'right';
		break;
	default:
		$class = '';
		break;
}
 
if( $class == 'left' ){
  
    $right_class = 'col-xs-12 col-sm-9 col-md-9 pull-right';
    $left_class = 'col-xs-12 col-sm-3 col-md-3 pull-left';
    $class = 'left';
}  
elseif( $class == 'right' ){
    
    $right_class = 'col-xs-12 col-sm-9 col-md-9';
    $left_class = 'col-xs-12 col-sm-3 col-md-3';
    $class = 'right';
}     
else{ 
    $class = '';
}
?>
<div class="container">
    <article class="row"> 
        <section class="project-section">  
           <?php
                if( $class ) echo'<article class="' .$right_class.'">'; 
                    if( $portfolio_categories= get_terms( 'portfolio-types' ) ) {
                        echo '<div class="portfolioFilter">';
                            echo '<a rel="*" href="'.get_page_link( $post_id ).'" class="nav-all current">'.__('All','wpt').'</a>';
                            
        				    foreach( $portfolio_categories as $category ){
        				    	
                                echo '<a href="'.get_term_link( $category ).'" class="nav-all">'.$category->name.' <span>('. $category->count .')</span></a>';    
                            }
                        echo '</div>'; 
                    } 
				    
                    $portfolio_args = array( 
						'post_type' => 'portfolio',
						'posts_per_page' => ( !empty( $portfolio_post_count ) ) ? $portfolio_post_count : '8' ,
						'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
						'order' => ( !empty( $post_order ) ) ? $post_order : 'ASC',
						'orderby' => ( !empty( $post_orderby) ) ? $post_orderby : 'date',
				    );

					$temp = $wp_query;
					$wp_query = null;
					$wp_query = new WP_Query();
					$wp_query->query( $portfolio_args );
                    
                    if ( $wp_query->have_posts() )
                    { 
	                    echo '<div class="grid">';
	                            while ( have_posts() )
							    {
								    the_post();
	                                include( plugin_dir_path(__FILE__).'content-portfolio.php' );
	                                
	                            }
	                            
	                    echo '</div>';
	                    
	                    echo '<article class="col-xs-12 col-sm-12 col-md-12 text-right">';
						    echo '<ul class="pagination">';
	                           wpt_portfolio_pagination();
						    echo '</ul>';
						echo '</article>';
               		}
               		
                    $wp_query = $temp;
    				wp_reset_query(); 
    				the_post();  
    					
                if( $class ) echo '</article>';    
                
                if( $class ){ 
                    echo '<article class="' .$left_class. '">';
                            echo'<aside>';
                                echo'<div class="sidebar">';
                                    include( get_stylesheet_directory(). '/sidebar.php' );
                                echo '</div>';
                            echo '</aside>';
                    echo'</article>';
        
                } 
              ?> 
        </section> <!--project-section end-->
    </article>
</div>
<?php get_footer(); ?>
