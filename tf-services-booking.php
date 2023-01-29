<?php
/*
Plugin Name: TF Service Booking
Plugin URI: https://wordpress.org/plugins/
Description: TF Service Booking
Version: 0.1.1
Author: Sadekur Rahman
Author URI: 
License: GPLv2 or later
Text Domain: tf-services-booking
*/

define('tfsb_plugin_directory_path', plugin_dir_path(__FILE__));
define('TFSBCSS', plugins_url ( 'css/main.css', __FILE__));
define('TFSBJS', plugins_url ( 'js/main.js', __FILE__));
/*
* Frontend style/js
*/
function tfsb_enqueue_scripts() {
	wp_enqueue_style( 'tfsb-style', TFSBCSS, '', time(), false );

	wp_enqueue_script( 'tfsb-js', TFSBJS, '', time(), true );
	wp_localize_script('tfsb-js','TFSB',['ajax_url' => admin_url('admin-ajax.php'), 'post_count' => ceil(wp_count_posts('tfsb')->publish / 2)]);
}
add_action( 'wp_enqueue_scripts', 'tfsb_enqueue_scripts' );

/*
Custom post type
*/
function tfsb_register_post_types(){
	register_post_type('tfsb',[
		'labels' => [
			'name' 						=> 'TF Service Booking',
			'singular_name' 			=> 'TF Service Booking',
			'menu_name' 				=> 'TF Services',
			"add_new_item" 				=> 'Add New TF Service',
			"add_new" 					=> "Add New TF Service",
			"edit_item" 				=> 'Edit TF Service',
			"new_item" 					=> 'New TF Service',
			"view_item" 				=> 'View TF Service',
			"view_items" 				=> 'View TF Services',
			"search_items" 				=> 'Search TF Service',
			"not_found" 				=> 'Not TF Service Found',
			"not_found_in_trash" 		=> 'No TF Service Found in Trush',
			"filter_items_list" 		=> 'Filter TF Service List',
			"items_list_navigation" 	=> 'TF Service list navigation',
			"items_list" 				=> 'TF Service list',
			"item_reverted_to_draft" 	=> 'TF Service reverted to draft'
		],
		'public' => true,
		'publicly_queryable' =>true,
		'menu_icon' => 'dashicons-format-video',
		'has_archive' =>true,
		'rewrite' => ['slug' => 'tfsb'],
		'supports' => [
			'title',
			'editor',
			'thumbnail',
		],
	]
);
}
add_action('init','tfsb_register_post_types');
/*
Shortcode for view
*/
function tfsb_shortcode_func(){
	ob_start();
	$variables = [];
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args =[
		'post_type' => 'tfsb',
		'posts_per_page' => 5,
		'paged' => $paged
	];
	$types = new WP_Query($args); ?>
		<main class="main-fun" style="">
		<!-- Search Form -->
		<div class="topnav">
			<div class="search-container">
				<form id="tf-services-search-form">
					<input type="text" id="tf-services-search-input" placeholder="Search TF Services">
					<input type="submit" id="tf-services-search-submit" value="Search">
				</form> 
			</div>
		</div>
		<div class="main-cls" style="">
			<div class="xyz_container" style="width: 100%; margin: 0 auto; height: 700px;">
				<br>
				<?php if($types->have_posts()){ ?>
					<div class="js-xyz row">
						<?php while ($types->have_posts()){
							$types->the_post();
							?>
							<div class="column column-4">
								<?php if(has_post_thumbnail()){ ?>
									<img width="500" height="250" src="<?php echo get_the_post_thumbnail_url( get_the_ID(),'full' ); ?>" alt="<?php the_title(); ?>">
								</picture>
							<?php } ?>
							<!-- <p class="description" style="color: red;"><?php the_excerpt(); ?></p> -->
							<h4><?php the_title(); ?></h4>
							<?php
							$variables[ get_the_ID() ] = get_the_title();
								echo "<a href=" . wc_get_cart_url()."?add-to-cart=".get_the_ID(). ">Add To Cart</a>";
							
							?>
						</div>
						<?php
					}
					wp_reset_postdata();
					?>
				</div>
			<?php } ?>
		</div>

	<!--Pagination -->
	<?php
	$total_pages = $types->max_num_pages;
	if ($total_pages > 1){
		$current_page = max(1, get_query_var('paged'));?>
		<div class="pagination-fst">
			<?php
			echo paginate_links(array(
				'base' => get_pagenum_link(1) . '%_%',
				'format' => 'page/%#%',
				'current' => $current_page,
				'total' => $total_pages,
				'prev_text'    => __('« prev'),
				'next_text'    => __('next »'),
			));
		}?>
	</div>	
</div>			
</main>
<?php
$ends = ob_get_clean();
return $ends;
}
add_shortcode('tf_service_result', 'tfsb_shortcode_func');


