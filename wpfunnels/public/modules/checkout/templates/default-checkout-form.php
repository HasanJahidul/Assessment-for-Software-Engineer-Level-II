<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$hide_section_settings = \WPFunnels\Wpfnl_functions::get_checkout_section_heading_settings( 'order' , get_the_ID() );
$billing_active_cls ='';
	if( is_user_logged_in() ){
		$billing_active_cls ='current';
	}else {
		if( 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ){
			$billing_active_cls ='current';
		}else{
			$billing_active_cls ='';
		}
	}


	$get_checkout_fields = get_post_meta(get_the_ID(),'wpfnl_checkout_additional_fields',true);
	$is_order_comments_enable = isset($get_checkout_fields['order_comments']['enable']) ? $get_checkout_fields['order_comments']['enable'] : true;
?>
<?php if( class_exists( '\WPFunnels\Wpfnl_functions' ) && \WPFunnels\Wpfnl_functions::is_wpfnl_pro_activated() && isset( $_SESSION[ 'checkout_layout' ] ) && ( 'wpfnl-express-checkout' === $_SESSION[ 'checkout_layout' ] || 'wpfnl-multistep' === $_SESSION[ 'checkout_layout' ] || 'wpfnl-2-step' === $_SESSION[ 'checkout_layout' ] )){?>
<ul class="wpfnl-multistep-wizard">
	<?php  if( ! is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ){ ?>
		<li class="login current">
			<button type="button" data-target="login">
				<span class="step-icon">
					<?php include WPFNL_DIR.'public/assets/icons/icon-login.php'; ?>
				</span>
				<span class="step-title">
					<?php  esc_html_e( 'Login', 'wpfnl' ); ?>
				</span>
			</button>
		</li>
	<?php } ?>

	<?php if( is_user_logged_in() ){ ?>
		<li class="billing <?php echo $billing_active_cls; ?>">
			<button type="button" data-target="billing">
				<span class="step-icon">
					<?php include WPFNL_DIR.'public/assets/icons/icon-billing.php'; ?>
				</span>
				<span class="step-title">
					<?php esc_html_e( 'Billing', 'wpfnl' );  ?>
				</span>
			</button>
		</li>
		<?php if( $is_order_comments_enable ||  WC()->cart->needs_shipping_address()) { ?>
			<li class="shipping">
				<button type="button" data-target="shipping">
				<span class="step-icon">
					<?php include WPFNL_DIR.'public/assets/icons/icon-shipping.php'; ?>
				</span>
					<?php if( WC()->cart->needs_shipping_address() ){ ?>
						<span class="step-title">
						<?php esc_html_e( 'Shipping', 'wpfnl' ); ?>
					</span>
					<?php }else{ ?>
							<span class="step-title">
								<?php esc_html_e( 'Additional Information', 'wpfnl' ); ?>
							</span>
					<?php } ?>
				</button>
			</li>
		<?php } ?>


		<li class="order-review">
			<button type="button" data-target="order-review">
				<span class="step-icon">
					<?php include WPFNL_DIR.'public/assets/icons/icon-order-review.php'; ?>
				</span>
				<span class="step-title">
					<?php esc_html_e( 'Your order', 'wpfnl' ); ?>
				</span>
			</button>
		</li>
	<?php }else{

		if( ! $checkout->is_registration_required() || 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) || $checkout->is_registration_enabled() ){
		?>
			<li class="billing <?php echo $billing_active_cls; ?>">
				<button type="button" data-target="billing">
					<span class="step-icon">
						<?php include WPFNL_DIR.'public/assets/icons/icon-billing.php'; ?>
					</span>
					<span class="step-title">
						<?php esc_html_e( 'Billing', 'wpfnl' );  ?>
					</span>
				</button>
			</li>
			<?php if( $is_order_comments_enable ||  WC()->cart->needs_shipping_address()) { ?>
			<li class="shipping">
				<button type="button" data-target="shipping">
					<span class="step-icon">
						<?php include WPFNL_DIR.'public/assets/icons/icon-shipping.php'; ?>
					</span>
					<?php if( WC()->cart->needs_shipping_address() ){ ?>
						<span class="step-title">
							<?php esc_html_e( 'Shipping', 'wpfnl' ); ?>
						</span>
					<?php }else{ ?>
						<span class="step-title">
							<?php esc_html_e( 'Additional Information', 'wpfnl' ); ?>
						</span>
					<?php } ?>
				</button>
			</li>
			<?php } ?>
			<li class="order-review">
				<button type="button" data-target="order-review">
					<span class="step-icon">
						<?php include WPFNL_DIR.'public/assets/icons/icon-order-review.php'; ?>
					</span>
					<span class="step-title">
						<?php esc_html_e( 'Your order', 'wpfnl' ); ?>
					</span>
				</button>
			</li>
		<?php
		}
	}
	?>

</ul>
<?php  } ?>

<?php
do_action( 'woocommerce_before_checkout_form', $checkout );


// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'wpfnl' ) ) );
	return;
}

$loged_in_cls = '';
$create_acc_field = '';
if( ! is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ){
	$loged_in_cls = ' user-not-logedin ';
}

if( ! is_user_logged_in() && 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) && $checkout->is_registration_required() ){
	$create_acc_field = ' no-create-acc-field ';
}

?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout <?php echo $loged_in_cls.' '.$create_acc_field; ?>" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="col2-set" id="customer_details">
				<?php 
					/**
					 * Fires before customer details in checkout page
					 * @since 2.8.21
					 */
					do_action( 'wpfunnels/before_customer_details' ); 
				?>
				<div class="col-1" id="wpfnl_checkout_billing">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					
				</div>

				<div class="col-2" id="wpfnl_checkout_shipping">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
				<?php 
					/**
					 * Fires after customer details in checkout page
					 * @since 2.8.21
					 */
					do_action( 'wpfunnels/after_customer_details' ); 
				?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php if( !$hide_section_settings || ( isset($hide_section_settings['custom_heading']) && $hide_section_settings['custom_heading'] != '' )  ) { ?>
				<h3 id="order_review_heading">
					<?php
						if( isset($hide_section_settings['custom_heading']) && $hide_section_settings['custom_heading'] != '' ){
							esc_html_e( $hide_section_settings['custom_heading'] );
						}else{
							esc_html_e( 'Your order', 'wpfnl' );
						}
					?>
				</h3>
			<?php } ?>

			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	</form>
	<?php if(class_exists( '\WPFunnels\Wpfnl_functions' ) && \WPFunnels\Wpfnl_functions::is_wpfnl_pro_activated() && isset( $_SESSION[ 'checkout_layout' ] ) && ( 'wpfnl-express-checkout' === $_SESSION[ 'checkout_layout' ] || 'wpfnl-multistep' === $_SESSION[ 'checkout_layout' ] || 'wpfnl-2-step' === $_SESSION[ 'checkout_layout' ] ) ){ ?>
	<div class="wpfnl-multistep-navigation">
		<button type="button" class="previous btn-default" data-target="" disabled><?php echo __( 'Previous', 'wpfnl' ); ?></button>
		<?php  if( !is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ){ ?>
			<button type="button" class="next btn-default" data-target="billing"><?php echo __( 'Next', 'wpfnl' ); ?></button>
		<?php  }else{ ?>
			<button type="button" class="next btn-default" data-target="shipping"><?php echo __( 'Next', 'wpfnl' ); ?></button>
		<?php  } ?>
	</div>
    <?php } ?>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
