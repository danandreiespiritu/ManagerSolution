<tr>
    @php
        $selectedAccountId = data_get($ln, 'account_id') ?? null;
        $selectedCashCategory = data_get($ln, 'cash_category') ?? null;
        $prefilledCode = data_get($ln, 'account_code');
        $found = null;
        if (empty($prefilledCode) && $selectedAccountId) {
            if (is_iterable($accounts)) {
                foreach ($accounts as $a) {
                    if ((int)$a->id === (int)$selectedAccountId) {
                        $found = $a;
                        break;
                    }
                }
            }
            $prefilledCode = $found->account_code ?? '';
        }
    @endphp

    <td class="px-3 py-2">
        <select name="lines[][account_id]" class="account-select w-full border-gray-300 rounded">
            <option value="">-- select account --</option>
            @foreach($accounts as $account)
            <option value="{{ $account->id }}" data-code="{{ $account->account_code }}" {{ (int)$selectedAccountId === (int)$account->id ? 'selected' : '' }}>
                {{ $account->account_name }}
            </option>
            @endforeach
        </select>
    </td>
    <td class="px-3 py-2">
        <select name="lines[][cash_category]" class="cash-category w-full border-gray-300 rounded">
            <option value="">None / Select category</option>
            <option value="Operating activities" {{ in_array($selectedCashCategory, ['Operating activities', 'Operational Activities'], true) ? 'selected' : '' }}>Operating activities</option>
            <option value="Investing activities" {{ in_array($selectedCashCategory, ['Investing activities', 'Investing Activities'], true) ? 'selected' : '' }}>Investing activities</option>
            <option value="Financing activities" {{ in_array($selectedCashCategory, ['Financing activities', 'Financing Activities'], true) ? 'selected' : '' }}>Financing activities</option>
        </select>
    </td>
    <td class="px-3 py-2">
        <input type="text" name="lines[][account_code]" class="code-input w-full border-gray-200 rounded bg-gray-100 py-1 px-2" readonly placeholder="-- code --" value="{{ $prefilledCode }}">
    </td>
    <td class="px-3 py-2 text-right">
        <input type="number" name="lines[][debit_amount]"
            step="0.01" min="0"
            class="w-32 text-right border-gray-200 rounded line-amount debit"
            value="{{ data_get($ln, 'debit_amount', '') }}">
    </td>

    <td class="px-3 py-2 text-right">
        <input type="number" name="lines[][credit_amount]"
            step="0.01" min="0"
            class="w-32 text-right border-gray-200 rounded line-amount credit"
            value="{{ data_get($ln, 'credit_amount', '') }}">
    </td>

    <td class="px-3 py-2 text-center">
        <button type="button"
            class="removeLine px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded">
            Remove
        </button>
    </td>
</tr>