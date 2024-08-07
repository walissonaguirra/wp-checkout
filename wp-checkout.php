<?php

/*
 * Plugin Name:       WP Checkout
 * Plugin URI:        
 * Description:       Custumização para o fluxo do woocommerce checkout
 * Version:           0.4.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Walisson Aguirra
 * Author URI:        https://github.com/walissonaguirra
 * License:           
 * License URI:       
 * Update URI:        
 * Text Domain:       wp-checkout
 * Domain Path:       
 * Requires Plugins:  woocommerce
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once 'class-setup.php';
require_once 'class-code-snippet.php';
require_once 'class-checkout-process.php';
require_once 'class-order-process.php';

new WPC_Setup();
new WPC_Code_Snippet();
new WPC_Checkout_Process();
new WPC_Order_Process();