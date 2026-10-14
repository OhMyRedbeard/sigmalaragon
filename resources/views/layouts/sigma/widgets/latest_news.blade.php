{{-- @foreach($news as $article)
    <div class="accordion-item">
        <div class="accordion-item border-0">
            <div class="accordion-header rounded">
                <button class="accordion-button collapsed btn py-2" data-bs-toggle="collapse" data-bs-target="#article{{ $loop->iteration }}" type="button">
                    <div class="d-flex row m-0 w-100">
                        
                    </div>
                </button>
            </div>
        </div>
    @endforeach --}}

    <div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Latest News</h5>
  </div>

  <div class="list-group list-group-flush">

    <!-- News item (repeat this block per post) -->
    <a href="#" class="list-group-item list-group-item-action">
      <div class="row align-items-center g-2">
        <div class="col-12 col-md-2">
          <time class="text-body-secondary small" datetime="2026-10-13">Oct 13, 2026</time>
        </div>
        <div class="col-12 col-md-7">
          <span class="fw-semibold">Post title goes here</span>
        </div>
        <div class="col-12 col-md-3 text-md-end">
          <span class="text-body-secondary small">by Author Name</span>
        </div>
      </div>
    </a>
    <!-- /News item -->

    <a href="#" class="list-group-item list-group-item-action">
      <div class="row align-items-center g-2">
        <div class="col-12 col-md-2">
          <time class="text-body-secondary small" datetime="2026-10-10">Oct 10, 2026</time>
        </div>
        <div class="col-12 col-md-7">
          <span class="fw-semibold">Another post title</span>
        </div>
        <div class="col-12 col-md-3 text-md-end">
          <span class="text-body-secondary small">by Another Author</span>
        </div>
      </div>
    </a>

  </div>
</div>