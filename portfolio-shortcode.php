<?php
add_shortcode( 'portfolio', 'portfolioShortcode' );

function portfolioShortcode( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'count' => '4',
		'category' => '',
		'orderby' => 'menu_order',
		'order' => 'ASC',
	), $attr));
	    
	$args = array( 
		'post_type' => 'portfolio',
		'posts_per_page' => intval($count),
		'paged' => -1,
        'post_status'=> 'publish',
		'orderby' => $orderby,
		'order' => $order,	
		'ignore_sticky_posts' =>1,
	);
    if( $category ) $args['portfolio-types'] = $category;
    
    $query = new WP_Query( $args );
    if ($query->have_posts())
	{ 
        $portfolio_title = get_option('portfolio_title');
         if( empty( $portfolio_title ) )
           $portfolio_title = 'Our recent works';
           
        $portfolio_content = get_option( 'portfolio_content' );   
	    $portfolio_content = stripslashes ( $portfolio_content );
	    
        $html = '';
		$html .='<section class="recent_project clearfix">';		
		$html .='<div class="title-area">';	
		$html .='<h2 class="section-title">'. $portfolio_title. '</h2>';
		$html .='<div class="section-divider divider-inside-top"></div>';
		$html .='<p class="section-sub-text">'.$portfolio_content. '</p>';
        $html .='<article class="project-list">';
        $html .='<section class="row grid">';
        
        while ($query->have_posts()) 
		{
                    $query->the_post();
                    /*$term_count = '';
                    $portfolio_terms_deatils = get_the_terms( get_the_ID(), 'portfolio_category' );
                    
                    $term_count = count($portfolio_terms_deatils);*/
                   	$pf_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'portfolio-homepage');
                    $large_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'portfolio-homepagelarge');
                    $pf_imgURL = $pf_imgArray[0];
                    $large_imgURL = $large_imgArray[0];
                    $full_title =  get_the_title();
					$sub_title = explode(' ',$full_title, 2); 
                    
                     $html .='<figure class="col-xs-6 col-sm-3 col-md-3 effect-apollo">';
						 if( !empty( $pf_imgURL ) ){

							     $html .='<img alt="" src="' .$pf_imgURL.  '">';
						} 
						 else{
							 	
								 $html .='<img alt="" src="' .WPT_PLUGIN_URL. 'images/no-img-portfolio-240x240.jpg'.  '" >';
								
						} 
						         $html .='<figcaption>';
						             $html .='<h3>' .$sub_title[0]. '<span>' .$sub_title[1]. '</span></h3>';
						             $html .='<a href="' .get_permalink(). '"></a>';
						         $html .='</figcaption>';
						 $html .='</figure>';       
                                       
				} 
		$html .='</section>';
        $html .='</article>';
        $html .='</div>';
	    $html .='</section>';	  
	}
	wp_reset_query();	
    return $html;
}
?>