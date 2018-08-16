<?php
/**
 * Plugin Name: WooCommerce Backorder CEM Plugin
 * Description: A WooCommerce Backorder CEM Plugin.
 * Version: 1.4.0
 * Author: Webethics
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

class iWC_Orderby_Stock_Status
{
public function __construct()
{
// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
add_filter('posts_clauses', array($this, 'order_by_stock_status'), 2000);
}
}
public function order_by_stock_status($posts_clauses)
{
global $wpdb;
// only change query on WooCommerce loops
if (is_woocommerce() && (is_shop() || is_product_category() || is_product_tag())) {
$posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
$posts_clauses['orderby'] = " istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
$posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];
}
return $posts_clauses;
}
}
new iWC_Orderby_Stock_Status;
class WC_Settings_Tab_Demo {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_backorder', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_backorder', __CLASS__ . '::update_settings' );
		wp_register_style('backorder_css'   , plugins_url('/assets/css/woocommerce-backorder-cem.css'     , __FILE__));
		wp_register_script('backorder_js'   , plugins_url('/assets/js/woocommerce-backorder-cem.js' , __FILE__) ,array('jquery'));   
		wp_enqueue_style('backorder_css');
		wp_localize_script( 'backorder_js', 'backorder_select_params', array(
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
		) );
		wp_enqueue_script('backorder_js');
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_backorder'] = __( 'Backorder', 'woocommerce-settings-tab-backorder' );
		wp_enqueue_style( 'woocommerce_admin_styles', plugins_url( '/woocommerce-backorder-cem/assets/css/woocommerce-backorder-cem.css', WC_PLUGIN_FILE ) ); 
        return $settings_tabs;
    }
    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }
    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
	 
	 
	public function get_settings( $current_section = '' ) {
		$settings = array();

		if ( '' === $current_section ) {
			$settings = apply_filters( 'woocommerce_tab_backorder_settings', array(

				array(
					'title' => __( 'In Stock Status Settings', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'wc_settings_tab_backorder_section_title',
				),

				array(
					'title'         => __( 'Label', 'woocommerce' ),
					'id'            => 'woocommerce_demo_instock_title',
					'type'          => 'text',
					'desc_tip'      => __( 'Enter label to be shown in frontent for in stock products.', 'woocommerce' ),
				),


				array(
					'title'         => __( 'Description', 'woocommerce' ),
					'desc_tip'      => __( 'Enter Description to be shown in frontent for in stock products.', 'woocommerce' ),
					'id'            => 'woocommerce_demo_instock_description',
					'type'          => 'text',
				),
				array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_backorder_section_title',
				),

				array(
					'title' => __( 'Out Of Stock Status Settings', 'woocommerce' ),
					'desc'  => __( '', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'wc_settings_tab_backorder_outofstock_section_title',
				),

				array(
					'title'    => __( 'Label', 'woocommerce' ),
					'id'       => 'woocommerce_out_of_stock_label_id',
					'type'     => 'text',
					'desc_tip' => 'Enter label to be shown in frontent for out of stock products.',
				),

				array(
					'title'    => __( 'Description', 'woocommerce' ),
					'id'       => 'woocommerce_out_of_stock_desc_id',
					'type'     => 'text',
					'default'  => '',
					'desc_tip' => 'Enter Description to be shown in frontent for out of stock products.',
				),

				array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_backorder_outofstock_section_title',
				),

				array( 'title' => __( 'Backorder Status Settings', 'woocommerce' ), 'type' => 'title', 'desc' => __( '', 'woocommerce' ), 'id' => 'wc_settings_tab_backorder_section_title' ),

				array(
					'title'    => __( 'Label', 'woocommerce' ),
					'desc'     => __( '', 'woocommerce' ),
					'id'       => 'woocommerce_backorder_label_id',
					'type'     => 'text',
					'desc_tip' =>  'Enter label to be shown in frontent for backorder products.',
				),

				array(
					'title'    => __( 'Description', 'woocommerce' ),
					'desc'     => __( '', 'woocommerce' ),
					'id'       => 'woocommerce_backorder_desc_id',
					'type'     => 'text',
					'desc_tip' => 'Enter Description to be shown in frontent for backorder products.',
				),
				array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_backorder_section_title',
				),
				
				
				array(
					'title' => __( 'Discontinued Status Settings', 'woocommerce' ),
					'desc'  => __( '', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'wc_settings_tab_discontinued_section_title',
				),
				
				array(
					'title'    => __( 'Label', 'woocommerce' ),
					'desc'     => __( '', 'woocommerce' ),
					'id'       => 'woocommerce_discontinued_label_id',
					'type'     => 'text',
					'desc_tip' => 'Enter label to be shown in frontent for discontinued products.',
				),

				array(
					'title'    => __( 'Description', 'woocommerce' ),
					'desc'     => __( '', 'woocommerce' ),
					'id'       => 'woocommerce_discontinued_desc_id',
					'type'     => 'text',
					'desc_tip' => 'Enter description to be shown in frontent for discontinued products.',
				),

				

				array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_discontinued_section_title',
				),


			) );

		
		}

		return apply_filters( 'wc_settings_tab_backorder_settings', $settings );
	}
	
 
}
WC_Settings_Tab_Demo::init();



add_action( 'woocommerce_before_add_to_cart_quantity', 'custom_notice_for_backorders_allowed' );
 
function custom_notice_for_backorders_allowed() 
    {
        //instantinate product object
        global $product;
		$value =  get_post_meta( $product->id, '_checkbox', true ); 
if($product->is_type( 'simple' ) ){
       // if ( $product->backorders_allowed() ) 
        if ( $product->backorders_allowed()) 
        
           {
            $backorderlabel = get_option( 'woocommerce_backorder_label_id' );
            $backorderdesc = get_option( 'woocommerce_backorder_desc_id' );
			 if(!empty($backorderlabel)){
				//echo '<span class="backorderstat">'.$backorderlabel.'</span>';
				
				if( ! $product->is_type( 'simple' ) ){
					echo '<span class="backorderstat_productdet" id="variable_txt_instock" style="display:none;">'.$backorderlabel.'</span>';
					
				}else{
					echo '<span class="instocktxt backorderstat_productdet">'.$backorderlabel.'</span>';
				}
				
			 }else{
				//echo '<span class="backorderstat">Backorder</span>'; 
			 }
			 echo '<div class="instockdesc_estimate">'.get_estimated_ship_time().'</div>';	
			// echo '<span class="instockdesc_estimate"><b>Estimated Ship Time:</b> '.get_estimated_ship_time().'</span>';	
			if(!empty($backorderdesc)){
					if( ! $product->is_type( 'simple' ) ){
						echo '<span class="instockdesc" id="variable_desc_instock" style="display:none;">'.$backorderdesc.'</span>';
						
					}else{
						echo '<span class="instockdesc">'.$backorderdesc.'</span>';
					}
				}
		
		
        }
        
    }
	}
 
 
function get_estimated_ship_time(){
	
	global $product;
		if($product->is_type( 'simple' )){
		$product_ID = $product->get_id();
	$variationspostid = get_post_meta($product_ID, '_profile_name_field', true ); 
	$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
	$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
	$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
	$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
	$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
	$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
	$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
	
	$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
	if($value6_n < $curr_date){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
			$count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
	
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$variations['test'] .= 'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		$variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
	}
	}else{
		if($value6_n < $curr_date){
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;
		}
		
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		 $variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
		}
	}
	
		}else{
			
			
		}
		//echo date('Y-m-d',strtotime ("+".$arrival_days+$days." day"));
		//return $arrival_days+$days;
		return $variations['test'];
}
//output after product title
add_action( 'woocommerce_before_add_to_cart_quantity', 'custom_notice_for_backorders_not_allowed' );
 
function custom_notice_for_backorders_not_allowed() 
    {
        global $product;
    if($product->is_type( 'simple' )){    
        //check if backorders are not allowed
        if ( ! $product->backorders_allowed() ) 
           {    
              $instocklabel = get_option( 'woocommerce_demo_instock_title' );
              $instockdesc = get_option( 'woocommerce_demo_instock_description' );
				if(!empty($instocklabel)){
				
					//echo '<span class="instockstat">'.$instocklabel.'</span>';
					if( ! $product->is_type( 'simple' ) ){
						echo '<span class="instockstat_productdet" id="variable_txt_instock" style="display:none;">'.$instocklabel.'</span>';
						
					}else{
						echo '<span class="instockstat_productdet">'.$instocklabel.'</span>';
					}
					
					 
				}else{
				   
				//	echo '<span class="instockstat">In Stock</span>';  
				 
			
				}
				if( ! $product->is_type( 'simple' ) ){
				echo '<div class="instockdesc_estimate"><b>Estimated Ship Time:</b> <span id="estimate_ship_time">'.get_estimated_ship_time().'</span></div>';	
				}
				if(!empty($instockdesc)){
					if( ! $product->is_type( 'simple' ) ){
						echo '<span class="instockdesc" id="variable_desc_instock" style="display:none;">'.$instockdesc.'</span>';
						
					}else{
						echo '<span class="instockdesc">'.$instockdesc.'</span>';
					}
					}
	
          }  
	}
    } 
	
add_action( 'woocommerce_single_product_summary', 'custom_notice_for_out_of_stock' ,40	);	
function custom_notice_for_out_of_stock() 
    {
		 global $product;
		//echo $product->is_in_stock();
			   if (! $product->is_in_stock() ){
					 $outofstocklabel = get_option( 'woocommerce_out_of_stock_label_id' );
						$outofstockdesc = get_option( 'woocommerce_out_of_stock_desc_id' );
				
					if(!empty($outofstocklabel)){
				
					//echo '<span class="outofstockstat">'.$outofstocklabel.'</span>';
					if( ! $product->is_type( 'simple' ) ){
						echo '<span class="outstockstat_productdet" id="variable_txt_instock" style="display:none;">'.$outofstocklabel.'</span>';
						
					}else{
						echo '<span class="outstockstat_productdet">'.$outofstocklabel.'</span>';
					}
					
					 
				}else{
				   
					//echo '<span class="instockstat">Out Of Stock</span>';  
				}
				if(!empty($outofstockdesc)){
					if( ! $product->is_type( 'simple' ) ){
						echo '<span class="instockdesc" id="variable_desc_instock" style="display:none;">'.$outofstockdesc.'</span>';
						
					}else{
						echo '<span class="instockdesc">'.$outofstockdesc.'</span>';
					}
					}
					
				}
	}
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_stock', 10 );
function woocommerce_template_loop_stock() {
    global $product;
		$value =  get_post_meta( $product->id, '_checkbox', true ); 

       // if ( $product->backorders_allowed() ) 
        if ( $value == 'yes') {
	//if($product->backorders_allowed() && $product->is_in_stock()){
		
		 $backorderlabel = get_option( 'woocommerce_backorder_label_id' );
		 if(!empty($backorderlabel)){
				echo '<span class="backorderstat">'.$backorderlabel.'</span>';
		 }else{
				echo '<span class="backorderstat">Backorder</span>';	 
		 }
	}
	
	if($product->is_in_stock() && $value != 'yes'){
		$instocklabel = get_option( 'woocommerce_demo_instock_title' );
		 if(!empty($instocklabel)){
		 echo '<span class="instockstat">'.$instocklabel.'</span>';
		 }else{
		 echo '<span class="instockstat">In Stock</span>';	 
		 }
	}
	
    if (! $product->is_in_stock() ){
		$outofstocklabel = get_option( 'woocommerce_out_of_stock_label_id' );
		 if(!empty($instocklabel)){
        echo '<span class="outofstockstat">'.$outofstocklabel.'</span>';
		 }else{
		 echo '<span class="outofstockstat">Out of stock</span>';	 
		 }
	}
}	

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'profiles',
    array(
      'labels' => array(
        'name' => __( 'Backorder' ),
        'singular_name' => __( 'backorder' )
      ),
      'public' => true,
      'has_archive' => true,
	  'menu_position'         => 80,
    )
  );
}

add_action( 'admin_init', 'remove_cpt_submenus_wpse_95797' );

function remove_cpt_submenus_wpse_95797()
{
    global $submenu;
     unset(
        $submenu['edit.php?post_type=profiles'][10]
    ); 
	$post_type = 'profiles';
    remove_post_type_support( $post_type, 'editor');
}

function backorder_change_post_label() {
    global $menu;
    global $submenu;
    $menu[81][0] = 'Backorder';
	
    $submenu['edit.php?post_type=profiles'][5][0] = 'Profiles';
   
}
add_action( 'admin_menu', 'backorder_change_post_label' );

add_action( 'admin_menu', 'nerfherder_add_submenu_pages' );
function nerfherder_add_submenu_pages() {
	add_submenu_page( 'edit.php?post_type=profiles', __( 'Settings', 'settings' ), __( 'Settings', 'settings' ), 'manage_options', 'profile_show_settings', 'profiles_settings_tab' );
	
}
function profiles_settings_tab(){
	$url = admin_url().'admin.php?page=wc-settings&tab=settings_tab_backorder';
	?>
	 <script>location.href='<?php echo $url;?>';</script>
	<?php
}
function date_range($first, $last, $step ='+2 weeks',$output_format='Y-m-d'){
	$dates=array();
	$current = strtotime($first);
	$last = strtotime($last);
	while($current<= $last){
		$dates[]= date($output_format,$current);
		$current = strtotime($step,$current);
	}
	return $dates;
}
function my_meta_box_add() {
    add_meta_box( 'my-meta-box-id', 'Estimated Ship Time Calculator', 'my_meta_box', 'profiles', 'normal', 'high' );
} add_action( 'add_meta_boxes', 'my_meta_box_add' );


function my_meta_box( $post ) {
	$value = get_post_meta($post->ID, '_wporg_meta_key', true);
	$value1 = get_post_meta($post->ID, '_wparrival_meta_key', true);
	 $value2 = get_post_meta($post->ID, '_wpselweek_meta_key', true);
	 $value3 = get_post_meta($post->ID, '_wpsdatetype_meta_key', true);
	$value4 = get_post_meta($post->ID, '_wpselday_meta_key', true);
	$value5 = get_post_meta($post->ID, '_wpselmonth_meta_key', true);
	 $value6 = get_post_meta($post->ID, '_first_ship_date_meta_key', true);
	 $value6_n = date('Y-m-d',strtotime($value6));
	 $curr_date = date('Y-m-d');
	$now = time(); 
	$your_date = strtotime($value6);
	$datediff = $now - $your_date;

 $daysnum = floor($datediff / (60 * 60 * 24));


	if($value3 == 'Week'){
		if($value6_n < $curr_date){
	 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
	 $count = count($dates_new)-1;
	  $deliver = $dates_new[$count];
		}else{
		 $deliver = $value6_n;	
		}
	if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
		  $daten .= 'Estimated Ship Time: '. date('Y-m-d',$newenddate);
	}else{
		 $daten .= 'Estimated Ship Time: '. date('Y-m-d',strtotime($deliver));
	}
	}else{
		if($value6_n < $curr_date){
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
		 $deliver = $dates_new[$count];
		}else{
		  $deliver = $value6_n;
		}
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		   $daten .= 'Estimated Ship Time: '. date('Y-m-d',$newenddate);
		}else{
			
			 $daten .= 'Estimated Ship Time: '. date('Y-m-d',strtotime($deliver));
		}
		
	}
    ?>
    <p>
		<label for="my_meta_box_post_type">Time between placing order and arrival at warehouse in days: </label>
		<input id ="arrivaldays" name="arrivaldays" type="text" value="<?php echo $value1; ?>"/>
		</p>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script>
			$( function() {
			$( "#selday" ).datepicker();

			} ).on("change", function() {
			var selday = $('#selday').val();

			$('#first_ship_date').val(selday);
			});
		</script>
	
	    <p class="order_days_field">
        <label for="my_meta_box_post_type">Supplier	ordering schedule: </label>
		<div class="order_days_box">
		Every <select id="selweek" name="selweek"><option value="">select</option><?php for($j=1;$j<=6;$j++){ ?><option value="<?php echo $j; ?>" <?php if($j == $value2){ echo 'selected';} ?>><?php echo $j; ?></option><?php } ?></select> <select id="datetype" name="datetype"><option value="">select</option><option value="Month" <?php if($value3 == "Month" ){ echo 'selected';} ?>>Month</option><option value="Week" <?php if($value3 == "Week" ){ echo 'selected';} ?>>Week</option></select> on <input type="text" id="selday" name="selday" value="<?php echo $value4; ?>" style="display:inline-block;">
		<span>format:m/d/Y</span>
		<input type="hidden" name="current_year" id="current_year" value="<?php echo date('Y'); ?>" />
	 
		<input type="hidden" name="first_ship_date" id="first_ship_date" value="<?php echo $value6; ?>" />
	 </div>

    </p>

    <?php   
}	

function wporg_save_postdata($post_id)
{
    if (array_key_exists('my_meta_box_post_type', $_POST)) {
        update_post_meta(
            $post_id,
            '_wporg_meta_key',
            $_POST['my_meta_box_post_type']
        );
    }
	
	if (array_key_exists('arrivaldays', $_POST)) {
        update_post_meta(
            $post_id,
            '_wparrival_meta_key',
            $_POST['arrivaldays']
        );
    }
	if (array_key_exists('first_ship_date', $_POST)) {
        update_post_meta(
            $post_id,
            '_first_ship_date_meta_key',
            $_POST['first_ship_date']
        );
    }
	
	if (array_key_exists('selweek', $_POST)) {
        update_post_meta(
            $post_id,
            '_wpselweek_meta_key',
            $_POST['selweek']
        );
    }
	if (array_key_exists('datetype', $_POST)) {
        update_post_meta(
            $post_id,
            '_wpsdatetype_meta_key',
            $_POST['datetype']
        );
    }
	if (array_key_exists('selday', $_POST)) {
        update_post_meta(
            $post_id,
            '_wpselday_meta_key',
            $_POST['selday']
        );
    }
	if (array_key_exists('selmonth', $_POST)) {
        update_post_meta(
            $post_id,
            '_wpselmonth_meta_key',
            $_POST['selmonth']
        );
    }
	
}
add_action('save_post', 'wporg_save_postdata');


function your_columns_head($defaults) {  

    $new = array();
    $tags = $defaults['my_meta_box_post_type'];  // save the tags column
    unset($defaults['my_meta_box_post_type']);   // remove it from the columns list

    foreach($defaults as $key=>$value) {
        if($key=='date') {  // when we find the date column
           $new['my_meta_box_post_type'] = $tags;  // put the tags column before it
        }    
        $new[$key]=$value;
    }  

    return $new;  
} 
add_filter('manage_posts_columns', 'your_columns_head');  



add_action( 'woocommerce_product_options_inventory_product_data', 'wc_custom_add_custom_fields' );
function wc_custom_add_custom_fields($post) {
	
	 $value =  get_post_meta( get_the_ID(), '_profile_name_field', true ); 
	 
//echo get_post_meta( $post_id , '_wporg_meta_key' , true ); 	
	?>
<p class="form-field _profile_name_field_field">
<label for="product_field_type"><?php _e( 'Backorder Profile', 'woocommerce' ); ?></label>

<?php 	$args = array(
    'post_type' => 'profiles',
    'post_status' => 'publish',
  //  'meta_key' => '_wporg_meta_key',
);
$posts = get_posts($args);

	
 ?>
<select id="_profile_name_field" name="_profile_name_field" >
<option value="">--select--</option>
	<?php foreach($posts as $vals){ ?>
		<option value="<?php echo $vals->ID ; ?> " <?php if($value == $vals->ID){ echo 'selected'; } ?>><?php echo $vals->post_title  ?> </option>
	<?php } ?>
</select> 
</p>
<?php  
$value1 =  get_post_meta((int)$value, '_wporg_meta_key', true ); ?>
<!--p class="form-field _estimated_ship_field_field">
<label for="product_field_type"><?php _e( 'Estimated ship time', 'woocommerce' ); ?></label>

<input id="_estimated_ship_field" class="short" name="_estimated_ship_field" value="<?php echo $value1; ?>" readonly >  (Days)

</p-->
	<?php

} 


add_action( 'woocommerce_process_product_meta', 'wc_custom_save_custom_fields' );
function wc_custom_save_custom_fields( $post_id ) {
	
    
        update_post_meta( $post_id, '_profile_name_field', esc_attr( $_POST['_profile_name_field'] ) );
    
	if ( ! empty( $_POST['_estimated_ship_field'] ) ) {
        update_post_meta( $post_id, '_estimated_ship_field', esc_attr( $_POST['_estimated_ship_field'] ) );
    }
} 


// Add Variation Settings
add_action( 'woocommerce_variation_options_inventory','variation_settings_fields', 10, 3 );
// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
/**
 * Create new fields for variations
 *
*/
 function variation_settings_fields( $loop, $variation_data, $variation ) {
    // Text Field
   ?>
   	<div class="form-row form-row-full downloadable_files">
   <p class="form-field _profile_name_field1[<?php echo $variation->ID; ?>]_field ">
   <?php 	$args = array(
    'post_type' => 'profiles',
    'post_status' => 'publish',
    //'meta_key' => '_wporg_meta_key',
);
$posts = get_posts($args);
$value = get_post_meta( $variation->ID, '_profile_name_field1', true );
?>
		<label for="_profile_name_field1[<?php echo $variation->ID; ?>]">Backorder Profile</label>
		
		<select id="_profile_name_field1[<?php echo $variation->ID; ?>]" name="_profile_name_field1[<?php echo $variation->ID; ?>]">
			<option value="">--select--</option>
			<?php foreach($posts as $vals){ ?>
			<option value="<?php echo $vals->ID ; ?> " <?php if($value == $vals->ID){ echo 'selected'; } ?>><?php echo $vals->post_title  ?> </option>
			<?php } ?>
		</select> </p>
		</div>
   <?php

    // Number Field
 /*    woocommerce_wp_text_input( 
        array( 
		
            'id'          => '_estimated_ship_field1[' . $variation->ID . ']', 
            'label'       => __( 'Estimated ship time', 'woocommerce' ), 
            'desc_tip'    => 'true',
            'description' => __( 'This is a Estimated ship time field.', 'woocommerce' ),
            'value'       => get_post_meta( $variation->ID, '_estimated_ship_field1', true ),
        )
    ); */
    // Hidden field
    woocommerce_wp_hidden_input(
    array( 
        'id'    => '_hidden_field[' . $variation->ID . ']', 
        'value' => 'hidden_value'
        )
    );
} 
/**
 * Save new fields for variations
 *
*/
function save_variation_settings_fields( $post_id ) {
    // Text Field
    $profile_name_field = $_POST['_profile_name_field1'][ $post_id ];
    
        update_post_meta( $post_id, '_profile_name_field1', esc_attr( $profile_name_field ) );
    

    // Number Field1
    $estimated_ship_field = $_POST['_estimated_ship_field1'][ $post_id ];
    if( ! empty( $estimated_ship_field ) ) {
        update_post_meta( $post_id, '_estimated_ship_field1', esc_attr( $estimated_ship_field ) );
    }
  
    // Hidden field
    $hidden = $_POST['_hidden_field'][ $post_id ];
    if( ! empty( $hidden ) ) {
        update_post_meta( $post_id, '_hidden_field', esc_attr( $hidden ) );
    }
} 
	
	
/************* Add sorting by attributes **************/
 
