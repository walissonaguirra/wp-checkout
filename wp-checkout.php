<?php

/*
 * Plugin Name:       WP Checkout
 * Plugin URI:        
 * Description:       Custumização para o fluxo do woocommerce checkout
 * Version:           0.0.1
 * Requires at least: 6.6
 * Requires PHP:      8.2
 * Author:            Walisson Aguirra
 * Author URI:        https://github.com/walissonaguirra
 * License:           
 * License URI:       
 * Update URI:        
 * Text Domain:       wp-checkout
 * Domain Path:       
 * Requires Plugins:  woocommerce
 */

/**
 * Limpar o carrinho antes de adicionar um novo item
 */
add_filter( 'woocommerce_add_to_cart_validation', function ($passed) {
	WC()->cart->empty_cart();
    return $passed;
});

/**
 * Desabilita alerta: Produto adicionado ao carrinho
 */
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/**
 * Redireciona direto para página de checkout
 */
add_filter( 'woocommerce_add_to_cart_redirect', function (){
    return wc_get_checkout_url();
});

/**
 * Desativar woocommerce_webhook_deliver_async no WooCommerce
 * para que os webhooks sejão enviados imediatamente
 */
apply_filters('woocommerce_webhook_deliver_async', '__return_false' );
