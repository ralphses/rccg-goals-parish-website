<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_department_with_image_members_and_leader(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $leader = User::factory()->create();
        $member = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('dashboard.departments.store'), [
            'name' => 'Ushering',
            'description' => 'Welcomes and coordinates guests.',
            'status' => 'active',
            'leader_id' => $leader->id,
            'users' => [$leader->id, $member->id],
            'image' => UploadedFile::fake()->image('department.jpg'),
        ]);

        $response->assertRedirect(route('dashboard.departments.index'));

        $department = Department::where('name', 'Ushering')->firstOrFail();

        $this->assertSame($leader->id, $department->leader_id);
        $this->assertNotNull($department->image);
        $this->assertEqualsCanonicalizing([$leader->id, $member->id], $department->users()->pluck('users.id')->all());
        $this->assertTrue(MediaUrl::isAbsolute($department->image));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($department->image));
    }

    public function test_create_allows_leader_who_is_not_selected_as_department_member(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $leader = User::factory()->create();
        $member = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('dashboard.departments.store'), [
            'name' => 'Protocol',
            'description' => 'Handles protocol and planning.',
            'status' => 'active',
            'leader_id' => $leader->id,
            'users' => [$member->id],
        ]);

        $response->assertRedirect(route('dashboard.departments.index'));

        $department = Department::where('name', 'Protocol')->firstOrFail();

        $this->assertSame($leader->id, $department->leader_id);
        $this->assertEquals([$member->id], $department->users()->pluck('users.id')->all());
    }

    public function test_update_replaces_department_image_and_deletes_old_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $leader = User::factory()->create();
        $newMember = User::factory()->create();
        $department = Department::factory()->create([
            'image' => url(Storage::url(UploadedFile::fake()->image('old.jpg')->store('departments', 'public'))),
        ]);
        $oldImagePath = MediaUrl::toStoragePath($department->image);

        $department->users()->sync([$leader->id]);

        $response = $this->actingAs($admin)->put(route('dashboard.departments.update', $department), [
            'name' => $department->name,
            'description' => 'Updated description',
            'status' => 'active',
            'leader_id' => $leader->id,
            'users' => [$leader->id, $newMember->id],
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        $response->assertRedirect(route('dashboard.departments.index'));

        $department->refresh();

        Storage::disk('public')->assertMissing($oldImagePath);
        $this->assertTrue(MediaUrl::isAbsolute($department->image));
        Storage::disk('public')->assertExists(MediaUrl::toStoragePath($department->image));
        $this->assertEqualsCanonicalizing([$leader->id, $newMember->id], $department->users()->pluck('users.id')->all());
    }

    public function test_create_and_update_succeed_without_image(): void
    {
        $pastor = User::factory()->create([
            'role' => UserRole::PASTOR,
        ]);
        $leader = User::factory()->create();

        $createResponse = $this->actingAs($pastor)->post(route('dashboard.departments.store'), [
            'name' => 'Choir',
            'description' => 'Music ministry.',
            'status' => 'created',
            'leader_id' => $leader->id,
            'users' => [$leader->id],
        ]);

        $createResponse->assertRedirect(route('dashboard.departments.index'));

        $department = Department::where('name', 'Choir')->firstOrFail();
        $this->assertNull($department->image);

        $updateResponse = $this->actingAs($pastor)->put(route('dashboard.departments.update', $department), [
            'name' => 'Choir',
            'description' => 'Updated music ministry.',
            'status' => 'suspended',
            'leader_id' => $leader->id,
            'users' => [$leader->id],
        ]);

        $updateResponse->assertRedirect(route('dashboard.departments.index'));
        $department->refresh();
        $this->assertNull($department->image);
    }

    public function test_unauthorized_users_cannot_create_or_update_departments(): void
    {
        $member = User::factory()->create([
            'role' => UserRole::MEMBER,
        ]);
        $leader = User::factory()->create();
        $department = Department::factory()->create();

        $createResponse = $this->actingAs($member)->post(route('dashboard.departments.store'), [
            'name' => 'Sanctuary',
            'status' => 'active',
            'leader_id' => $leader->id,
            'users' => [$leader->id],
        ]);

        $createResponse->assertSessionHas('error', 'You are not authorized to create a department.');
        $this->assertDatabaseMissing('departments', ['name' => 'Sanctuary']);

        $updateResponse = $this->actingAs($member)->put(route('dashboard.departments.update', $department), [
            'name' => 'Updated Sanctuary',
            'status' => 'active',
            'leader_id' => $leader->id,
            'users' => [$leader->id],
        ]);

        $updateResponse->assertSessionHas('error', 'You are not authorized to update this department.');
        $this->assertDatabaseMissing('departments', ['name' => 'Updated Sanctuary']);
    }

    public function test_guest_department_page_renders_uploaded_image_and_leader_name(): void
    {
        $leader = User::factory()->create([
            'name' => 'Leader Example',
        ]);
        $department = Department::factory()->create([
            'name' => 'Media',
            'leader_id' => $leader->id,
            'image' => 'departments/example.jpg',
        ]);

        $response = $this->get(route('department', $department));

        $response->assertOk();
        $response->assertSee('Leader Example');
        $response->assertSee(url('/storage/departments/example.jpg'));
    }

    public function test_department_edit_form_preserves_old_input_after_other_validation_error(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
        $leader = User::factory()->create([
            'name' => 'Old Leader',
            'email' => 'leader@example.com',
        ]);
        $department = Department::factory()->create();

        $response = $this->actingAs($admin)
            ->followingRedirects()
            ->from(route('dashboard.departments.edit', $department))
            ->put(route('dashboard.departments.update', $department), [
                'name' => '',
                'description' => 'Still editing',
                'status' => 'active',
                'leader_id' => $leader->id,
                'users' => [],
            ]);

        $response->assertOk();
        $response->assertSee('The name field is required.');
        $response->assertSee('value="' . $leader->id . '" selected', false);
    }
}
