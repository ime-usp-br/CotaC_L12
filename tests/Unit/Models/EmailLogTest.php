<?php

namespace Tests\Unit\Models;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    private function getDefaultData(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'notification_type' => 'TestNotification',
            'subject' => 'Test Subject',
        ];
    }

    public function test_fillable_contem_campos_esperados(): void
    {
        $log = new EmailLog;

        $this->assertEquals([
            'uuid',
            'notification_type',
            'notifiable_type',
            'notifiable_id',
            'recipient_email',
            'recipient_name',
            'subject',
            'status',
            'sent_at',
            'failed_at',
            'attempts',
            'error_message',
            'metadata',
        ], $log->getFillable());
    }

    public function test_casts_contem_campos_esperados(): void
    {
        $log = new EmailLog;
        $casts = $log->getCasts();

        $this->assertEquals('array', $casts['metadata']);
        $this->assertEquals('datetime', $casts['sent_at']);
        $this->assertEquals('datetime', $casts['failed_at']);
        $this->assertEquals('integer', $casts['attempts']);
    }

    public function test_scope_sent_filtra_por_status_enviado(): void
    {
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'sent', 'recipient_email' => 'a@test.com']));
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'failed', 'recipient_email' => 'b@test.com']));

        $result = EmailLog::sent()->get();

        $this->assertCount(1, $result);
        $this->assertEquals('sent', $result->first()->status);
    }

    public function test_scope_failed_filtra_por_status_falhado(): void
    {
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'sent', 'recipient_email' => 'a@test.com']));
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'failed', 'recipient_email' => 'b@test.com']));

        $result = EmailLog::failed()->get();

        $this->assertCount(1, $result);
        $this->assertEquals('failed', $result->first()->status);
    }

    public function test_scope_queued_filtra_por_status_na_fila(): void
    {
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'sent', 'recipient_email' => 'a@test.com']));
        EmailLog::create(array_merge($this->getDefaultData(), ['status' => 'queued', 'recipient_email' => 'b@test.com']));

        $result = EmailLog::queued()->get();

        $this->assertCount(1, $result);
        $this->assertEquals('queued', $result->first()->status);
    }

    public function test_scope_recent_filtra_por_data(): void
    {
        // Use insert to bypass timestamp auto-setting
        EmailLog::insert(array_merge($this->getDefaultData(), [
            'status' => 'sent',
            'recipient_email' => 'old@test.com',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]));
        EmailLog::insert(array_merge($this->getDefaultData(), [
            'status' => 'sent',
            'recipient_email' => 'new@test.com',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]));

        $result = EmailLog::recent(7)->get();

        $this->assertCount(1, $result);
        $this->assertEquals('new@test.com', $result->first()->recipient_email);
    }

    public function test_accessor_formatted_status(): void
    {
        $log = new EmailLog(['status' => 'sent']);
        $this->assertEquals('Enviado', $log->formatted_status);

        $log->status = 'failed';
        $this->assertEquals('Falhado', $log->formatted_status);

        $log->status = 'queued';
        $this->assertEquals('Na Fila', $log->formatted_status);
    }

    public function test_accessor_status_color(): void
    {
        $log = new EmailLog(['status' => 'sent']);
        $this->assertEquals('success', $log->status_color);

        $log->status = 'failed';
        $this->assertEquals('danger', $log->status_color);

        $log->status = 'queued';
        $this->assertEquals('warning', $log->status_color);
    }

    public function test_accessor_notification_type_name(): void
    {
        $log = new EmailLog(['notification_type' => 'App\Notifications\WelcomeNotification']);

        $this->assertEquals('WelcomeNotification', $log->notification_type_name);
    }

    public function test_relacao_notifiable(): void
    {
        $user = User::factory()->create();
        $log = EmailLog::create(array_merge($this->getDefaultData(), [
            'status' => 'sent',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'recipient_email' => $user->email,
        ]));

        $this->assertInstanceOf(User::class, $log->notifiable);
        $this->assertEquals($user->id, $log->notifiable->id);
    }
}
