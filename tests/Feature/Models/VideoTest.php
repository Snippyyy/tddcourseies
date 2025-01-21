<?php


use App\Http\Livewire\VideoPlayer;
use App\Models\{Course, User, Video};
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('gives back readable video duration', function () {
    // Arrange
    $video = Video::factory()->create([
        'duration_in_min' => 10
    ]);

    // Act
    $duration = $video->duration_in_min;

    // Assert
    expect($video->getReadableDuration())->toEqual('10min');
});

it('has course', function () {

    $video = Video::factory()
        ->has(Course::factory())
        ->create();

    expect($video->course)
        ->toBeInstanceOf(Course::class);
});

it('tells if current user has not yet watched a given video', function () {

    //Arrange

    $video = Video::factory()->create();

    //Act //Assert
    loginAsUser();

    expect($video->alreadyWatchedByCurrentUser())->toBeFalse();
});

it('tells if current user has already watched a given video', function () {

    //Arrange

    $user = User::factory()->has(Video::factory(), 'watchedVideos')->create();

    //Act //Assert
    loginAsUser($user);

    expect($user->watchedVideos()->first()->alreadyWatchedByCurrentUser())->toBe(true);
});


