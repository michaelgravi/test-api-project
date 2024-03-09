<?php

namespace App\Http\Controllers;

use App\Http\Requests\AmountTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Services\ExchangeRateService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{

    protected $transaction;
    protected $exchangeRateService;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Function to store a transaction in the database.
     */
    public function store(StoreTransactionRequest $request, LogService $logService)
    {
        $transaction = $this->transaction->create($request->all());
        $logService->log();
        return response()->json($transaction, 201);
    }

    /**
     * Retrieve a list of transactions from the database.
     */
    public function getTransactionsList(Request $request)
    {
        $transaction = auth()->user()->getTransactionsList($request->query('filter'));
        return response()->json($transaction);
    }

    /**
     * Function to delete a transaction.
     */
    public function delete($id)
    {
        $result = $this->transaction->deleteTransaction($id);
        return response()->json($result);
    }

    /**
     * Retrieve a transaction from the database.
     *
     */
    public function getTransaction($id)
    {
        $transaction = auth()->user()->transactions->find($id);
        return response()->json($transaction);
    }

    /**
     * Retrieve amount transaction from the database.
     *
     */
    public function getTransactionsAmount(AmountTransactionRequest $request)
    {
        $transaction = auth()->user()->getTransactionsAmount($request);
        return response()->json($transaction);
    }
}
