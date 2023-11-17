<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    /**
     * Create transaction with its initial status
     *
     * @param FormRequest $request
     * @return Response
     */
    public function createTransaction(FormRequest $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'payer_email' => 'required|email',
                'due_on' => 'required|date',
                'VAT' => 'required|numeric|between:0,100',
                'is_VAT_inclusive' => 'required|boolean',
            ]);

            $payer = User::firstWhere('email', $request->payer_email);

            if (!$payer) throw new Exception('Email does not exist.');

            $duoOnDate = Carbon::parse($request->due_on);
            $isOverDue = Carbon::now()->isAfter($duoOnDate);

            $transaction = Transaction::create([
                'amount' => $request->amount,
                'payer' => $payer->id,
                'due_on' => $duoOnDate,
                'VAT' => $request->VAT,
                'is_VAT_inclusive' => $request->is_VAT_inclusive,
            ]);

            $transactionStatusId = null;

            if ($isOverDue) {
                $transactionStatusId = Status::firstWhere('name', 'Overdue')->id;
            } else {
                $transactionStatusId = Status::firstWhere('name', 'Outstanding')->id;
            }
            $transaction->statuses()->attach($transactionStatusId);

            return response()->json([
                'transaction' => $transaction
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }

    }
}
