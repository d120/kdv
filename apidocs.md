## KDV Api

### Show User Display

    GET /api.php/me/display/
    Authentication: Basic ZW1haWw6cGFzc3dvcmQ=

Return html

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
* `{ "error" => "unknown_product" }` on unknown product
* `{ "error" => "transaction_failed" }` if user is broke