/**
 *  Defines the criteria for sorting with options defined in the method below
 */
add_filter('woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args');
 
function custom_woocommerce_get_catalog_ordering_args( $args ) {
    global $wp_query;
	$default_order =  apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	$orderby = $_GET['orderby'];
	if($default_order == 'stock_status' && $orderby == ''){
	   if ($default_order) {
        switch ($default_order) :
            case 'stock_status' :
                $args['meta_key'] = '_backorders';
               $args['orderby']  = array(
					'meta_value' => 'ASC'
				);
            break;
        endswitch;
    }	
	}
        // Changed the $_SESSION to $_GET
    if (isset($_GET['orderby'])) {
        switch ($_GET['orderby']) :
            case 'stock_status' :
                $args['meta_key'] = '_backorders';
               $args['orderby']  = array(
					'meta_value' => 'ASC'
				);
            break;
        endswitch;
    }

    return $args;
}
 
/**
 *  Adds the sorting options to dropdown list .. The logic/criteria is in the method above
 */
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter('woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby');
 


function custom_woocommerce_catalog_orderby( $sortby ) {
    $sortby['stock_status'] = 'Sort by Stock Status';
    return $sortby;
}
 
/**
 *  Save custom attributes as post's meta data as well so that we can use in sorting and searching
 */
