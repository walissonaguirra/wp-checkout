<?php

class WPC_Order_Process
{
	private string $webhook_url;

	public function __construct()
	{
		// Salva maigic-link nos meta_data do pedido
		add_action('woocommerce_new_order', [$this, 'save_magic_link'], 10, 2);

		// Interronpe disparado do webhook quando status do pedido for diferente de 'processing' 
		add_filter('woocommerce_webhook_should_deliver', [$this, 'stop_webhook_delivery'], 10, 3);

		// Adiciona Header personalizado WebHook Woocommerce
		add_filter('woocommerce_webhook_http_args', [$this, 'add_custom_header'], 10, 1);

		// Filtra dados enviados pelo WebHook Woocommerce para confirmar pagamento
		add_filter('woocommerce_webhook_payload', [$this, 'webhook_payload_format'], 10, 2);
	}

	private function getWebhookURL()
	{
		$this->webhook_url = get_option('wpc_endpoint_get_token');
	}

	public function save_magic_link($order_id, $order)
	{
		$order->add_meta_data('magic-link', $GLOBALS['magic-link']);
		$order->save();
	}

	public function stop_webhook_delivery($should_deliver, $webhook, $arg)
	{
		$payload = $webhook->build_payload($arg);

		if (!isset($payload['status']) || $payload['status'] != 'processing') {
			return false;
		}

		return $should_deliver;
	}

	/**
	 * Faz autenticação no endpoint de confirmação de pagamento
	 * com dados do usuário (order) e adicionar o Bearer Token
	 * no Header do WooCommerce WebHook 
	 * 
	 * @param array $http_args
	 * @return array New http_args
	 */
	public function add_custom_header($http_args)
	{
		$payload = json_decode($http_args['body']);

		if (!isset($payload->status) || $payload->status != 'processing') {
			return $http_args;
		}

		$this->getWebhookURL();

		$response = wp_remote_post($this->webhook_url, [
			'method'    => 'POST',
			'headers'   => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json'
			],
			'body'      => json_encode([
				"email"       => $payload->email,
				"password"    => $payload->password,
				"fcm_token"   => "wp_token",
				"device_name" => "Wp"
			]),
		]);

		if (is_wp_error($response)) {
			error_log('[woocommerce_webhook_http_args]');
			error_log(print_r($response->get_error_message(), true));
			return $http_args;
		}

		$response_body = wp_remote_retrieve_body($response);
		$response_data = json_decode($response_body);

		$http_args['headers']['Authorization'] = "Bearer {$response_data->data->token}";
		$http_args['headers']['Accept'] = 'application/json';

		return $http_args;
	}

	public function webhook_payload_format($payload, $resource)
	{
		if ($resource != 'order') {
			return $payload;
		}

		if ($payload['status'] != 'processing') {
			return $payload;
		}

		$order = wc_get_order($payload['id']);

		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			$plan = get_post_meta($product_id, 'plan', true);
			break;
		}

		$asaas = json_decode($this->get_meta_data($payload['meta_data'], '__ASAAS_ORDER'));

		return [
			'email'       => $payload['billing']['email'],
			'password'	  => $this->get_meta_data($payload['meta_data'], '_billing_user_password'),
			'plan'        => $plan,
			'billingType' => $asaas->billingType,
			'status'      => $payload['status'],
			'value'       => "$asaas->value",
			'paymentId'   => $asaas->id
		];
	}

	/**
	 * Pega o valor de meta campo no pedido
	 *
	 * @param  array   $meta_data  Meta campos
	 * @param  string  $key        Key do meta campo
	 */
	private function get_meta_data(array $meta_data, string $key)
	{
		$data = array_filter($meta_data, function ($item) use ($key) {
			return $item['key'] == $key;
		});

		return $data[array_key_first($data)]['value'] ?? null;
	}
}
