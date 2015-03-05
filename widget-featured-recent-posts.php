<?php
/**
 * Widget Portfolio Featured Recent Posts
 *
 * @package Portfolio
 * @author August Infotech
 */

class Wpt_Portfolio_Featured_Recent_Posts_Widget extends WP_Widget {

	
	/* ---------------------------------------------------------------------------
	 * Constructor
	 * --------------------------------------------------------------------------- */
	function Wpt_Portfolio_Featured_Recent_Posts_Widget() {
		$widget_ops = array( 'classname' => 'widget_wpt_portfolio_featured_recent_posts', 'description' => __( 'The most featured portfolio recent posts on your site.', 'wpt' ) );
		$this->WP_Widget( 'widget_wpt_portfolio_featured_recent_posts', __( 'Portfolio Featured Recent Posts', 'wpt' ), $widget_ops );
		$this->alt_option_name = 'widget_wpt_portfolio_featured_recent_posts';
	}
	
	
	/* ---------------------------------------------------------------------------
	 * Outputs the HTML for this widget.
	 * --------------------------------------------------------------------------- */
	function widget( $args, $instance ) {

		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = null;
		extract( $args, EXTR_SKIP );

		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
		
		$args = array( 
						'post_type' => 'portfolio',
						'posts_per_page' => $instance['count'] ? intval($instance['count']) : 0,
						'no_found_rows' => true, 
						'meta_key' => 'featured-checkbox',
						'meta_value' => 'yes',
						'post_status' => 'publish',
			            'ignore_sticky_posts' => true,
						'order' => 'DESC',
						'orderby' => 'date'
		);				
		
		
		$r = new WP_Query( apply_filters( 'widget_posts_args', $args ) );
		
		if ($r->have_posts()){
			if( $title ) 
			 $output = $before_title . $title . $after_title;	
				$output .= '<ul>';
				while ( $r->have_posts() ){
					$r->the_post();
					$output .= '<li><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';
				}
				wp_reset_postdata();
				$output .= '</ul>'."\n";
		}
		echo $output;
		
		echo $after_widget;
	}


	/* ---------------------------------------------------------------------------
	 * Deals with the settings when they are saved by the admin.
	 * --------------------------------------------------------------------------- */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = (int) $new_instance['count'];
		
		return $instance;
	}

	
	/* ---------------------------------------------------------------------------
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 * --------------------------------------------------------------------------- */
	function form( $instance ) {
		
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 2;

		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wpt' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of posts:', 'wpt' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" size="3"/>
			</p>
			
		<?php
	}
}

/* ---------------------------------------------------------------------------
 * Add portfolio widgets
 * --------------------------------------------------------------------------- */
 
function wpt_register_widget() {
    register_widget('Wpt_Portfolio_Featured_Recent_Posts_Widget');
}
add_action( 'widgets_init', 'wpt_register_widget' );
?>