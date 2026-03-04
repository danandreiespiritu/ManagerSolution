<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\BlsAccountandGroup\IBlsAccountRepository;
use App\Http\Requests\BlsAccountandGroupRequest\BlsGroupRequest;
use App\Http\Requests\BlsAccountandGroupRequest\BlsAccountRequest;
use App\Repositories\PlAccountandGroup\IPlAccountRepository;
use App\Http\Requests\PlAccountandGroupRequest\PlGroupRequest;
use App\Http\Requests\PlAccountandGroupRequest\PlAccountRequest;
use App\Models\ChartofAccounts;

class HomeController extends Controller
{
    public function __construct(
        protected IBlsAccountRepository $accountGroups,
        protected IPlAccountRepository $plAccountGroups
    ) {
    }

    public function chartofaccountBulkDelete(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $ids = $data['ids'];

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } elseif (session('current_business_id')) {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::whereIn('id', $ids)
            ->where('user_id', $userId);
        if ($businessId) $q->where('business_id', $businessId);

        $deleted = $q->delete();

        return redirect()->route('chartofaccountIndex')->with('success', "Deleted {$deleted} accounts.");
    }

    public function sidebar() : View 
    {
        return view('components.sidebar');
    }

    public function chartofaccountIndex(): View
    {
        $userId = auth()->id();

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        // Balance Sheet groups and their accounts
        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $blGroups = $qb->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        $balanceSections = $blGroups->map(function ($group) use ($userId, $businessId) {
            $q = ChartofAccounts::where('user_id', $userId)
                ->where('account_type', 'BL')
                ->whereNotNull('account_name')
                ->where('account_group', $group)
                ->orderBy('account_name');

            if ($businessId) {
                $q->where('business_id', $businessId);
            }

            $accounts = $q->get()->map(fn($m) => ['name' => $m->account_name, 'id' => $m->id]);

            return [
                'name' => $group,
                'accounts' => $accounts->toArray(),
            ];
        })->toArray();

        // Profit and Loss groups and their accounts (account_type = PL)
        $plGroups = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        $profitSections = $plGroups->map(function ($group) use ($userId, $businessId) {
            $q = ChartofAccounts::where('user_id', $userId)
                ->where('account_type', 'PL')
                ->whereNotNull('account_name')
                ->where('account_group', $group)
                ->orderBy('account_name');

            if ($businessId) {
                $q->where('business_id', $businessId);
            }

            $accounts = $q->get()->map(fn($m) => ['name' => $m->account_name, 'id' => $m->id]);

            return [
                'name' => $group,
                'accounts' => $accounts->toArray(),
            ];
        })->toArray();

        $sections = [
            'balance_sheet' => $balanceSections,
            'profit_and_loss' => $profitSections,
        ];

        return view('chartofaccount.index', compact('sections'));
    }

    public function chartofaccountCreateBlGroup(Request $request): View
    {
        $userId = $request->user()->id;
        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        // Fetch only groups belonging to the authenticated user and current business
        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $blgroup = $qb->get();
        return view('chartofaccount.createBlGroup', compact('blgroup'));
    }
    
