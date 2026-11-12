# Changelog

## Unreleased

### Breaking

- `InvoiceCreditCardInput` requires `client_ip`: the constructor argument `clientIp`, placed before
  `installments`, and the `client_ip` key in `fromArray()`. It must be the buyer's IP (IPv4 or IPv6),
  captured from the original browser request. A missing or malformed value throws `InvalidArgumentException`
  before any HTTP call.
- `InvoiceService::payWithCreditCard()` sends `client_ip` in the JSON body of
  `POST /invoices/{invoice}/pay/credit-card`. When the API rejects the field with a 422 (for example, a
  non-public IP in production), the error is returned in `InvoiceCreditCardPaymentResponse::raw()` under
  `errors[].meta.fields`.

### Deploy order

1. Publish this SDK version.
2. Update the WooCommerce plugin to capture the buyer's IP and pass it to the SDK.
3. Deploy the 62Pay API version that requires `client_ip` (zer062/62pay#1051).

The SDK sends no ClearSale SessionID, app key or credentials. 62Pay generates the session on the server.

### Added

- PHPUnit test suite (`composer test`).
