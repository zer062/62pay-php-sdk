# 62Pay SDK para PHP

Um SDK robusto para integrar as soluções de pagamento **62Pay** em suas aplicações PHP de forma simples e segura.

---

## 📋 Requisitos

- **PHP** 8.0 ou superior
- **Composer**
- Conta válida na **62Pay** e credenciais de API
- SSL habilitado para ambientes de produção

---

## 💻 Instalação

Instale o SDK via Composer:

```bash
composer require 62pay/php-sdk
```

## 🔧 Configuração

Inicialize o SDK com sua API Key e ambiente desejado:

```php
use Sixtytwopay\Sixtytwopay;

$sdk = new Sixtytwopay('SIXTYTWOPAY_API_KEY', 'SANDBOX');
```

#### ⚠️ Importante: Nunca versione sua chave de API. Utilize variáveis de ambiente.

## **📦 Serviços Disponíveis**

| Método no SDK      | Serviço         | Descrição                                         |
|--------------------|-----------------|---------------------------------------------------|
| `$sdk->customer()` | CustomerService | Criação, listagem e atualização de clientes       |
| `$sdk->invoice()`  | InvoiceService  | Emissão, gestão e pagamento com cartão de crédito |
| `$sdk->payment()`  | PaymentService  | Processamento e consulta de pagamentos            |

## 🌟 **Funcionalidades**

- Gestão de **clientes**
- Emissão e gestão de **cobranças**
- Processamento de **pagamentos**
- Checkout seguro
- Suporte a múltiplos métodos de pagamento
- Tratamento de erros e integração com **webhooks**

---

## 📘 Exemplos de Uso

### **Clientes**

#### Criar cliente (mínimo)

```php
$customer = $sdk->customer()->create([
'name' => 'João da Silva',
]);
```

#### Criar cliente (completo)

```php
$customer = $sdk->customer()->create([
'type' => 'LEGAL',
'name' => 'João da Silva',
'legal_name' => 'João A. da Silva',
'email' => 'joao@example.com',
'phone' => '11988887777',
'document_number' => '12345678900',
'address' => 'Rua das Palmeiras',
'address_number' => '123',
'complement' => 'Apto 45',
'postal_code' => '01311000',
'province' => 'Bela Vista',
'city' => 'São Paulo',
'state' => 'SP',
'tags' => ['vip', 'beta-tester'],
]);
```

#### Atualizar cliente

```php
$updatedCustomer = $sdk->customer()->update('01987533-17dc-7120-a54b-de803aa04bc0', [
'email' => 'joao.novo@example.com',
'phone' => '11999998888',
'tags' => ['vip', 'premium'],
]);
```

#### Buscar cliente por ID

```php
$customer = $sdk->customer()->get('01987533-17dc-7120-a54b-de803aa04bc0');
```

#### Listar clientes

```php
$customers = $sdk->customer()->list();
```

#### Excluir cliente

```php
$sdk->customer()->delete('01987533-17dc-7120-a54b-de803aa04bc0');
```

### **Cobranças**

#### Criar fatura (mínimo)

```php
$invoice = $sdk->invoice()->create([
'customer' => '01987533-17dc-7120-a54b-de803aa04bc0',
'payment_method' => 'PIX',
'amount' => 10000,
'due_date' => '2025-08-15',
'description' => 'Serviço de Assinatura',
]);
```

#### Criar fatura (completo, com juros/multa/desconto e parcelas)

```php
$invoice = $sdk->invoice()->create([
'customer' => '01987533-17dc-7120-a54b-de803aa04bc0',
'payment_method' => 'CREDIT_CARD',
'amount' => 250000,
'due_date' => '2025-08-20',
'description' => 'Plano Anual Premium',
'installments' => 6,
'immutable' => false,
'interest_percent' => 500,
'fine_type' => 'PERCENTAGE',
'fine_value' => 200,
'discount_type' => 'PERCENTAGE',
'discount_value' => 1000,
'discount_deadline'=> '2025-08-10',
'tags' => ['campanha-ano', 'premium'],
]);
```

#### Atualizar fatura (parcial)

```php
$invoice = $sdk->invoice()->update('01987533-8e9e-7384-a2e3-96fb04d556f2', [
'amount' => 12000,
'due_date' => '2025-08-25',
'description' => 'Serviço de Assinatura (ajustado)',
'installments' => 3,
'immutable' => false,
'tags' => ['ajuste', 'cliente-vip'],
]);
```

> ℹ️ **Nota:** O `buildUpdatePayload()` só aceita um subconjunto de campos (sem juros/multa/desconto). Para alterar
> regras de juros/multa/desconto após criação, verifique as políticas/recursos da sua API.

#### Buscar fatura por ID

```php
$invoice = $sdk->invoice()->get('01987533-8e9e-7384-a2e3-96fb04d556f2');
```

