<div class="max-w-4xl mx-auto mt-8 p-6 bg-gray-100 rounded shadow-md">
    @if ($this->selectedPayment ?? null)
        <div>Payment code</div>
    @else
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Transaction ID</th>
                    <th class="py-2 px-4 border-b">Amount</th>
                    <th class="py-2 px-4 border-b">Payer Email</th>
                    <th class="py-2 px-4 border-b">Due On</th>
                    <th class="py-2 px-4 border-b">VAT</th>
                    <th class="py-2 px-4 border-b">Is VAT Inclusive</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions['data'] as $transaction)
                    <tr class="cursor-pointer hover:bg-gray-200" wire:click="selectPayment({{ $transaction['id'] }})">
                        <td class="py-2 px-4 border-b">{{ $transaction['id'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $transaction['amount'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $transaction['email'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $transaction['due_on'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $transaction['VAT'] }}</td>
                        <td class="py-2 px-4 border-b">{{ $transaction['is_VAT_inclusive'] ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex items-center mr-2 justify-end">
            <div class="text-navy-dark font-semibold text-sm mr-2">
                {{(((int) $this->currentPage - 1) * (int) $this->perPage ) + 1}} -
                {{min($this->perPage * (int) $this->currentPage, $transactions['total']) }}
                of
                {{$transactions['total']}}
            </div>
            <a wire:click="previousPage" class="cursor-pointer mr-2">
                <x-icons.left-arrow />
            </a>
            <a wire:click="nextPage" class="cursor-pointer">
                <x-icons.right-arrow />
            </a>
        </div>
    @endif
</div>
