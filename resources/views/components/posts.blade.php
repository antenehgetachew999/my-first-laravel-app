



    <a href="/post/{{$post->id}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$post->user->avatar}}" />
        <strong>{{$post->title}}</strong>
         <span class="text-muted small">b
            @if(!@isset($hideAuthor))
             {{$post->user->username}}
             @endif
              on {{$post->created_at->format('d/m/Y')}} </span>
      </a>
