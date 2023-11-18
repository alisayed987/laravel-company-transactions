<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    /**
     * Checks if request sent from admin
     *
     * @param string $fullToken
     * @return boolean
     */
    private function checkIfAdmin($fullToken)
    {
        [$bearer_id, $token] = explode('|', $fullToken, 2);

        $tokenId = explode(' ', $bearer_id, 2)[1];
        /**
         * @var User
         */
        $user = User::select(['*', 'personal_access_tokens.id as p_id', 'users.id as id'])
            ->join('personal_access_tokens', 'users.id', 'personal_access_tokens.tokenable_id')
            ->firstWhere('personal_access_tokens.id', $tokenId);

        return $user->hasRole('admin');
    }

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
            ->join('transaction_statuses', 'transactions.id', 'transaction_statuses.transaction_id')
            ->join('statuses', 'statuses.id', 'transaction_statuses.status_id')
            ->orderBy('transaction_statuses.created_at', 'DESC')
            ->firstWhere('transactions.id', $transactionId);

        if (!$transaction) throw new Exception('transaction id does not exist.');

        if (strtolower($transaction->status_name) == 'paid') {
            throw new Exception('Transaction already paid.');
        }

        return $transaction;
    }

    /**
     * Add payment & add new on store status
     *
     * @param FormRequest $request
     * @return Response
     */
    public function addPayment(FormRequest $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required',
                'amount' => 'required|numeric|min:0|not_in:0',
                'details' => 'required|string'
            ]);

            $transaction = $this->validateTransaction($request->transaction_id);

            $fullToken = $request->header('Authorization') ?? null;

            if (!$fullToken) throw new Exception('No token provided.');

            $isAdmin = $this->checkIfAdmin($fullToken);

            if (!$isAdmin) throw new Exception('Admins only can add payments');

            $allPayments = Payment::where('transaction_id', $request->transaction_id)
                ->orderBy('created_at', 'DESC')->get();

            /**
             * Check if there was old payments for the same Transaction
             */
            if ($allPayments->count() > 0) {
                $lastPayment = $allPayments->first();
                $newRemaining = (double) $lastPayment['remaining_amount'] - (double) $request->amount;
            } else {
                $newRemaining = (double) $transaction->amount - $request->amount;
            }

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

            return response()->json([
                'payment' => $payment,
                'new_stored_transaction_status' => $transactionStatus->name
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
