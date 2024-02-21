<?php

namespace App\Providers;

use App\Observers\CmMemberObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Models\MessageReceiverContact;
use App\Observers\MessageReceiverContactObserver;

use Modules\ClubMembers\Entities\CmMember;
use Modules\HR\Entities\Fingerprint;
use App\Observers\HrFingerprintsObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        CmMember::class => [CmMemberObserver::class],
    ];

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        MessageReceiverContact::observe(MessageReceiverContactObserver::class);
        Fingerprint::observe(HrFingerprintsObserver::class);
        CmMember::observe(CmMemberObserver::class);

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
