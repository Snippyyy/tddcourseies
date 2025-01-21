<?php


use Illuminate\Support\Carbon;
use Spatie\WebhookClient\Models\WebhookCall;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertSame;

it('can create a valid Paddle webhook signature', function () {

    //ARRANGE

    $originalTimestamp = 1718139311;

    [$originalArrBody, $originalSigHeader, $originalRawJsonBody] = getValidPaddleWebhookRequest();


    //ACT

    [$body, $header] = generateValidSignedPaddleWebhookRequest($originalArrBody, $originalTimestamp);

    assertSame(json_encode($body), $originalRawJsonBody);
    assertSame($header, $originalSigHeader);

});

it('stores a paddle purchase request', function () {

    assertDatabaseCount(WebhookCall::class, 0);

    [$arrData] = getValidPaddleWebhookRequest();

    //We will have to generate a fresh signature because the timestamp cannos be older
    // than 5 seconds, or our webhook signature validator middleware will block the request

    [$requestBody, $requestHeader] = generateValidSignedPaddleWebhookRequest($arrData);

    postJson('webhooks', $requestBody, $requestHeader);

    assertDatabaseCount(WebhookCall::class, 1);
});

it('does not store invalid paddle purchase request', function () {

    assertDatabaseCount(WebhookCall::class, 0);

    post('webhooks', []);

    assertDatabaseCount(WebhookCall::class, 0);

});


it('dispatches a job for a valid paddle request', function () {



});

it('does not dispatch a job for invalid paddle request', function () {

    

});


