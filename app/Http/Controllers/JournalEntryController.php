<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalEntryRequest;
use App\Repositories\JournalEntry\IJournalEntryRepository;
use App\Services\JournalEntryAdjustmentService;
use App\Services\JournalEntryValidationService;
use App\Models\ChartofAccounts;
use App\Models\Scopes\BusinessScope;
use App\Models\AccountingPeriod;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    protected IJournalEntryRepository $repo;
    protected JournalEntryAdjustmentService $adjustmentService;
    protected JournalEntryValidationService $validationService;

    public function __construct(
        IJournalEntryRepository $repo,
        JournalEntryAdjustmentService $adjustmentService,
        JournalEntryValidationService $validationService
    ) {
        $this->repo = $repo;
        $this->adjustmentService = $adjustmentService;
        $this->validationService = $validationService;
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

        // -------------------------
        // Recent entries with search & pagination
        // -------------------------
        $search = $request->input('search');

        $recentEntriesQuery = \App\Models\JournalEntry::with('lines')
            ->where('user_id', $userId)
            ->orderByDesc('entry_date');

        if ($request->input('search')) {
            $recentEntriesQuery->where(function($q) use ($request) {
                $search = $request->input('search');
                $q->where('reference_type', 'like', "%{$search}%")
                ->orWhere('reference_id', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $recentEntries = $recentEntriesQuery->paginate(10)->appends($request->all());

        // -------------------------
        // Return JSON if AJAX
        // -------------------------
        if ($request->ajax()) {
            return response()->json([
                'entries' => view('journalEntry.partials.entries-table', compact('recentEntries'))->render(),
                'pagination' => (string) $recentEntries->links(),
            ]);
        }

        // -------------------------
        // Otherwise return normal page
        // -------------------------
        return view('journalEntry.index', compact('accounts', 'periods', 'recentEntries'));
    }

    public function show(Request $request, int $id)
    {
        $entry = $this->repo->getById($id);
        if (! $entry) abort(404);
        
        // Get balance details including adjustments
        $balanceDetails = $this->validationService->getBalanceDetails($entry);
        $allLines = $this->validationService->getAllLines($entry);
        
        return view('journalEntry.show', compact('entry', 'balanceDetails', 'allLines'));
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

        $recentEntries = $this->repo->paginate($userId, 10);

        return view('journalEntry.index', compact('accounts', 'periods', 'entry', 'recentEntries'));
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

        $entry = $this->repo->update($id, $payload);
        
        $message = 'Journal entry updated.';
        if ($entry->adjustments()->count() > 0) {
            $message .= ' An adjustment entry was created to balance the entry.';
        }

        return redirect()->route('journal.index')->with('success', $message);
    }

    public function destroy(Request $request, int $id)
    {
        $this->repo->delete($id);
        return redirect()->route('journal.index')->with('success', 'Journal entry deleted.');
    }

    public function store(JournalEntryRequest $request)
    {
        $payload = $request->validated();

        // Attach user id
        $payload['user_id'] = $request->user()->id;

        // Attach business id if available
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } elseif (session('current_business_id')) {
            $payload['business_id'] = session('current_business_id');
        }

        // Mark who created the entry
        $payload['created_by'] = $request->user()->id;

        // Create the main journal entry
        $entry = $this->repo->create($payload);

        // Optional: check if adjustments were created
        $message = 'Journal entry created.';
        if ($entry->adjustments()->count() > 0) {
            $message .= ' An adjustment entry was automatically created to balance the entry.';
        }

        return redirect()->route('journal.index')->with('success', $message);
    }

    /**
     * Show adjustments for a specific journal entry
     */
    public function showAdjustments(Request $request, int $id)
    {
        $entry = $this->repo->getById($id);
        if (! $entry) abort(404);
        
        $adjustments = $this->adjustmentService->getActiveAdjustments($entry);
        $balanceDetails = $this->validationService->getBalanceDetails($entry);
        
        return view('journalEntry.adjustments', compact('entry', 'adjustments', 'balanceDetails'));
    }

    /**
     * Apply adjustments to post them to the ledger
     */
    public function applyAdjustments(Request $request, int $id)
    {
        $entry = $this->repo->getById($id);
        if (! $entry) abort(404);
        
        $count = $this->validationService->applyAllAdjustments($entry);
        
        return redirect()->route('journal.show', $id)
            ->with('success', "Applied {$count} adjustment entry(ies) to the ledger.");
    }

    public function create()
    {
        $accounts = ChartofAccounts::where('is_active', true)->get();
        return view('payments.create', compact('accounts'));
    }
}
