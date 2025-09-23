<?php

namespace App\Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * Fetch users with optional search, sorting, and pagination.
     * 
     * @param string|null $search
     * @param string|null $sortBy
     * @param int $paginate
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function handleFetchUsers($search, $sortBy, $paginate)
    {
        $users = User::with('orders')->where('active', true)
                ->when(!empty($search), function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                })
                ->when(!empty($sortBy), function ($query) use ($sortBy) {
                    $query->orderBy($sortBy, 'asc');
                })->paginate($paginate);

        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'orders_count' => $user->orders->count(),
                'can_edit' => $user->isEditable($user->id, $user->role),
            ];
        });
    }

    /**
     * Create a new user.
     * 
     * @param array $data
     * @return User
     */
    public function handleCreateUser($data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'user',
            'active' => true,
        ]);
    }

    /**
     * Update an existing user.
     * 
     * @param UserRequest $request
     * @param User $user
     * @return User
     */
    public function handleUpdateUser($request, $user)
    {
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return $user;
    }

    /**
     * Send email notification.
     * 
     * @param array $data
     * @return void
     */
    public function handleSendEmail($data)
    {
        Mail::send($data['view'], ['name' => $data['name'], 'email' => $data['email'], 'role' => $data['role']], function ($message) use ($data) {
            $message->to($data['to']);
            $message->subject($data['subject']);
        });
    }

    /**
     * Get the administrator user.
     */
    public function getAdministrator()
    {
        return User::where('role', 'administrator')->first();
    }

    /**
     * Check if the authenticated user can edit the given user.
     * 
     * @param User $user
     * @return bool
     */
    public function isUserEditable($user)
    {
        $authUser = auth()->user();

        return $authUser->isEditable($user->id, $user->role);
    }

    /**
     * Handle user login and return a token.
     * 
     * @param array $credentials
     * @return array  
     */
    public function handleUserLogin($credentials)
    {
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'User logged in successfully.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid email or password.',
        ];
    }

    /**
     * Handle user logout and revoke the token.
     * 
     * @param Request $request
     * @return array
     */
    public function handleUserLogout($request)
    {
        $request->user()->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Successfully logged out'
        ];
    }
}
