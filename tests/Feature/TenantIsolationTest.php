<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_are_scoped_to_current_business(): void
    {
        $user = User::factory()->create();

        $bizA = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Biz A',
            'email' => 'biza@example.test',
            'is_active' => true,
        ]);

        $bizB = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Biz B',
            'email' => 'bizb@example.test',
            'is_active' => true,
        ]);

        app()->instance('currentBusiness', $bizA);
        $custA = Customer::create([
            'user_id' => $user->id,
            'customer_name' => 'Customer A',
            'email' => 'a@example.test',
            'is_active' => true,
        ]);

        app()->instance('currentBusiness', $bizB);
        $custB = Customer::create([
            'user_id' => $user->id,
            'customer_name' => 'Customer B',
            'email' => 'b@example.test',
            'is_active' => true,
        ]);

        app()->instance('currentBusiness', $bizA);
        $this->assertSame([$custA->id], Customer::query()->pluck('id')->all());

        app()->instance('currentBusiness', $bizB);
        $this->assertSame([$custB->id], Customer::query()->pluck('id')->all());
    }

    public function test_business_id_cannot_be_spoofed_on_create(): void
    {
        $user = User::factory()->create();

        $bizA = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Biz A',
            'email' => 'biza@example.test',
            'is_active' => true,
        ]);

        $bizB = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Biz B',
            'email' => 'bizb@example.test',
            'is_active' => true,
        ]);

        app()->instance('currentBusiness', $bizA);

        $cust = Customer::create([
            'user_id' => $user->id,
            'business_id' => $bizB->id, // attempt to spoof another business
            'customer_name' => 'Customer X',
            'email' => 'x@example.test',
            'is_active' => true,
        ]);

        $this->assertSame($bizA->id, (int) $cust->business_id);
    }
}
