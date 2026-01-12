<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountingPeriodRequest;
use App\Repositories\AccountingPeriod\IAccountingPeriodRepository;
use Illuminate\Http\Request;

class AccountingPeriodController extends Controller
{
    protected IAccountingPeriodRepository $repo;

    public function __construct(IAccountingPeriodRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $periods = $this->repo->getAllForUser($userId);
        return view('accountingperiod.index', compact('periods'));
    }

    public function store(AccountingPeriodRequest $request)
    {
        $payload = $request->validated();
        $payload['user_id'] = $request->user()->id;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $payload['business_id'] = $b->id;
        } else {
            $payload['business_id'] = session('current_business_id');
        }

        $this->repo->create($payload);
        return redirect()->route('accountingperiod.index')->with('success', 'Accounting period created.');
    }

    public function edit(Request $request, int $id)
    {
        $period = $this->repo->getById($id);
        if (!$period) return redirect()->route('accountingperiod.index')->with('error', 'Not found.');
        return view('accountingperiod.edit', compact('period'));
    }

    public function update(AccountingPeriodRequest $request, int $id)
    {
        $payload = $request->validated();
        $updated = $this->repo->update($id, $payload);
        if (!$updated) return redirect()->route('accountingperiod.index')->with('error', 'Update failed.');
        return redirect()->route('accountingperiod.index')->with('success', 'Accounting period updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->repo->delete($id);
        return redirect()->route('accountingperiod.index')->with('success', 'Accounting period deleted.');
    }
}