/* add_action( 'save_post', 'save_woocommerce_attr_to_meta' );
function save_woocommerce_attr_to_meta( $post_id ) {
        // Get the attribute_names .. For each element get the index and the name of the attribute
        // Then use the index to get the corresponding submitted value from the attribute_values array.
    foreach( $_REQUEST['attribute_names'] as $index => $value ) {
        update_post_meta( $post_id, $value, $_REQUEST['attribute_values'][$index] );
    }
} */


/************ End of Sorting ***************************/
add_action( 'woocommerce_product_options_general_product_data', 'woocom_general_product_data_custom_field' );


function woocom_general_product_data_custom_field() {
	 woocommerce_wp_checkbox( 
		array( 
		'id' => '_checkbox', 
		'label' => __('Backorder Product', 'woocommerce' ), 
		'description' => __( '', 'woocommerce' ) 
		) 
	);


}

add_action( 'woocommerce_process_product_meta', 'woocom_save_general_proddata_custom_field' );

function woocom_save_general_proddata_custom_field( $post_id ) {
$checkbox = isset( $_POST['_checkbox'] ) ? 'yes' : 'no'; update_post_meta( $post_id, '_checkbox', $checkbox );

}



add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
add_action( 'wp_ajax_nopriv_myaction', 'so_wp_ajax_function' );
function so_wp_ajax_function(){
	echo $value1 =  get_post_meta((int)$_POST['postid'], '_wporg_meta_key', true ); 
	wp_die(); // ajax call must die to avoid trailing 0 in your response
}



