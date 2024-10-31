<?php

namespace App\Admin\Challenge;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChallengeRequest;
use App\Http\Services\InsideAreaService;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\Round;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChallengeController extends Controller
{
    public function __construct(private InsideAreaService $insideAreaService)
    {

    }

    public function index()
    {

        $challenge = Challenge::with("areas")->get();
        return Inertia::render('Challenge/Index', ['challenge' => $challenge]);
    }

    public function edit(int $challengeId)
    {
        $challenge = Challenge::with('areas')->findOrFail($challengeId);
        return Inertia::render('Admin/Challenge/Edit', ['challenge' => $challenge]);
    }

    public function create(Round $round)
    {

        return Inertia::render('Admin/Challenge/Create', ['round' => $round]);
    }

    public function store(ChallengeRequest $request)
    {

        $challenge = new Challenge();

        // Retrieve validated data from the request
        $validatedData = $request->validated();

        // Assign the validated data to the Challenge model
        $challenge->fill($validatedData);

        // Save the challenge into the database
        $challenge->save();
        dd($challenge);
        // Redirect or return response
        //return redirect()->route('admin.challenge.edit',['challengeId'=>$challenge->id])->with('success', 'Challenge created successfully.');

    }

    public function update(ChallengeRequest $request, $id)
    {
        $challenge = Challenge::findOrFail($id);
        $validatedData = $request->validated();
        $challenge->fill($validatedData);
        $challenge->save();
        //return redirect()->route('admin.rounds.index');
    }

    public function destroy(Challenge $challengeId)
    {
        $challengeId->delete();

        return redirect()->route('admin.rounds.index');
    }

}
