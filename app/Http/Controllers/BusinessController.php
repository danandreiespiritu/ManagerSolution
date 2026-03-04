<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Repositories\Business\IBusinessRepository;
use App\Repositories\BlsAccountandGroup\IBlsAccountRepository;
use App\Repositories\PlAccountandGroup\IPlAccountRepository;
use App\Models\JournalEntryLine;
use App\Http\Controllers\Controller;
use App\Models\ChartofAccounts;

class BusinessController extends Controller
{
    protected IBusinessRepository $repo;

    public function __construct(IBusinessRepository $repo)
    {
        $this->repo = $repo;
    }

    // Show dashboard with businesses (paginated)
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 15);

        $businesses = $this->repo->paginate($userId, $perPage);

        // Attach quick totals for each business to display on dashboard
        foreach ($businesses as $b) {
            $lines = JournalEntryLine::where('business_id', $b->id)
                ->selectRaw('SUM(COALESCE(debit_amount,0)) as debit_sum, SUM(COALESCE(credit_amount,0)) as credit_sum')
                ->first();
            $debit = (float) ($lines->debit_sum ?? 0.0);
            $credit = (float) ($lines->credit_sum ?? 0.0);
            $net = $debit - $credit;
            $b->totals = [
                'debit' => number_format($debit, 2),
                'credit' => number_format($credit, 2),
                'net' => number_format($net, 2),
            ];
        }

        return view('dashboard', compact('businesses'));
    }

    // Store new business
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $this->repo->create($request->user()->id, $data);

        return redirect()->back()->with('success', 'Business created successfully.');
    }

    // Show edit form (optional view)
    public function edit($id)
    {
        $biz = $this->repo->getById(auth()->id(), (int) $id);

        if (! $biz) {
            return redirect()->back()->with('error', 'Business not found.');
        }

        return view('business.edit', ['business' => $biz]);
    }

    // Business financial summary (revised to match chartofaccount.index)
    public function summary($id)
    {
        $biz = $this->repo->getById(auth()->id(), (int) $id);

        if (! $biz) {
            return redirect()->back()->with('error', 'Business not found.');
        }

        request()->session()->put('current_business_id', $biz->id);
        app()->instance('currentBusiness', $biz);

        $userId = auth()->id();
        $businessId = $biz->id;

        // Compute account balances from journal entry lines
        $lines = JournalEntryLine::where('business_id', $businessId)
            ->selectRaw('account_id, SUM(COALESCE(debit_amount,0)) as debit_sum, SUM(COALESCE(credit_amount,0)) as credit_sum')
            ->groupBy('account_id')
            ->get();

        $accountBalances = [];
        foreach ($lines as $ln) {
            $accountBalances[$ln->account_id] = (float) $ln->debit_sum - (float) $ln->credit_sum;
        }

        // Initialize totals
        $totals = [
            'assets' => 0.0,
            'liabilities' => 0.0,
            'equity' => 0.0,
            'income' => 0.0,
            'expenses' => 0.0,
            'net' => 0.0,
        ];

        // -------------------------------
        // Balance Sheet Sections (BL)
        // -------------------------------
        $blGroups = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->whereNotNull('group')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        $balanceSections = $blGroups->map(function ($group) use ($userId, $businessId, $accountBalances) {
            $accounts = ChartofAccounts::where('user_id', $userId)
                ->where('account_type', 'BL')
                ->where('account_group', $group)
                ->whereNotNull('account_name')
                ->when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->get()
                ->map(fn($acct) => [
                    'name' => $acct->account_name,
                    'id' => $acct->id,
                    'balance' => number_format($accountBalances[$acct->id] ?? 0, 2),
                ])
                ->toArray();

            return [
                'name' => $group,
                'accounts' => $accounts,
            ];
        })->toArray();

        // -------------------------------
        // Profit & Loss Sections (PL)
        // -------------------------------
        $plGroups = ChartofAccounts::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->whereNotNull('group')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->pluck('group')
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values();

        $profitSections = $plGroups->map(function ($group) use ($userId, $businessId, $accountBalances) {

            $grpLower = strtolower($group);

            // 🔥 Skip building Income accounts here (we'll inject from service)
            if (str_contains($grpLower, 'income') || str_contains($grpLower, 'revenue')) {
                return [
                    'name' => $group,
                    'accounts' => [], // will be filled later
                ];
            }

            // ✅ Keep Expense logic untouched
            $accounts = ChartofAccounts::where('user_id', $userId)
                ->where('account_type', 'PL')
                ->where('account_group', $group)
                ->whereNotNull('account_name')
                ->when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->get()
                ->map(fn($acct) => [
                    'name' => $acct->account_name,
                    'id' => $acct->id,
                    'balance' => number_format(($accountBalances[$acct->id] ?? 0), 2),
                ])
                ->toArray();

            return [
                'name' => $group,
                'accounts' => $accounts,
            ];
        })->toArray();

        // 🔥 Inject Income accounts using P&L service
        $plService = app(\App\Services\ProfitAndLossReportService::class);

        $built = $plService->build(
            $businessId,
            now()->startOfYear()->format('Y-m-d'),
            now()->endOfYear()->format('Y-m-d'),
            'accrual',
            'off'
        );

        $incomeAccounts = [];

        $grouped = $built['grouped'] ?? [];

        foreach ($grouped as $groupName => $groupData) {
            $low = strtolower($groupName);

            if (
                str_contains($low, 'revenue') ||
                str_contains($low, 'income') ||
                str_contains($low, 'sales') ||
                str_contains($low, 'gain')
            ) {
                foreach ($groupData['accounts'] ?? [] as $acct) {
                    $incomeAccounts[] = [
                        'name' => $acct['account_name'] ?? $acct['name'] ?? '',
                        'id' => $acct['account_id'] ?? null,
                        'balance' => number_format((float) ($acct['amount'] ?? 0), 2),
                    ];
                }
            }
        }

        // Replace Income section accounts
        foreach ($profitSections as &$section) {
            if (
                str_contains(strtolower($section['name']), 'income') ||
                str_contains(strtolower($section['name']), 'revenue')
            ) {
                $section['accounts'] = $incomeAccounts;
            }
        }
        unset($section);

        $sections = [
            'balance_sheet' => $balanceSections,
            'profit_and_loss' => $profitSections,
        ];

        // -------------------------------
        // Compute totals from sections
        // -------------------------------
        foreach ($sections['balance_sheet'] as $group) {
            foreach ($group['accounts'] as $acct) {
                $amt = (float) str_replace(',', '', $acct['balance']);
                $grpLower = strtolower($group['name']);
                if (str_contains($grpLower, 'asset')) $totals['assets'] += $amt;
                elseif (str_contains($grpLower, 'liab')) $totals['liabilities'] += $amt;
                elseif (str_contains($grpLower, 'equity') || str_contains($grpLower, 'capital')) {
                    $totals['equity'] += (-1 * $amt); // flip sign (credit-normal)
                }
                else $totals['assets'] += $amt; // fallback
            }
        }

        // Use the SAME service logic as P&L for income
        $plService = app(\App\Services\ProfitAndLossReportService::class);

        $built = $plService->build(
            $businessId,
            now()->startOfYear()->format('Y-m-d'), 
            now()->endOfYear()->format('Y-m-d'),
            'accrual', // match your default
            'off'
        );

        $totals['income'] = (float) ($built['totalRevenue'] ?? 0);
        $totals['expenses'] = (float) ($built['totalExpense'] ?? 0);

        $totals['net'] = $totals['income'] - $totals['expenses'];
        $totals['equity'] += $totals['net'];

        // Format totals for display
        foreach ($totals as $k => $v) {
            $totals[$k] = number_format($v, 2);
        }

        // Test output
        // dd($sections);

        return view('business.summary', [
            'business' => $biz,
            'sections' => $sections,
            'totals' => $totals,
        ]);
    }

    // Switch current business for the session
    public function switch(Request $request): RedirectResponse
    {
        $id = (int) $request->get('business_id');
        $biz = $this->repo->getById($request->user()->id, $id);

        if (! $biz) {
            return redirect()->back()->with('error', 'Business not found or not accessible.');
        }

        $request->session()->put('current_business_id', $biz->id);

        return redirect()->back()->with('success', 'Switched business.');
    }

    // Update business
    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $updated = $this->repo->update(auth()->id(), (int) $id, $data);

        if (! $updated) {
            return redirect()->back()->with('error', 'Unable to update business.');
        }

        return redirect()->back()->with('success', 'Business updated.');
    }

    // Delete business
    public function destroy($id): RedirectResponse
    {
        $deleted = $this->repo->delete(auth()->id(), (int) $id);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Unable to delete business.');
        }

        return redirect()->back()->with('success', 'Business deleted.');
    }
}
