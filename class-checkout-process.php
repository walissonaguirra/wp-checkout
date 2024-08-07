<?php

class WPC_Checkout_Process
{
	private string $webhook_url;

	public function __construct()
	{
		//Cadastra usuário na API Laravel antes de processa o pedido
		add_action('woocommerce_checkout_process', [$this, 'api_register_new_user']);	
	}

	private function getWebhookURL()
	{
		$this->webhook_url = get_option('wpc_endpoint_user_create');
	}

	public function api_register_new_user()
	{
		$first_name 	= filter_input(INPUT_POST, 'billing_first_name');
		$last_name 		= filter_input(INPUT_POST, 'billing_last_name');
		$cpf 			= filter_input(INPUT_POST, 'billing_cpf');
		$company 		= filter_input(INPUT_POST, 'billing_company');
		$cnpj 			= filter_input(INPUT_POST, 'billing_cnpj');
		$birthdate 		= filter_input(INPUT_POST, 'billing_birthdate');
		$country 		= filter_input(INPUT_POST, 'billing_country');
		$postcode 		= filter_input(INPUT_POST, 'billing_postcode');
		$address_1 		= filter_input(INPUT_POST, 'billing_address_1');
		$number 		= filter_input(INPUT_POST, 'billing_number');
		$address_2 		= filter_input(INPUT_POST, 'billing_address_2');
		$neighborhood 	= filter_input(INPUT_POST, 'billing_neighborhood');
		$city 			= filter_input(INPUT_POST, 'billing_city');
		$state 			= filter_input(INPUT_POST, 'billing_state');
		$phone 			= filter_input(INPUT_POST, 'billing_phone');
		$email 			= filter_input(INPUT_POST, 'billing_email');
		$password 	    = filter_input(INPUT_POST, 'billing_user_password');
		
		$date      = date_create_from_format('d/m/Y', $birthdate);
		$birthdate = $date->format('Y-m-d');

		$this->getWebhookURL();
				
		$response    = wp_remote_post($this->webhook_url, [
	        'method'    => 'POST',
	        'headers'   => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json'
	        ],
	        'body'      => json_encode([
				'name' 	    => "$first_name $last_name",
				'email' 	=> $email,
				'password' 	=> $password,
				'phone' 	=> $phone,
				'document' 	=> $cpf,
				'birthdate' => $birthdate,

				'address'    => $address_1,
				'number'     => $number,
				'complement' => $address_2,
				'district'   => $neighborhood,
				'city'       => $city,
				'state'      => $state,
				'country'    => $country,
				'zipcode'    => $postcode,

				'cnpj'               => $cnpj,
				'company_name'       => $company,
				'company_zipcode'    => $postcode,
				'company_address'    => $address_1,
				'company_city'       => $city,
				'company_state'      => $state,
				'company_number'     => $number,
				'company_complement' => $address_2
			])
	    ]);
		
	    if (is_wp_error($response)) {
			error_log('[woocommerce_checkout_process]');
			error_log(print_r($response->get_error_message(), true));
	        wc_add_notice(__('Erro ao conectar ao webhook. Tente novamente mais tarde.'), 'error');
	        return;
	    }
		
		$response_code = wp_remote_retrieve_response_code($response);
		$response_body = wp_remote_retrieve_body($response);
		$response_data = json_decode($response_body);
		
		if ($response_code != 200) {
			wc_add_notice($response_data->message, 'error');
	        return;
	    }
		
		$GLOBALS['magic-link'] = filter_var($response_data->data->{'magic-link'}, FILTER_SANITIZE_URL);
	}
}
