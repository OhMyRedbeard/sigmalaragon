@foreach($news as $article)
    <div class="accordion-item">
        <div class="accordion-item border-0">
            <div class="accordion-header rounded">
                <button class="accordion-button collapsed btn py-2" data-bs-toggle="collapse" data-bs-target="#article{{ $loop->iteration }}" type="button">
                    <div class="d-flex row m-0 w-100">
                        <div class="col-md-5 m-0 p-0">{{ $article->created_at->format('M d, Y') }}</div>
                        <div class="col-md-7 m-0 p-0 text-truncate">
                            {!! $article->subject !!}
                        </div>
                    </div>
                </button>
            </div>
        </div>
    @endforeach