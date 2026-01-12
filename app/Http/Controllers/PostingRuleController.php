<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\PostingRuleSetting;
use App\Services\PostingRules\PostingRuleRegistry;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class PostingRuleController extends Controller
{
    public function __construct(protected PostingRuleRegistry $registry)
    {
    }

    public function index(HttpRequest $request): View
    {
        $businessId = session('current_business_id');
        $rules = $this->registry->all();

        $settings = [];
        foreach ($rules as $name => $handler) {
            $settings[$name] = PostingRuleSetting::firstOrNew([
                'business_id' => $businessId,
                'rule_name' => $name,
            ]);
        }

        return view('posting_rules.index', ['rules' => $rules, 'settings' => $settings]);
    }

    public function update(HttpRequest $request): RedirectResponse
    {
        $businessId = session('current_business_id');
        $data = $request->validate([
            'rule' => ['required','string'],
            'enabled' => ['nullable','boolean'],
            'config' => ['nullable','string'],
        ]);

        $rule = $data['rule'];
        $enabled = isset($data['enabled']) ? (bool) $data['enabled'] : false;
        $config = null;
        if (! empty($data['config'])) {
            // attempt to parse JSON
            $decoded = json_decode($data['config'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', 'Config must be valid JSON.');
            }
            $config = $decoded;
        }

        $setting = PostingRuleSetting::updateOrCreate([
            'business_id' => $businessId,
            'rule_name' => $rule,
        ], [
            'enabled' => $enabled,
            'config' => $config,
        ]);

        return redirect()->route('postingrules.index')->with('success', 'Rule settings updated.');
    }
}
