<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'nick_usuario' => ['required', 'string', function($attribute, $value, $fail){
                if (is_string($value) && !empty($value)) {
                    $user = User::where('nick_usuario', '=', $value)->first();
                }
                if($user){
                    if($user->estado_usuario!=1){
                        $fail(__('Este usuario se encuentra desactivado.'));
                    }
                }else{
                    $fail(__('Este usuario no se encuentra en nuestros registros.'));
                }
            }],
            'password' => ['required', 'string',],
        ];
    }

    public function messages()
    {
        return [
            'nick_usuario.required'   => 'El nombre de usuario es obligatorio.',
            'nick_usuario.string'   => 'El nombre de usuario debe contener cadenas de texto.',
            'password.required'   => 'Debe ingresar su contraseña.',
            'password.string'   => 'La contraseña debe contener cadenas de texto',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('nick_usuario', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nick_usuario' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nick_usuario' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('nick_usuario')) . '|' . $this->ip());
    }
}
