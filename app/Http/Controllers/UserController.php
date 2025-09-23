<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate', 15);
        $search = $request->query('search', null);
        $sortBy = $request->query('sortBy', 'created_at');

        // fetch users with active status, including their orders count
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

    /**
     * Store a newly created resource in storage.
     * 
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        // insert new record
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'role' => 'user',
            'active' => true,
        ]);

        // send email to user
        $view = 'emails.user_created';
        Mail::send($view, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('User Created Successfully');
        });

        // send email to administrator
        $administrator = User::where('role', 'administrator')->first();
        if ($administrator) {
            $view = 'emails.admin_user_created';
            Mail::send($view, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'createdAt' => $user->created_at], function ($message) use ($administrator) {
                $message->to($administrator->email);
                $message->subject('New User Created');
            });
        }

        $response = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at,
        ];

        return response()->json($response, 201);
    }

    public function update(UserRequest $request, User $user)
    {
        // Check if the authenticated user has permission to edit
        if (!auth()->user()->isEditable($user->id, $user->role)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update user details
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
}
