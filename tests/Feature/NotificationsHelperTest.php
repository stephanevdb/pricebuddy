<?php

use App\Enums\NotificationMethods;
use App\Services\Helpers\NotificationsHelper;
use App\Settings\AppSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\Pushover\PushoverChannel;
use Tests\TestCase;

class NotificationsHelperTest extends TestCase
{
    use RefreshDatabase;

    protected array $testSettings = [
        NotificationMethods::Mail->value => [
            'enabled' => true,
            'smtp_host' => 'my.smtp.com',
            'smtp_port' => '25',
            'smtp_user' => 'mailuser',
            'smtp_password' => 'mailpass',
        ],
        NotificationMethods::Pushover->value => [
            'enabled' => false,
            'token' => 'test_po_token',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setNotificationSettings($this->testSettings);
    }

    public function test_get_all_notification_services()
    {
        $services = NotificationsHelper::getServices();
        $this->assertIsArray($services->toArray());
        $this->assertSame('my.smtp.com', $services->get('mail')['smtp_host']);
        $this->assertSame('test_po_token', $services->get('pushover')['token']);

        foreach ($services->keys() as $service) {
            $this->assertArrayHasKey('enabled', $services[$service]);
        }
    }

    public function test_get_all_enabled_notification_services()
    {
        $this->assertSame(['mail'], NotificationsHelper::getEnabled()->keys()->toArray());
    }

    public function test_get_custom_enabled_notification_services()
    {
        $newSettings = $this->testSettings;
        $newSettings[NotificationMethods::Pushover->value]['enabled'] = true;
        $this->setNotificationSettings($newSettings);

        $this->assertSame(
            ['mail', PushoverChannel::class],
            NotificationsHelper::getEnabledChannels()
        );

        $this->setNotificationSettings($this->testSettings);
    }

    public function test_get_notification_service_setting()
    {
        $this->assertSame('my.smtp.com', NotificationsHelper::getSetting('mail', 'smtp_host'));
        $this->assertSame('test_po_token', NotificationsHelper::getSetting('pushover', 'token'));
    }

    protected function setNotificationSettings(array $settings): void
    {
        AppSettings::new()->fill([
            'notification_services' => $settings,
        ])->save();
    }
}
