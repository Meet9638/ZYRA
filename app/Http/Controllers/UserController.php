<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display user profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load(['orders']);

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent'  => $user->orders()
                            ->whereNotIn('status', ['cancelled', 'refunded'])
                            ->sum('total_amount'),
            'total_returns' => ReturnModel::whereHas('orderItem.order', fn($q) => $q->where('user_id', $user->id))->count(),
        ];

        return view('users.profile', compact('user', 'stats'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'gender'         => 'nullable|in:male,female,other',
            'address'        => 'nullable|string|max:1000',
            'phone'          => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('users.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password'=> ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('users.profile')->with('success', 'Password updated successfully!');
    }

    /**
     * Display order history
     */
    public function orders()
    {
        $orders = Auth::user()->orders()
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.orders.index', compact('orders'));
    }

    /**
     * Get user statistics (API)
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($user),
            'orders'             => [
                'total'     => $user->orders()->count(),
                'pending'   => $user->orders()->where('status', 'pending')->count(),
                'delivered' => $user->orders()->where('status', 'delivered')->count(),
                'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
            ],
            'spending'           => [
                'total'         => $user->orders()->whereNotIn('status', ['cancelled', 'refunded'])->sum('total_amount'),
                'average_order' => $user->orders()->whereNotIn('status', ['cancelled', 'refunded'])->avg('total_amount'),
            ],
        ];

        return response()->json(['success' => true, 'statistics' => $stats]);
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect']);
        }

        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'processing', 'shipped'])->count();

        if ($pendingOrders > 0) {
            return back()->withErrors([
                'account' => 'Cannot delete account with pending orders. Please wait for all orders to be completed.',
            ]);
        }

        Auth::logout();
        $user->delete();

        return redirect()->route('home')->with('success', 'Your account has been deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function calculateProfileCompletion($user): int
    {
        $fields    = ['name', 'email', 'gender', 'address', 'phone'];
        $completed = collect($fields)->filter(fn($f) => !empty($user->$f))->count();

        return round(($completed / count($fields)) * 100);
    }
}
