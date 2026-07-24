<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Mission;

class ServiceRequestPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_flow_cash_option()
    {
        // 1. Setup Data
        $client = User::factory()->create(['role' => 'client']);
        $artisan = User::factory()->create(['role' => 'artisan']);
        $service = Service::factory()->create(['artisan_id' => $artisan->id]);

        // Client creates a pending request
        $serviceRequest = ServiceRequest::create([
            'user_id' => $client->id,
            'service_id' => $service->id,
            'requested_service_name' => 'Plomberie',
            'city' => 'Kinshasa',
            'address' => '123 Test',
            'urgency' => 'normal',
            'budget_range' => '50-100$',
            'status' => 'pending',
        ]);

        // 2. Artisan accepts request
        $response = $this->actingAs($artisan)
                         ->post(route('user.service-requests.accept', $serviceRequest->id));
        $response->assertSessionHas('success');
        $this->assertEquals('accepted', $serviceRequest->fresh()->status);
        
        // Ensure a pending mission is created (based on existing accept logic)
        $mission = Mission::where('service_request_id', $serviceRequest->id)->first();
        $this->assertNotNull($mission, 'Mission should be created when accepted');
        $this->assertEquals('pending', $mission->status);

        // 3. Client views the show page
        $response = $this->actingAs($client)
                         ->get(route('user.service-requests.show', $serviceRequest->id));
        $response->assertStatus(200);
        $response->assertSee('Paiement en espèces');

        // 4. Client selects Cash
        $response = $this->actingAs($client)
                         ->post(route('user.service-requests.pay-cash', $serviceRequest->id));
        $response->assertSessionHas('success');
        
        // Assert Service Request is in_progress
        $serviceRequest->refresh();
        $this->assertEquals('in_progress', $serviceRequest->status);
        $this->assertNotNull($serviceRequest->accepted_at);

        // Assert Mission is in_progress
        $mission->refresh();
        $this->assertEquals('in_progress', $mission->status);
        $this->assertEquals('cash', $mission->payment_channel);

        // 5. Artisan completes the mission
        $response = $this->actingAs($artisan)
                         ->post(route('user.service-requests.complete', $serviceRequest->id));
        $response->assertSessionHas('success');

        // Assert final statuses
        $serviceRequest->refresh();
        $this->assertEquals('completed', $serviceRequest->status);
        
        $mission->refresh();
        $this->assertEquals('completed', $mission->status);
        $this->assertEquals('pending_payout', $mission->payout_status);
    }
}
