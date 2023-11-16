<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    /**
     * Switch between login/register forms
     * @var boolean
     */
    public bool $loginForm = true;
    public string $name = "";
    public string $email = "";
    public string $password = "";
    public bool $disabledButton = true;

    public ?string $redirect_to = null;

    protected $loginRules = [
        'email'=> 'required|email',
        'password'=> 'required|min:4'
    ];

    /**
     * @return void
     */
    public function mount()
    {
        /**
         * redirect to url that was addressed before login
         */
        if (session()->has('url.intended')) {
            $this->redirect_to = session('url.intended');
        }
    }

    /**
     * Toggle login form to switch between login and register
     * @return void
     */
    public function switchForm()
    {
        $this->loginForm = !$this->loginForm;
        $this->reset('name', 'email', 'password');
    }

    public function updated($prop)
    {
        $this->disabledButton =true;
        $this->validateOnly($prop, $this->loginRules);
        $this->disabledButton=false;
    }

    public function login()
    {
        if (! Auth::attempt(array('email' => $this->email, 'password' => $this->password))) {
            $this->addError('loginErr', 'username or password is not correct');
            return;
        }

        if ($this->redirect_to) {
            return $this->redirect($this->redirect_to);
        }

        return redirect()->route('/');
    }

    public function register()
    {
        try {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'min:4'],
            ]);
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            event(new Registered($user));

            Auth::login($user);
        } catch (\Throwable $th) {
            $this->addError('loginErr', $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
