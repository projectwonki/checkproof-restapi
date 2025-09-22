<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $paginate = $request->query('paginate', 15);
        $search = $request->query('search', null);
        $sortBy = $request->query('sortBy', 'created_at');

        $users = User::with('orders')->where('active', true)
                ->when(!empty($search), function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                })
                ->when(!empty($sortBy), function ($query) use ($sortBy) {
                    $query->orderBy($sortBy, 'asc');
                })->paginate($paginate);

        $users = $users->map(function ($user) {
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

        $response = [
            'page' => $request->query('page', 1),
            'users' => $users,
        ];

        return response()->json($response);
    }
}
