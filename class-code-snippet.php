<?php

class WPC_Code_Snippet
{
	public function __construct()
	{
		// Adicionar código JavaScript na página de Checkout WooCommerce
		add_action('wp_footer', [$this, 'add_scripts_checkout_page']);

		// Adicionar botão com magic-link para login automatico na epostal dash
		add_action('woocommerce_order_details_after_order_table', [$this, 'add_magic_link_button']);

		// Criar shortcode para botões na página 'order-received'
		add_shortcode('wpc_order_received', [$this, 'add_content_order_received']);
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

	/**
	 * Criar Botões para 'Ver Fatura', 'Acessa plataforma' e
	 * adicionar a descrição do pedido
	 */
	public function add_content_order_received()
	{
		if (!is_wc_endpoint_url('order-received')) {
			return;
		}

		global $wp;

		$order_id  = absint($wp->query_vars['order-received']);

		if (empty($order_id) || $order_id == 0) {
			return;
		}

		$order = wc_get_order($order_id);

		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			$product = wc_get_product($product_id);
			$description = $product->get_description();
			$title = $item->get_name();
		}

		$assas = json_decode($order->get_meta('__ASAAS_ORDER'));

		return <<<HTML
		<style>.d-none{display:none!important;}</style>
		
		<!-- Título do produto -->
		<div class="elementor-element elementor-element-3b23dd8 e-con-full e-flex e-con e-child" data-id="3b23dd8" data-element_type="container">
			<div class="elementor-element elementor-element-4e542fb elementor-widget elementor-widget-heading" data-id="4e542fb" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
				<h2 class="elementor-heading-title elementor-size-default">Plano selecionado:</h2>		
			</div>
			</div>
			<div class="elementor-element elementor-element-003595f elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="003595f" data-element_type="widget" data-widget_type="heading.default">
				<div class="elementor-widget-container">
					<h2 class="elementor-heading-title elementor-size-default">{$title}</h2>		
				</div>
			</div>
		</div>

		<div class="elementor-element elementor-element-ac1bbf0 elementor-widget elementor-widget-heading" data-id="ac1bbf0" data-element_type="widget" data-widget_type="heading.default">
			<div class="elementor-widget-container">
				<h2 class="elementor-heading-title elementor-size-default">Endereço selecionado:</h2>		
			</div>
		</div>

		<!-- Descrição do produto -->
		<div class="elementor-element elementor-element-88557dd elementor-widget elementor-widget-text-editor" data-id="88557dd" data-element_type="widget" data-widget_type="text-editor.default">
			<div class="elementor-widget-container">
				<style>.elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap{background-color:#69727d;color:#fff}.elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap{color:#69727d;border:3px solid;background-color:transparent}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap{margin-top:8px}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter{width:1em;height:1em}.elementor-widget-text-editor .elementor-drop-cap{float:left;text-align:center;line-height:1;font-size:50px}.elementor-widget-text-editor .elementor-drop-cap-letter{display:inline-block}</style>
				{$description}
			</div>
		</div>
		
		<!-- Botão: Ver Fatura -->
		<div class="elementor-element elementor-element-fc15a31 elementor-align-justify elementor-widget elementor-widget-button" data-id="fc15a31" data-element_type="widget" data-widget_type="button.default">
			<div class="elementor-widget-container">
				<div class="elementor-button-wrapper">
					<a class="elementor-button elementor-button-link elementor-size-sm" href="{$assas->invoiceUrl}" target="_blank">
					<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">Ver Fatura</span>
					</span>
					</a>
				</div>
			</div>
		</div>

		<!-- Botão: Acesso á plataform -->
		<div class="elementor-element elementor-element-caebd69 elementor-align-justify elementor-widget elementor-widget-button" data-id="caebd69" data-element_type="widget" data-widget_type="button.default">
			<div class="elementor-widget-container">
				<div class="elementor-button-wrapper">
					<a class="elementor-button elementor-button-link elementor-size-sm" href="{$order->get_meta('magic-link')}" target="_blank">
						<span class="elementor-button-content-wrapper">
							<span class="elementor-button-text">Acessa á plataforma</span>
						</span>
					</a>
				</div>
			</div>
		</div>
		HTML;
	}
}
