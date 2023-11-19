<?php

namespace App\Livewire;

use App\Http\Controllers\TransactionsController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GenerateReports extends Component
{
    public $from = null;
    public $to = null;

    public $reportData = null;

    protected $rules = [
        'from' => 'required|date',
        'to' => 'required|date'
    ];

    public function submitForm()
    {
        try {
            $transController = new TransactionsController();
            $body = new FormRequest([
                'from' => $this->from,
                'to' => $this->to,
            ]);
            $body->validate($this->rules);

            $res = $transController->getReport($body, auth()->user());

            $this->reportData = (array) $res->getData();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $this->addError('error', $th->getMessage());
        }

    }

    public function render()
    {
        return view('livewire.generate-reports');
    }
}
