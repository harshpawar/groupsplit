<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JoinGroupController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $group = Group::query()->where('invite_token', $token)->firstOrFail();

        if (! Auth::check()) {
            session(['url.intended' => route('groups.join', $token)]);

            return redirect()->route('login')->with('status', 'Please log in to join this group.');
        }

        return view('groups.join', compact('group', 'token'));
    }

    public function store(string $token): RedirectResponse
    {
        $group = Group::query()->where('invite_token', $token)->firstOrFail();
        $user = Auth::user();
        $mobile = User::normalizeMobile($user->mobile);

        if (! $mobile) {
            return back()->withErrors(['mobile' => 'Add your mobile number in profile before joining a group.']);
        }

        $member = GroupMember::query()
            ->where('group_id', $group->id)
            ->where(function ($query) use ($user, $mobile) {
                $query->where('user_id', $user->id)
                    ->orWhere('mobile', $mobile);
            })
            ->first();

        if ($member) {
            $member->update([
                'user_id' => $user->id,
                'joined_at' => $member->joined_at ?? now(),
            ]);
        } else {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'mobile' => $mobile,
                'joined_at' => now(),
            ]);
        }

        $group->members()
            ->where('mobile', $mobile)
            ->update(['user_id' => $user->id]);

        $group->bills()->with('splits')->get()->each(function ($bill) use ($user, $mobile) {
            $bill->splits()
                ->whereHas('groupMember', fn ($query) => $query->where('mobile', $mobile))
                ->update(['user_id' => $user->id]);
        });

        return redirect()->route('groups.show', $group)->with('success', 'You joined the group successfully.');
    }
}
