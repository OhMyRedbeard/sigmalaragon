@php($editing = $article->exists)
@extends('admin.app')
@section('title', $editing ? 'Edit article' : 'Add article')

@section('content')
  @if($errors->any())
    <div class="alert alert-danger">
      <ul style="margin: 0; padding-left: 18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $editing ? route('crewnews.admin.update', $article->id) : route('crewnews.admin.store') }}"
        method="POST" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="card">
      <div class="content">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" class="form-control" maxlength="255" required
                 value="{{ old('title', $article->title) }}">
        </div>

        <div class="form-group">
          <label for="summary">Summary</label>
          <textarea id="summary" name="summary" class="form-control" rows="3" maxlength="600" required>{{ old('summary', $article->summary) }}</textarea>
          <p class="help-block">Shown on the news cards and under the headline. Up to 600 characters.</p>
        </div>

        <div class="form-group">
          <label for="image">Card image</label>
          @if($editing && $article->image_url)
            <div style="margin-bottom: 10px;">
              <img src="{{ $article->image_url }}" alt="" style="max-width: 320px; width: 100%;">
              <div class="checkbox">
                <label><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
              </div>
            </div>
          @endif
          <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
          <p class="help-block">JPG, PNG or WebP, up to 4&nbsp;MB. 16:9 works best (e.g. 1280&times;720); other shapes are cropped to fit.
            @if($editing) Uploading a new file replaces the current one. @endif</p>
        </div>

        <div class="form-group">
          <label for="body">Article</label>
          <textarea id="body" name="body" class="form-control" rows="16" required>{{ old('body', $article->body) }}</textarea>
          <p class="help-block">Markdown is supported (headings, bold, lists, links, images). Raw HTML is stripped.</p>
        </div>

        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label for="published_at">Publish date</label>
              <input type="datetime-local" id="published_at" name="published_at" class="form-control"
                     value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
              <p class="help-block">Leave blank for "now". A future date schedules the article.</p>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="form-group">
              <label>Visibility</label>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="is_published" value="1"
                         @checked(old('is_published', $article->is_published))>
                  Published
                </label>
              </div>
              <p class="help-block">Untick to keep it as a draft.</p>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ $editing ? 'Save changes' : 'Create article' }}</button>
        <a href="{{ route('crewnews.admin.index') }}" class="btn btn-default">Cancel</a>
      </div>
    </div>
  </form>
@endsection
