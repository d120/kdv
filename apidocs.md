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


### Transfer Money To Another User

    POST /api.php/me/wiretransfer/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

    charge=0.23&transfer_to=<recipient>&verwendungszweck=transaction+comment

`<recipient>` may be either the recipients mail address or the recipients account number

Returns JSON:

* `{ "success" => true }` on success
* `{ "success" => false, "error" => "transaction_failed" }` if sending user is broke
* `{ "success" => false, "error" => "invalid_charge" }` if charge was not positive a positive number
* `{ "success" => false, "error" => "invalid_account_number" }` or
* `{ "success" => false, "error" => "user_not_found" }` if recipient was not found

### Register for GCM Push Notifications

    GET /api.php/me/register_notifications/?token=<gcm_token>
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

`<gcm_token>` must be a valid gcm registration token or the string `null` to unregister.

Returns JSON:

* `{ "success" => true, "changed" => true }` if the registriation was successful
* `{ "success" => true, "changed" => false }` if this token is already registered
* `{ "success" => false, "message" => "InvalidRegistration" }` if an invalid token was passed
* `{ "success" => true }` if unregistration was successful


### Get Ad

    GET /api.php/ad/


### Set Ad

    PUT /api.php/ad/
    Content-Type: text/plain

    Hier koennte
    Ihre Werbung
      stehen


