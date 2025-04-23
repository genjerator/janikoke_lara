<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoreResource;
use App\Http\Services\ScoreService;
use App\Models\Round;
use App\Models\Score;
use Inertia\Inertia;

class TestmapController extends Controller
{
    public function __construct(private ScoreService $scoreService)
    {

    }

    public function index()
    {
//        Mapper::map( 45.56793752875635, 19.43481230445069,['zoom'=>17]);
//        $map = str_replace("<!--[if ENDBLOCK]><![endif]-->", "", Mapper::render());
//        $map = str_replace("<!--[if BLOCK]><![endif]-->", "", $map);
        $map ="asasas";
        return Inertia::render('Mapx',['map'=>'aaa']);
    }
}
