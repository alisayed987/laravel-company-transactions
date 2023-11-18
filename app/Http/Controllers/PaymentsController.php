<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\AbilityTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    use AbilityTrait;

    /**
     * Validate transaction to:
     * 1- exists
     * 2- not already paid
     *
     * @param string $transactionId
     * @return Transaction
     */
    public function validateTransaction($transactionId)
    {
        $transaction = Transaction::select('transactions.*', 'statuses.name as status_name')
            ->leftJoin('transaction_statuses', 'transactions.id', 'transaction_statuses.transaction_id')
            ->leftJoin('statuses', 'statuses.id', 'transaction_statuses.status_id')
            ->orderBy('transaction_statuses.created_at', 'DESC')
            ->firstWhere('transactions.id', $transactionId);

        if (!$transaction) throw new Exception('transaction id does not exist.');

        if (strtolower($transaction->status_name) == 'paid') {
            throw new Exception('Transaction already paid.');
        }

        return $transaction;
    }

    /**
     * Get remaining amount after this payment to save it with payment (will be used as a log)
     *
     * @param FormRequest $request
     * @param Transaction $transaction
     * @return double
     */
    protected function getNewRemainingAmount(FormRequest $request, Transaction $transaction)
    {
        $allPayments = Payment::where('transaction_id', $request->transaction_id)
            ->orderBy('created_at', 'DESC')->get();

        $newRemaining = null;

        /**
         * Check if there was old payments for the same Transaction
         */
        if ($allPayments->count() > 0) {
            $lastPayment = $allPayments->first();
            $newRemaining = (double) $lastPayment['remaining_amount'] - (double) $request->amount;
        } else {
            $newRemaining = (double) $transaction->amount - (double) $request->amount;
        }

        return $newRemaining;
    }

    /**
     * Get Transaction status depending on transaction duo_on & remaining amount
     *
     * @param Transaction $transaction
     * @param double $newRemaining
     * @return Status
     */
    protected function getNewTransactionStatus(Transaction $transaction, $newRemaining)
    {
        /**
         * Check the new store status for the Transaction
         */
        $transactionStatus = null;
        if ($newRemaining <= 0) {
            $transactionStatus = Status::firstWhere('name', 'Paid');
        } else {
            $duoOnDate = Carbon::parse($transaction->due_on);
            $isOverDue = Carbon::now()->isAfter($duoOnDate);
            if ($isOverDue) {
                $transactionStatus = Status::firstWhere('name', 'Overdue');
            } else {
                $transactionStatus = Status::firstWhere('name', 'Outstanding');
            }
        }

        return $transactionStatus;
    }

    /**
     * Save payment and new transaction status into database
     *
     * @param FormRequest $request
     * @param double $newRemaining
     * @param Transaction $transaction
     * @param Status $transactionStatus
     * @return Payment
     */
    protected function savePaymentAndStatus(
        $request,
        $newRemaining,
        $transaction,
        $transactionStatus,
    )
    {
        $payment = null;
        $dbTransactionSuccess = false;
        DB::transaction(function () use (
            $request,
            $newRemaining,
            &$payment,
            $transaction,
            $transactionStatus,
            &$dbTransactionSuccess
        ) {
            $payment = Payment::create([
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'paid_on' => Carbon::now(),
                'remaining_amount' => $newRemaining,
                'details' => $request->details
            ]);

            $transaction->statuses()->attach($transactionStatus->id);

            /**
             * Will only be true if no issue above lead to rollback
             */
            $dbTransactionSuccess = true;
        }, 3);

        if (!$dbTransactionSuccess) throw new Exception('Database transaction rolled back.');

        return $payment;
    }

    /**
     * Add payment & add new on store status
     *
     * @param FormRequest $request
     * @return Response
     */
    public function addPayment(FormRequest $request, User $authUser = null)
    {
        try {
            $request->validate([
                'transaction_id' => 'required',
                'amount' => 'required|numeric|min:0|not_in:0',
                'details' => 'required|string'
            ]);

            $transaction = $this->validateTransaction($request->transaction_id);

            $this->validateTokenAndAdminRole($request, $authUser);

            $newRemaining = $this->getNewRemainingAmount($request, $transaction);

            $transactionStatus = $this->getNewTransactionStatus($transaction, $newRemaining);

            $payment = $this->savePaymentAndStatus(
                $request,
                $newRemaining,
                $transaction,
                $transactionStatus,
            );

            return response()->json([
                'payment' => $payment,
                'new_stored_transaction_status' => $transactionStatus->name
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * @param FormRequest $request
     * @return Response
     */
    public function getTransactionPayments(FormRequest $request, User $authUser = null)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|integer',
            ]);

            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $payments = Payment::where('transaction_id', $request->transaction_id)
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage, ['*'], 'page', $page)->toArray();

            $transaction = Transaction::firstWhere('id', $request->transaction_id);
            $status = empty($payments['data']) ? null :
                $this->getNewTransactionStatus($transaction, $payments['data'][0]['remaining_amount']);

            return response()->json([
                'payments' => $payments ?? [],
                'transaction_current_status' => empty($payments['data']) ? null : $status->name
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
