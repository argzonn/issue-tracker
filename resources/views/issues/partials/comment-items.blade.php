@foreach($comments as $c)
  <li class="list-group-item">
    <div class="d-flex justify-content-between">
      <strong>{{ $c->author_name }}</strong>
      <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
    </div>
    <div class="mt-1">{{ $c->body }}</div>
  </li>
@endforeach
