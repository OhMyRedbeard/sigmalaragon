<div class="mb-5">
  <a href="{{ route('crewnews.show', $article->slug) }}" class="d-block position-relative overflow-hidden text-decoration-none text-white">
    <div class="ratio ratio-16x9 bg-dark">
      @if($article->image_url)
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="object-fit-cover" loading="lazy">
      @endif
    </div>

    {{-- Panel: half the tile width, flush to the bottom-left corner --}}
    <div class="position-absolute bottom-0 start-0 w-50 bg-dark bg-opacity-75 p-3" style="min-width: 16rem;">
      <p class="fs-5 fw-bold lh-sm mb-0">{{ $article->summary }}</p>
    </div>
  </a>
</div>
