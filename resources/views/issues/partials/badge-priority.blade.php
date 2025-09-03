@php $map = ['low'=>'secondary','medium'=>'info','high'=>'danger']; @endphp
<span class="badge text-bg-{{ $map[$priority] ?? 'light' }}">{{ ucfirst($priority) }}</span>
@include('issues.partials.badge-status')
@include('issues.partials.badge-priority')
@include('issues.partials.comment-items', ['comments' => $comments])