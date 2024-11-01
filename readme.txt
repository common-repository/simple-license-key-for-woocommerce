=== Simple License Key for WooCommerce ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: license, license key, woocommerce
Requires at least: 4.7
Requires PHP: 8.0
Tested up to: 6.6
Stable tag: 1.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate a license key to in WooCommerce product for web services.

== Description ==

Generate a license key to in WooCommerce product for web services.

= REST API =
* When an order is placed, it encrypts the customer's license key and outputs it as a REST API. When it expires, it is gone.

= Issue a license key =
* To activate the license key, you need code to client side.

= Cooperation with other plugins =
* Work with [WooCommerce](https://wordpress.org/plugins/woocommerce/).

== Installation ==

1. Upload `simple-license-key-for-woocommerce` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

none

== Screenshots ==

1. Settings
2. Product
3. Order details
4. Order
5. Mail
6. REST API

== Changelog ==

= 1.15 =
Changed json_encode to wp_json_encode.

= 1.14 =
Supported WordPress 6.4.
PHP 8.0 is now required.

= 1.13 =
The license key is generated with 8 characters that are difficult to make a mistake.

= 1.12 =
Supported High Performance Order Storage(COT).

= 1.11 =
Supported WordPress 6.1.

= 1.10 =
Passphrase can now be locked.

= 1.09 =
Fixed generate key.

= 1.08 =
Fixed generate key.

= 1.07 =
Rebuild react.

= 1.06 =
Fixed translation.

= 1.05 =
Fixed problem of change the expiration date.

= 1.04 =
Fixed problem of change the expiration date.

= 1.03 =
Logging is now available.
It is now possible to change the expiration date.
Changed to output the REST API with object( product_id, open_key, date_expiry, expiry_stamp ).

= 1.02 =
Changed to output the REST API with the product ID as the key and the encrypted license key as the value.

= 1.01 =
Supported Glotpress.

= 1.00 =
Initial release.

== Upgrade Notice ==

= 1.00 =
Initial release.

