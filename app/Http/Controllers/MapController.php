<?php

namespace App\Http\Controllers;


use Mapper;

class MapController extends Controller
{
    public function index()
    {
        Mapper::map( 45.56793752875635, 19.43481230445069,['zoom'=>17]);
        $map = str_replace("<!--[if ENDBLOCK]><![endif]-->", "", Mapper::render());
        $map = str_replace("<!--[if BLOCK]><![endif]-->", "", $map);
        return view('map', ['map' => $map]);
    }
}