function getValidPaddleWebhookRequest() {

    $sigHeader = ['Paddle-Signature' =>
    'ts=1718139311,h1=a796c887f8b8f3342fed46fa4cf05062a08dc676494dd6c73736600a5a6ea59b'];

    $parsedData = [
        "event_id" => "evt_01jhqxabehswf9nbqxkxa3evhe",
        "event_type" => "transaction.completed",
        "occurred_at" => "2025-01-16T15:57:09.457275Z",
        "notification_id" => "ntf_01jhqxabky66ekjfrxjxccw8qj",
        "data" => [
            "id" => "txn_01jhqx75ghvvk7n3zh2mpmcmv6",
            "items" => [
                [
                    "price" => [
                        "id" => "pri_01jhqsmer4fc106jrdsxc5ej3e",
                        "name" => "Pago Laravel For Beginners",
                        "type" => "standard",
                        "status" => "active",
                        "quantity" => [
                            "maximum" => 10000,
                            "minimum" => 1
                        ],
                        "tax_mode" => "account_setting",
                        "created_at" => "2025-01-16T14:52:46.212362Z",
                        "product_id" => "pro_01jhqsgayg9eh8cr503eaddw1r",
                        "unit_price" => [
                            "amount" => "1500",
                            "currency_code" => "EUR"
                        ],
                        "updated_at" => "2025-01-16T14:52:46.212362Z",
                        "custom_data" => null,
                        "description" => "Pago unico",
                        "trial_period" => null,
                        "billing_cycle" => null,
                        "unit_price_overrides" => [
                        ]
                    ],
                    "price_id" => "pri_01jhqsmer4fc106jrdsxc5ej3e",
                    "quantity" => 1,
                    "proration" => null
                ]
            ],
            "origin" => "web",
            "status" => "completed",
            "details" => [
                "totals" => [
                    "fee" => "124",
                    "tax" => "260",
                    "total" => "1500",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "1116",
                    "subtotal" => "1240",
                    "grand_total" => "1500",
                    "currency_code" => "EUR",
                    "credit_to_balance" => "0"
                ],
                "line_items" => [
                    [
                        "id" => "txnitm_01jhqx7tntf3zg1e4zvrwftmb5",
                        "totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "item_id" => null,
                        "product" => [
                            "id" => "pro_01jhqsgayg9eh8cr503eaddw1r",
                            "name" => "Laravel For Beginners",
                            "type" => "standard",
                            "status" => "active",
                            "image_url" => null,
                            "created_at" => "2025-01-16T14:50:31.248Z",
                            "updated_at" => "2025-01-16T14:50:31.248Z",
                            "custom_data" => [
                                "product" => "one"
                            ],
                            "description" => "Laravel For Beginners",
                            "tax_category" => "standard"
                        ],
                        "price_id" => "pri_01jhqsmer4fc106jrdsxc5ej3e",
                        "quantity" => 1,
                        "tax_rate" => "0.21",
                        "unit_totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "is_tax_exempt" => false,
                        "revised_tax_exempted" => false
                    ]
                ],
                "payout_totals" => [
                    "fee" => "125",
                    "tax" => "262",
                    "total" => "1512",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "1125",
                    "fee_rate" => "0.05",
                    "subtotal" => "1250",
                    "grand_total" => "1512",
                    "currency_code" => "USD",
                    "exchange_rate" => "1.0081210999999999",
                    "credit_to_balance" => "0"
                ],
                "tax_rates_used" => [
                    [
                        "totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "tax_rate" => "0.21"
                    ]
                ],
                "adjusted_totals" => [
                    "fee" => "124",
                    "tax" => "260",
                    "total" => "1500",
                    "earnings" => "1116",
                    "subtotal" => "1240",
                    "grand_total" => "1500",
                    "currency_code" => "EUR"
                ]
            ],
            "checkout" => [
                "url" => "https://localhost?_ptxn=txn_01jhqx75ghvvk7n3zh2mpmcmv6"
            ],
            "payments" => [
                [
                    "amount" => "1500",
                    "status" => "captured",
                    "created_at" => "2025-01-16T15:57:04.120017Z",
                    "error_code" => null,
                    "captured_at" => "2025-01-16T15:57:06.360045Z",
                    "method_details" => [
                        "card" => [
                            "type" => "visa",
                            "last4" => "4242",
                            "expiry_year" => 2025,
                            "expiry_month" => 6,
                            "cardholder_name" => "pepito palotes"
                        ],
                        "type" => "card"
                    ],
                    "payment_method_id" => "paymtd_01jhqxa66zssqwf4zct242e41c",
                    "payment_attempt_id" => "547f7bea-2677-4cbb-9225-d7a67bf24ada",
                    "stored_payment_method_id" => "fa409916-75eb-441b-a57d-03c7cad6988a"
                ]
            ],
            "billed_at" => "2025-01-16T15:57:06.568954Z",
            "address_id" => "add_01jhqx7te0sy51xq1htd7jm2w7",
            "created_at" => "2025-01-16T15:55:25.120215Z",
            "invoice_id" => "inv_01jhqxa93jv69x32r6zkgxh2c4",
            "updated_at" => "2025-01-16T15:57:08.936242114Z",
            "business_id" => null,
            "custom_data" => null,
            "customer_id" => "ctm_01jhqx7tdmyamd4vj1ynf6f1jg",
            "discount_id" => null,
            "receipt_data" => null,
            "currency_code" => "EUR",
            "billing_period" => null,
            "invoice_number" => "10626-10001",
            "billing_details" => null,
            "collection_mode" => "automatic",
            "subscription_id" => null
        ]
    ];

$rawJsonBody = '{"event_id":"evt_01jhqxabehswf9nbqxkxa3evhe","event_type":"transaction.completed","occurred_at":"2025-01-16T15:57:09.457275Z","notification_id":"ntf_01jhqxabky66ekjfrxjxccw8qj","data":{"id":"txn_01jhqx75ghvvk7n3zh2mpmcmv6","items":[{"price":{"id":"pri_01jhqsmer4fc106jrdsxc5ej3e","name":"Pago Laravel For Beginners","type":"standard","status":"active","quantity":{"maximum":10000,"minimum":1},"tax_mode":"account_setting","created_at":"2025-01-16T14:52:46.212362Z","product_id":"pro_01jhqsgayg9eh8cr503eaddw1r","unit_price":{"amount":"1500","currency_code":"EUR"},"updated_at":"2025-01-16T14:52:46.212362Z","custom_data":null,"description":"Pago unico","trial_period":null,"billing_cycle":null,"unit_price_overrides":[]},"price_id":"pri_01jhqsmer4fc106jrdsxc5ej3e","quantity":1,"proration":null}],"origin":"web","status":"completed","details":{"totals":{"fee":"124","tax":"260","total":"1500","credit":"0","balance":"0","discount":"0","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR","credit_to_balance":"0"},"line_items":[{"id":"txnitm_01jhqx7tntf3zg1e4zvrwftmb5","totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"item_id":null,"product":{"id":"pro_01jhqsgayg9eh8cr503eaddw1r","name":"Laravel For Beginners","type":"standard","status":"active","image_url":null,"created_at":"2025-01-16T14:50:31.248Z","updated_at":"2025-01-16T14:50:31.248Z","custom_data":{"product":"one"},"description":"Laravel For Beginners","tax_category":"standard"},"price_id":"pri_01jhqsmer4fc106jrdsxc5ej3e","quantity":1,"tax_rate":"0.21","unit_totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"is_tax_exempt":false,"revised_tax_exempted":false}],"payout_totals":{"fee":"125","tax":"262","total":"1512","credit":"0","balance":"0","discount":"0","earnings":"1125","fee_rate":"0.05","subtotal":"1250","grand_total":"1512","currency_code":"USD","exchange_rate":"1.0081210999999999","credit_to_balance":"0"},"tax_rates_used":[{"totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"tax_rate":"0.21"}],"adjusted_totals":{"fee":"124","tax":"260","total":"1500","earnings":"1116","subtotal":"1240","grand_total":"1500","currency_code":"EUR"}},"checkout":{"url":"https:\/\/localhost?_ptxn=txn_01jhqx75ghvvk7n3zh2mpmcmv6"},"payments":[{"amount":"1500","status":"captured","created_at":"2025-01-16T15:57:04.120017Z","error_code":null,"captured_at":"2025-01-16T15:57:06.360045Z","method_details":{"card":{"type":"visa","last4":"4242","expiry_year":2025,"expiry_month":6,"cardholder_name":"pepito palotes"},"type":"card"},"payment_method_id":"paymtd_01jhqxa66zssqwf4zct242e41c","payment_attempt_id":"547f7bea-2677-4cbb-9225-d7a67bf24ada","stored_payment_method_id":"fa409916-75eb-441b-a57d-03c7cad6988a"}],"billed_at":"2025-01-16T15:57:06.568954Z","address_id":"add_01jhqx7te0sy51xq1htd7jm2w7","created_at":"2025-01-16T15:55:25.120215Z","invoice_id":"inv_01jhqxa93jv69x32r6zkgxh2c4","updated_at":"2025-01-16T15:57:08.936242114Z","business_id":null,"custom_data":null,"customer_id":"ctm_01jhqx7tdmyamd4vj1ynf6f1jg","discount_id":null,"receipt_data":null,"currency_code":"EUR","billing_period":null,"invoice_number":"10626-10001","billing_details":null,"collection_mode":"automatic","subscription_id":null}}';

    return [$parsedData, $sigHeader, $rawJsonBody];
}

function generateValidSignedPaddleWebhookRequest( array $data, ?int $timestamp = null): array {

    $ts = $timestamp ?? Carbon::now()->unix();

    $secret = config('services.paddle.notification-endpoint-secret-key');

    $rawJsonBody = json_encode($data);

    $calculatedSig = hash_hmac('sha256', "{$ts}:{$rawJsonBody}", $secret);

    $header = [
        'Paddle-Signature' => "ts={$ts},h1={$calculatedSig}",
    ];

    return [$data, $header];
}
