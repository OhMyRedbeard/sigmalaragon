@extends('app')
@section('title', 'News')

@section('content')
  <div class="container-fluid py-3">
    <h1 class="h3 mb-4">News</h1>

    @forelse($news as $item)
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <h2 class="h5 card-title mb-1">
            <a href="{{ route('crewnews.show', $item->id) }}" class="text-decoration-none">
              {{ $item->subject }}
            </a>
          </h2>
          <p class="small text-body-secondary mb-2">
            {{ $item->created_at->format('M j, Y') }}
            @if($item->user)
              &middot; {{ $item->user->name }}
            @endif
          </p>
          <p class="card-text mb-3">
            {{ \Illuminate\Support\Str::limit(strip_tags($item->body), 220) }}
          </p>
          <a href="{{ route('crewnews.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
            Read more
          </a>
        </div>
      </div>
    @empty
      <div class="alert alert-info">There is no news yet.</div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
      {{ $news->links('pagination::bootstrap-5') }}
    </div>
  </div>
@endsection
