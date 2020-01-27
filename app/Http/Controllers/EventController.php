<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\Event as EventRequest;
use App\Http\Resources\Event as EventResource;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function index(Request $request)
    {
        $events = $this->event->whereBetween('schedule_start', [$request->get('start_date'), $request->get('end_date')])->get();

        return EventResource::collection($events);
    }

    public function store(EventRequest $request)
    {
        $insert_events_data = [];
        $event = $request->get('event');

        if ($request->has('days_of_the_weeks') && !empty($request->get('days_of_the_weeks'))) {
            $date_ranges = CarbonPeriod::create($request->get('start_date'), $request->get('end_date'))->toArray();

            foreach ($date_ranges as $date_range) {
                $insert_events_data[] = [
                    'event' => $event,
                    'schedule_start' => $date_range->format('Y-m-d'),
                ];
            }

            if ($request->has('days_of_the_weeks')) {
                $days_of_the_weeks = $request->get('days_of_the_weeks');
                $insert_events_data = array_filter($insert_events_data, function ($value) use ($days_of_the_weeks) {
                    $toBeFilteredDate = strtolower(Carbon::parse($value['schedule_start'])->format('l'));
                    if (in_array($toBeFilteredDate, $days_of_the_weeks)) {
                        return $value;
                    }
                });
            }
        } else {
            $insert_events_data[] = [
                'event' => $event,
                'schedule_start' => $request->get('start_date'),
                'schedule_end' => $request->get('end_date'),
            ];
        }

        if ($this->event->insert($insert_events_data)) {
            return response()->json(['message' => 'Events has been saved']);
        }
    }
}
