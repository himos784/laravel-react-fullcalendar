<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $return = [
            'title' => $this->event,
            'start' => $this->schedule_start,
        ];

        if (!is_null($this->schedule_end)) {
            $return['end'] = Carbon::parse($this->schedule_end)->addDay(1)->format('Y-m-d');
        }

        return  $return;
    }
}
