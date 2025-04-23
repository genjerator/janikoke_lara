<?php

namespace App\Domains\Person\Controllers;

use App\Domains\Person\Resources\PersonResource;
use App\Domains\Person\Services\PersonFilterService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PeopleController extends Controller
{
    public function index(PersonFilterService $productFinderService)
    {
        $people = $productFinderService->getPersonArea();
//        dd($people->toArray());
        return new JsonResponse($people);
    }
}