    public function chartofaccountCreateBlAccount(Request $request): View
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        // fetch available BL groups (group names) for account_group suggestions
        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $groups = $qb->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        return view('chartofaccount.createBlAccount', compact('groups'));
    }

    // PL: create group view
    public function chartofaccountCreatePlGroup(Request $request)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $plgroup = $qb->get();

        return view('chartofaccount.createPlGroup', compact('plgroup'));
    }

    // PL: create account view
    public function chartofaccountCreatePlAccount(Request $request)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $groups = $qb->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        return view('chartofaccount.createPlAccount', compact('groups'));
    }

    public function chartofaccountStorePlAccount(PlAccountRequest $request)
    {
        $data = $request->validated();

        $payload = [
            'user_id' => $request->user()->id,
            'account_type' => 'PL',
            'account_name' => $data['account_name'],
            'account_code' => $data['account_code'] ?? null,
            'account_group' => $data['account_group'],
            'cash_flow_category' => $data['cash_flow_category'] ?? null,
            'is_active' => 1,
        ];

        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } elseif (session()->has('current_business_id')) {
            $payload['business_id'] = session('current_business_id');
        }

        ChartofAccounts::create($payload);

        return redirect()
            ->route('PlAccountCreate')
            ->with('success', 'Profit & Loss account created successfully.');
    }

    public function chartofaccountEditPlAccount(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id)
            ->where('account_type', 'PL');

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $groups = $qb->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        return view('chartofaccount.editPlAccount', compact('account', 'groups'));
    }

    public function chartofaccountUpdatePlAccount(PlAccountRequest $request, int $id)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id)
            ->where('account_type', 'PL');

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $data = $request->validated();

        $account->update([
            'account_name' => $data['account_name'],
            'account_code' => $data['account_code'] ?? null,
            'account_group' => $data['account_group'],
            'cash_flow_category' => $data['cash_flow_category'] ?? null,
        ]);

        return redirect()
            ->route('chartofaccountIndex')
            ->with('success', 'Account updated successfully.');
    }

    public function chartofaccountDestroyPlAccount(Request $request, int $id)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id)
            ->where('account_type', 'PL');

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $account->delete();

        return redirect()
            ->route('chartofaccountIndex')
            ->with('success', 'Account deleted successfully.');
    }
    
    public function chartofaccountStoreBlAccount(BlsAccountRequest $request)
    {
        $data = $request->validated();

        $payload = [
            'user_id' => $request->user()->id,
            'account_type' => 'BL',
            'account_name' => $data['account_name'],
            'account_code' => $data['account_code'] ?? null,
            'account_group' => $data['account_group'],
            'cash_flow_category' => $data['cash_flow_category'] ?? null,
            'is_active' => 1,
        ];

        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } elseif (session()->has('current_business_id')) {
            $payload['business_id'] = session('current_business_id');
        }

        ChartofAccounts::create($payload);

        return redirect()
            ->route('BlAccountCreate')
            ->with('success', 'Balance sheet account created successfully.');
    }
    
    public function chartofaccountEditBlAccount(Request $request, int $id): View
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id);

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $qb = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->whereNotNull('group')
            ->orderBy('group');

        if ($businessId) {
            $qb->where('business_id', $businessId);
        }

        $groups = $qb->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        return view('chartofaccount.editBlAccount', compact('account', 'groups'));
    }

    public function chartofaccountUpdateBlAccount(BlsAccountRequest $request, int $id)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id);

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $data = $request->validated();

        $account->update([
            'account_name' => $data['account_name'],
            'account_code' => $data['account_code'] ?? null,
            'account_group' => $data['account_group'],
            'cash_flow_category' => $data['cash_flow_category'] ?? null,
        ]);

        return redirect()
            ->route('chartofaccountIndex')
            ->with('success', 'Account updated successfully.');
    }

    public function chartofaccountDestroyBlAccount(Request $request, int $id)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $userId)
            ->where('id', $id);

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $account = $q->firstOrFail();

        $account->delete();

        return redirect()
            ->route('chartofaccountIndex')
            ->with('success', 'Account deleted successfully.');
    }
    public function chartofaccountStoreBlGroup(BlsGroupRequest $request)
    {
        $data = $request->validated();

        // Map request fields to the BlGroup model columns
        $groupData = [
            'user_id' => $request->user()->id,
            'account_type' => 'BL',
            'group' => $data['name'],
            'group_category' => $data['category'] ?? null,
            'account_name' => '',
            'account_code' => '',
        ];

        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $groupData['business_id'] = $b->id;
        } elseif (session()->has('current_business_id')) {
            $groupData['business_id'] = session('current_business_id');
        }

        ChartofAccounts::create($groupData);

        return redirect()
            ->route('BlGroupCreate')
            ->with('success', 'Account group created successfully.');

    }
    
    public function chartofaccountStorePlGroup(BlsGroupRequest $request)
    {
        $data = $request->validated();

        $groupData = [
            'user_id' => $request->user()->id,
            'account_type' => 'PL',
            'group' => $data['name'],
            'group_category' => $data['category'] ?? null,
            'account_name' => '',
            'account_code' => '',
        ];

        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $groupData['business_id'] = $b->id;
        } elseif (session()->has('current_business_id')) {
            $groupData['business_id'] = session('current_business_id');
        }

        ChartofAccounts::create($groupData);

        return redirect()
            ->route('PlGroupCreate')
            ->with('success', 'Account group created successfully.');

    }
    public function chartofaccountUpdateBlGroup(BlsGroupRequest $request, int $id, int $userId)
    {
        $data = $request->validated();

        // Ensure the group belongs to the current user before updating
        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $q = ChartofAccounts::where('user_id', $request->user()->id)
            ->where('id', $id);

        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        $group = $q->first();

        if ($group) {
            $group->update([
                'group' => $data['name'],
                'group_category' => $data['category'] ?? null,
            ]);
        }

        return redirect()
            ->route('BlGroupCreate')
            ->with('success', 'Account group updated successfully.');
    }
}
