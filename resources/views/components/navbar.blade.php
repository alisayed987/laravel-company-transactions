@auth
    <nav x-data="{ open: false }" class="bg-blue-400 p-4 flex justify-between">
        <div >
            Transactions App
        </div>
        <div class="flex justify-center">
            <a class="underline mx-3 text-white text-xl" href="{{url('/transactions')}}">View Transactions</a>
            @if (Auth::user()->hasRole('admin'))
                <a class="underline mx-3 text-white text-xl" href="{{url('/createFinancials')}}">Create Transactions/Payment</a>
                <a class="underline mx-3 text-white text-xl" href="{{url('/generateReports')}}">Show Report Data</a>
            @endif
        </div>
        <!-- User Dropdown -->
        <div class="relative inline-block text-left text-xl" x-data="{ open: false }">
            <div>
                <button @click="open = !open" type="button" class="text-white focus:outline-none flex items-center">
                    <x-icons.navbar-user />
                    {{ Auth::user()->name }} <i class="fas fa-caret-down ml-2"></i>
                    <x-icons.down-arrow />
                </button>
            </div>

            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
        <!-- End User Dropdown -->
    </nav>
@else

@endauth
