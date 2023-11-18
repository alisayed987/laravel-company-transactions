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
     * Validate user role
     *
     * @param FormRequest $request
     * @param User $authUser
     * @return void
     */
    protected function validateTokenAndUserRole(FormRequest $request, $authUser = null)
    {
        if ($authUser) {
            $isAdmin = $authUser->hasRole('admin');
        } else {
            $fullToken = $request->header('Authorization') ?? null;

            if (!$fullToken) throw new Exception('No token provided.');

            $isAdmin = $this->checkIfAdmin($fullToken);

            if (!$isAdmin) throw new Exception('Admins only can add transactions');
        }
    }

    /**
     * Get Tranasction status based on current time & due on date
     *
     * @param Carbon $dueOnDate
     * @return Status
     */
    protected function getNewTransactionStatus($dueOnDate)
    {
        $isOverDue = Carbon::now()->isAfter($dueOnDate);
        $transactionStatus = null;

        if ($isOverDue) {
            $transactionStatus = Status::firstWhere('name', 'Overdue');
        } else {
            $transactionStatus = Status::firstWhere('name', 'Outstanding');
        }

        return $transactionStatus;
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
    function insertTransaction(FormRequest $request, User $payer, $dueOnDate, $transactionStatus)
    {
        $transaction = Transaction::create([
            'amount' => $request->amount,
            'payer' => $payer->id,
            'due_on' => $dueOnDate,
            'VAT' => $request->VAT,
            'is_VAT_inclusive' => $request->is_VAT_inclusive,
        ]);

        $transaction->statuses()->attach($transactionStatus->id);

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

            $this->validateTokenAndUserRole($request, $authUser);

            $dueOnDate = Carbon::parse($request->due_on);

            $transactionStatus = $this->getNewTransactionStatus($dueOnDate);

            $transaction = $this->insertTransaction($request, $payer, $dueOnDate, $transactionStatus);

            return response()->json([
                'transaction' => $transaction
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    public function allTransactions(FormRequest $request)
    {

    }

    public function userTransactions(FormRequest $request)
    {

    }
}
