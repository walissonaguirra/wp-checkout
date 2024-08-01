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

		$asaas = json_decode(get_meta_data($payload['meta_data'], '__ASAAS_ORDER'));

	    return [
	    	// WP Post ID
	    	'id' => $payload['id'],

	    	// Informações pessoa fisica
		    'name'     	=> $payload['billing']['first_name'] . ' ' . $payload['billing']['last_name'],
		    'email'    	=> $payload['billing']['email'],
		    'password' 	=> get_meta_data($payload['meta_data'], '_billing_user_password'),
		    'phone'    	=> $payload['billing']['phone'],
		    'document'  => $payload['billing']['cpf'],
		    'birthdate' => get_meta_data($payload['meta_data'], '_billing_birthdate'),

		    // Infomações de endereço
	    	'address'    => $payload['billing']['address_1'],
			'number'     => $payload['billing']['number'],
		    'complement' => $payload['billing']['address_2'],
		    'district'   => $payload['billing']['neighborhood'],
		    'city'       => $payload['billing']['city'],
		    'state'      => $payload['billing']['state'],
			'zipcode'    => $payload['billing']['postcode'],

		    // Informações pessoa juridica
		    'cnpj'               => $payload['billing']['cnpj'],
		    "company_name"       => $payload['billing']['company'],
		    "company_zipcode"    => $payload['billing']['postcode'],
		    "company_address"    => $payload['billing']['address_1'],
		    "company_city"       => $payload['billing']['city'],
		    "company_state"      => $payload['billing']['state'],
		    "company_number"     => $payload['billing']['number'],
		    "company_complement" => $payload['billing']['address_2'],

		    // Informações de pagamento
		    "email"       => $payload['billing']['email'],
		    "plan"        => "Empresa",
		    "billingType" => $asaas->billingType,
		    "value"       => $asaas->value,
		    "paymentId"   => $asaas->id
		];
    }

    return $payload;

}, 10, 4);

/**
 * Salva á resposta do WebHook em meta campo do pedido
 */
add_action('woocommerce_webhook_delivery', function($http_args, $response, $duration, $arg, $webhook_id) {

	if ($response['response']['code'] == 200 && isset($response['body']['magic-link'])) {

		update_post_meta(
			$response['body']['id'],
			'magic-link',
			$response['body']['magic-link']
		);

	}

}, 1, 5);

/**
 * Adicionar botão com magic-link para login na plataform
 */
add_action('woocommerce_order_details_after_order_table', function ($order) {

	echo <<<HTML
	<a class="button alt" target="_black" href="{$order->get_meta('magic-link')}">Acessa á plataforma</a>
	HTML;

}, 10);