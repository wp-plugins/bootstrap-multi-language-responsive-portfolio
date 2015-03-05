<?php

/** 
 * The Template for displaying all single posts.
 * 
 * @package Portfolio
 * @author Purplethemes
 */
get_header(); 

global $wp_query;
$post_id = $wp_query->get_queried_object_id();

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
    <section class="project-section"> 
        <section class="row"> 
        <?php     
            if( $class ) echo'<article class="' .$right_class.'">';  
            
		        			while ( have_posts() ) {
		        			   the_post();
		                       include( plugin_dir_path(__FILE__).'content-single-portfolio.php' );          
		            		}	   
                    		
            if( $class ) echo '</article>';
                 
            if( $class ){ 
                echo '<article class="' .$left_class. '">';
                    echo'<aside>';
                        echo'<div class="sidebar">';
                            get_sidebar();
                         echo '</div>';
                    echo '</aside>';
                echo'</article>';
        
            }  
        ?>
        </section> <!--row end-->
    </section> <!--project-section end-->
</div><!--container end-->
<?php get_footer(); ?>