add_action( 'wp_ajax_variation_id_time', 'variation_id_time_wp_ajax_function' );
add_action( 'wp_ajax_nopriv_variation_id_time', 'variation_id_time_wp_ajax_function' );
function variation_id_time_wp_ajax_function(){
	//echo $value1 =  get_post_meta((int)$_POST['postid'], '_profile_name_field1', true ); 
	
	$value =  get_post_meta( (int)$_POST['postid'], '_profile_name_field1', true ); 
			$arrival_days = get_post_meta( (int)$value, '_wparrival_meta_key', true );
			$select_type = get_post_meta( (int)$value, '_wpsdatetype_meta_key', true ); 
			$week_num = get_post_meta( (int)$value, '_wpselweek_meta_key', true ); 
			if($select_type == 'Month'){
				$delected_day = get_post_meta( (int)$value, '_wpselday_meta_key', true ); 
				$curr_month = date('m');
				$curr_year = date('Y');
				$selected_date = $curr_year.'-'.$curr_month.'-'.$delected_day;
				$sel_date = date('Y-m-d',strtotime($selected_date));
				$today_date = date('Y-m-d');
				$nextdate = date('Y-m-d', strtotime('+'.$week_num.' month', strtotime($selected_date)));	
				$date1 = new DateTime($today_date);
				$date2 = new DateTime($nextdate);
				$days = $date2->diff($date1)->format("%a");
				echo $date_of_arrival = date('Y-m-d',strtotime ("+".$arrival_days+$days." day"));
			}else if($select_type == 'Week'){
			$weekday = get_post_meta( (int)$value, '_wpselmonth_meta_key', true ); 
			if($week_num == 1){
				 $nextdate = date('Y-m-d', strtotime('next '.$weekday.''));
			}else if($week_num == 2){
				 $nextdate = date('Y-m-d', strtotime('+1 week '.$weekday.''));
			}else if($week_num == 3){
				 $nextdate = date('Y-m-d', strtotime('+2 week '.$weekday.''));
			}else if($week_num == 4){
				 $nextdate = date('Y-m-d', strtotime('+3 week '.$weekday.''));
			}else if($week_num == 5){
				 $nextdate = date('Y-m-d', strtotime('+4 week '.$weekday.''));
			}else if($week_num == 6){
				 $nextdate = date('Y-m-d', strtotime('+5 week '.$weekday.''));
			}
			$curr_date = date('Y-m-d');
			$datediff = $nextdate - $curr_date;

			$date1=date_create($curr_date);
			$date2=date_create($nextdate);
			$diff=date_diff($date1,$date2);
				
			//	$days = $diff->format("%d");
				
			$date11 = new DateTime($curr_date);
			$date21 = new DateTime($nextdate);

			$days = $date21->diff($date11)->format("%a");
			//echo $arrival_days+$days;
			$days_check = date('Y-m-d',strtotime ("+".$arrival_days+$days." day"));
			
			echo $date_of_arrival = date('Y-m-d',strtotime ("+".$arrival_days+$days." day"));
			}
	wp_die(); // ajax call must die to avoid trailing 0 in your response
}



