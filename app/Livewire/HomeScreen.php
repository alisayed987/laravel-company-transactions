<?php

namespace App\Livewire;

use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\TransactionsController;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class HomeScreen extends Component
{
    public bool $transactionType = true;

    // Transaction form fields
    public $amount = null;
    public $payer_email = null;
    public $due_on = null;
    public $VAT = null;
    public $is_VAT_inclusive = false;

    // Payment form fields
    public $transactionId;
    public $paymentAmount;
    public $paymentDetails;

    protected array $transactionRules = [
        'amount' => 'required|numeric',
        'payer_email' => 'required|string|email',
        'due_on' => 'required|date',
        'VAT' => 'required|numeric|between:0,100',
        'is_VAT_inclusive' => 'boolean',
    ];

    protected array $paymentRules = [
        'transactionId' => 'required|string',
        'paymentAmount' => 'required|numeric',
        'paymentDetails' => 'nullable|string',
    ];

    public function switchFormType()
    {
        $this->transactionType = !$this->transactionType;
        $this->reset('amount', 'payer_email', 'due_on', 'VAT', 'is_VAT_inclusive',
            'transactionId', 'paymentAmount','paymentDetails');
    }

    public function submitForm()
    {
        // Validate form inputs based on the selected form type
        $rules = $this->transactionType ? $this->transactionRules : $this->paymentRules;

        try {
            $this->validate($rules);

            if ($this->transactionType) {
                $transController = new TransactionsController();
                $body = new FormRequest([
                    'amount' => $this->amount,
                    'payer_email' => $this->payer_email,
                    'due_on' => $this->due_on,
                    'VAT' => $this->VAT,
                    'is_VAT_inclusive' => $this->is_VAT_inclusive,
                ]);
                $response = $transController->createTransaction($body, auth()->user());
                if ($response->status() == 400) throw new Exception($response->original['message']);
            } else {
                $paymentController = new PaymentsController();
                $body = new FormRequest([
                    'transaction_id' => $this->transactionId,
                    'amount' => $this->paymentAmount,
                    'details' => $this->paymentDetails,
                ]);
                $response = $paymentController->addPayment($body, auth()->user());
                if ($response->status() == 400) throw new Exception($response->original['message']);
            }

            $this->reset('amount', 'payer_email', 'due_on', 'VAT', 'is_VAT_inclusive',
                'transactionId', 'paymentAmount','paymentDetails');

            $this->redirect('/');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $this->addError('generalError', $th->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.home-screen');
    }
}
