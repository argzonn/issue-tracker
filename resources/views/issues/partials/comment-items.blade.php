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
@endforeach
