<div class="modal fade" id="tagsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Manage Tags</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" data-tags-root data-issue-id="{{ $issue->id }}">
        <form data-add class="d-flex gap-2 mb-3">
          <select name="tag_id" class="form-select">
            <option value="">Select tag…</option>
            @foreach(\App\Models\Tag::orderBy('name')->get() as $t)
              <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
          </select>
          <button class="btn btn-primary" type="submit">Attach</button>
        </form>
        <ul class="list-group" data-list>
          @foreach($issue->tags as $t)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <span class="badge" style="background-color: {{ $t->color ?? '#6c757d' }}">&nbsp;</span>
                {{ $t->name }}
              </span>
              <button class="btn btn-sm btn-outline-danger" data-remove="{{ $t->id }}">Detach</button>
            </li>
          @endforeach
        </ul>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>
