<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Build query with optional filters
        $query = User::query()->with('hospital')->latest();

        // Non-superadmin can only see users in the same hospital and never superadmin
        if (!auth()->user()->isSuperadmin()) {
            $query->where('hospital_id', auth()->user()->hospital_id)
                  ->where('role', '!=', User::ROLE_SUPERADMIN);
        }

        // Apply simple filters
        if ($name = request('name')) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($email = request('email')) {
            $query->where('email', 'like', "%{$email}%");
        }
        if ($role = request('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(10)->appends(request()->query());

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,hospital_admin,finance_costing,hr_payroll,facility_asset,simrs_integration,support_unit,clinical_unit,medrec_claims,pathway_team,management_auditor,admin,mutu,klaim,manajemen,observer',
            'department' => 'required|string|max:255',
            'hospital_id' => 'nullable|exists:hospitals,id',
        ]);

        // Only superadmin can create a superadmin user
        if (!auth()->user()->isSuperadmin() && $request->role === User::ROLE_SUPERADMIN) {
            return redirect()->back()
                ->with('error', 'You are not authorized to assign superadmin role.')
                ->withInput();
        }

        // Only superadmin can assign hospital_id
        if (!auth()->user()->isSuperadmin() && $request->hospital_id) {
            return redirect()->back()
                ->with('error', 'You are not authorized to assign hospital.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->department = $request->department;
            
            // Only superadmin can assign hospital_id
            if (auth()->user()->isSuperadmin()) {
                $user->hospital_id = $request->hospital_id;
            } else {
                $user->hospital_id = auth()->user()->hospital_id;
            }
            
            $user->save();

            DB::commit();
            
            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->ensureCanManage($user);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->ensureCanManage($user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->ensureCanManage($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:superadmin,hospital_admin,finance_costing,hr_payroll,facility_asset,simrs_integration,support_unit,clinical_unit,medrec_claims,pathway_team,management_auditor,admin,mutu,klaim,manajemen,observer',
            'department' => 'required|string|max:255',
            'hospital_id' => 'nullable|exists:hospitals,id',
        ]);

        // Only superadmin can change someone to superadmin
        if (!auth()->user()->isSuperadmin() && $request->role === User::ROLE_SUPERADMIN) {
            return redirect()->back()
                ->with('error', 'You are not authorized to assign superadmin role.')
                ->withInput();
        }

        // Only superadmin can assign hospital_id
        if (!auth()->user()->isSuperadmin() && $request->hospital_id && $request->hospital_id != $user->hospital_id) {
            return redirect()->back()
                ->with('error', 'You are not authorized to change hospital assignment.')
                ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->department = $request->department;
        
        // Only superadmin can assign hospital_id
        if (auth()->user()->isSuperadmin()) {
            $user->hospital_id = $request->hospital_id;
        }
        
        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->ensureCanManage($user);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin in the same hospital
        if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_HOSPITAL_ADMIN) {
            $adminCount = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_HOSPITAL_ADMIN])
                ->where('hospital_id', $user->hospital_id)
                ->count();
            if ($adminCount <= 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Cannot delete the last admin user for this hospital.');
            }
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for changing user password.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePasswordForm(User $user)
    {
        $this->ensureCanManage($user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, User $user)
    {
        $this->ensureCanManage($user);
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Password pengguna berhasil diperbarui.');
    }

    /**
     * Ensure the authenticated user can manage the target user under row-based multi-tenancy.
     */
    protected function ensureCanManage(User $target): void
    {
        $current = auth()->user();
        
        // Check permission
        if (!$current->hasPermission('view-users')) {
            abort(403, 'You are not authorized to view users.');
        }

        if ($current->isSuperadmin()) {
            return; // Superadmin can manage all users
        }

        // Non-superadmin: must be same hospital and target must not be superadmin
        if ($target->hospital_id !== $current->hospital_id || $target->role === User::ROLE_SUPERADMIN) {
            abort(403, 'You are not authorized to access this user.');
        }
    }
}
