<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For create operations (POST), allow all authenticated users
        if ($this->isMethod('post')) {
            return true;
        }

        // For update operations (PUT/PATCH), check if user is editable
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $user = $this->route('user'); // Get the user from route parameter
            if ($user) {
                $userService = app(\App\Modules\User\Services\UserService::class);
                return $userService->isUserEditable($user);
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user ? $this->user->id : null;

        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => $this->isMethod('post')
                ? ['required', 'string', 'min:8'] // create
                : ['nullable', 'string', 'min:8'], // update
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json(['message' => 'Unauthorized'], 403)
        );
    }
}
