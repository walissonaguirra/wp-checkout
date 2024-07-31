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
  "persontype": "F",
  "birthdate": "04/01/2000",
  "cpf": "21520388004",
  "cnpj": "02992059000139",
  "name": "Walisson Aguirra",
  "company": "",
  "email": "walissonaguirra@icloud.com",
  "phone": "(11) 99999-9999",
  "password": "Mudar123",
  "address": {
    "zipcode": "00000-000",
    "street": "São João de Calor",
    "number": "1234",
    "complement": "",
    "state": "SP",
    "city": "São Paulo"
  },
  "woocommerce_order": {
    "id": 17,
    "title": "MEI Florianópolis",
    "price": 397,
    "payment_status": "processing"
  }
}
```

**Detalhes**
|Chave JSON|Valor|Descrição|
|---|---|---|
|persontype|`F` ou `J`| `F` (Pessoa Física) ou `J` (Pessoa Juridica)|
|payment_status|`pending` ou `processing`| `pending` É o valor default quando o pedido é criado. `processing` Significa que o Getwey de pagamento confirmou o pagamento|



