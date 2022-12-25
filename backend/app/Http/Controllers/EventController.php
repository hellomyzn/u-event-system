<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;


class EventController extends Controller
{    
    /**
     * eventService
     *
     * @var EventService
     */
    protected $eventService;
    
    /**
     * __construct
     *
     * @param  EventService
     * @return void
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = DB::table('events')
            ->orderBy('start_date', 'asc')
            ->paginate(10);

        return view('managers.events.index', 
                    compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('managers.events.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        $check = EventService::checkEventDuplication(
            $request['event_date'],
            $request['start_time'],
            $request['end_time']
        );

        if($check){
            session()->flash('status', 'この時間帯はすでに他の予約が存在します');
            return to_route('managers.events.create');
        }
        
        $event = $this->eventService->create($request);

        session()->flash('status', 'イベントが登録されました');

        return to_route('managers.events.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $event = Event::findOrFail($event->id);
        $eventDate = $event->eventDate;
        $startTime = $event->startTime;
        $endTime = $event->endTime;
        
        return view('managers.events.show', 
            compact([
                'event',
                'eventDate',
                'startTime',
                'endTime'
            ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
