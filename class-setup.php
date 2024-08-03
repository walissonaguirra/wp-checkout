<?php

class WPC_Setup
{
	public function __construct()
	{
		// Desabilita alerta: Produto adicionado ao carrinho
		add_filter('wc_add_to_cart_message_html', '__return_false');

		// Impedir que um webhook mau sucedido seja desativado
		add_filter('woocommerce_webhook_delivery_failed', '__return_false');

		// Redireciona direto para página de checkout
		add_filter('woocommerce_add_to_cart_redirect', fn () => wc_get_checkout_url());

		// Desabilita a repopulação de formulario na página de checkout do Woocommerce
		add_filter('woocommerce_checkout_get_value', fn () => '');

		// Limpar o carrinho antes de adicionar um novo item
		add_filter('woocommerce_add_to_cart_validation', [$this, 'empty_cart_before_add_item']);

		// Reireciona página 'Shop' para pagina inicial do ePostal
		add_action('template_redirect', [$this, 'redirect_from_shop_page_to_epostal']);

		// Desabilita webHook async
		// add_filter('woocommerce_webhook_deliver_async', '__return_false');
	}

	public function empty_cart_before_add_item($passed)
	{
		WC()->cart->empty_cart();
		return $passed;
	}

	public function redirect_from_shop_page_to_epostal()
	{
		if (is_shop()) {
			wp_redirect('https://epostal.com.br', 301);
			exit;
		}
	}
}
