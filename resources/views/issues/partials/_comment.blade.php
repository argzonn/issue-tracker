<li class="list-group-item py-2">
    <div class="d-flex justify-content-between">
        <strong>{{ $comment->author_name }}</strong>
        <small class="text-muted">{{ $comment->created_at?->diffForHumans() }}</small>
    </div>
    <div class="mt-1">
        {{ $comment->body }}
    </div>
</li>
