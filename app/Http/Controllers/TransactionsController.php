<?php

namespace App\Http\Controllers;

use App\Definitions\TransactionsStatuses;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\AbilityTrait;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    use AbilityTrait;

    /**
     * Get Transaction status based on current time & due on date
     *
     * @param Carbon $dueOnDate
     * @return Status
     */
    protected function getNewTransactionStatus($dueOnDate)
    {
        $isOverDue = Carbon::now()->isAfter($dueOnDate);

        if ($isOverDue) {
            return TransactionsStatuses::OVERDUE;
        } else {
            return TransactionsStatuses::OUTSTANDING;
        }
    }

    /**
     * Add transaction with its on store status in database
     *
     * @param FormRequest $request
     * @param User $payer
     * @param Carbon $dueOnDate
     * @param Status $transactionStatus
     * @return Transaction
     */
    function insertTransaction(FormRequest $request, User $payer, $dueOnDate)
    {
        $transaction = Transaction::create([
            'amount' => $request->amount,
            'payer' => $payer->id,
            'due_on' => $dueOnDate,
            'VAT' => $request->VAT,
            'is_VAT_inclusive' => $request->is_VAT_inclusive,
            'is_paid' => false
        ]);

        return $transaction;
    }
    /**
     * Create transaction with its initial status
     *
     * @param FormRequest $request
     * @return Response
     */
    public function createTransaction(FormRequest $request, User $authUser = null)
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

            $this->validateTokenAndAdminRole($request, $authUser);

            $dueOnDate = Carbon::parse($request->due_on);

            $transactionStatus = $this->getNewTransactionStatus($dueOnDate);

            $transaction = $this->insertTransaction($request, $payer, $dueOnDate, $transactionStatus);

            return response()->json([
                'transaction' => $transaction,
                'transaction_status' => $transactionStatus
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Get all transactions by admin only
     *
     * @param FormRequest $request
     * @param User $authUser
     * @return void
     */
    public function allTransactions(FormRequest $request, $authUser = null)
    {
        try {
            $request->validate([
                'per_page' => 'integer',
                'page' => 'integer',
            ]);

            $this->validateTokenAndAdminRole($request, $authUser);

            $perPage = $request->per_page ?? 5;
            $page = $request->page ?? 1;
            $transactions = Transaction::select('transactions.*', 'users.email')
                ->leftJoin('users', 'transactions.payer', 'users.id')
                ->paginate($perPage, ['*'], 'page', $page)->toArray();

            return response()->json([
                'transactions' => $transactions,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Get current Auth user Transactions
     *
     * @param FormRequest $request
     * @param User $authUser
     * @return Response
     */
    public function customerTransactions(FormRequest $request, $authUser = null)
    {
        try {
            $request->validate([
                'per_page' => 'integer',
                'page' => 'integer',
            ]);

            if ($authUser) { $user = $authUser; }
            else { $user = $this->extractUserFromToken($request->header('Authorization')); }

            $perPage = $request->per_page ?? 5;
            $page = $request->page ?? 1;
            $transactions = Transaction::where('payer', $user->id)
                ->paginate($perPage, ['*'], 'page', $page)->toArray();

            return response()->json([
                'transactions' => $transactions,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Get current Auth user Transactions
     *
     * @param FormRequest $request
     * @param User $authUser
     * @return Response
     */
    public function getReport(FormRequest $request, $authUser = null)
    {
        try {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
            ]);

            $this->validateTokenAndAdminRole($request, $authUser);

            $from = Carbon::parse($request->from);
            $to = Carbon::parse($request->to);

            $report = Transaction::selectRaw(
                    "sum(case when is_paid = true then 1 else 0 end) AS paidCount,
                    sum(case when TIMESTAMPDIFF(SECOND, Now(), due_on) > 0 AND is_paid = false then 1 else 0 end) AS overDueCount,
                    sum(case when  TIMESTAMPDIFF(SECOND, Now(), due_on) < 0 AND is_paid = false then 1 else 0 end) AS outStandingCount"
                )
                ->whereBetween('created_at', [$from, $to])
                ->get()->first();

            return response()->json([
                'from' => $from,
                'to' => $to,
                'paid' => $report->paidCount,
                'outstanding' => $report->overDueCount,
                'overdue' => $report->outStandingCount
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
