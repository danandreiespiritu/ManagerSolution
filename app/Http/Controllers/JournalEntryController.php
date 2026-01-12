<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalEntryRequest;
use App\Repositories\JournalEntry\IJournalEntryRepository;
use App\Models\ChartofAccounts;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Scopes\BusinessScope;
use App\Models\AccountingPeriod;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    protected IJournalEntryRepository $repo;

    public function __construct(IJournalEntryRepository $repo)
    {
        $this->repo = $repo;
    }

    // Show the journal entry form
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }
        $accountsQuery = ChartofAccounts::query()
            ->withoutGlobalScope(BusinessScope::class)
            ->where('user_id', $userId)
            ->whereNotNull('account_name')
            ->where('account_name', '<>', '')
            ->where('is_active', 1);

        if ($businessId) {
            $accountsQuery->where('business_id', $businessId);
        }

        $accounts = $accountsQuery->orderBy('account_name')->get();

        $periods = AccountingPeriod::where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->orderByDesc('start_date')
            ->get();

        $customers = Customer::query()
            ->where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', true)
            ->orderBy('customer_name')
            ->get();

        $suppliers = Supplier::query()
            ->where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', true)
            ->orderBy('supplier_name')
            ->get();

        // recent entries for listing below the form
        $recentEntries = $this->repo->paginate($userId, 10);

        return view('journalEntry.index', compact('accounts', 'periods', 'customers', 'suppliers', 'recentEntries'));
    }

    public function show(Request $request, int $id)
    {
        $entry = $this->repo->getById($id);
        if (! $entry) abort(404);
        return view('journalEntry.show', compact('entry'));
    }

    public function edit(Request $request, int $id)
    {
        $entry = $this->repo->getById($id);
        if (! $entry) abort(404);

        // reuse index view but provide the entry to prefill the form
        $userId = $request->user()->id;

        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id;
        } else {
            $businessId = session('current_business_id');
        }

        $accountsQuery = ChartofAccounts::query()
            ->withoutGlobalScope(BusinessScope::class)
            ->where('user_id', $userId)
            ->whereNotNull('account_name')
            ->where('account_name', '<>', '')
            ->where('is_active', 1);

        if ($businessId) {
            $accountsQuery->where('business_id', $businessId);
        }

        $accounts = $accountsQuery->orderBy('account_name')->get();

        $periods = AccountingPeriod::where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->orderByDesc('start_date')
            ->get();

        $customers = Customer::query()
            ->where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', true)
            ->orderBy('customer_name')
            ->get();

        $suppliers = Supplier::query()
            ->where('user_id', $userId)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', true)
            ->orderBy('supplier_name')
            ->get();

        $recentEntries = $this->repo->paginate($userId, 10);

        return view('journalEntry.index', compact('accounts', 'periods', 'customers', 'suppliers', 'entry', 'recentEntries'));
    }

    public function update(JournalEntryRequest $request, int $id)
    {
        $payload = $request->validated();
        $payload['user_id'] = $request->user()->id;

        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } elseif (session('current_business_id')) {
            $payload['business_id'] = session('current_business_id');
        }

        $payload['created_by'] = $request->user()->id;

        $this->repo->update($id, $payload);

        return redirect()->route('journal.index')->with('success', 'Journal entry updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->repo->delete($id);
        return redirect()->route('journal.index')->with('success', 'Journal entry deleted.');
    }

    // Store the journal entry
    public function store(JournalEntryRequest $request)
    {
        $payload = $request->validated();

        // attach user id
        $payload['user_id'] = $request->user()->id;

        // attach business id when available
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } elseif (session('current_business_id')) {
            $payload['business_id'] = session('current_business_id');
        }

        // mark who created the entry
        $payload['created_by'] = $request->user()->id;

        $entry = $this->repo->create($payload);

        return redirect()->route('journal.index')->with('success', 'Journal entry created.');
    }
}
