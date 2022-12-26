<?php

namespace App\Repositories\Events;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Event;
use App\Repositories\Interfaces\EventRepositoryInterface;

class EventMysqlRepository implements EventRepositoryInterface
{
    
    /**
     * model
     *
     * @var Event
     */
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $event;
    }
            
    /**
     * getById
     *
     * @param  int $id
     * @return Event
     */
    public function getById(int $id): Event
    {
        try {
                $event = $this->model->findOrFail($id);

                return $event;
        } catch(Exceptions $e) {
            \Log::error(__METHOD__.'@'.$e->getLine().': '.$e->getMessage());

            return [
                'msg' => $e->getMessage(),
                'err' => false,
            ];
        }
    }
    
    /**
     * getAllOrderByStartDateAsc
     *
     * @return object
     */
    public function getFutureEvents(): object
    {
        try {
            $today = Carbon::today();
            $events = DB::table('events')
                ->whereDate('start_date', '>', $today)
                ->orderBy('start_date', 'asc')
                ->paginate(10);

            return $events;
        } catch(Exceptions $e) {
            \Log::error(__METHOD__.'@'.$e->getLine().': '.$e->getMessage());

            return [
                'msg' => $e->getMessage(),
                'err' => false,
            ];
        }
    }
        
    /**
     * getPastEvents
     *
     * @return object
     */
    public function getPastEvents(): object
    {
        try {
            $today = Carbon::today();
            $events = DB::table('events')
                ->whereDate('start_date', '<', $today)
                ->orderBy('start_date', 'desc')
                ->paginate(10);

            return $events;
        } catch(Exceptions $e) {
            \Log::error(__METHOD__.'@'.$e->getLine().': '.$e->getMessage());

            return [
                'msg' => $e->getMessage(),
                'err' => false,
            ];
        }
    }

    /**
     * create
     *
     * @param  array $requestData
     * @return Event
     */
    public function create(array $requestData): Event
    {
        try {
            return DB::transaction(function () use ($requestData) {
                $event = $this->model->create($requestData);

                return $event;
            });
        } catch(Exceptions $e) {
            \Log::error(__METHOD__.'@'.$e->getLine().': '.$e->getMessage());

            return [
                'msg' => $e->getMessage(),
                'err' => false,
            ];
        }
    }
    
    /**
     * update
     *
     * @param  array $requestData
     * @param  int $id
     * @return Event
     */
    public function update(array $requestData, int $id): Event
    {
        try {
            return DB::transaction(function () use($requestData, $id) {
                $event = $this->getById($id);
                $event->name = $requestData['name'];
                $event->information = $requestData['information'];
                $event->start_date = $requestData['start_date'];
                $event->end_date = $requestData['end_date'];
                $event->max_people = $requestData['max_people'];
                $event->is_visible = $requestData['is_visible'];
                $event->save();

                return $event;
            });
        } catch(Exceptions $e) {
            \Log::error(__METHOD__.'@'.$e->getLine().': '.$e->getMessage());

            return [
                'msg' => $e->getMessage(),
                'err' => false,
            ];
        }
    }
}