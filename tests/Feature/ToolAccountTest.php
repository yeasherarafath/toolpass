<?php

namespace Tests\Feature;

use App\Events\ToolAccount\ToolAccountCreated;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\ToolCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Tests\TenantTestCase;

class ToolAccountTest extends TenantTestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function makeTool(): Tool
    {
        return Tool::factory()->create(['category_id' => ToolCategory::factory()->create()->id]);
    }

    public function test_password_is_encrypted_at_rest(): void
    {
        $tool = $this->makeTool();

        $this->actingAs($this->admin())
            ->post(route('admin.tool-accounts.store'), [
                'tool_id' => $tool->id,
                'name' => 'Acct One',
                'login_email' => 'owner@example.com',
                'login_password' => 'topsecret',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.tool-accounts.index'));

        $account = ToolAccount::where('name', 'Acct One')->first();
        $this->assertNotSame('topsecret', $account->login_password_encrypted);
        $this->assertSame('topsecret', Crypt::decryptString($account->login_password_encrypted));
    }

    public function test_observer_sets_created_by(): void
    {
        $admin = $this->admin();
        $tool = $this->makeTool();

        $this->actingAs($admin)
            ->post(route('admin.tool-accounts.store'), [
                'tool_id' => $tool->id,
                'name' => 'Acct Two',
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('tool_accounts', ['name' => 'Acct Two', 'created_by' => $admin->id]);
    }

    public function test_creation_dispatches_event(): void
    {
        Event::fake([ToolAccountCreated::class]);
        $tool = $this->makeTool();

        $this->actingAs($this->admin())
            ->post(route('admin.tool-accounts.store'), [
                'tool_id' => $tool->id,
                'name' => 'Acct Evt',
                'status' => 'active',
            ]);

        Event::assertDispatched(ToolAccountCreated::class);
    }

    public function test_blank_password_keeps_existing_on_update(): void
    {
        $tool = $this->makeTool();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'login_password_encrypted' => Crypt::encryptString('original'),
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.tool-accounts.update', $account), [
                'tool_id' => $tool->id,
                'name' => 'Acct Upd',
                'status' => 'active',
            ]);

        $this->assertSame('original', Crypt::decryptString($account->fresh()->login_password_encrypted));
    }

    public function test_admin_can_delete_account(): void
    {
        $tool = $this->makeTool();
        $account = ToolAccount::factory()->create(['tool_id' => $tool->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.tool-accounts.destroy', $account))
            ->assertRedirect(route('admin.tool-accounts.index'));

        $this->assertSoftDeleted('tool_accounts', ['id' => $account->id]);
    }
}
