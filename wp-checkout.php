<?php

/*
 * Plugin Name:       WP Checkout
 * Plugin URI:        
 * Description:       Custumização para o fluxo do woocommerce checkout
 * Version:           0.0.1
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
add_filter('woocommerce_webhook_deliver_async', '__return_false' );

/**
 * Filtra dados enviados pelo WebHook Woocommerce
 * 
 * @filter woocommerce_webhook_payload
 * @priority 10
 * @arguments 4
 * 
 * @param array $payload
 * @param \WC_Order|\WC_Product|WP_User $resource
 * @param int $resource_id
 * @param string $webhook_id
 * @return array New payload
 */
add_filter('woocommerce_webhook_payload', function($payload, $resource) {

	/**
	 * Pega o valor de meta campo no pedido
	 *
	 * @param  array   $meta_data  Meta campos
	 * @param  string  $key        Key do meta campo
	 */
    function get_meta_data(array $meta_data, string $key) 
    {
    	$data = array_filter($meta_data, function ($item) use ($key) {
    		return $item['key'] == $key;
    	});

    	return $data[array_key_first($data)]['value'] ?? null;
    }

	if ($resource == 'order') {
	    return [
	    	// Informações pessoais
		    'persontype' => $payload['billing']['persontype'],
		    'birthdate'  => get_meta_data($payload['meta_data'], '_billing_birthdate'),
		    'cpf'        => $payload['billing']['cpf'],
		    'cnpj'       => $payload['billing']['cnpj'],
		    'name'       => $payload['billing']['first_name'] . ' ' . $payload['billing']['last_name'],
		    'company'    => $payload['billing']['company'],
		    'email'      => $payload['billing']['email'],
		    'phone'      => $payload['billing']['phone'],
		    'password'   => get_meta_data($payload['meta_data'], '_billing_user_password'),

		    // Infomações de endereço
		    'address'    => [
			    'zipcode'    => $payload['billing']['postcode'],
		    	'street'     => $payload['billing']['address_1'],
			    'number'     => $payload['billing']['number'],
			    'complement' => $payload['billing']['address_2'],
			    'state'      => $payload['billing']['state'],
			    'city'       => $payload['billing']['city']
		    ],

		    // Informações sobre a compra
		    'woocommerce_order' => [
		    	'id'             => $payload['line_items'][0]['product_id'],
		    	'title'          => $payload['line_items'][0]['name'],
		    	'price'          => $payload['line_items'][0]['price'],
		    	'payment_status' => $payload['status']
		    ]
		];
    }

    return $payload;

}, 10, 4);
