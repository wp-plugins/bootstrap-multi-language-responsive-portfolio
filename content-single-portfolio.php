<?php
/*The template for displaying content in the single-portfolio.php template
 *
 * @package Portfolio
 * @author August Infotech
 */

$pf_client = get_post_meta(get_the_ID(), 'client', true);
$pf_url = get_post_meta(get_the_ID(), 'url', true); 
$pf_url_text = get_post_meta(get_the_ID(), 'url_text', true);
$full_title =  get_the_title();
$sub_title = explode(' ',$full_title, 2);
$img_id = get_post_meta(get_the_ID(), 'image_id', true);
$terms = get_the_terms($post->ID, 'portfolio-types');
$cnt = 0;
if(!empty($term->name)){
	foreach ( $terms as $term ){ 
		$cnt++ ; 
	    $skill_name[$cnt] = $term->name; 
	}  
}
?>
<article class="col-xs-12 col-sm-6 col-md-8 project-slider wow fadeInLeft animated">
    <div id="myCarousel" class="carousel slide" data-interval="3000" data-ride="carousel">
        <!-- Carousel indicators -->
        <?php 
        echo '<ol class="carousel-indicators">';
            for( $i=0; $i < $img_id; $i++ ){
                    $slider_class = '';
                    if( $i == 0 )
                        $slider_class = 'active';
                     
                    echo '<li data-target="#myCarousel" data-slide-to="'.$i.'" class="'.$slider_class.'"></li>';
            } 
        echo '</ol>';
         ?>
        <!-- Carousel items -->
        <div class="carousel-inner">
            <?php 
           
                for( $i=0; $i < $img_id; $i++ )
                {
                    $slider_img_class = '';
                    
                    if( $i == 0 )
                        $slider_img_class = 'active';
                     
                    $portfolio_slider_image_id = get_post_meta(get_the_ID(),'imagebox'.$i,true); 
                    $portfolio_slider_image_src = wp_get_attachment_image_src($portfolio_slider_image_id, 'large');
                     echo '<div class="item '.$slider_img_class.'">'; 
	                    if( !empty( $portfolio_slider_image_src ) )
	                    {
							echo '<img src="'.$portfolio_slider_image_src[0].'" alt="'.$full_title.'" />';
						}
                   		else{ 
							echo '<img src="' .WPT_PLUGIN_URL. 'images/no-img-portfolio.jpg'.'" alt="'.$full_title.'" />';
						}
                    echo '</div>';
                }
            ?>
        </div>
        <!-- Carousel nav -->
        <a class="carousel-control left" href="#myCarousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="carousel-control right" href="#myCarousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>
</article>          
<article class="col-xs-12 col-sm-6 col-md-4 project-detail wow fadeInRight animated" data-wow-delay="0.3s">
    <h4 class="title"><strong><?php echo $sub_title[0]; ?></strong><?php echo $sub_title[1];?></h4>
    <p class="desc"><?php echo the_content(); ?></p>
    <p><strong><?php _e( 'Client:', 'wpt' );?></strong><?php echo $pf_client; ?></p>
    <p><strong><?php _e( 'Date :', 'wpt' ); ?></strong> <?php echo get_the_date(); ?></p>
    <?php if( !empty( $skill_name[$cnt] ) ){ ?>
    <p>
      <strong><?php _e( 'Skills :', 'wpt' ); ?></strong> 
        <?php for ( $i=1; $i <= $cnt; $i++ ){ 
                echo  $skill_name[$i];
                  if ( $i <= ( $cnt - 1 ) )
                      echo ', ';
               } ?>
    </p><?php } ?>
    <p><strong><?php _e( 'Link :', 'wpt' ); ?></strong> <a href="<?php echo $pf_url;?>" target="_blank"><?php echo $pf_url_text; ?></a></p><br/>
    <div class="gap row wow fadeInDown" data-wow-delay="0.8s" id="sharing">
        <div class="col-md-12">
            <div class="btn-group sharing-btns">
                <button class="btn btn-default disabled"><?php _e( 'Share:', 'wpt' ); ?></button>    
                <a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(the_permalink('','',false)); ?>&amp;t=<?php echo strip_tags($post->post_title); ?>" target="_blank" class="btn btn-default facebook"><i class="fa fa-facebook fa-lg fb"></i> </a>
                <a href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode(the_permalink('','',false)); ?>&amp;text=<?php echo strip_tags($post->post_title); ?>&amp;tw_p=tweetbutton&amp;url=<?php echo urlencode(the_permalink('','',false)); ?>" target="_blank" class="btn btn-default twitter"><i class="fa fa-twitter fa-lg tw"></i></a>
                 <a href="https://plusone.google.com/_/+1/confirm?hl=en-US&amp;url=<?php echo urlencode(the_permalink('','',false)); ?>" target="_blank" class="btn btn-default google"><i class="fa fa-google-plus fa-lg google"></i></a>
                 <a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode(the_permalink('','',false)); ?>&amp;media=<?php echo $blog_detail_url; ?>&amp;description=<?php echo strip_tags($post->post_title); ?>" target="_blank" class="btn btn-default pinterest"> <i class="fa fa-pinterest fa-lg pinterest"></i></a>
            </div>
        </div>
    </div>
</article>