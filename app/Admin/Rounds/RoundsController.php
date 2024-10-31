<?php

namespace App\Admin\Rounds;

use App\Http\Controllers\Controller;
use App\Http\Services\InsideAreaService;
use App\Models\Round;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoundsController extends Controller
{
    public function __construct(private InsideAreaService $insideAreaService)
    {

    }
    public function index()
    {

        $rounds = Round::with("challenges.areas")->get();
        return Inertia::render('Admin/Rounds/Index', ['rounds' => $rounds]);
    }

    public function create()
    {
        return Inertia::render('Admin/Rounds/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        Round::create($request->all());

        return redirect()->route('rounds.index');
    }

    public function edit(Round $round)
    {
        return Inertia::render('Admin/Rounds/Edit', ['round' => $round]);
    }

    public function update(Request $request, Round $round)
    {
        $request->validate([
            'name' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        $round->update($request->all());

        return redirect()->route('rounds.index');
    }

    public function destroy(Round $round)
    {
        $round->delete();

        return redirect()->route('rounds.index');
    }
}