// this is in Order summary. It show Url variable under product name. Same place where Variations are shown.
add_filter( 'woocommerce_get_item_data', 'item_data', 10, 2 );
function item_data( $data, $cart_item ) {
	$backorder = $cart_item['data']->get_backorders();
	if($backorder != 'no'){
	if($cart_item['variation_id'] != 0){
    if ( isset( $cart_item['variation_id'] ) ) {
	// $value2 = get_post_meta( $cart_item['variation_id'], '_estimated_ship_field1', true );
	$variationspostid = get_post_meta($cart_item['variation_id'], '_profile_name_field1', true ); 
	$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
	$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
	$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
	$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
	$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
	$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
	$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
		$count = count($dates_new)-1;
		$deliver = $dates_new[$count];
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
			$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		
		$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	}
	}else{
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
		 $deliver = $dates_new[$count];
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
		
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
			
		}
	}
	
	
			
			 
		
       
    }
	}else{
	if ( isset( $cart_item['product_id'] ) ) {	
		$product_ID = $cart_item['product_id'];
		$variationspostid = get_post_meta($product_ID, '_profile_name_field', true ); 
		$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
		$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
		$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
		$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
		$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
		$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
		$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
		
	$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
		$count = count($dates_new)-1;
		$deliver = $dates_new[$count];
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
			$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		
		$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	}
	}else{
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
		 $deliver = $dates_new[$count];
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
		
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$data['estimate_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
			
		}
	}
	
	
	
	
		
	
        
	}	
		
	}}
	
    return $data;
	
	
}

