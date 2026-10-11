@extends('app')
@section('title', 'News')

@section('content')
  {{-- w-100 + text-start + me-auto: opt out of any centering done by the theme's content wrapper --}}
  <div class="container-fluid w-100 text-start py-3">
    <h1 class="h3 fw-bold mb-4">News</h1>

    @if($articles->isEmpty())
      <div class="alert alert-info">There is no news yet.</div>
    @endif

    {{-- max-width keeps tile sizes close to Delta's (large tile ~600px wide). Raise or remove to go wider. --}}
    <div class="w-100 me-auto text-start" style="max-width: 960px;">
      <div class="row gx-lg-4">
        {{-- Main column (~2/3): the first 5 stories on this page, large --}}
        <div class="col-lg-8">
          @foreach($large as $article)
            @include('crewnews::partials.tile-large', ['article' => $article])
          @endforeach
        </div>

        {{-- Side column (~1/3): the next 5, small --}}
        <div class="col-lg-4">
          @foreach($small as $article)
            @include('crewnews::partials.tile-small', ['article' => $article])
          @endforeach
        </div>
      </div>

      <div class="d-flex justify-content-start mt-4">
        {{ $articles->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
@endsection