#### Listar faturas

```php
$faturas = $sdk->invoice()->list();
```

#### Estornar/Refund

```php
$sdk->invoice()->refund('01987533-8e9e-7384-a2e3-96fb04d556f2');
```

#### Excluir fatura

```php
$sdk->invoice()->delete('01987533-8e9e-7384-a2e3-96fb04d556f2');
```

### **Pagamentos**

#### Buscar pagamento por ID de fatura

```php
$payment = $sdk->payment()->get('01987533-8e9e-7384-a2e3-96fb04d556f2');
```

#### Atualizar regras de pagamento (juros/multa/desconto/descrição)

```php
$payment = $sdk->payment()->update('01987533-8e9e-7384-a2e3-96fb04d556f2', [
'interest_percenter' => 500,
'fine_type' => 'PERCENTAGE',
'fine_value' => 200,
'discount_type' => 'FIXED',
'discount_value' => 1000,
'discount_deadline' => '2025-08-31',
'description' => 'Pagamento ajustado com condições promocionais',
]);
```

#### Estornar pagamento (refund)

```php
$sdk->payment()->refund('01987533-8e9e-7384-a2e3-96fb04d556f2', [
'reason' => 'CUSTOMER_REQUESTED',
]);
```

> ℹ️ **Notas**
> - Os métodos recebem o **ID da fatura** (`invoice`) no path: `payments/{invoice}`.
> - `refund()` não retorna corpo (**void**) de acordo com o serviço.
> - O `update()` aplica apenas os campos aceitos em `buildUpdatePayload()`; envie somente o que deseja alterar.
> - **Valide** se a chave correta é `interest_percenter` ou `interest_percent` conforme sua API.

### **Pagamento com cartão de crédito**

`client_ip` é obrigatório. Envie o IP do comprador observado pelo seu servidor na requisição original do navegador
(IPv4 ou IPv6). Nunca envie o IP do próprio servidor nem o primeiro valor arbitrário de `X-Forwarded-For`.
Se `client_ip` estiver ausente ou malformado, o SDK lança `InvalidArgumentException` antes de chamar a API.
A 62Pay gera a sessão ClearSale no servidor: não envie app key, SessionID nem dados do SDK ClearSale.

```php
use Sixtytwopay\Inputs\Invoice\InvoiceCreditCardInput;

$input = InvoiceCreditCardInput::fromArray([
'holder_name' => 'Maria Oliveira',
'number' => '5555444433331111',
'card_expiry_date' => '07/29',
'ccv' => '987',
'installments' => 6,
'billing_name' => 'Maria Oliveira',
'billing_email' => 'maria@example.com',
'billing_document_number' => '98765432100',
'billing_postal_code' => '20031000',
'billing_street' => 'Rua Exemplo',
'billing_address_number' => '456',
'billing_address_complement' => 'Apto 1203',
'billing_neighborhood' => 'Centro',
'billing_city' => 'Rio de Janeiro',
'billing_state' => 'RJ',
'billing_phone' => '21977776666',
'client_ip' => '203.0.113.10',
]);

$response = $sdk->invoice()->payWithCreditCard('01987533-8e9e-7384-a2e3-96fb04d556f2', $input);

if ($response->failed()) {
    $fields = $response->raw()['errors'][0]['meta']['fields'] ?? [];
}
```

> ℹ️ `203.0.113.10` é um IP reservado para documentação. Use o IP real do comprador.

## ⚡ Boas Práticas

Sempre utilize variáveis de ambiente para credenciais

Ative o error reporting no ambiente de desenvolvimento

Valide todos os dados de entrada

Trate exceções de forma adequada

Mantenha o SDK atualizado

## **🔍 Tratamento de Erros**

```php
use Sixtytwopay\Exceptions\ApiException;

try {
$fatura = $sdk->invoice()->create($dadosFatura);
} catch (ApiException $e) {
echo "Erro da API: " . $e->getMessage();
} catch (\Exception $e) {
echo "Erro geral: " . $e->getMessage();
}
```

## 🛠️ Desenvolvimento

```bash
composer install --dev
composer test
```

## **🤝 Contribuindo**

##### 1. Faça um fork do repositório

##### 2. Crie uma branch para sua feature (git checkout -b feature/minha-feature)

##### 3. Commit suas alterações (git commit -m 'Minha nova feature')

##### 4. Faça o push (git push origin feature/minha-feature)

##### 5.Abra um Pull Request

## 🔒 Segurança

Para reportar vulnerabilidades, siga nossa Política de Segurança.

## 📄 Licença

Este projeto está licenciado sob a MIT License – veja o arquivo LICENSE para mais detalhes.

## 💬 Suporte

- Para dúvidas sobre a API: suporte@62pay.com.br

- Para problemas com o SDK: abra uma issue no GitHub

- Para casos urgentes: entre em contato com nosso time de suporte
