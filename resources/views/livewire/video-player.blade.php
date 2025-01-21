<div>
    <iframe src="https://player.vimeo.com/video/{{$video->vimeo_id}}"
        webkitallowfullscreen mozallowfullscreen allowfullscreen
    ></iframe>
    <h3>{{ $video->title }}</h3>
    <h3>{{ $video->description }}</h3>
    <h3>{{$video->getReadableDuration()}}</h3>
    @if($video->alreadyWatchedByCurrentUser())
        <button wire:click="markVideoAsNotCompleted">Mark as not completed</button>
    @else
        <button wire:click="markVideoAsCompleted">Mark as completed</button>
    @endif




    <ul>
        @foreach($courseVideos as $coursevideo)
            @if($this->isCurrentVideo($coursevideo))
                <li>
                {{$coursevideo->title}}
            @else
                <a href="{{route('pages.course-videos',[$coursevideo->course ,$coursevideo] )}}">{{$coursevideo->title}}</a>
            @endif
                </li>
        @endforeach

    </ul>
</div>
