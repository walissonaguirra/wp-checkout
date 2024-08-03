<?php

class WPC_Code_Snippet
{
	public function __construct()
	{
		// Adicionar código JavaScript na página de Checkout WooCommerce
		add_action('wp_footer', [$this, 'add_scripts_checkout_page']);

		// Adicionar botão com magic-link para login automatico na epostal dash
		add_action('woocommerce_order_details_after_order_table', [$this, 'add_magic_link_button']);
	}

	public function add_scripts_checkout_page()
	{
		global $wp;

		if (
			is_checkout() &&
			empty($wp->query_vars['order-pay']) &&
			!isset($wp->query_vars['order-received'])
		) {

			echo <<<HTML
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						const wrap = document.querySelector('.wp-checkout-address');
						const billing_postcode = document.querySelector('#billing_postcode');

						billing_postcode.addEventListener('keyup', function () {
							wrap.classList.remove('wp-checkout-address-hidden');
						});

						if (billing_postcode.value !== '') {
							wrap.classList.remove('wp-checkout-address-hidden');
						}
					});
				</script>
			HTML;
		}
	}

	public function add_magic_link_button($order)
	{
		echo <<<HTML
			<a class="button" target="_black" href="{$order->get_meta('magic-link')}">
				Acessa á plataforma
			</a>
		HTML;
	}
}
