<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ['user', 'attendees', 'attendees.user'];


    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = $this->loadRelationShips(Event::query());

       return EventResource::collection($query->latest()->paginate());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ... $request->validate([
                'name'=>'required|string|max:255',
                'description'=>"nullable|string",
                "start_date"=>"required|date",
                "end_date"=>"required|date|after:start_date",
            ]),
            "user_id"=>1
        ]);

        return new EventResource($this->loadRelationShips($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {

        $event->load(['user', 'attendees']);

        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        if (!Gate::allows('update-event', $event)) {
            abort(403, "You are not allowed for this action");
        }

        $event->update(
            $request->validate([
                'name'=>'sometimes|string|max:255',
                'description'=>"nullable|string",
                "start_date"=>"sometimes|date",
                "end_date"=>"sometimes|date|after:start_date",
            ])
        );

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        return $event->delete();

    }
}