add_filter('woocommerce_get_cart_item_from_session','wc_get_cart_item_from_session',10,2);

function wc_get_cart_item_from_session($cart_item, $values) {
$backorder = $cart_item['data']->get_backorders();
	if($backorder != 'no'){
	if($cart_item['variation_id'] != 0){
    if ( isset( $cart_item['variation_id'] ) ) {
		 	$variationspostid = get_post_meta($cart_item['variation_id'], '_profile_name_field1', true ); 
	$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
	$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
	$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
	$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
	$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
	$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
	$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
		if($value6_n < $curr_date){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
			$count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
	
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
			$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		
		$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	}
	}else{
		if($value6_n < $curr_date){
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
		 $deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
		
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
			
		}
	}
			
	
    }
	}else{
	if ( isset( $cart_item['product_id'] ) ) {
		
			$product_ID = $cart_item['product_id'];
		$variationspostid = get_post_meta($product_ID, '_profile_name_field', true ); 
		$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
		$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
		$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
		$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
		$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
		$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
		$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
		
	$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
		if($value6_n < $curr_date){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
			$count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
		
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
			$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		
		$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
	}
	}else{
		if($value6_n < $curr_date){
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
		 $deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
		
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$cart_item['estimated_ship_time'] = array('name' => 'Estimated Ship Time', 'value' =>  date('Y-m-d',$newenddatefinal));
			
		}
	}
	
	
	

	}	
		
	}}
    return $cart_item;
}
add_action('woocommerce_add_order_item_meta', 'estimate_wc_order_item_meta', 10, 2);
function estimate_wc_order_item_meta($item_id, $cart_item) {
	global $woocommerce,$wpdb;

        woocommerce_add_order_item_meta( $item_id, 'Estimated Ship Time', $cart_item['estimated_ship_time']['value'] );


}


