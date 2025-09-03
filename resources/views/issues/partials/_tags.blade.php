@php
    /** @var \App\Models\Issue $issue */
@endphp

<div class="d-flex flex-wrap gap-2">
@forelse($issue->tags as $t)
    <span class="badge rounded-pill text-bg-secondary align-middle" data-tag-id="{{ $t->id }}">
        @if($t->color)
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $t->color }};margin-right:6px;"></span>
        @endif
        {{ e($t->name) }}
        @can('update', $issue->project)
            <button type="button"
                    class="btn btn-sm btn-link text-white ms-1 p-0 align-baseline tag-detach"
                    data-tag-id="{{ $t->id }}"
                    aria-label="Remove tag">✕</button>
        @endcan
    </span>
@empty
    <span class="text-muted">No tags</span>
@endforelse
</div>
