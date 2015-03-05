<?php
/* ---------------------------------------------------------------------------
* Portfolio Custom Post type
* --------------------------------------------------------------------------- */

if ( ! class_exists( 'Portfolio_Post_Type' ) ) :

	class Portfolio_Post_Type {
		
		
		function __construct() 
        {

			// Runs when the plugin is activated
			register_activation_hook( __FILE__, array( &$this, 'plugin_activation' ) );
			
			add_action( 'admin_menu', array( $this, 'portfolio_setting_admin_menu' ) );

			// Add support for translations
			load_plugin_textdomain( 'wpt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Adds the portfolio post type and taxonomies
			add_action( 'init', array( &$this, 'portfolio_init' ) );
            
            // Adds meta boxes
            add_action( 'add_meta_boxes', array( &$this, 'portfolio_init_add_metaboxes' ) );
            
            //Save meta-box values
            add_action('save_post', array( &$this, 'save_portfolioposttype_values' ));
            
			// Thumbnail support for portfolio posts
			
			add_image_size( 'portfolio-admin-thumbnail',100,100 ); // Admin listing thumbnail
			add_image_size( 'portfolio-listing', 480, 360, true ); // portfolio - listing (2col,3col,4col)
	        add_image_size( 'portfolio-homepage', 240, 240, true ); // portfolio - shortcode- box
	        add_image_size( 'portfolio-homepagelarge', 799, 539, true ); //portfolio - shortcode - lightbox (large)
			

			// Adds thumbnails to column view
			add_filter( 'manage_edit-portfolio_columns', array( &$this, 'add_portfolio_thumbnail_column'), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'display_portfolio_thumbnail' ), 10, 1 );
            
           
			// Allows filtering of posts by taxonomy in the admin view
			add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );
            
            //Allows filtering of post using portfolio meta field 
            add_filter( 'parse_query', array( &$this, 'portfolio_admin_posts_filter_data' ) );
            add_action( 'restrict_manage_posts', array( &$this, 'portfolio_admin_posts_filter_restrict_manage_posts' ) );
  
                        
			// Show portfolio post counts in the dashboard
			add_action( 'dashboard_glance_items', array( &$this, 'add_portfolio_counts' ) );

			// Give the portfolio menu item a unique icon
			add_action( 'admin_head', array( &$this, 'portfolio_icon' ) );
			
			add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_frontside_scripts' ), 0 );
			
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles_scripts'), 0 );
			
			add_filter('widget_text', 'do_shortcode');
	
		}
		
		function enqueue_admin_styles_scripts() {
			
		        /* included javascript of admin section */
		        
		        wp_enqueue_script('jquery');
		        
		        wp_enqueue_script('jquery.portfolio-post-slider', WPT_PLUGIN_URL .'js/portfolio-post.js', false, WPT_PLUGIN_VERSION, true );
		        
		        /* included javascript section end */
		        
		        /* css section  */
		        
		        wp_enqueue_style('portfolio-post-slider', WPT_PLUGIN_URL.'css/portfolio-post-options.css', array(), WPT_PLUGIN_VERSION);

                /* css section end  */              	        
		}
		
		function plugin_frontside_scripts() {
			
		        /* included javascript section */
		        
		        wp_enqueue_script('jquery');
		        
		        wp_enqueue_script( 'jquery-bootstrap-js', WPT_PLUGIN_URL .'js/bootstrap.min.js', false, WPT_PLUGIN_VERSION, true );
		        
		        
		        /* included javascript section end */
		        
		        /* css section  */
		        
		        wp_enqueue_style('jquery.bootstrap', WPT_PLUGIN_URL.'css/bootstrap.css', array(), WPT_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.font-awesome', WPT_PLUGIN_URL.'css/font-awesome.min.css', array(), WPT_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.portfolio', WPT_PLUGIN_URL.'css/portfolio.css', array(), WPT_PLUGIN_VERSION);

                /* css section end  */              	        
		}


		/**
		 * Flushes rewrite rules on plugin activation to ensure portfolio posts don't 404
		 * http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
		 */

		function portfolio_activation() 
        {
			$this->portfolio_init();
			flush_rewrite_rules();
		}

		function portfolio_init() 
        {
           
			/**
			 * Enable the Portfolio custom post type
			 * http://codex.wordpress.org/Function_Reference/register_post_type
			 */

			$labels = array(
				'name' => __( 'Portfolio', 'wpt' ),
				'singular_name' => __( 'Portfolio Item', 'wpt' ),
				'add_new' => __( 'Add New Item', 'wpt' ),
				'add_new_item' => __( 'Add New Portfolio Item', 'wpt' ),
				'edit_item' => __( 'Edit Portfolio Item', 'wpt' ),
				'new_item' => __( 'Add New Portfolio Item', 'wpt' ),
				'view_item' => __( 'View Item', 'wpt' ),
				'search_items' => __( 'Search Portfolio', 'wpt' ),
				'not_found' => __( 'No portfolio items found', 'wpt' ),
				'not_found_in_trash' => __( 'No portfolio items found in trash', 'wpt' )
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true, 
        		'query_var' => true,
				'supports' => array( 'title', 'editor','thumbnail','page-attributes'),
				'capability_type' => 'post',
				'rewrite' => array("slug" => "portfolio-item"), // Permalinks format
                'menu_icon' => 'dashicons-portfolio',
				'menu_position' => 5,
				'has_archive' => true
			);

			$args = apply_filters('portfolioposttype_args', $args);

			register_post_type( 'portfolio', $args );
            
			/**
			 * Register a taxonomy for Portfolio Tags
			 * http://codex.wordpress.org/Function_Reference/register_taxonomy
			 */

			$taxonomy_portfolio_tag_labels = array(
				'name' => __( 'Portfolio Tags', 'wpt' ),
				'singular_name' => __( 'Portfolio Tag', 'wpt' ),
				'search_items' => __( 'Search Portfolio Tags', 'wpt' ),
				'popular_items' => __( 'Popular Portfolio Tags', 'wpt' ),
				'all_items' => __( 'All Portfolio Tags', 'wpt' ),
				'parent_item' => __( 'Parent Portfolio Tag', 'wpt' ),
				'parent_item_colon' => __( 'Parent Portfolio Tag:', 'wpt' ),
				'edit_item' => __( 'Edit Portfolio Tag', 'wpt' ),
				'update_item' => __( 'Update Portfolio Tag', 'wpt' ),
				'add_new_item' => __( 'Add New Portfolio Tag', 'wpt' ),
				'new_item_name' => __( 'New Portfolio Tag Name', 'wpt' ),
				'separate_items_with_commas' => __( 'Separate portfolio tags with commas', 'wpt' ),
				'add_or_remove_items' => __( 'Add or remove portfolio tags', 'wpt' ),
				'choose_from_most_used' => __( 'Choose from the most used portfolio tags', 'wpt' ),
				'menu_name' => __( 'Portfolio Tags', 'wpt' )
			);

			$taxonomy_portfolio_tag_args = array(
				'labels' => $taxonomy_portfolio_tag_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => false,
				'rewrite' => array( 'slug' => 'portfolio_tag' ),
				'show_admin_column' => true,
				'query_var' => true
			);

			register_taxonomy( 'portfolio_tag', array( 'portfolio' ), $taxonomy_portfolio_tag_args );
			
			register_taxonomy_for_object_type( 'portfolio_tag', 'portfolio' );

		    /**
			 * Register a taxonomy for Portfolio types
			 * http://codex.wordpress.org/Function_Reference/register_taxonomy
			 */

			$taxonomy_portfolio_category_labels = array(
				'name' => __( 'Portfolio Categories', 'wpt' ),
				'singular_name' => __( 'Portfolio Category', 'wpt' ),
				'search_items' => __( 'Search Portfolio Categories', 'wpt' ),
				'popular_items' => __( 'Popular Portfolio Categories', 'wpt' ),
				'all_items' => __( 'All Portfolio Categories', 'wpt' ),
				'parent_item' => __( 'Parent Portfolio Category', 'wpt' ),
				'parent_item_colon' => __( 'Parent Portfolio Category:', 'wpt' ),
				'edit_item' => __( 'Edit Portfolio Category', 'wpt' ),
				'update_item' => __( 'Update Portfolio Category', 'wpt' ),
				'add_new_item' => __( 'Add New Portfolio Category', 'wpt' ),
				'new_item_name' => __( 'New Portfolio Category Name', 'wpt' ),
				'separate_items_with_commas' => __( 'Separate portfolio categories with commas', 'wpt' ),
				'add_or_remove_items' => __( 'Add or remove portfolio categories', 'wpt' ),
				'choose_from_most_used' => __( 'Choose from the most used portfolio categories', 'wpt' ),
				'menu_name' => __( 'Portfolio Categories', 'wpt' ),
			);

			$taxonomy_portfolio_category_args = array(
				'labels' => $taxonomy_portfolio_category_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => array('slug' => 'portfolio-types' ),
				'query_var' => true,
			);

			register_taxonomy( 'portfolio-types', array( 'portfolio' ), $taxonomy_portfolio_category_args );
			
			register_taxonomy_for_object_type( 'portfolio-types', 'portfolio' );
			
			flush_rewrite_rules();

        }
        
        /**
		* Added submenu setting page in menu of magazines.
		*
		* Function Name: mim_issue_setting_admin_menu.
		*
		*
		**/
		
		function portfolio_setting_admin_menu() {
							
			add_submenu_page( 'edit.php?post_type=portfolio', __( 'Portfolio Settings', 'wpt' ), __( 'Portfolio Settings', 'wpt' ), 'manage_options', 'portfolio-settings', array( $this, 'portfolio_settings_page' ) );
		
		}
		
		function portfolio_settings_page() {
			
			if(isset($_REQUEST['update_portfolio_settings']))
			{ 
				if ( !isset($_POST['wpt_portfolio_nonce']) || !wp_verify_nonce($_POST['wpt_portfolio_nonce'],'portfolio_general_setting') )
				{
				    _e('Sorry, your nonce did not verify.', 'wpt');
				   exit;
				}
				
				else
				{
				  	update_option('portfolio-page',$_POST['portfolio-page']);
				  	
				  	$portfolio_feature_post= !empty($_POST['portfolio_feature_post']) ;
				  	update_option('portfolio_feature_post',$portfolio_feature_post);
				  	
					$portfolio_title= !empty($_POST['portfolio_title']) ? $_POST['portfolio_title'] : 'Portfolio';
				  	update_option('portfolio_title',$portfolio_title);
				  	
				  	$portfolio_content = !empty($_POST['portfolio_content']) ? $_POST['portfolio_content'] : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
				  	update_option('portfolio_content',$portfolio_content);
				  	
				  	$portfolio_post_count = !empty($_POST['portfolio_post_count']) ? $_POST['portfolio_post_count'] : '8';
				  	update_option('portfolio_post_count',$portfolio_post_count);
				  	
				  	$portfolio_layout = !empty($_POST['portfolio_layout']) ? $_POST['portfolio_layout'] : '2col';
				  	update_option('portfolio_layout', $portfolio_layout);
				  	
				  	$portfolio_order_by= !empty($_POST['portfolio_order_by']) ? $_POST['portfolio_order_by'] : 'title';
				    update_option('portfolio_order_by',$portfolio_order_by);
				    
				    $portfolio_order= !empty($_POST['portfolio_order']) ? $_POST['portfolio_order'] : 'ASC';
				    update_option('portfolio_order',$portfolio_order);
				    
				}
			}
			
			
			
			?>
			
			<form id="portfolio-setting" method="post" action="" enctype="multipart/form-data" >
			
				<h2 style='margin-bottom: 10px;' ><?php _e( 'Portfolio General Settings', 'wpt' ); ?></h2>
					
					<table id="portfolio-table" cellpadding="20">
					 	
					 	<tr>
					 		<th><?php _e('Portfolio Page', 'wpt'); ?><br/>
					 			<i><?php _e('(Assign page for portfolio)' , 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<select name="portfolio-page">
					 			      <option value=""><?php _e('-- select --','wpt') ?></option>
									<?php
									 $pages = get_pages('sort_column=post_title&hierarchical=0');
								     $wpt_sel_portfolio_page_id =get_option('portfolio-page');
									 foreach ( $pages as $page ) {
									   echo '<option value="'.$page->ID.'"'.selected($wpt_sel_portfolio_page_id, $page->ID, false).'>'.$page->post_title.'</option>';
			}?>										
								</select>
					 		</td>
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_portfolio_post_number = get_option('portfolio_post_count');
					 	$portfolio_post_count = !empty($check_portfolio_post_number) ? $check_portfolio_post_number : '8';
					 	?>
					 		<th><?php _e('Number of Posts', 'wpt'); ?><br/>
					 			<i><?php _e('(Specify the number of post to be displayed per page)', 'wpt'); ?></i>
					 		</th>
					 		<td>
					 			<input type="text" id="portfolio_post_count" name="portfolio_post_count" value="<?php _e($portfolio_post_count, 'wpt'); ?>" /><br/><br/>
					 		</td>
					 	</tr>
					 	<tr>
					 	<?php
					 	$check_portfolio_layout = get_option('portfolio_layout');
						$default='';
						if(isset($check_portfolio_layout)){
							if($check_portfolio_layout == ''){
								$default='checked';
							}
						}
						else
						 $default='checked';
					 	?>
						 	<th><?php _e('Portfolio Page Layout','wpt'); ?> <br/>
						 		<i><?php _e('(Layout for portfolio items list. Choose between 2, 3 or 4 column layout)', 'wpt'); ?></i>
						 	</th>
						 	
						 	<td>
								<input type="radio" name="portfolio_layout" value="2col" <?php if (isset ($check_portfolio_layout ) ) checked($check_portfolio_layout, '2col' ); ?> <?php echo $default;?>/>
								<img name="portfolio_layout" src="<?php echo WPT_PLUGIN_URL. 'images/two-column.png'; ?>">
							  	
							  	<input type="radio" name="portfolio_layout" value="3col" <?php if (isset ($check_portfolio_layout ) ) checked($check_portfolio_layout, '3col' ); ?> />      
								<img name="portfolio_layout" src="<?php echo WPT_PLUGIN_URL. 'images/three-column.png'; ?>">
							  	
							  	<input type="radio" name="portfolio_layout" value="4col" <?php if (isset ($check_portfolio_layout ) ) checked($check_portfolio_layout, '4col' ); ?> />
								<img src="<?php echo WPT_PLUGIN_URL. 'images/four-column.png'; ?>">
	  						</td>
					 	</tr>
					 	<tr>
					 		<th><?php _e('Order By', 'wpt'); ?><br/>
					 			<i><?php _e('(Portfolio item order by column )', 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<select name="portfolio_order_by">
									<?php
										$wpt_curr_sel_orderby_val=get_option('portfolio_order_by');
										$wpt_orderby=array('menu_order'=>'Manual order','date'=>'Date', 'title'=>'Title');
										foreach($wpt_orderby as $wpt_k=>$wpt_v){?>
												<option value="<?php _e($wpt_k,'wpt');?>" <?php selected( $wpt_curr_sel_orderby_val,$wpt_k ,$echo = true);?>><?php _e($wpt_v,'wpt');?></option>										
									<?php } ?>
								
								</select>
					 		</td>
					 	</tr>
					 	<tr>
					 		<th><?php _e('Order', 'wpt'); ?><br/>
					 			<i><?php _e('(Portfolio items order)' , 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<select name="portfolio_order">
									<?php
										$wpt_curr_sel_order_val=get_option('portfolio_order');
										$wpt_order = array('ASC' => 'Ascending','DESC' => 'Descening');
										foreach($wpt_order as $wpt_k=>$wpt_v){?>
												<option value="<?php _e($wpt_k,'wpt');?>" <?php selected( $wpt_curr_sel_order_val,$wpt_k ,$echo = true);?>><?php _e($wpt_v,'wpt');?></option>										
									<?php } ?>
									
								</select>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 		<th>
					 			<h2><?php _e('To display Title and Content for Portfolio Shortcode','wpt'); ?></h2>
					 		</th>
					 		
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_portfolio_title = get_option('portfolio_title');
					 	$portfolio_title = !empty($check_portfolio_title) ? $check_portfolio_title : 'Portfolio';
					 	?>
					 		<th><?php _e('Title  :','wpt');?><br/>
					 			<i><?php _e('(Specify the title to be displayed)', 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<input type="text" id="portfolio_title" name="portfolio_title" value="<?php _e( $portfolio_title, 'wpt'); ?>" /><br/><br/>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_portfolio_content = get_option('portfolio_content');
					 	$portfolio_content = !empty($check_portfolio_content) ? $check_portfolio_content : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
					 	?>
					 		<th><?php _e('Content :', 'wpt'); ?><br/>
					 			<i><?php _e('(Specify the content to be displayed)','wpt'); ?></i>
					 		</th>
					 		<td>
					 			<textarea rows="7" cols="49" style="resize:none" id="portfolio_content" name="portfolio_content" ><?php _e($portfolio_content,'wpt'); ?>									
					 			</textarea><br/><br/>
					 		</td>
					 	</tr>
					</table>
					<?php wp_nonce_field( 'portfolio_general_setting', 'wpt_portfolio_nonce' ); ?>
				    <p class="submit">
				        <input id="wpt-submit" class="button-primary" type="submit" name="update_portfolio_settings" value="<?php _e( 'Save Settings', 'wpt' ) ?>" />
				    </p> 
				    
				    <tr>
				    
				    	<td colspan="3" align="center">
				    	<p><strong><?php _e('Note:','wpt'); ?></strong></p>
				    		<p><?php _e('You can add the portfolio shortcode using [portfolio] in any page.','wpt'); ?></p>
				    		<p><?php _e('Attributes such as count, orderby and order can be passed in the shortcode.','wpt'); ?></p>
				    		<p><?php _e('Eg: [portfolio count="2" orderby ="title" order="asc" ]','wpt'); ?></p>
				    	</td>
				    </tr>
				    
			</form>
			
		<?php	
		}
         
         
        /**
		* Adding meta-box for Portfolio
		*
		* Function Name: portfolio_init_add_metaboxes
		
		**/   
          
        function portfolio_init_add_metaboxes()
        {
            add_meta_box("add_client_meta", "Portfolio Item Option", array( &$this, 'add_portfolioposttype_metaboxes' ), "portfolio", "normal", "low");
            
            add_meta_box("featurepost_meta", "Featured Post", array( &$this, 'add_portfolio_feature_metaboxes' ), "portfolio", "side", "low");
               
            add_meta_box("add_image_meta", "Portfolio Image Item Option", array( &$this, 'add_portfolio_imageposttype_metaboxes' ), "portfolio", "normal", "low");
                    
            wp_enqueue_style( 'portfoliotheme-post-option', WPT_PLUGIN_URL . '/css/portfolio-post-option.css', array( 'portfoliotheme-post-option', 'genericons' ), '20131205' );
                    

        
        }
                     
        function add_portfolioposttype_metaboxes()
        {
            global $post;
            $custom = get_post_custom($post->ID);
            $client = $custom["client"][0];
            $url = $custom["url"][0];
            $url_text = $custom["url_text"][0];
           
            ?>
            <label><?php _e('Client:', 'wpt');?></label>
            <input name="client" value="<?php echo esc_attr($client);?>" />
                      
            <br/>
            <em><?php _e('Project description: Client.', 'wpt'); ?></em>
            <br/>
            <br/>
            
            <label><?php _e('Url Text:', 'wpt');?></label>
            <input name="url_text" value="<?php echo esc_attr($url_text);?>" />
            <br/>
            <em><?php _e('Project description: URL', 'wpt'); ?></em>
            <br/>
            <br/>
            
            <label>URL:</label>
            <input name="url" value="<?php echo esc_url($url);?>" />
            <br/>
            <em><?php _e('Project description: Eg:http://www.websitename.com.', 'wpt'); ?></em>
            
			 <?php $pf_nonce = wp_create_nonce( 'porftolio-nonce' ); ?>
			<input type="hidden" name="pf_wpnonce" value="<?php echo esc_attr($pf_nonce);?>" />
			
			<?php
        } 
        
        function add_portfolio_feature_metaboxes(){
        	global $post;
        	 
        	$prfx_stored_meta = get_post_meta( $post->ID );
        	 ?>
			<label for="featured-checkbox">
	            <input type="checkbox" name="featured-checkbox" id="featured-checkbox" value="yes" <?php if ( isset ( $prfx_stored_meta['featured-checkbox'] ) ) checked( $prfx_stored_meta['featured-checkbox'][0], 'yes' ); ?> />
	            <?php _e( 'Featured Post', 'wpt' )?>
	        </label>
  
           
		<?php }           
                
        // Adding meta-box for image post type 
                 
        function  add_portfolio_imageposttype_metaboxes() {
                        
            global $post;
            $values = get_post_custom( $post->ID );  
            $imagebox = isset( $values['image_id'] ) ? esc_attr( $values['image_id'][0] ) : "1"; ?>
            
          <p>
			<h3 class="hndle">
			  <a href="javascript:void(0);" class="add button button-primary button-sm" style="float:right;"><?php _e('+ Add More', 'wpt'); ?></a>
			  <span><?php _e('Manage Portfolio Slider Images', 'wpt'); ?></span>
			</h3>
		  </p>
		
		  <div class="portfolio_meta_control">
			<div class="imageDetailsClone">
			<?php 
			  for($i=0;$i<$imagebox;$i++):
					$portfolio_upload_attach_id = isset( $values['imagebox'.$i] ) ? esc_attr( $values['imagebox'.$i][0] ) : "";   
					$portfolio_upload_image_src = wp_get_attachment_image_src($portfolio_upload_attach_id, 'thumbnail');
					
                        
					$portfolioCheckImg = "none";
					if(!empty($portfolio_upload_image_src[0]))
						$portfolioCheckImg = "inline-block";
			   ?>
				<div class="postbox clone imgbox-<?php echo $i;?>">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php _e('Portfolio Image Details', 'wpt'); ?></span></h3>
					<div class="inside" style="margin-left: 20px;">
						<div class="form-field">
							<label for="cover_image"><?php _e('Portfolio Image', 'wpt'); ?></label>
							<div class="cover_image" style="display:<?php echo esc_attr($portfolioCheckImg)?>;">
							  <img src="<?php echo esc_attr($portfolio_upload_image_src[0]); ?>" name="slider_display_cover_image" />
							</div>
							<p><span><i><?php _e('Best image size :', 'wpt'); ?> <strong><?php _e('1600px * 837px', 'wpt'); ?></strong> <?php _e('(upload : JPG, PNG & GIF )', 'wpt');?></i></span></p>
							
							<input type="hidden" size="36" name="slider_upload_image[]" value="<?php echo esc_attr($portfolio_upload_attach_id); ?>" />
							<p>
								<input name="slider_upload_image_button" type="button" value="Upload" class="portfolio_image_issue button button-primary"/>
								<input name="slider_remove_image_button" type="button" value="Remove Image"  width="8%" class="portfolio_remove_issue button button-primary" style="display:<?php echo esc_attr($portfolioCheckImg);?>;">
							</p>
						</div>
					</div>
					<?php if($i > 0):?>
						<div class="hr" style="margin-bottom: 10px;"></div>
						<p style="overflow:hidden; padding-right:10px;">
							<a href="javascript:void(0);" onclick="removebox('<?php echo $i;?>');" class="btn-right button button-remove button-sm"><?php _e('- Remove', 'wpt'); ?></a>
						</p>
					<?php endif;?>
				</div>
			<?php endfor;?>
			</div>
		</div> <!-- #end main div -->
		<input type="hidden" name="image_id" value="<?php echo esc_attr($imagebox);?>" />
            
          <?php 
        } 
                   
                   
        // Function to Save meta-box values
            
        function save_portfolioposttype_values( $post_id ){
        	
          global $post;
        	
          $pf_nonces = $_REQUEST['pf_wpnonce'];
        		
          if(! wp_verify_nonce( $pf_nonces, 'porftolio-nonce' ))
			return;
					
                 
         update_post_meta($post->ID, "client", $_POST["client"]);
         update_post_meta($post->ID, "url_text", $_POST["url_text"]);
         update_post_meta($post->ID, "url", $_POST["url"]);
         
         if( isset( $_POST[ 'featured-checkbox' ] ) ) {
	    	update_post_meta( $post_id, 'featured-checkbox', 'yes' );
	     }else{
	    	update_post_meta( $post_id, 'featured-checkbox', 'no' );
	     }

                 
         // for image box    
	     if( isset( $_POST['image_id'] ) )  
		  update_post_meta( $post_id, 'image_id', $_POST['image_id']);
         
          for($i=0;$i<$_POST['image_id'];$i++){
          // for image
		  if( isset( $_POST['slider_upload_image'][$i] ) )  
			update_post_meta( $post_id, 'imagebox'.$i , $_POST['slider_upload_image'][$i]);
		  }
        }           

		/**
		 * Add Columns to Portfolio Edit Screen
		 * http://wptheming.com/2010/07/column-edit-pages/
		 */
        
        
		function add_portfolio_thumbnail_column( $columns ) {
            unset($columns['taxonomy-portfolio_tag']);

			$column_thumbnail = array( 'thumbnail' => __('Thumbnail','wpt' ) );
            
            $column_client = array( 'client' => __('Client','wpt' ) );
            
			$columns = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, NULL, true );
            $columns = array_slice( $columns, 0, 3, true ) + $column_client + array_slice( $columns, 3, NULL, true );
          
			return $columns;
		}

		function display_portfolio_thumbnail( $column ) {
			global $post;
			switch ( $column ) {
				case 'thumbnail':
				$portfolio_image_thumbnail = get_the_post_thumbnail( $post->ID, 'portfolio-admin-thumbnail' );
					if (!empty($portfolio_image_thumbnail)){
						echo $portfolio_image_thumbnail;
					}
					else {
						echo '<img src="'.WPT_PLUGIN_URL. 'images/no-img-portfolio.jpg'.'" alt="" style="width:100px;height:75px;"/>';
					}
					break;
                    
                case 'client':
					echo get_post_meta($post->ID, 'client', true );
					break;
                
			}
		}
        
        
		/**
		 * Adds taxonomy filters to the portfolio admin page
		 * 
		 */

		function add_taxonomy_filters() {
			global $typenow;
            
			// An array of all the taxonomies you want to display. Use the taxonomy name or slug
			$taxonomies = array( 'portfolio-types');

			// must set this to the post type you want the filter(s) displayed on
			if ( $typenow == 'portfolio' ) {

				foreach ( $taxonomies as $tax_slug ) {
					$current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
					$tax_obj = get_taxonomy( $tax_slug );
                    
					$tax_name = $tax_obj->labels->name;
                    
					$terms = get_terms($tax_slug);
					if ( count( $terms ) > 0) {
						echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
						echo "<option value=''>$tax_name</option>";
						foreach ( $terms as $term ) {
                           
							echo '<option value=' . $term->slug, $current_tax_slug == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
						}
						echo "</select>";
					}
				}
			}
		}
           // Add custom field filter on admin page
           
        function portfolio_admin_posts_filter_data( $query) {
               global $pagenow;
                    
	                    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['portfolio_client_filter_articles']) && !empty($_GET['portfolio_client_filter_articles'])) {
	                        
	                        $query->query_vars['meta_value'] = $_GET['portfolio_client_filter_articles'] ;
	                   
	                    }
                    }
                   
                    
        function portfolio_admin_posts_filter_restrict_manage_posts() {
                    
              global $wpdb, $typenow,$wp_query;
					
					$clients=$wpdb->get_col("
						SELECT meta.meta_value FROM ".$wpdb->postmeta." as meta 
						LEFT JOIN ".$wpdb->posts." AS posts ON (posts.ID=meta.post_id) 
						WHERE meta.meta_key = 'client' 
						AND posts.post_type = 'portfolio'
						ORDER BY meta.meta_value 
					");				
                    if ($typenow=='portfolio'){
                    ?>
					    <select name="portfolio_client_filter_articles" id="client">
					        <option value=""><?php _e( 'Show all client', 'wpt'  ); ?></option>
					        <?php foreach ($clients as $client) { ?>
					        <option value="<?php echo esc_attr( $client ); ?>" <?php if(isset($_GET['portfolio_client_filter_articles']) && !empty($_GET['portfolio_client_filter_articles']) ) selected($_GET['portfolio_client_filter_articles'], $client); ?>>
							<?php echo esc_attr( $client ); ?>
					        </option>
					        <?php } ?>
					    </select>
					    <?php
                    }
                    } 
              
		/**
		 * Add Portfolio count to "Right Now" Dashboard Widget
		 */

		function add_portfolio_counts() {
			if ( ! post_type_exists( 'portfolio' ) ) {
				return;
			}

			$num_posts = wp_count_posts( 'portfolio' );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( 'Portfolio Item', 'Portfolio Items', intval($num_posts->publish) );
			if ( current_user_can( 'edit_posts' ) ) {
				$output = "<a href='edit.php?post_type=portfolio'>$num $text</a>";
			}
			echo '<li class="post-count portfolio-count">' . $output . '</li>';

			if ($num_posts->pending > 0) {
				$num = number_format_i18n( $num_posts->pending );
				$text = _n( 'Portfolio Item Pending', 'Portfolio Items Pending', intval($num_posts->pending) );
				if ( current_user_can( 'edit_posts' ) ) {
					$num = "<a href='edit.php?post_status=pending&post_type=portfolio'>$num</a>";
				}
				echo '<li class="post-count portfolio-count">' . $output . '</li>';
			}
		}

		/**
		 * Displays the custom post type icon in the dashboard
		 */

		function portfolio_icon() { ?>
        <style type="text/css" media="screen">
           .portfolio-count a:before{content:"\f322"!important}
        </style>
		<?php }

	}
	new Portfolio_Post_Type;
endif;
?>