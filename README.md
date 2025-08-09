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
| `$sdk->invoice()`  | InvoiceService  | Emissão e gerenciamento de cobranças              |
| `$sdk->payment()`  | PaymentService  | Processamento e consulta de pagamentos            |
| `$sdk->checkout()` | CheckoutService | Pagamento direto via checkout (Cartão de crédito) |

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
'document_number' => '12345678900',
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

### **Checkout**

#### Pagar fatura com **cartão de crédito** (mínimo)

```php
$response = $sdk->checkout()->payWithCreditCard('01987533-8e9e-7384-a2e3-96fb04d556f2', [
'holder_name' => 'João da Silva',
'number' => '4111111111111111',
'card_expiry_date' => '12/28',
'ccv' => '123',
'billing_name' => 'João da Silva',
'billing_email' => 'joao@example.com',
'billing_document_number' => '12345678900',
'billing_postal_code' => '01311000',
'billing_address_number' => '123',
'billing_phone' => '11988887777',
]);
```

#### Com parcelas e endereço completo

```php
$response = $sdk->checkout()->payWithCreditCard('01987533-8e9e-7384-a2e3-96fb04d556f2', [
'holder_name' => 'Maria Oliveira',
'number' => '5555444433331111',
'card_expiry_date' => '07/29',
'ccv' => '987',
'installments' => 6,
'billing_name' => 'Maria Oliveira',
'billing_email' => 'maria@example.com',
'billing_document_number' => '98765432100',
'billing_postal_code' => '20031000',
'billing_address_number' => '456',
'billing_address_complement' => 'Apto 1203',
'billing_phone' => '21977776666',
]);
```

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
