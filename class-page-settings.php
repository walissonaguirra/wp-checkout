<?php

class WPC_Page_Settings
{
	public function __construct()
	{
		// Cria página de configurações no menu lateral
		add_action('admin_menu', [$this, 'create_page_settings']);

		// Criar form para configurações
		add_action('admin_init', [$this, 'form_settings']);
	}

	public function create_page_settings()
	{
		add_options_page(
			'WP Checkout Configurações', 	 // Título da página
			'WP Checkout', 					 // Nome no menu do Painel
			'manage_options', 				 // Permissões necessárias
			'wp-checkout-settings', 		 // Valor do parâmetro "page" no URL
			[$this, 'page_settings_content'] // Função que imprime o conteúdo da página
		);
	}

	public function page_settings_content()
	{
?>
		<div class="wrap">
			<h1><?= get_admin_page_title() ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields('wpc_settings');
				do_settings_sections('wpc_settings');
				submit_button();
				?>
			</form>
		</div>
<?php
	}

	public function form_settings()
	{
		add_settings_section(
			'pwc_section',
			'EndPoints API',
			function () {
				echo <<<HTML
					<p>
						Coloque aqui os endpoints para integração com à API.
						Explicação detalhada na <a href="https://github.com/walissonaguirra/wp-checkout" target="_blank">documentação</a> 
					</p>
				HTML;
			},
			'wpc_settings'
		);

		$this->add_field(
			'wpc_endpoint_user_create',
			'URL para cadastro de usuários',
			function ($value) {
				if (empty($value)) {
					add_settings_error(
						'wpc_endpoint_user_create',
						'wpc_endpoint_user_create_erro',
						'A URL para cadastro de usuários não pode ser um valor em branco.',
						'error'
					);

					return get_option('wpc_endpoint_user_create');
				}

				return $value;
			}
		);

		$this->add_field(
			'wpc_endpoint_get_token',
			'URL para obter o token de confirmação de pagamento',
			function ($value) {
				if (empty($value)) {
					add_settings_error(
						'wpc_endpoint_get_token',
						'wpc_endpoint_get_token_erro',
						'A URL para obter o token de confirmação de pagamento não pode ser um valor em branco.',
						'error'
					);

					return get_option('wpc_endpoint_get_token');
				}

				return $value;
			}
		);
	}

	/**
	 * Criar campos para o formularia de configuração
	 *
	 * @param string    $name               The name
	 * @param string    $label              The label
	 * @param callable  $sanitize_callback  The sanitize callback
	 */
	public function add_field(string $name, string $label, callable $sanitize_callback)
	{
		register_setting(
			'wpc_settings',
			$name,
			[
				'sanitize_callback' => $sanitize_callback,
			]
		);

		add_settings_field(
			$name,
			$label,
			function ($args) use ($name) {
				$options = get_option($name);

				echo <<<HTML
					<input 
						type="text" 
						id="{$args['label_for']}" 
						name="$name" 
						value="{$options}"
					/>
				HTML;
			},
			'wpc_settings',
			'pwc_section',
			[
				'label_for' => "{$name}_html_id",
			]
		);
	}
}
