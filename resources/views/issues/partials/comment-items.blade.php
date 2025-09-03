@foreach($comments as $c)
  <div class="border rounded p-2 mb-2">
    <div class="small text-muted">
      {{ e($c->author_name) }} • {{ optional($c->created_at)->diffForHumans() }}
    </div>
    <div>{{ e($c->body) }}</div>
  </div>
  @include('issues.partials.badge-status')
@include('issues.partials.badge-priority')
@include('issues.partials.comment-items', ['comments' => $comments])
@endforeach
