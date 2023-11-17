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

            $fullToken = $request->header('Authorization');
            $isAdmin = $this->checkIfAdmin($fullToken);

            if (!$isAdmin) throw new Exception('Admins only can add transactions');

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
