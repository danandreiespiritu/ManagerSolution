<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Standard Chart of Accounts (COA)
     |--------------------------------------------------------------------------
     | This list is used to seed default Balance Sheet and P&L accounts per
     | business. Account codes are the primary identifiers.
     |
     | Fields:
     | - code: string
     | - name: string
     | - type: one of: asset, liability, equity, income, expense, contra-income, contra-asset
     | - statement: 'balance-sheet' | 'profit-loss'
     | - group: high-level group used for UI/report grouping
     | - group_category: broad category label
     | - is_control_account: bool (e.g., AR/AP)
     */
    'accounts' => [
        // Assets (Balance Sheet)
        ['code' => '1000', 'name' => 'Cash on Hand', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Cash & cash equivalents', 'is_control_account' => false],
        ['code' => '1010', 'name' => 'Cash in Bank', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Cash & cash equivalents', 'is_control_account' => false],
        ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Operating activities', 'is_control_account' => true],
        ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '1500', 'name' => 'Property, Plant & Equipment', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Non-Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Investing activities', 'is_control_account' => false],
        ['code' => '1510', 'name' => 'Accumulated Depreciation', 'type' => 'contra-asset', 'statement' => 'balance-sheet', 'group' => 'Non-Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Investing activities', 'is_control_account' => false],

        // Liabilities (Balance Sheet)
        ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'statement' => 'balance-sheet', 'group' => 'Current Liabilities', 'group_category' => 'Liabilities', 'cash_flow_category' => 'Operating activities', 'is_control_account' => true],
        ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'liability', 'statement' => 'balance-sheet', 'group' => 'Current Liabilities', 'group_category' => 'Liabilities', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '2200', 'name' => 'VAT Payable', 'type' => 'liability', 'statement' => 'balance-sheet', 'group' => 'Current Liabilities', 'group_category' => 'Liabilities', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '2300', 'name' => 'Deferred Revenue', 'type' => 'liability', 'statement' => 'balance-sheet', 'group' => 'Current Liabilities', 'group_category' => 'Liabilities', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '2500', 'name' => 'Long Term Loans Payable', 'type' => 'liability', 'statement' => 'balance-sheet', 'group' => 'Long-term Liabilities', 'group_category' => 'Liabilities', 'cash_flow_category' => 'Financing activities', 'is_control_account' => false],

        // Equity (Balance Sheet)
        ['code' => '3000', 'name' => "Owner’s Capital", 'type' => 'equity', 'statement' => 'balance-sheet', 'group' => 'Equity', 'group_category' => 'Equity', 'cash_flow_category' => 'Financing activities', 'is_control_account' => false],
        ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'statement' => 'balance-sheet', 'group' => 'Equity', 'group_category' => 'Equity', 'cash_flow_category' => 'Financing activities', 'is_control_account' => false],
        ['code' => '3200', 'name' => 'Drawings / Owner Withdrawals', 'type' => 'equity', 'statement' => 'balance-sheet', 'group' => 'Equity', 'group_category' => 'Equity', 'cash_flow_category' => 'Financing activities', 'is_control_account' => false],

        // Revenue (Profit & Loss)
        ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'income', 'statement' => 'profit-loss', 'group' => 'Revenue Accounts', 'group_category' => 'Income', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '4010', 'name' => 'Sales Discounts', 'type' => 'contra-income', 'statement' => 'profit-loss', 'group' => 'Revenue Accounts', 'group_category' => 'Income', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '4020', 'name' => 'Sales Returns and Allowances', 'type' => 'contra-income', 'statement' => 'profit-loss', 'group' => 'Revenue Accounts', 'group_category' => 'Income', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],

        // COGS (Profit & Loss)
        ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Cost of Goods Sold (COGS)', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '5010', 'name' => 'Purchase Returns', 'type' => 'contra-income', 'statement' => 'profit-loss', 'group' => 'Cost of Goods Sold (COGS)', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '5020', 'name' => 'Freight In', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Cost of Goods Sold (COGS)', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],

        // Operating Expenses (Profit & Loss)
        ['code' => '6000', 'name' => 'Salaries Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6100', 'name' => 'Rent Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6200', 'name' => 'Utilities Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6300', 'name' => 'Office Supplies', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6400', 'name' => 'Depreciation Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6500', 'name' => 'Marketing Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '6600', 'name' => 'Bad Debt Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Operating Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],

        // Other Income (Profit & Loss)
        ['code' => '7000', 'name' => 'Interest Income', 'type' => 'income', 'statement' => 'profit-loss', 'group' => 'Other Income', 'group_category' => 'Income', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '7100', 'name' => 'Other Misc Income', 'type' => 'income', 'statement' => 'profit-loss', 'group' => 'Other Income', 'group_category' => 'Income', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],

        // Other Expenses (Profit & Loss)
        ['code' => '8000', 'name' => 'Interest Expense', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Other Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '8100', 'name' => 'Bank Charges', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Other Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
        ['code' => '8200', 'name' => 'Penalties & Fines', 'type' => 'expense', 'statement' => 'profit-loss', 'group' => 'Other Expenses', 'group_category' => 'Expenses', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],

        // Fallback / Suspense
        ['code' => '9999', 'name' => 'Suspense Account', 'type' => 'asset', 'statement' => 'balance-sheet', 'group' => 'Current Assets', 'group_category' => 'Assets', 'cash_flow_category' => 'Operating activities', 'is_control_account' => false],
    ],

    /*
     |--------------------------------------------------------------------------
     | Keyword → Account Code Mapping
     |--------------------------------------------------------------------------
     | These keywords are matched against a text (lowercased) using substring
     | match. Longest keywords should win.
     |
     | Fallbacks:
     | - Revenue-side credit notes => 4020
     | - Purchase-side credit notes => 5010
     | - Unknown => 9999
     */
    'keyword_map' => [
        // Revenue-side returns / credits
        'sales returns' => '4020',
        'sales return' => '4020',
        'return' => '4020',
        'refund' => '4020',
        'credit note' => '4020',
        'allowance' => '4020',

        // Purchases / purchase returns
        'purchase returns' => '5010',
        'purchase return' => '5010',

        // Balance sheet shortcuts
        'inventory' => '1200',
        'stock' => '1200',
        'accounts receivable' => '1100',
        'receivable' => '1100',
        'accounts payable' => '2000',
        'payable' => '2000',
        'cash in bank' => '1010',
        'bank' => '1010',
        'cash on hand' => '1000',
        'cash' => '1000',

        // Common expenses
        'salar' => '6000',
        'rent' => '6100',
        'utilit' => '6200',
        'office supplies' => '6300',
        'depreciation' => '6400',
        'marketing' => '6500',
        'bad debt' => '6600',
        'interest income' => '7000',
        'interest expense' => '8000',
        'bank charges' => '8100',
        'penalt' => '8200',
        'fine' => '8200',
    ],

    'fallbacks' => [
        'revenue_credit_note' => '4020',
        'purchase_credit_note' => '5010',
        'unknown' => '9999',
    ],
];
