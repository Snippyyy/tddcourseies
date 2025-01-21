<?php


use App\Models\Course;
use App\Models\User;
use App\Models\Video;

it('adds given courses', function(){

    $this->assertDatabaseCount(Course::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(Course::class, 3);

    $this->assertDatabaseHas(Course::class, ['title' => 'Laravel for Beginners']);
    $this->assertDatabaseHas(Course::class, ['title' => 'Advanced Laravel']);
    $this->assertDatabaseHas(Course::class, ['title' => 'TDD The Laravel way']);
});

it('adds given courses only once', function (){

    $this->artisan('db:seed');
    $this->artisan('db:seed');

    $this->assertDatabaseCount(Course::class, 3);
});

it('adds given videos', function () {

    $this->assertDatabaseCount(Video::class, 0);

    $this->artisan('db:seed');
    $laravelForBeginnersCourse = Course::where('title', 'Laravel for Beginners')->firstOrFail();
    $advancedLaravelCourse = Course::where('title', 'Advanced Laravel')->firstOrFail();
    $tddTheLaravelWayCourse = Course::where('title', 'TDD The Laravel way')->firstOrFail();


    $this->assertDatabaseCount(Video::class, 8);
    $this->assertDatabaseCount(Video::class, 8);

    expect($laravelForBeginnersCourse)
        ->videos
        ->toHaveCount(3);

    expect($advancedLaravelCourse)
        ->videos
        ->toHaveCount(3);

    expect($tddTheLaravelWayCourse)
        ->videos
        ->toHaveCount(2);
});

it('adds given videos only once', function () {

    $this->assertDatabaseCount(Video::class, 0);

    $this->artisan('db:seed');
    $this->artisan('db:seed');

    $this->assertDatabaseCount(Video::class, 8);

});

it('adds local test user', function () {
    //
    App::partialMock()->shouldReceive('environment')->andReturn('local');


    $this->assertDatabaseCount(User::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(User::class, 1);
});

it('does not test user for production', function () {

    App::partialMock()->shouldReceive('environment')->andReturn('production');

    //Asser
    $this->assertDatabaseCount(User::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(User::class, 0);
});
