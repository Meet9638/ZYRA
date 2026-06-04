<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
          $query = User::query()->latest();

        // Live search (used by server-side if needed)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        // Stats (always from full table, not the paginated slice)
        $stats = [
            'total'    => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            // 'admins'   => User::where('role', 'admin')->count(),
            // 'banned'   => User::whereNotNull('banned_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));

    }

      /**
     * Show a single user's profile and activity.
     */
   
      public function show(User $user)
    {
        // Eager-load any relations you display on the detail page
        $user->loadCount(['orders']);          // adjust to your actual relations
        // $user->load('orders', 'reviews');  // uncomment as needed

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent an admin from deleting their own account
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$name}\" has been deleted.");
    }
}
