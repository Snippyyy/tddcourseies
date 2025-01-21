<?php


use App\Http\Livewire\VideoPlayer;
use App\Models\Course;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

function createCourseAndVideos(int $count = 3): Course
{
    return Course::factory()
        ->has(Video::factory()->count($count))
        ->create();
}

beforeEach(function () {
    $this->logguedInUser = loginAsUser();
});

it('shows details for given video', function () {
    // Arrange

    $course = createCourseAndVideos();


    $video = $course->videos->first();
    // Act   // Assert


    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertSeeText([
            $video->title,
            $video->description,
            $video->duration_in_min . 'min'
        ]);

});

it('shows given video', function () {

    // Arrange
    $course = Course::factory()->has(Video::factory())->create();
    // Act // Assert
    $video = $course->videos->first();

    Livewire::test(VideoPlayer::class, ['video' => $video])
        ->assertSeeHtml('<iframe src="https://player.vimeo.com/video/'. $video->vimeo_id .'"');

});


it('shows list of all course videos', function () {

    //Arrange

    $course = createCourseAndVideos(3);

    //Act //Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos()->first()])
        ->assertSee($course->videos->pluck('title')->toArray())
        ->assertSeeHtml([
            route('pages.course-videos', ['course' => $course,'video' => $course->videos[1]]),
            route('pages.course-videos', ['course' => $course,'video' => $course->videos[2]])
        ]);

});

it('mark video as completed', function () {



    $course = Course::factory()->has(Video::factory())->create();

    $this->logguedInUser->purchasedCourses()->attach($course);

    expect($this->logguedInUser->watchedVideos)->toHaveCount(0);

    actingAs($this->logguedInUser);

    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markVideoAsCompleted')
        ->call('markVideoAsCompleted')
        ->assertMethodWired('markVideoAsNotCompleted')
        ->assertMethodNotWired('markVideoAsCompleted');

    $this->logguedInUser->refresh();

    expect($this->logguedInUser->watchedVideos)
        ->toHaveCount(1)
        ->first()->title->toEqual($course->videos->first()->title);
});

it('mark video as not completed', function () {


    $course = createCourseAndVideos();

    $this->logguedInUser->purchasedCourses()->attach($course);
    $this->logguedInUser->watchedVideos()->attach($course->videos->first());

    expect($this->logguedInUser->watchedVideos)->toHaveCount(1);

    actingAs($this->logguedInUser);

    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markVideoAsNotCompleted')
        ->call('markVideoAsNotCompleted')
        ->assertMethodWired('markVideoAsCompleted')
        ->assertMethodNotWired('markVideoAsNotCompleted');

    $this->logguedInUser->refresh();

    expect($this->logguedInUser->watchedVideos)
        ->toHaveCount(0);
});

it('does not include route for current video', function () {

    $course = createCourseAndVideos(1);
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertDontSeeHtml(route('pages.course-videos', $course->videos->first()));
});
