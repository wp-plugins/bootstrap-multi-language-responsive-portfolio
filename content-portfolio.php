<?php
/**
 * The template for displaying content in the template-portfolio.php template
 *
 * @package Portfolio
 * @author August Infotech
 */
 
$layout = get_option( 'portfolio_layout' );
switch( $layout ){
    case '2col':
		$layout_class = 'col-xs-6 col-sm-6 col-md-6 effect-apollo two-col';
        break;
	case '3col':
		$layout_class = 'col-xs-6 col-sm-4 col-md-4 effect-apollo three-col';
		break;
	case '4col':
		$layout_class = 'col-xs-6 col-sm-3 col-md-3 effect-apollo four-col';
		break;
    default :
        $layout_class = '';
		break;
}
$pf_listing_full_imgArray = wp_get_attachment_image_src(get_post_thumbnail_id( $id ), 'portfolio-listing');
$pf_listing_imgURL = $pf_listing_full_imgArray[0];
$full_title =  get_the_title();
$sub_title = explode(' ',$full_title, 2);               
?>                    
<figure class="<?php echo $layout_class; ?>">
<?php if( !empty( $pf_listing_imgURL ) ){ ?>
	    <img alt="" src="<?php  echo $pf_listing_imgURL; ?>">
<?php } 
	 else{
	 	if( $layout == '2col' ){ ?>
			<img alt="" src="<?php  echo WPT_PLUGIN_URL. 'images/no-img-portfolio-480x360.jpg';  ?>" > 
		<?php }else if( $layout == '3col' ){ ?>
			<img alt="" src="<?php  echo WPT_PLUGIN_URL. 'images/no-img-portfolio-480x360.jpg';  ?>" > 
		<?php }else if( $layout == '4col' ){ ?>
			<img alt="" src="<?php  echo WPT_PLUGIN_URL. 'images/no-img-portfolio-480x360.jpg';  ?>" >
		<?php } ?>
<?php } ?>
        <figcaption>
            <h3><?php echo $sub_title[0]; ?><span><?php echo $sub_title[1]; ?></span> </h3>
            <a href="<?php the_permalink(); ?>"><?php __('View more','wpt')?></a>
        </figcaption>
</figure>                   