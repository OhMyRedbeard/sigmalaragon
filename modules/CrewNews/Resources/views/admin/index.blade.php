@extends('admin.app')
@section('title', 'News')

@section('actions')
  <li><a href="{{ route('crewnews.admin.create') }}"><i class="ti-plus"></i> Add article</a></li>
@endsection

@section('content')
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card">
    <div class="content table-responsive table-full-width">
      <table class="table table-hover">
        <thead>
          <tr>
            <th style="width: 90px;">Image</th>
            <th>Title</th>
            <th>Status</th>
            <th>Publish date</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($articles as $article)
            <tr>
              <td>
                @if($article->image_url)
                  <img src="{{ $article->image_url }}" alt="" style="width: 80px; height: 45px; object-fit: cover;">
                @endif
              </td>
              <td>
                <strong>{{ $article->title }}</strong><br>
                <small class="text-muted">{{ \Illuminate\Support\Str::limit($article->summary, 100) }}</small>
              </td>
              <td>
                @if(!$article->is_published)
                  <span class="label label-default">Draft</span>
                @elseif($article->published_at->isFuture())
                  <span class="label label-warning">Scheduled</span>
                @else
                  <span class="label label-success">Published</span>
                @endif
              </td>
              <td>{{ $article->published_at ? $article->published_at->format('M j, Y g:i A') : '—' }}</td>
              <td class="text-right">
                @if($article->is_published && !$article->published_at->isFuture())
                  <a href="{{ route('crewnews.show', $article->slug) }}" target="_blank" class="btn btn-xs btn-default">View</a>
                @endif
                <a href="{{ route('crewnews.admin.edit', $article->id) }}" class="btn btn-xs btn-primary">Edit</a>
                <form action="{{ route('crewnews.admin.destroy', $article->id) }}" method="POST" style="display: inline;"
                      onsubmit="return confirm('Delete this article? This cannot be undone.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">No articles yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="text-center">{{ $articles->links() }}</div>
@endsection
