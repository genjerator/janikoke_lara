<?php

namespace App\Events;

use App\Models\UserChallengeArea;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInsideAreaEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param UserChallengeArea $model
     * @return void
     */
    public function __construct(private UserChallengeArea $model)
    {
    }

    public function getUserChallengeArea(): UserChallengeArea
    {
        return $this->model;
    }

}
