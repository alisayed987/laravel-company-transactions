<div class="max-w-md mx-auto my-10">
    <div class="bg-white p-8 rounded shadow border border-gray-300">
        <h2 class="text-2xl font-semibold mb-6 text-center">{{ $loginForm ? 'Login' : 'Register' }}</h2>
        @error('loginErr') <div class="font-medium text-small text-red-600 w-full m-auto">{{ $message }}</div> @enderror

        @if($loginForm)
            <form wire:submit.prevent="login">
                <!-- Login Form -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" wire:model.live="email" id="email" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-500">
                </div>
                @error('email') <span class="font-medium text-small text-red-600 w-full m-auto">{{ $message }}</span> @enderror

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                    <input type="password" wire:model.live="password" id="password" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-blue-500">
                </div>
                @error('password') <span class="font-medium text-small text-red-600 w-full m-auto">{{ $message }}</span> @enderror

                <div class="flex items-center justify-between">
                    <button type="submit" @disabled($this->disabledButton) class="bg-blue-500 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">Login</button>
                    <span class="ml-2 text-sm text-gray-600 cursor-pointer" wire:click="switchForm">Switch to Register</span>
                </div>
            </form>
        @else
            <form wire:submit.prevent="register">
                <!-- Registration Form -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-600">Name</label>
                    <input type="text" wire:model.live="name" id="name" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-green-500">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" wire:model.live="email" id="email" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-green-500">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                    <input type="password" wire:model.live="password" id="password" class="mt-1 p-2 w-full border rounded focus:outline-none focus:border-green-500">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded focus:outline-none focus:shadow-outline-green">Register</button>
                    <span class="ml-2 text-sm text-gray-600 cursor-pointer" wire:click="switchForm">Switch to Login</span>
                </div>
            </form>
        @endif
    </div>
</div>
