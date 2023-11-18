<?php

namespace App\Livewire;

use App\Http\Controllers\TransactionsController;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Livewire\Component;

class TransactionsAndPayments extends Component
{
    public ?array $transactions = null;

    public $perPage = 10;
    public $currentPage = 1;

    public function mount()
    {
        $this->getTransactions();
    }

    /**
     * Get Query string
     *
     * @return array
     */
    protected function queryString(): array
    {
        return [
            'perPage'  => ['except' => 10],
            'currentPage' => ['except' => 1],
        ];
    }

    public function selectPayment($transactionId)
    {
        // TODO:: add payment selection logic
    }

    /**
     * query required transactions from database
     *
     * @return void
     */
    public function getTransactions() {
        /**
         * @var User
         */
        $user = auth()->user();
        $transController = new TransactionsController();
        $body = new FormRequest([
            'per_page' => $this->perPage,
            'page' => $this->currentPage
        ]);
        if ($user->hasRole('admin')) {
            $this->transactions = $transController->allTransactions($body, $user)->getData('transactions')['transactions'];
        } else {
            $this->transactions = $transController->customerTransactions($body, $user)->getData('transactions')['transactions'];
        }
    }

    /**
     * on Clicking right page arrow
     *
     * @return void
     */
    public function nextPage()
    {
        if ($this->currentPage < $this->transactions['last_page']) {
            $this->currentPage += 1;
            $this->getTransactions();
        }
    }

    /**
     * on Clicking left page arrow
     *
     * @return void
     */
    public function previousPage()
    {
        if ($this->currentPage >1) {
            $this->currentPage -= 1;
            $this->getTransactions();
        }
    }

    public function render()
    {
        return view('livewire.transactions-and-payments');
    }
}
