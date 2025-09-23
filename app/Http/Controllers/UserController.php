<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;    
    }
    
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
        $users = $this->userService->handleFetchUsers($search, $sortBy, $paginate);

        $response = [
            'page' => $request->query('page', 1),
            'users' => $users,
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created user in storage.
     * 
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        // insert new record
        $user = $this->userService->handleCreateUser($request->only(['name', 'email', 'password']));

        // collect data for sending email
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'to' => $user->email,
            'createdAt' => $user->created_at,
        ];
        $data['view'] = 'emails.user_created';
        $data['subject'] = 'User Created Successfully';
        
        // send email to user
        $this->userService->handleSendEmail($data);

        // send email to administrator
        $data['view'] = 'emails.admin_user_created';
        $data['subject'] = 'New User Created';
        $administrator = $this->userService->getAdministrator();
        if ($administrator) {
            $data['to'] = $administrator->email;
            // send email to admin
            $this->userService->handleSendEmail($data);
        }

        $response = [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at,
        ];

        return response()->json($response, 201);
    }

    /**
     * Update the user.
     * 
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, User $user)
    {
        // Update user details
        $createdUser = $this->userService->handleUpdateUser($request, $user);

        return response()->json([
            'id' => $createdUser->id,
            'email' => $createdUser->email,
            'name' => $createdUser->name,
            'created_at' => $createdUser->created_at,
            'updated_at' => $createdUser->updated_at,
        ]);
    }
}
