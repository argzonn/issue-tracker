@include('issues.partials.badge-status')
@include('issues.partials.badge-priority')
@include('issues.partials.comment-items', ['comments' => $comments])

@php $map = ['open'=>'success','in_progress'=>'warning','closed'=>'secondary']; @endphp
<span class="badge text-bg-{{ $map[$status] ?? 'light' }}">{{ ucfirst(str_replace('_',' ', $status)) }}</span>