/*Ajax search called*/
function tfsb_filter_posts(){
	$term = $_POST['term'];
	$args = array(
		'post_type' 	 => 'tfsb',
		'posts_per_page' => 5,
		's'	 			 => $term,
		'paged' 		 => 1
	);
	$types = new WP_Query($args); ?>
		<div class="xyz_container" style="width: 100%; margin: 0 auto; height: 700px;">
			<br>
			<?php if($types->have_posts()){ ?>
				<div class="js-xyz row">
					<?php while ($types->have_posts()){
						$types->the_post();
						?>
						<div class="column column-4">
							<?php if(has_post_thumbnail()){ ?>
								<img width="500" height="250" src="<?php echo get_the_post_thumbnail_url( get_the_ID(),'full' ); ?>" alt="<?php the_title(); ?>">
							</picture>
						<?php } ?>
						<h4><?php the_title(); ?></h4>
						<?php
						echo "<a href=" . wc_get_cart_url()."?add-to-cart=".get_the_ID(). ">Add To Cart</a>";
						?>
					</div>
					<?php
				}
				?>
			</div>
			<?php 
		}else{
		}
		wp_reset_postdata();
		?>
	</div>	
	<?php
	//if ($types>=5) {?>
		<div class="btn__wrapper" style="text-align: center;">
			<button id="view_more" class="view_more">View More</button>
		</div>
		<?php
	//}
	wp_die();
}
add_action('wp_ajax_tf_services_search','tfsb_filter_posts');
add_action('wp_ajax_nopriv_tf_services_search','tfsb_filter_posts');

/*View More Post*/

function view_action() {
	$args = array(
		'post_type' 	 => 'tfsb',
		'paged' 		 => $_POST['page'],
		'posts_per_page' => 5
	);
	$the_liad_query = new WP_Query($args);?>
	<?php if($the_liad_query->have_posts()){ ?>
		<?php while ($the_liad_query->have_posts()){
			$the_liad_query->the_post();
			?>
			<div class="column column-4">
				<?php if(has_post_thumbnail()){ ?>
					<img width="500" height="250" src="<?php echo get_the_post_thumbnail_url( get_the_ID(),'full' ); ?>" alt="<?php the_title(); ?>">
				</picture>
			<?php } ?>
			<h4><?php the_title(); ?></h4>
			<?php
			echo "<a href=" . wc_get_cart_url()."?add-to-cart=".get_the_ID(). ">Add To Cart</a>";
			?>
		</div>
		<?php
	} 
}else{
	echo "No More Post Yet...";
}
wp_reset_postdata();
wp_die();
}
add_action( 'wp_ajax_view_action', 'view_action' );
add_action( 'wp_ajax_nopriv_view_action', 'view_action' );


/*
Page Template Create
*/

function tfsb_page_template(){
	$temp = [];
	$temp['tfsb-template.php'] = 'TFSB Page Template';
	return $temp;
}
function tfsb_register_temp($page_template, $theme, $post){
	$templates = tfsb_page_template();
	foreach ($templates as $key => $value) {
		$page_template[$key] = $value;
	}
	return $page_template;

}
add_filter('theme_page_templates','tfsb_register_temp', 10, 3);


/*add_action( 'wp', 'cxc_woocommerce_add_product_to_cart' );
function cxc_woocommerce_add_product_to_cart() {
	$variables = [];
	$args = array(
		'post_type' 	 => 'tfsb',
		'posts_per_page' => -1
	);
	$products = new WP_Query($args);
	if ($products->have_posts()) {
		while ($products->have_posts()) {
			$products->the_post();
			$variables[ get_the_ID() ] = get_the_title();
			foreach ($variables as $key => $value) {
				print_r($key);
			}
		}
		wp_reset_postdata();
	}
	$product_id = $key;
	WC()->cart->add_to_cart( $product_id ); 
}*/
