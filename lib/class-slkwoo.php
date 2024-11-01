<?php
/**
 * Simple License Key for WooCommerce
 *
 * @package    Simple License Key for WooCommerce
 * @subpackage SlkWoo Main Functions
/*  Copyright (c) 2021- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$slkwoo = new SlkWoo();

/** ==================================================
 * Class Main function
 *
 * @since 1.00
 */
class SlkWoo {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'slkwoo_create_custom_field' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'slkwoo_save_custom_field' ) );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'slkwoo_add_custom_field_item_data' ), 10, 4 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'slkwoo_before_calculate_totals' ), 10, 1 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'slkwoo_cart_item_name' ), 10, 3 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'slkwoo_add_custom_data_to_order' ), 10, 4 );
		add_action( 'woocommerce_thankyou', array( $this, 'custom_woocommerce_auto_complete_order' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10, 1 );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ), 100 );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
	}

	/** ==================================================
	 * Main
	 *
	 * @param int    $product_id  product_id.
	 * @param string $product_name  product_name.
	 * @param object $order  order.
	 * @param int    $expiry_days  expiry_days.
	 * @since 1.00
	 */
	public function slkwoo_func( $product_id, $product_name, $order, $expiry_days ) {

		/* License key */
		$license_key = $this->generate_license_key( 8 );
		/* Expiry secound */
		$expiry_second = $expiry_days * 3600 * 24;

		/* Optional Data */
		$firstname = $order->get_billing_first_name();
		$lastname  = $order->get_billing_last_name();
		$email     = $order->get_billing_email();

		$create_date = wp_date( 'Y-m-d H:i:s' );
		$stamp_date = intval( wp_date( 'U' ) );
		$stamp_expiry = $stamp_date + $expiry_second;
		$expiry_date = wp_date( 'Y-m-d H:i:s', $stamp_expiry );

		$slkwoo_apis = get_option( 'slkapi', array() );
		$slkwoo_apis[] = array(
			'product_id' => $product_id,
			'open_key' => $this->encrypt( $license_key ),
			'date_expiry' => $expiry_date,
			'expiry_stamp' => intval( $stamp_expiry ),
		);
		update_option( 'slkapi', $slkwoo_apis );

		$slkwoo_logs_all = get_option( 'slkwoo', array() );
		$slkwoo_logs_all[] = array(
			'product_id' => $product_id,
			'product_name' => $product_name,
			'passphrase' => get_option( 'slkwoo_passphrase' ),
			'open_key' => $this->encrypt( $license_key ),
			'name' => $firstname . ' ' . $lastname,
			'email' => $email,
			'date_created' => $create_date,
			'date_expiry' => $expiry_date,
			'expiry_stamp' => intval( $stamp_expiry ),
		);
		$slkwoo_logs_all2 = $this->sort_slkwoo_logs( $slkwoo_logs_all );

		/* Slice array */
		$count = 0;
		$log_limit = 100;
		$slkwoo_logs = array();
		foreach ( $slkwoo_logs_all2 as $key => $value ) {
			$slkwoo_logs[ $key ] = $value;
			++$count;
			if ( $log_limit == $count ) {
				break;
			}
		}
		update_option( 'slkwoo', $slkwoo_logs );

		return $license_key;
	}

	/** ==================================================
	 * Sort by date_created
	 *
	 * @param array $slkwoo_logs  slkwoo_logs.
	 * @since 1.00
	 */
	private function sort_slkwoo_logs( $slkwoo_logs ) {

		$date_created = array();
		foreach ( $slkwoo_logs as $key => $value ) {
			$date_created[ $key ] = $value['date_created'];
		}
		array_multisort( $date_created, SORT_DESC, $slkwoo_logs );

		return $slkwoo_logs;
	}

	/** ==================================================
	 * Display the custom checkbox
	 *
	 * @since 1.00
	 */
	public function slkwoo_create_custom_field() {

		global $thepostid;

		woocommerce_wp_checkbox(
			array(
				'id'    => 'slkwoo_addlicense',
				'label' => __( 'Add License Key', 'simple-license-key-for-woocommerce' ),
			)
		);

		if ( get_post_meta( $thepostid, 'slkwoo_expiry', true ) ) {
			$expiry_days = get_post_meta( $thepostid, 'slkwoo_expiry', true );
		} else {
			$expiry_days = 1;
		}

		woocommerce_wp_text_input(
			array(
				'id'                => 'slkwoo_expiry',
				'label'             => __( 'Expiry days', 'simple-license-key-for-woocommerce' ),
				'type'              => 'number',
				'value'             => $expiry_days,
				'custom_attributes' => array(
					'step'  => '1',
					'min'   => '1',
				),
				'desc_tip' => true,
				'description' => __( 'Expiry date of license. This is not the expiration date of the license itself, but the expiration date of the certification. If you have been authenticated before the deadline, the license will continue after the deadline.', 'simple-license-key-for-woocommerce' ),
			)
		);
	}

	/** ==================================================
	 * Save the custom field
	 *
	 * @param int $post_id  post_id.
	 * @since 1.00
	 */
	public function slkwoo_save_custom_field( $post_id ) {

		if ( ! ( isset( $_POST['woocommerce_meta_nonce'] ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
			return false;
		}
		$product = wc_get_product( $post_id );
		if ( isset( $_POST['slkwoo_addlicense'] ) && ! empty( $_POST['slkwoo_addlicense'] ) ) {
			$slkwoo_addlicense = sanitize_text_field( wp_unslash( $_POST['slkwoo_addlicense'] ) );
		} else {
			$slkwoo_addlicense = null;
		}
		$product->update_meta_data( 'slkwoo_addlicense', $slkwoo_addlicense );
		if ( isset( $_POST['slkwoo_expiry'] ) && ! empty( $_POST['slkwoo_expiry'] ) ) {
			$product->update_meta_data( 'slkwoo_expiry', intval( $_POST['slkwoo_expiry'] ) );
		}
		$product->save();
	}

	/** ==================================================
	 * Add the text field as item data to the cart object
	 *
	 * @param array $cart_item_data  Cart item meta data.
	 * @param int   $product_id  Product ID.
	 * @param int   $variation_id  Variation ID.
	 * @param bool  $quantity  Quantity.
	 * @since 1.00
	 */
	public function slkwoo_add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {

		if ( get_post_meta( $product_id, 'slkwoo_addlicense', true ) ) {
			$cart_item_data['slkwoo_license_key'] = __( 'Add License Key', 'simple-license-key-for-woocommerce' );
		}
		if ( get_post_meta( $product_id, 'slkwoo_expiry', true ) ) {
			$cart_item_data['slkwoo_expiry'] = get_post_meta( $product_id, 'slkwoo_expiry', true );
		} else {
			$cart_item_data['slkwoo_expiry'] = 1;
		}

		return $cart_item_data;
	}

	/** ==================================================
	 * Update the price in the cart
	 *
	 * @param object $cart_obj  cart_obj.
	 * @since 1.00
	 */
	public function slkwoo_before_calculate_totals( $cart_obj ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		/* Iterate through each cart item */
		foreach ( $cart_obj->get_cart() as $key => $value ) {
			if ( isset( $value['total_price'] ) ) {
				$price = $value['total_price'];
				$value['data']->set_price( ( $price ) );
			}
		}
	}

	/** ==================================================
	 * Display the custom field value in the cart
	 *
	 * @param string $name  name.
	 * @param array  $cart_item  cart_item.
	 * @param object $cart_item_key  cart_item_key.
	 * @since 1.00
	 */
	public function slkwoo_cart_item_name( $name, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['slkwoo_license_key'] ) ) {
			/* translators: %1$s: Add license key %2$s,%3$d: Expiry days */
			$name .= sprintf(
				'<div>%1$s</div><div>%2$s: %3$d</div>',
				esc_html( $cart_item['slkwoo_license_key'] ),
				esc_html( __( 'Expiry days', 'simple-license-key-for-woocommerce' ) ),
				esc_html( $cart_item['slkwoo_expiry'] )
			);
		}
		return $name;
	}

	/** ==================================================
	 * Add custom field to order object
	 *
	 * @param array  $item  item.
	 * @param object $cart_item_key  cart_item_key.
	 * @param array  $values  values.
	 * @param object $order  order.
	 * @since 1.00
	 */
	public function slkwoo_add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
		foreach ( $item as $cart_item_key => $values ) {
			if ( isset( $values['slkwoo_license_key'] ) ) {
				$license_key = $this->slkwoo_func( $item->get_product_id(), $item->get_name(), $order, $values['slkwoo_expiry'] );
				if ( $license_key ) {
					$item->add_meta_data( __( 'License Key', 'simple-license-key-for-woocommerce' ), $license_key, true );
					$item->add_meta_data( __( 'Expiry days', 'simple-license-key-for-woocommerce' ), $values['slkwoo_expiry'], true );
				}
			}
		}
	}

	/** ==================================================
	 * Auto Complete all WooCommerce orders
	 *
	 * @param int $order_id  order_id.
	 * @since 1.00
	 */
	public function custom_woocommerce_auto_complete_order( $order_id ) {

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		/* No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods. */
		if ( in_array( $order->get_payment_method(), array( 'bacs', 'cod', 'cheque', '' ) ) ) {
			return;
		} else if ( $order->has_status( 'processing' ) ) {
			/* For paid Orders with all others payment methods (paid order status "processing") */
			$order->update_status( 'completed' );
		}
	}

	/** ==================================================
	 * Crypt AES 256
	 *
	 * @param string $data  data.
	 * @return encrypted $data  data.
	 * @since 1.00
	 */
	private function encrypt( $data ) {

		return @openssl_encrypt( $data, 'aes-256-cfb', get_option( 'slkwoo_passphrase' ), 0, openssl_cipher_iv_length( 'aes-256-cfb' ) );
	}

	/** ==================================================
	 * Register Rest API
	 *
	 * @since 1.00
	 */
	public function register_rest() {

		register_rest_route(
			'rf/slk-woo-open-key_api',
			'/token',
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'open_key_get' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'rf/slkwoo-admin_api',
			'/token',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'passphrase_logs_save' ),
				'permission_callback' => array( $this, 'rest_permission' ),
			)
		);
	}

	/** ==================================================
	 * Rest Permission
	 *
	 * @since 1.00
	 */
	public function rest_permission() {

		return current_user_can( 'manage_options' );
	}

	/** ==================================================
	 * Open Key Rest API get
	 *
	 * @since 1.00
	 */
	public function open_key_get() {

		$slkwoo_apis = get_option( 'slkapi' );

		$current_stamp = intval( wp_date( 'U' ) );

		$change_option = false;
		foreach ( $slkwoo_apis as $key => $value ) {
			if ( intval( $value['expiry_stamp'] ) < $current_stamp ) {
				$change_option = true;
				unset( $slkwoo_apis[ $key ] );
			}
		}
		if ( $change_option ) {
			update_option( 'slkapi', $slkwoo_apis );
		}

		return new WP_REST_Response( $slkwoo_apis, 200 );
	}

	/** ==================================================
	 * Rest API save for passphrase and logs
	 *
	 * @param object $request  changed data.
	 * @since 1.00
	 */
	public function passphrase_logs_save( $request ) {

		$args = json_decode( $request->get_body(), true );

		update_option( 'slkwoo_passphrase', $args['text'] );
		update_option( 'slkwoo_passphrase_lock', $args['lock'] );
		update_option( 'slkwoo', $args['logs'] );

		$slkwoo = get_option( 'slkwoo' );

		$slkwoo_apis = array();
		foreach ( $slkwoo as $key => $value ) {
			$date = new DateTime( $value['date_expiry'], wp_timezone() );
			$stamp_expiry = $date->format( 'U' );
			$slkwoo_apis[] = array(
				'product_id' => $value['product_id'],
				'open_key' => $value['open_key'],
				'date_expiry' => $value['date_expiry'],
				'expiry_stamp' => intval( $stamp_expiry ),
			);
		}
		update_option( 'slkapi', $slkwoo_apis );

		return new WP_REST_Response( $args, 200 );
	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		if ( ! get_option( 'slkwoo_passphrase' ) ) {
			update_option( 'slkwoo_passphrase', wp_generate_password() );
		}
		if ( ! get_option( 'slkwoo_passphrase_lock' ) ) {
			update_option( 'slkwoo_passphrase_lock', false );
		}

		if ( get_option( 'slkwoo' ) ) {
			$slkwoo_logs = get_option( 'slkwoo' );
			/* Version 1.02 or earlier */
			$new_slkwoo_logs = array();
			foreach ( $slkwoo_logs as $key => $value ) {
				if ( array_key_exists( 'first_name', $value ) ) {
					$new_slkwoo_logs[] = array(
						'product_id' => $value['product_id'],
						'product_name' => get_the_title( $value['product_id'] ),
						'passphrase' => get_option( 'slkwoo_passphrase' ),
						'open_key' => $value['open_key'],
						'name' => $value['first_name'] . ' ' . $value['last_name'],
						'email' => $value['email'],
						'date_created' => $value['date_created'],
						'date_expiry' => $value['date_expiry'],
						'expiry_stamp' => $value['expiry_stamp'],
					);
					update_option( 'slkwoo', $new_slkwoo_logs );
				}
			}
		}
	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'simple-license-key-for-woocommerce/slkwoo.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=slkwoo_set' ) . '">' . __( 'Settings' ) . '</a>';
		}
		return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'License Key', 'simple-license-key-for-woocommerce' ),
			__( 'License Key', 'simple-license-key-for-woocommerce' ),
			'manage_woocommerce',
			'slkwoo_set',
			array( $this, 'plugin_options' )
		);
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		echo '<div id="slkwooadmin"></div>';
	}

	/** ==================================================
	 * Load scripts
	 *
	 * @param string $hook_suffix  hook_suffix.
	 * @since 1.00
	 */
	public function admin_scripts( $hook_suffix ) {

		if ( 'woocommerce_page_slkwoo_set' !== $hook_suffix ) {
			return;
		}

		$asset_file = include plugin_dir_path( __DIR__ ) . 'guten/dist/slkwoo-admin.asset.php';

		wp_enqueue_style(
			'slkwooadmin',
			plugin_dir_url( __DIR__ ) . 'guten/dist/slkwoo-admin.css',
			array( 'wp-components' ),
			'1.0.0',
		);

		wp_enqueue_script(
			'slkwooadmin',
			plugin_dir_url( __DIR__ ) . 'guten/dist/slkwoo-admin.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$slkwoo_logs = get_option( 'slkwoo', array() );

		wp_localize_script(
			'slkwooadmin',
			'slkwoo_data',
			array(
				'slkwoo_passphrase' => get_option( 'slkwoo_passphrase' ),
				'slkwoo_passphrase_lock' => get_option( 'slkwoo_passphrase_lock' ),
				'settings' => __( 'Settings' ),
				'passphrase_text' => __( 'Common passphrase for Encrypt and Decrypt', 'simple-license-key-for-woocommerce' ),
				'rest_api_description' => __( 'When an order is placed, it encrypts the customer\'s license key and outputs it as a REST API to the following URL. When it expires, it is gone.', 'simple-license-key-for-woocommerce' ),
				'decrypt_text' => __( 'Decrypt', 'simple-license-key-for-woocommerce' ),
				'decrypt_description' => __( 'The API\'s encrypt data ($encrypt_data) can be decrypted with the following code. Please contact me with the code that worked in your language. I will post it below.', 'simple-license-key-for-woocommerce' ),
				'generate' => __( 'Generate', 'simple-license-key-for-woocommerce' ),
				'apiurl' => home_url() . '/wp-json/rf/slk-woo-open-key_api/token',
				'slkwoo_logs' => wp_json_encode( $slkwoo_logs, JSON_UNESCAPED_SLASHES ),
				'logs' => __( 'Logs', 'simple-license-key-for-woocommerce' ),
				'logs_description' => __( 'Displays the original data of the last 100 REST APIs. Old data will be deleted in order. Can change the "Expiration" to control the output of the above REST API.', 'simple-license-key-for-woocommerce' ),
				'product_id' => __( 'Product', 'woocommerce' ) . ' ID',
				'product_name' => __( 'Product name', 'woocommerce' ),
				'passphrase' => __( 'Passphrase', 'simple-license-key-for-woocommerce' ),
				'encrypt_data' => __( 'API\'s encrypt data', 'simple-license-key-for-woocommerce' ),
				'name' => __( 'Name' ),
				'mail' => __( 'Email' ),
				'date' => __( 'Date' ),
				'expiry_date' => __( 'Expiration' ),
				'lock' => __( 'Lock', 'simple-license-key-for-woocommerce' ),
				'lock_text' => __( 'Passphrase is locked.', 'simple-license-key-for-woocommerce' ),
				'unlock_text' => __( 'New passphrase is possible.', 'simple-license-key-for-woocommerce' ),
			)
		);

		$this->credit_gutenberg( 'slkwooadmin' );
	}

	/** ==================================================
	 * Credit for Gutenberg
	 *
	 * @param string $handle  handle.
	 * @since 1.00
	 */
	private function credit_gutenberg( $handle ) {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}

		wp_localize_script(
			$handle,
			'credit',
			array(
				'links'          => __( 'Various links of this plugin', 'simple-license-key-for-woocommerce' ),
				'plugin_version' => __( 'Version:' ) . ' ' . $plugin_ver_num,
				/* translators: FAQ Link & Slug */
				'faq'            => sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'simple-license-key-for-woocommerce' ), $slug ),
				'support'        => 'https://wordpress.org/support/plugin/' . $slug,
				'review'         => 'https://wordpress.org/support/view/plugin-reviews/' . $slug,
				'translate'      => 'https://translate.wordpress.org/projects/wp-plugins/' . $slug,
				/* translators: Plugin translation link */
				'translate_text' => sprintf( __( 'Translations for %s' ), $plugin_name ),
				'facebook'       => 'https://www.facebook.com/katsushikawamori/',
				'twitter'        => 'https://twitter.com/dodesyo312',
				'youtube'        => 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w',
				'donate'         => __( 'https://shop.riverforest-wp.info/donate/', 'simple-license-key-for-woocommerce' ),
				'donate_text'    => __( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'simple-license-key-for-woocommerce' ),
				'donate_button'  => __( 'Donate to this plugin &#187;' ),
			)
		);
	}

	/** ==================================================
	 * Generate license key
	 *
	 * @param int $length  length.
	 * @since 1.13
	 */
	private function generate_license_key( $length ) {

		$chars = '23456789abcdefhjkmnpstwxy';

		$license_key = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$license_key .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $license_key;
	}
}
