@extends('app')
@section('title', $article->subject)

@section('content')
  <div class="container-fluid py-3">
    <div class="row g-4">
      <div class="col-lg-8">
        <a href="{{ route('crewnews.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
          &larr; Back to news
        </a>

        <article class="card shadow-sm border-0">
          <div class="card-body">
            <h1 class="h3 mb-1">{{ $article->subject }}</h1>
            <p class="small text-body-secondary border-bottom pb-3 mb-3">
              {{ $article->created_at->format('F j, Y') }}
              @if($article->user)
                &middot; {{ $article->user->name }}
              @endif
            </p>

            {{-- Body is posted by admins in the phpVMS admin, so it is rendered as HTML. --}}
            <div>{!! $article->body !!}</div>
          </div>
        </article>
      </div>

      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-transparent fw-semibold">Latest news</div>
          <div class="list-group list-group-flush">
            @forelse($others as $item)
              <a href="{{ route('crewnews.show', $item->id) }}" class="list-group-item list-group-item-action">
                <div>{{ $item->subject }}</div>
                <small class="text-body-secondary">{{ $item->created_at->format('M j, Y') }}</small>
              </a>
            @empty
              <div class="list-group-item text-body-secondary">No other articles.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
