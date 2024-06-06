<?php

namespace App\Listeners;

use App\Events\UserInsideAreaEvent;
use App\Http\Services\ScoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PointsListener
{
    /**
     * Create the event listener.
     */
    public function __construct(public ScoreService $pointsService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserInsideAreaEvent $event): void
    {
        $userChallengeArea = $event->getUserChallengeArea();
        $this->pointsService->calculatePoints($userChallengeArea);
    }
}
