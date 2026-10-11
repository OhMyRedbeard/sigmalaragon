@extends('app')
@section('title', $article->title)

@section('content')
  <div class="container-fluid py-3">
    <a href="{{ route('crewnews.index') }}" class="btn btn-sm btn-outline-secondary mb-4">&larr; All news</a>

    <article class="mx-auto" style="max-width: 820px;">
      <p class="text-uppercase small fw-semibold text-primary mb-2">
        {{ $article->published_at->format('F j, Y') }}
        @if($article->user)
          &middot; {{ $article->user->name }}
        @endif
      </p>
      <h1 class="display-5 fw-bold mb-3">{{ $article->title }}</h1>
      <p class="lead text-body-secondary mb-4">{{ $article->summary }}</p>

      @if($article->image_url)
        <img src="{{ $article->image_url }}" alt="" class="img-fluid rounded w-100 mb-4">
      @endif

      <div class="fs-5">{!! $article->body_html !!}</div>
    </article>

    @if($more->isNotEmpty())
      <hr class="my-5">
      <h2 class="h4 fw-bold mb-4">More news</h2>
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($more as $article)
          <div class="col">@include('crewnews::partials.tile-small', ['article' => $article])</div>
        @endforeach
      </div>
    @endif
  </div>
@endsection
