<div>
    <div class="max-w-md mx-auto mt-8 p-6 bg-blue-50 rounded shadow-md">
        @error('error') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        <form wire:submit.prevent="submitForm">
            <div class="mb-4">
                <label for="fromDate" class="block text-sm font-medium text-gray-700">From Date:</label>
                <input type="datetime-local" id="fromDate" wire:model="from" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
            </div>

            <div class="mb-4">
                <label for="toDate" class="block text-sm font-medium text-gray-700">To Date:</label>
                <input type="datetime-local" id="toDate" wire:model="to" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400">
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Get Report</button>
        </form>
    </div>
    @if ($this->reportData)
        <div class="max-w-md mx-auto mt-8 p-6 bg-gray-200 rounded shadow-md">

            <form>
                <div class="mb-4">
                    <label for="fromDate" class="block text-sm font-medium text-gray-700">From Date:</label>
                    <input type="datetime-local" id="fromDate" value="{{$this->from}}" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400" readonly>
                </div>

                <div class="mb-4">
                    <label for="toDate" class="block text-sm font-medium text-gray-700">To Date:</label>
                    <input type="datetime-local" id="toDate" value="{{$this->to}}" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400" readonly>
                </div>

                <div class="mb-4">
                    <label for="numPaid" class="block text-sm font-medium text-gray-700">Number of Paid:</label>
                    <input type="number" id="numPaid" value="{{$this->reportData['paid']}}" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400" readonly>
                </div>

                <div class="mb-4">
                    <label for="numOverdue" class="block text-sm font-medium text-gray-700">Number of Overdue:</label>
                    <input type="number" id="numOverdue" value="{{$this->reportData['overdue']}}" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400" readonly>
                </div>

                <div class="mb-4">
                    <label for="numOutstanding" class="block text-sm font-medium text-gray-700">Number of Outstanding:</label>
                    <input type="number" id="numOutstanding" value="{{$this->reportData['outstanding']}}" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-400" readonly>
                </div>

                <!-- Display a message indicating that the form is read-only -->
                <p class="text-sm text-gray-500">This form is in read-only mode.</p>
            </form>
        </div>
    @endif
</div>
