<div class="mb-4">
  <a href="{{ route('crewnews.show', $article->slug) }}" class="d-block text-decoration-none">
    <div class="ratio ratio-16x9 bg-dark overflow-hidden">
      @if($article->image_url)
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="object-fit-cover" loading="lazy">
      @endif
    </div>
    <div class="fs-6 fw-bold lh-sm text-primary mt-2">{{ $article->summary }}</div>
  </a>
</div>
