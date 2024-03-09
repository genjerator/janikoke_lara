<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengeResource;
use App\Models\Round;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    public function challenges(Round $round)
    {
        return ChallengeResource::collection($round->challenges);
    }
}