add_action('woocommerce_review_order_before_payment', 'customise_checkout_field');
 
function customise_checkout_field($checkout)
{
	
	
	$check_backorder = array();
	    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $item = $cart_item['data'];
        if(!empty($item)){
            $product = new WC_product($item->id);
          array_push($check_backorder,$product->get_backorders());
        }
    }

	if(in_array('notify',$check_backorder) || in_array('yes',$check_backorder)){
			echo '<div id="customise_checkout_field">You have both in stock and backordered products in your cart. How would you like us to ship them?';
	woocommerce_form_field('customised_field_name', array(
		'type' => 'radio',
		'class' => array(
			'my-field-class form-row-wide'
		) ,
		'options' => array( 'Wait until all products are in stock and ship together' => __( 'Wait until all products are in stock and ship together', 'woocommerce' ), 'Ship in stock products now' => __( 'Ship in stock products now', 'woocommerce' )) ,
		'required' => true,
	) , 'Wait until all products are in stock and ship together');
	echo '</div>';
	}

}

add_action('woocommerce_checkout_process', 'customise_checkout_field_process');

 
add_action('woocommerce_checkout_update_order_meta', 'customise_checkout_field_update_order_meta');
 
function customise_checkout_field_update_order_meta($order_id)
{
	
	if (!empty($_POST['customised_field_name'])) {
		update_post_meta($order_id, 'You have both in stock and backordered products in your cart. How would you like
us to ship them?', sanitize_text_field($_POST['customised_field_name']));
	}
	if (!empty($_POST['customised_field_name'])) {
		
		

$text = apply_filters('the_excerpt', get_post_field('post_excerpt', $order_id));


	 $my_post = array(
      'ID'           => $order_id,
      'post_excerpt'   => strip_tags($text. '<b>You have both in stock and backordered products in your cart. How would you like
us to ship them?'.'</b></br>     '.sanitize_text_field($_POST['customised_field_name'])),
  );
wp_update_post( $my_post );
	}
}	

add_filter('woocommerce_email_order_customised_field_name', 'my_custom_order_customised_field_name');

function my_custom_order_customised_field_name( $keys ) {
     $keys[] = 'You have both in stock and backordered products in your cart. How would you like
us to ship them?'; 
     return $keys;
}

// remove default sorting dropdown
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Add New Variation Settings
add_filter( 'woocommerce_available_variation', 'load_variation_settings_fields' );
/**
 * Add custom fields for variations
 *
*/
function load_variation_settings_fields( $variations ) {
	

	$variations['text_field'] = get_post_meta( $variations[ 'variation_id' ], '_backorders', true );
	$variations['stock_status'] = get_post_meta( $variations[ 'variation_id' ], '_stock_status', true );
	if($variations['text_field'] == 'notify' || $variations['text_field'] == 'yes'){
		$variationspostid = get_post_meta($variations[ 'variation_id' ], '_profile_name_field1', true ); 
	$value = get_post_meta((int)$variationspostid, '_wporg_meta_key', true);
	$value1 = get_post_meta((int)$variationspostid, '_wparrival_meta_key', true);
	$value2 = get_post_meta((int)$variationspostid, '_wpselweek_meta_key', true);
	$value3 = get_post_meta((int)$variationspostid, '_wpsdatetype_meta_key', true);
	$value4 = get_post_meta((int)$variationspostid, '_wpselday_meta_key', true);
	$value5 = get_post_meta((int)$variationspostid, '_wpselmonth_meta_key', true);
	$value6 = get_post_meta((int)$variationspostid, '_first_ship_date_meta_key', true);
	
	$value6_n = date('Y-m-d',strtotime($value6));
	$curr_date = date('Y-m-d');
	if($value3 == 'Week'){
	if($value6_n < $curr_date){
		$dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." weeks",$output_format='Y-m-d');
			$count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;	
		}
	
		if($deliver < $curr_date){
			$newenddate = date(strtotime($deliver . "+".$value2." weeks"));
			$newenddatefinal1 = date('Y-m-d',$newenddate);
			$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		$variations['test'] .= 'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
	}else{
		$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
		$variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
	}
	}else{
		if($value6_n < $curr_date){
		 $dates_new = date_range($value6_n,$curr_date, $step ="+".$value2." months",$output_format='Y-m-d');
		 $count = count($dates_new)-1;
			$deliver = $dates_new[$count];
		}else{
			$deliver = $value6_n;
		}
		
		if($deliver < $curr_date){
		$newenddate = date(strtotime($deliver . "+".$value2." months"));
		$newenddatefinal1 = date('Y-m-d',$newenddate);
		$newenddatefinal = date(strtotime($newenddatefinal1 . "+".$value1." days"));
		 $variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
		}else{
			$newenddatefinal = date(strtotime($deliver . "+".$value1." days"));
			$variations['test'] .=  'Estimated Ship Time: '. date('Y-m-d',$newenddatefinal);
		}
	}
	

	
		//$variations['test'] = $variations[ 'variation_id' ];
		$backorderlabel = get_option( 'woocommerce_backorder_label_id' );
		$backorderdesc = get_option( 'woocommerce_backorder_desc_id' );
			 if(!empty($backorderlabel)){
				//echo '<span class="backorderstat">'.$backorderlabel.'</span>';
					$variations['availablity'] = '<span class="backorderstat_productdet" id="variable_txt_instock">'.$backorderlabel.'</span>';
			 }else{
				$variations['availablity']= '<span class="backorderstat">Backorder</span>'; 
			 }
			 if(!empty($backorderdesc)){
						$variations['desc'] = '<span class="instockdesc" id="variable_desc_instock">'.$backorderdesc.'</span>';	
				}
	}
	if($variations['stock_status'] == 'outofstock' && $variations['text_field'] == 'no'){
		$outofstocklabel = get_option( 'woocommerce_out_of_stock_label_id' );
		$outofstockdesc = get_option( 'woocommerce_out_of_stock_desc_id' );
				
		if(!empty($outofstocklabel)){
			$variations['availablity'] = '<span class="outstockstat_productdet" id="variable_txt_instock" >'.$outofstocklabel.'</span>';
		}else{
		   $variations['availablity'] = '<span class="instockstat">Out Of Stock</span>';  
			}
		if(!empty($outofstockdesc)){
			$variations['desc'] = '<span class="instockdesc" id="variable_desc_instock">'.$outofstockdesc.'</span>';
		}
				
	}
	if($variations['stock_status'] == 'instock' && $variations['text_field'] == 'no'){
	 $instocklabel = get_option( 'woocommerce_demo_instock_title' );
	  $instockdesc = get_option( 'woocommerce_demo_instock_description' );
				if(!empty($instocklabel)){
			
						$variations['availablity'] = '<span class="instockstat_productdet" id="variable_txt_instock" >'.$instocklabel.'</span>';
		 
				}else{
				   
					echo '<span class="instockstat">In Stock</span>';  
				 
			
				}
			
				if(!empty($instockdesc)){
					
						$variations['desc'] = '<span class="instockdesc" id="variable_desc_instock" >'.$instockdesc.'</span>';
						
				}
	}
	return $variations;
}

function lab_flavor_plugin_path() {
  // gets the absolute path to this plugin directory
   return untrailingslashit( plugin_dir_path( __FILE__ ) );
 }

add_filter( 'woocommerce_locate_template', 'lab_flavor_woocommerce_locate_template', 10, 3 );
function lab_flavor_woocommerce_locate_template( $template, $template_name, $template_path ) {
   global $woocommerce;
   $_template = $template;
   if ( ! $template_path ) $template_path = $woocommerce->template_url;
   $plugin_path  = lab_flavor_plugin_path() . '/woocommerce/';
 
  // Look within passed path within the theme - this is priority
   $template = locate_template(
     array(
       $template_path . $template_name,
       $template_name
     )
   );
 
  // Modification: Get the template from this plugin, if it exists
  if ( ! $template && file_exists( $plugin_path .'templates/'. $template_name ) )
    $template = $plugin_path .'templates/'. $template_name;

  // Use default template
   if ( ! $template ) 
    $template = $_template;
 
  // Return what we found
  return $template;
}


}

