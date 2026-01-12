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

    // Business financial summary
    public function summary($id)
    {
        $biz = $this->repo->getById(auth()->id(), (int) $id);

        if (! $biz) {
            return redirect()->back()->with('error', 'Business not found.');
        }

        // Ensure the selected business becomes the active tenant for this request
        // (and subsequent navigation) so all scoped models resolve consistently.
        request()->session()->put('current_business_id', $biz->id);
        app()->instance('currentBusiness', $biz);

        // compute account balances from journal entry lines
        $lines = JournalEntryLine::where('business_id', $biz->id)
            ->selectRaw('account_id, SUM(COALESCE(debit_amount,0)) as debit_sum, SUM(COALESCE(credit_amount,0)) as credit_sum')
            ->groupBy('account_id')
            ->get();

        $accountBalances = [];
        foreach ($lines as $ln) {
            $accountBalances[$ln->account_id] = (float) $ln->debit_sum - (float) $ln->credit_sum;
        }

        $totals = [
            'assets' => 0.0,
            'liabilities' => 0.0,
            'equity' => 0.0,
            'income' => 0.0,
            'expenses' => 0.0,
            'net' => 0.0,
        ];

        // Load chart of accounts for the current user using repositories
        $blRepo = app(IBlsAccountRepository::class);
        $plRepo = app(IPlAccountRepository::class);

        $blAccounts = $blRepo->getAll(auth()->id(), $biz->id);
        $plAccounts = $plRepo->getAll(auth()->id(), $biz->id);

        // Group BL accounts into Assets, Liabilities, Equity
        $balanceSheet = [
            'Assets' => [],
            'Liabilities' => [],
            'Equity' => [],
        ];

        foreach ($blAccounts as $acct) {
            $category = $acct->group_category ?? $acct->group ?? '';
            $label = $acct->account_name ?? $acct->account_code ?? 'Account';
            $amountVal = $accountBalances[$acct->id] ?? 0.0;
            $amount = number_format($amountVal, 2);

            $catLower = strtolower((string) $category);
            if (str_contains($catLower, 'asset')) {
                $balanceSheet['Assets'][] = ['label' => $label, 'amount' => $amount];
                $totals['assets'] += $amountVal;
            } elseif (str_contains($catLower, 'liab')) {
                $balanceSheet['Liabilities'][] = ['label' => $label, 'amount' => $amount];
                $totals['liabilities'] += $amountVal;
            } elseif (str_contains($catLower, 'equity')) {
                $balanceSheet['Equity'][] = ['label' => $label, 'amount' => $amount];
                $totals['equity'] += $amountVal;
            } else {
                // default to Assets when unknown
                $balanceSheet['Assets'][] = ['label' => $label, 'amount' => $amount];
                $totals['assets'] += $amountVal;
            }
        }

        // Group PL accounts into income / expenses
        $profitLoss = [
            'income' => [],
            'expenses' => [],
        ];

        foreach ($plAccounts as $acct) {
            $category = $acct->group_category ?? $acct->group ?? '';
            $label = $acct->account_name ?? $acct->account_code ?? 'Account';
            $amountVal = $accountBalances[$acct->id] ?? 0.0;
            $amount = number_format($amountVal, 2);

            $catLower = strtolower((string) $category);
            if (str_contains($catLower, 'income') || str_contains($catLower, 'revenue')) {
                $profitLoss['income'][] = ['label' => $label, 'amount' => $amount];
                $totals['income'] += $amountVal;
            } else {
                $profitLoss['expenses'][] = ['label' => $label, 'amount' => $amount];
                $totals['expenses'] += $amountVal;
            }
        }

        // compute net profit (income - expenses)
        $totals['net'] = $totals['income'] - $totals['expenses'];

        // format totals for display
        foreach (['assets','liabilities','equity','income','expenses','net'] as $k) {
            $totals[$k] = number_format($totals[$k], 2);
        }

        return view('business.summary', [
            'business' => $biz,
            'totals' => $totals,
            'balanceSheet' => $balanceSheet,
            'profitLoss' => $profitLoss,
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
