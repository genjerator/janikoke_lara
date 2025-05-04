<?php

namespace App\Http\Controllers;


use App\Domains\Person\Services\PersonFilterService;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Mapper;

class MapController extends Controller
{
    public function index(PersonFilterService $personFilterService)
    {
//        /** @var Area $areas */
//        $areas= Area::where('type',2)->get();
//        $allPolygons = $areas->map(function ($area) {
//            return $area->getPolygonCoordinatesForLeaflet();
//        })->toArray();
//        //$polygon = $areas->getPolygonCoordinatesForLeaflet();
//        //dd($allPolygons);
        $people = $personFilterService->getPersonArea();
        Mapper::map( 45.56793752875635, 19.43481230445069,['zoom'=>19,'rotateControl'=>true]);
        foreach ($people as $polygon) {
            Mapper::polygon($polygon);
        }

        $map = str_replace("<!--[if ENDBLOCK]><![endif]-->", "", Mapper::render());
        $map = str_replace("<!--[if BLOCK]><![endif]-->", "", $map);

//        $polygons = $areas->map(function ($area) {
//            return [
//                'id' => $area->id,
//                'name' => $area->name,
//                'coordinates' => $area->getPolygonCoordinatesForLeaflet(),
//            ];
//        })->toArray();

        return view('map', ['map' => $map]);
    }

    public function test()
    {
        $user = Auth::user();
        dd($user->name, $user->email, $user);
    }
}
