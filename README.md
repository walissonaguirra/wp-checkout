## WP Chekout
Este plugin faz modificações no fluxo de checkout do woocommerce e modifica a resposta do envida pelo WooCommerce WebHook

### Ações do plugin
- Limpar o carrinho antes de adicionar um novo item
- Desabilita alerta: Produto adicionado ao carrinho
- Redireciona direto para página de checkout
- Desativar woocommerce_webhook_deliver_async no WooCommerce para que os webhooks sejão enviados imediatamente
- Filtra dados enviados pelo WebHook Woocommerce

### WebHook: Order.Updated
Payloand envia pelo webhook
```json
{
  "id": 47,
  "name": "Walisson Aguirra",
  "email": "walissonaguirra@icloud.com",
  "password": "Mudar123",
  "phone": "(11) 99999-9999",
  "document": "88899977766",
  "birthdate": "04/01/2000",
  "address": "Avenida Castelo Branco",
  "number": "0000",
  "complement": "",
  "district": "Santa Lucia",
  "city": "Campinas",
  "state": "SP",
  "zipcode": "88888-888",
  "cnpj": "02992059000139",
  "company_name": "Joao Macaxeira",
  "company_zipcode": "88888-888",
  "company_address": "Avenida Castelo Branco",
  "company_city": "Campinas",
  "company_state": "SP",
  "company_number": "0000",
  "company_complement": "",
  "plan": "Empresa",
  "billingType": "BOLETO",
  "value": 397,
  "paymentId": "pay_06e9va0mm9o6i9il"
}
```

### Resposta WebHook: Order.Updated
Após o postagem dos dados do pedido feita pelo webhook woocommerce, ele espera receber os seguinte dados como respota:
```json
{
  "id": 47,
  "magic-link": "https://login.epostal.com.br/[hash_unica_para_login]"
}
```
