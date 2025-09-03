<<<<<<< HEAD
@foreach($comments as $c)
<li class="list-group-item py-2">
    <div class="d-flex justify-content-between">
        <strong>{{ $comment->author_name }}</strong>
        <small class="text-muted">{{ $comment->created_at?->diffForHumans() }}</small>
    </div>
    <div class="mt-1">
        {{ $comment->body }}
    </div>
</li>
  <div class="border rounded p-2 mb-2">
    <div class="small text-muted">
      {{ e($c->author_name) }} • {{ optional($c->created_at)->diffForHumans() }}
    </div>
    <div>{{ e($c->body) }}</div>
  </div>
  @include('issues.partials.badge-status')
@include('issues.partials.badge-priority')
@include('issues.partials.comment-items', ['comments' => $comments])
=======
@foreach ($comments as $comment)
    <li id="comment-{{ $comment->id }}" class="border-bottom py-2">
        <div class="small text-muted d-flex gap-2">
            <strong>{{ e($comment->author_name) }}</strong>
            <span>·</span>
            <time datetime="{{ $comment->created_at->toAtomString() }}">
                {{ $comment->created_at->diffForHumans() }}
            </time>
        </div>
        <div class="mt-1">{!! nl2br(e($comment->body)) !!}</div>
    </li>
>>>>>>> fix/comments-ajax
@endforeach
