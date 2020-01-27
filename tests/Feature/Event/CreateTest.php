<?php

namespace Tests\Feature\Event;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function success_without_days_of_the_weeks()
    {
        $data = [
            'event' => 'Sample Event',
            'start_date' => '2019-01-01',
            'end_date' => '2019-02-05',
            'days_of_the_weeks' => [],
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Events has been saved',
        ]);

        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-01',
            'schedule_end' => '2019-02-05',
        ]);
    }

    /** @test */
    public function success_with_days_of_the_weeks()
    {
        $data = [
            'event' => 'Sample Event',
            'start_date' => '2019-01-01',
            'end_date' => '2019-02-05',
            'days_of_the_weeks' => ['monday', 'tuesday', 'thursday', 'sunday'],
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Events has been saved',
        ]);

        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-01',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-03',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-06',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-07',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-08',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-10',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-13',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-14',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-15',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-17',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-20',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-21',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-22',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-24',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-27',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-28',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-29',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-01-31',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-02-03',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-02-04',
            'schedule_end' => null,
        ]);
        $this->assertDatabaseHas('events', [
            'event' => 'Sample Event',
            'schedule_start' => '2019-02-05',
            'schedule_end' => null,
        ]);
    }

    /** @test */
    public function required()
    {
        $data = [
            'event' => '',
            'start_date' => '',
            'end_date' => '',
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'event' => ['The event field is required.'],
            'start_date' => ['The start date field is required.'],
            'end_date' => ['The end date field is required.'],
        ]);
    }

    /** @test */
    public function invalid_dates()
    {
        $data = [
            'event' => 'Sample Event',
            'start_date' => 'a',
            'end_date' => 'a',
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'start_date' => ['The start date is not a valid date.'],
            'end_date' => ['The end date is not a valid date.'],
        ]);
    }

    /** @test */
    public function supposed_end_date_is_start_date()
    {
        $data = [
            'event' => 'Sample Event',
            'start_date' => '2019-02-05',
            'end_date' => '2019-01-01',
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'start_date' => ['The start date must be a date before end date.'],
            'end_date' => ['The end date must be a date after start date.'],
        ]);
    }

    /** @test */
    public function invalid_days_of_the_weeks()
    {
        $data = [
            'event' => 'Sample Event',
            'start_date' => '2019-01-01',
            'end_date' => '2019-02-05',
            'days_of_the_weeks' => ['monday', 'tuesday', 'thursday', 'sunday', 'way labot'],
        ];

        $response = $this->json('POST', 'api/events', $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'days_of_the_weeks' => ['Invalid days of the week was included.'],
        ]);
    }
}
