<?php

namespace Tests\Feature\Event;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetrieveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function success()
    {
        $events = [];
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-01'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-03'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-06'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-07'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-08'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-10'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-13'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-14'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-15'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-17'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-20'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-21'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-22'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-24'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-27'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-28'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-29'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-31'])->toArray();
        $events[] = factory(Event::class)->create(['schedule_start' => '2019-01-31', 'schedule_end' => '2019-02-01'])->toArray();
        factory(Event::class)->create(['schedule_start' => '2019-02-03']);
        factory(Event::class)->create(['schedule_start' => '2019-02-04']);
        factory(Event::class)->create(['schedule_start' => '2019-02-05']);

        foreach ($events as $key => $value) {
            $events[$key]['title'] = $value['event'];
            $events[$key]['start'] = $value['schedule_start'];
            if (!is_null($value['schedule_end'])) {
                $events[$key]['end'] = Carbon::parse($value['schedule_end'])->addDay(1)->format('Y-m-d');
            }
            unset($events[$key]['id'], $events[$key]['event'], $events[$key]['schedule_start'], $events[$key]['schedule_end'], $events[$key]['created_at'] ,$events[$key]['updated_at']);
        }

        $response = $this->json('GET', 'api/events?start_date=2019-01-01&end_date=2019-01-31');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'data' => $events,
        ]);
    }
}
