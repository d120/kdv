## KDV Api

### Authentication

A client application authenticates via the HTTP Authentication request header (Basic auth).

Two kinds of credentials can be used:

| username | password |
|----------|----------|
| the email address | the password (as set in account details) |
| `.apitoken.` | the api token (50 hex digits) |


### Show User Display

    GET /api.php/me/display/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

Returns a auto-reloading HTML page

### Get User Ledger

    GET /api.php/me/ledger/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

Returns JSON:

    {
      "success": true,
      "ledger": [
        // ...
      ],
      "debt": -4223
    }

* debt is the user's debt in cents

### Get Product List

    GET /api.php/productlist/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

Returns JSON:

    [
      { "id": 1, "code": "123412341234", "name": "Product name", "price": 100, "disabled_at": null },
      // ...
    ]

* code is the product's bar code
* price is the product's price in cents


### Buy Product

    POST /api.php/me/buy/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

    barcode=123412341234

Returns JSON:

* `{ "success" => true }` on success
* `{ "success" => false, "error" => "unknown_product" }` on unknown product
* `{ "success" => false, "error" => "transaction_failed" }` if user is broke


### Deposit Money

    POST /api.php/me/deposit/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

    amount=42.00

Returns JSON:

* `{ "success" => true }` on success
* `{ "success" => false, "error" => "invalid_amount" }` otherwise


### Get Ad

    GET /api.php/ad/


### Set Ad

    PUT /api.php/ad/
    Content-Type: text/plain

    Hier koennte
    Ihre Werbung
      stehen


