<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Staff;
use App\Models\StaffType;
use App\Models\Contract;
use App\Models\Vacation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private StaffType $staffType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->staffType = StaffType::create([
            'name' => 'Administrativo',
            'description' => 'Personal administrativo'
        ]);
    }

    public function test_vacation_index_page_requires_auth(): void
    {
        $response = $this->get(route('admin.vacation.index'));
        $response->assertRedirect('/login');
    }

    public function test_vacation_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.vacation.index'));
        $response->assertOk();
    }

    public function test_staff_without_contract_cannot_request_vacation(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.vacation.store'), [
                'staff_id' => $staff->id,
                'date_start' => '2026-07-01',
                'date_end' => '2026-07-10',
                'days_requested' => 10,
                'notes' => 'Vacaciones de Julio',
            ]);

        $response->assertSessionHasErrors(['staff_id']);
        $this->assertEquals(0, Vacation::count());
    }

    public function test_staff_with_temporary_contract_cannot_request_vacation(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'temporal',
            'date_start' => '2026-01-01',
            'date_end' => '2026-12-31',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.vacation.store'), [
                'staff_id' => $staff->id,
                'date_start' => '2026-07-01',
                'date_end' => '2026-07-10',
                'days_requested' => 10,
                'notes' => 'Vacaciones de Julio',
            ]);

        $response->assertSessionHasErrors(['staff_id']);
        $this->assertEquals(0, Vacation::count());
    }

    public function test_staff_with_permanent_contract_can_request_vacation(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.vacation.store'), [
                'staff_id' => $staff->id,
                'date_start' => '2026-07-01',
                'date_end' => '2026-07-10',
                'days_requested' => 10,
                'notes' => 'Vacaciones de Julio',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.vacation.index'));
        $this->assertEquals(1, Vacation::count());

        $vacation = Vacation::first();
        $this->assertEquals($staff->id, $vacation->staff_id);
        $this->assertEquals('pending', $vacation->state);
    }

    public function test_vacation_request_exceeding_available_days_fails(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 5, // Only 5 days available
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.vacation.store'), [
                'staff_id' => $staff->id,
                'date_start' => '2026-07-01',
                'date_end' => '2026-07-10',
                'days_requested' => 10, // Requesting 10 days
            ]);

        $response->assertSessionHasErrors(['days_requested']);
        $this->assertEquals(0, Vacation::count());
    }

    public function test_overlapping_vacation_requests_fail(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        // Create an existing pending vacation request
        Vacation::create([
            'staff_id' => $staff->id,
            'date_request' => '2026-06-01',
            'date_start' => '2026-07-01',
            'date_end' => '2026-07-10',
            'days_requested' => 10,
            'state' => 'pending',
        ]);

        // Attempt to create a overlapping request (July 5th to July 15th)
        $response = $this->actingAs($this->user)
            ->post(route('admin.vacation.store'), [
                'staff_id' => $staff->id,
                'date_start' => '2026-07-05',
                'date_end' => '2026-07-15',
                'days_requested' => 10,
            ]);

        $response->assertSessionHasErrors(['date_start']);
        $this->assertEquals(1, Vacation::count());
    }

    public function test_vacation_approval_subtracts_days(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $vacation = Vacation::create([
            'staff_id' => $staff->id,
            'date_request' => '2026-06-01',
            'date_start' => '2026-07-01',
            'date_end' => '2026-07-10',
            'days_requested' => 10,
            'state' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.vacation.approve', $vacation->id));

        $response->assertRedirect(route('admin.vacation.index'));
        $vacation->refresh();
        $staff->refresh();

        $this->assertEquals('approved', $vacation->state);
        $this->assertEquals(20, $staff->vacation_days); // 30 - 10 = 20
    }

    public function test_vacation_rejection_does_not_subtract_days(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $vacation = Vacation::create([
            'staff_id' => $staff->id,
            'date_request' => '2026-06-01',
            'date_start' => '2026-07-01',
            'date_end' => '2026-07-10',
            'days_requested' => 10,
            'state' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.vacation.reject', $vacation->id));

        $response->assertRedirect(route('admin.vacation.index'));
        $vacation->refresh();
        $staff->refresh();

        $this->assertEquals('rejected', $vacation->state);
        $this->assertEquals(30, $staff->vacation_days); // 30 (no change)
    }

    public function test_non_pending_vacations_cannot_be_approved_rejected_edited_or_deleted(): void
    {
        $staff = Staff::create([
            'dni' => '12345678',
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com',
            'staff_type_id' => $this->staffType->id,
            'status' => 'active',
            'vacation_days' => 30,
        ]);

        Contract::create([
            'contract_type' => 'permanente',
            'date_start' => '2026-01-01',
            'salary' => 2500,
            'state' => 'active',
            'staff_id' => $staff->id,
        ]);

        $vacation = Vacation::create([
            'staff_id' => $staff->id,
            'date_request' => '2026-06-01',
            'date_start' => '2026-07-01',
            'date_end' => '2026-07-10',
            'days_requested' => 10,
            'state' => 'approved', // already approved!
        ]);

        // Attempt edit
        $response = $this->actingAs($this->user)->get(route('admin.vacation.edit', $vacation->id));
        $response->assertRedirect(route('admin.vacation.index'));
        $response->assertSessionHas('error');

        // Attempt update
        $response = $this->actingAs($this->user)->put(route('admin.vacation.update', $vacation->id), [
            'staff_id' => $staff->id,
            'date_start' => '2026-07-01',
            'date_end' => '2026-07-10',
            'days_requested' => 10,
        ]);
        $response->assertRedirect(route('admin.vacation.index'));
        $response->assertSessionHas('error');

        // Attempt delete
        $response = $this->actingAs($this->user)->delete(route('admin.vacation.destroy', $vacation->id));
        $response->assertRedirect(route('admin.vacation.index'));
        $response->assertSessionHas('error');

        // Attempt approve again
        $response = $this->actingAs($this->user)->patch(route('admin.vacation.approve', $vacation->id));
        $response->assertRedirect(route('admin.vacation.index'));
        $response->assertSessionHas('error');

        // Attempt reject again
        $response = $this->actingAs($this->user)->patch(route('admin.vacation.reject', $vacation->id));
        $response->assertRedirect(route('admin.vacation.index'));
        $response->assertSessionHas('error');
    }
}
