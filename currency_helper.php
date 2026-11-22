<?php
// Supported Currencies
$SUPPORTED_CURRENCIES = [
    'USD' => ['symbol' => '$', 'name' => 'US Dollar (USD)'],
    'UGX' => ['symbol' => 'UGX ', 'name' => 'Uganda Shilling (UGX)'],
    'KES' => ['symbol' => 'KES ', 'name' => 'Kenyan Shilling (KES)'],
    'TZS' => ['symbol' => 'TZS ', 'name' => 'Tanzanian Shilling (TZS)'],
    'RWF' => ['symbol' => 'RWF ', 'name' => 'Rwandan Franc (RWF)'],
    'EUR' => ['symbol' => '€', 'name' => 'Euro (EUR)'],
    'GBP' => ['symbol' => '£', 'name' => 'British Pound (GBP)'],
];

// Update Active Currency in Session when user switches dropdown
if (isset($_GET['set_currency']) && array_key_exists($_GET['set_currency'], $SUPPORTED_CURRENCIES)) {
    $_SESSION['currency'] = $_GET['set_currency'];
}

$activeCurrency = $_SESSION['currency'] ?? 'USD';

// Fetch Real-Time Exchange Rates (Cached in Session for 3600 seconds)
if (!isset($_SESSION['exchange_rates']) || (time() - ($_SESSION['rates_timestamp'] ?? 0)) > 3600) {
    // Reliable fallback rates relative to 1 USD (used if offline)
    $fallbackRates = [
        'USD' => 1.0,
        'UGX' => 3720.0,
        'KES' => 129.0,
        'TZS' => 2650.0,
        'RWF' => 1360.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
    ];

    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $apiJson = @file_get_contents('https://er-api.com', false, $ctx);

    if ($apiJson !== false) {
        $decoded = json_decode($apiJson, true);
        if (isset($decoded['rates'])) {
            foreach ($fallbackRates as $code => $defaultVal) {
                $fallbackRates[$code] = $decoded['rates'][$code] ?? $defaultVal;
            }
        }
    }

    $_SESSION['exchange_rates'] = $fallbackRates;
    $_SESSION['rates_timestamp'] = time();
}

$currentRate = (float)($_SESSION['exchange_rates'][$activeCurrency] ?? 1.0);
$currencySymbol = $SUPPORTED_CURRENCIES[$activeCurrency]['symbol'];

function formatMoney(float $usdAmount): string {
    global $currentRate, $currencySymbol, $activeCurrency;
    $converted = $usdAmount * $currentRate;
    // Whole numbers for UGX/TZS/RWF; 2 decimals for USD/EUR/GBP/KES
    $decimals = in_array($activeCurrency, ['UGX', 'TZS', 'RWF'], true) ? 0 : 2;
    return $currencySymbol . number_format($converted, $decimals);
}
?>
