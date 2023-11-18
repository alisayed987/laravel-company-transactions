<div class="max-w-md mx-auto mt-8 p-6 bg-blue-50 rounded shadow-md">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Form Type:</label>
        <label class="relative inline-flex items-center mb-5 cursor-pointer">
            <input type="checkbox" value="{{$this->transactionType}}" wire:click="switchFormType" class="sr-only peer" @if ($this->transactionType) checked @endif>
            <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            <span class="ms-3 text-sm font-medium text-gray-400 dark:text-gray-500">{{($this->transactionType ? 'Transaction' : 'Payment')}}</span>
          </label>
    </div>

    <div>@error('generalError') <span class="text-sm text-red-500">{{ $message }}</span> @enderror</div>
    @if ($transactionType)
        <!-- Transaction Form -->
        <form wire:submit.prevent="submitForm">
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount:</label>
                <input type="text" id="amount" wire:model="amount" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('amount') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="payer_email" class="block text-sm font-medium text-gray-700">Payer Email:</label>
                <input type="text" id="payer" wire:model="payer_email" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('payer_email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="due_on" class="block text-sm font-medium text-gray-700">Due On:</label>
                <input type="datetime-local" id="due_on" wire:model="due_on" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('due_on') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="VAT" class="block text-sm font-medium text-gray-700">VAT:</label>
                <input type="text" id="VAT" wire:model="VAT" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('VAT') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="is_VAT_inclusive" class="block text-sm font-medium text-gray-700">Is VAT Inclusive:</label>
                <input type="checkbox" id="is_VAT_inclusive" wire:model="is_VAT_inclusive" class="mt-1">
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Submit</button>
        </form>
    @else
        <form wire:submit.prevent="submitForm">
            <div class="mb-4">
                <label for="transactionId" class="block text-sm font-medium text-gray-700">Transaction ID:</label>
                <input type="text" id="transactionId" wire:model="transactionId" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('transactionId') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="paymentAmount" class="block text-sm font-medium text-gray-700">Amount:</label>
                <input type="text" id="paymentAmount" wire:model="paymentAmount" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
                @error('paymentAmount') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="paymentDetails" class="block text-sm font-medium text-gray-700">Details:</label>
                <textarea id="paymentDetails" wire:model="paymentDetails" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400"></textarea>
                @error('paymentDetails') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Submit Payment</button>
        </form>
    @endif
</div>
