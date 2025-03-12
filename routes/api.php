<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/currency/convert', function (Request $request) {
    $request->validate([
        'from_currency' => 'required|string|size:3',
        'to_currency' => 'required|string|size:3',
        'amount' => 'required|numeric|min:0',
    ]);

    $from = strtoupper($request->input('from_currency'));
    $to = strtoupper($request->input('to_currency'));
    $amount = $request->input('amount');

    $exchangeRates = [
        'USD' => ['USD' => 1.0000, 'TWD' => 31.5000, 'JPY' => 148.5000],
        'TWD' => ['USD' => 0.0317, 'TWD' => 1.0000, 'JPY' => 4.7143],
        'JPY' => ['USD' => 0.00673, 'TWD' => 0.2121, 'JPY' => 1.0000],
    ];

    if (!isset($exchangeRates[$from]) || !isset($exchangeRates[$from][$to])) {
        return response()->json(['error' => '無效的貨幣代碼'], 400);
    }

    $rate = $exchangeRates[$from][$to];
    $convertedAmount = $amount * $rate;

    return response()->json([
        'from_currency' => $from,
        'to_currency' => $to,
        'amount' => $amount,
        'converted_amount' => round($convertedAmount, 2),
        'rate' => $rate,
    ]);
});
