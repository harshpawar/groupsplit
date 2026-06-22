<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GroupController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $groups = Group::query()
            ->where('admin_id', $user->id)
            ->orWhereHas('members', fn ($query) => $query->where('user_id', $user->id))
            ->with(['admin', 'members.user', 'bills.splits'])
            ->withCount('bills')
            ->latest()
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_numbers' => ['required', 'string'],
        ]);

        $mobiles = $this->parseMobileNumbers($validated['mobile_numbers']);

        if (empty($mobiles)) {
            return back()->withErrors(['mobile_numbers' => 'Enter at least one valid mobile number.'])->withInput();
        }

        $user = Auth::user();
        $adminMobile = User::normalizeMobile($user->mobile);

        if ($adminMobile && ! in_array($adminMobile, $mobiles, true)) {
            $mobiles[] = $adminMobile;
        }

        $group = Group::create([
            'name' => $validated['name'],
            'admin_id' => $user->id,
        ]);

        foreach ($mobiles as $mobile) {
            $matchedUser = User::query()->where('mobile', $mobile)->first();

            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $matchedUser?->id,
                'mobile' => $mobile,
                'is_admin' => $mobile === $adminMobile,
                'joined_at' => $matchedUser ? now() : null,
            ]);
        }

        if ($adminMobile) {
            $group->members()->where('mobile', $adminMobile)->update([
                'user_id' => $user->id,
                'is_admin' => true,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully.');
    }

    public function show(Group $group): View
    {
        $this->authorizeGroupAccess($group);

        $group->load(['admin', 'members.user', 'bills.splits.groupMember.user', 'bills.splits.payments']);

        $isAdmin = $group->isAdmin(Auth::user());
        $qrCode = QrCode::format('svg')->size(220)->margin(1)->generate($group->inviteUrl());

        return view('groups.show', compact('group', 'isAdmin', 'qrCode'));
    }

    public function edit(Group $group): View
    {
        $this->authorizeGroupAdmin($group);

        $group->load('members.user');

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $this->authorizeGroupAdmin($group);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_numbers' => ['nullable', 'string'],
        ]);

        $group->update(['name' => $validated['name']]);

        if (! empty($validated['mobile_numbers'])) {
            $mobiles = $this->parseMobileNumbers($validated['mobile_numbers']);
            $existingMobiles = $group->members()->pluck('mobile')->all();

            foreach ($mobiles as $mobile) {
                if (in_array($mobile, $existingMobiles, true)) {
                    continue;
                }

                $matchedUser = User::query()->where('mobile', $mobile)->first();

                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $matchedUser?->id,
                    'mobile' => $mobile,
                    'joined_at' => $matchedUser ? now() : null,
                ]);
            }
        }

        return redirect()->route('groups.show', $group)->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->authorizeGroupAdmin($group);
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted.');
    }

    public function searchMembers(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $normalized = User::normalizeMobile($query);
        
        $users = User::query()
            ->where(function ($q) use ($query, $normalized) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('mobile', 'like', "%{$query}%");
                
                if ($normalized) {
                    $q->orWhere('mobile', $normalized);
                }
            })
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'mobile')
            ->limit(10)
            ->get();

        return response()->json([
            'users' => $users->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'display' => "{$user->name} ({$user->mobile})"
            ])
        ]);
    }

    /** @return list<string> */
    private function parseMobileNumbers(string $input): array
    {
        $parts = preg_split('/[\s,;]+/', $input) ?: [];
        $mobiles = [];

        foreach ($parts as $part) {
            $mobile = User::normalizeMobile(trim($part));

            if ($mobile && strlen($mobile) >= 8) {
                $mobiles[] = $mobile;
            }
        }

        return array_values(array_unique($mobiles));
    }

    private function authorizeGroupAccess(Group $group): void
    {
        $user = Auth::user();

        if (! $group->isAdmin($user) && ! $group->hasMember($user)) {
            abort(403);
        }
    }

    private function authorizeGroupAdmin(Group $group): void
    {
        if (! $group->isAdmin(Auth::user())) {
            abort(403);
        }
    }
}
