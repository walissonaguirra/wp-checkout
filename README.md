## WP Chekout
Este plugin faz modificações no fluxo de checkout do woocommerce e se integra com a API epostal

### Ações do plugin
- Limpar o carrinho antes de adicionar um novo item
- Desabilita alerta: Produto adicionado ao carrinho
- Redireciona direto para página de checkout
- Impedir que um webhook mau sucedido seja desativado
- Filtra dados enviados pelo WebHook Woocommerce durante integração
- Reireciona página 'Shop' para pagina inicial do ePostal
- Desabilita webHook async (comentado por padrão)
- Adicionar código JavaScript na página de Checkout WooCommerce
- Adicionar botão com magic-link para login automatico na epostal dash
- Integração: Cadastra usuário na eposta
- Integração: Confirma status de pagamento na eposta

### WebHook: woocommerce_checkout_process
Payloand enviada pelo webhook para cadastro de usuário
```json
{
  "name": "Harry Potter",
  "email": "harrypotter@epostal.com",
  "password": "Trasgu_00",
  "phone": "(00) 00000-0000",
  "document": "337.116.950-27",
  "birthdate": "01/01/2000",

  "address": "Rua dos Alfeneiros",
  "number": "4",
  "complement": "",
  "district": "Little Whinging",
  "city": "Florianópolis",
  "state": "SC",
  "country": "BR",
  "zipcode": "88060-225",

  "cnpj": "44.020.614/0001-00",
  "company_name": "Harry Potter LTDA",
  "company_zipcode": "88060-225",
  "company_address": "Rua dos Alfeneiros",
  "company_city": "Florianópolis",
  "company_state": "SC",
  "company_number": "4",
  "company_complement": ""
}
```
Os campos são válidados no backend da API ePostal e as mensagem de erro são exibidar como alerta na tela de checkout

### Resposta WebHook: woocommerce_checkout_process
Após o postagem dos dados do pedido feita pelo WebHook: woocommerce_checkout_process, ele espera receber os seguinte dados como respota:
```json
{
  "data": {
    "magic-link": "https://login.epostal.com.br/[hash_unica_para_login]"
  }
}
```
Este magic-link será usando na página: Detalhes do pedido, para que o usuário possa fazer login na ePosta Dash

### WebHook: woocommerce_webhook_http_args
Payload para obter o Bearer Token 
```json
{
  "email": "harrypotter@epostal.com",
  "password": "Trasgu_00",
  "fcm_token": "wp_token",
  "device_name": "Wp"
}
```

### WebHook: woocommerce_webhook_payload
Payload para atualiza o status de pagamento for confirmado
```json
{
  "email": "harrypotter@epostal.com",
  "password": "Trasgu_00", // Nessecario para obter o Bearer Token
  "plan": "Empresa",
  "billingType": "BOLETO", // ASAAS
  "status": "processing", 
  "value": "397.00", // String
  "paymentId": 47 // ASAAS ID
}
```
Quando o GetWay de pagamento responde com a confirmação o WooCommerce atualiza o status do pedido para `processing`, (Significa pagamento recebido)