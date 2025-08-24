<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
   
    public function index()
    {
        return response()->json([
            'message' => 'User list retrieved successfully.',
            'data' => User::all(),
        ]);
    }

   
    public function store(Request $request)
    {
        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    
    public function show(string $id)
    {
        $user = User::find($id);

        return response()->json($user);
    }

   
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id); 

    $data = $request->validate([
        'name'  => ['sometimes','required','string','max:255'],
        'email' => ['sometimes','required','email', Rule::unique('users','email')->ignore($user->id)],
        'password' => ['nullable','string','min:8'],
       
    ]);

    if (!empty($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }

    $user->update($data);

    return response()->json($user, 200);
}


    
    public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
}


    public function assignRole(Request $request, User $user)
{
    if ($request->role === 'admin') {
        $user->is_admin = true;
    } else {
        $user->is_admin = false;
    }

    $user->save();

    return response()->json(['message' => 'Role assigned successfully', 'user' => $user]);
}

public function removeRole(Request $request, User $user)
{
    if ($request->role === 'admin') {
        $user->is_admin = false;
        $user->save();
    }

    return response()->json(['message' => 'Role removed successfully', 'user' => $user]);
}
  
}


