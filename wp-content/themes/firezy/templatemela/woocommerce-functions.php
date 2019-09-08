<?php 
// Add woocommerce support theme
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
// Disables woocommerce style
add_filter( 'woocommerce_enqueue_styles', '__return_false' );
// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment'); 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;	
	ob_start();	
	?>
	<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('View your shopping cart', 'firezy'); ?>">
														<span class="cart-qty"><?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'firezy'), $woocommerce->cart->cart_contents_count);?></span>
														<div class="cart-label"><?php echo esc_html_e('My Cart','firezy'); ?></div>
														<div class="item-total"><?php echo translate($woocommerce->cart->get_cart_total()); ?> </div>
													</a>
<?php	
	$fragments['a.cart-contents'] = ob_get_clean();	
	return $fragments;	
}

// Change the breadcrumb delimeter from '/' to '>'
add_filter( 'woocommerce_breadcrumb_defaults', 'templatemela_change_breadcrumb_delimiter' );
function templatemela_change_breadcrumb_delimiter( $defaults ) {
$defaults['delimiter'] = ' &raquo; ';
$defaults['before'] = '<span>';
$defaults['after'] = '</span>';
return $defaults;
}
?>