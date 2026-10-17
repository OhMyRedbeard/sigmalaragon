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

          <div class="btn-toolbar" id="md-toolbar" style="margin-bottom: 6px;">
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-default" data-md="bold" title="Bold (Ctrl+B)"><b>B</b></button>
              <button type="button" class="btn btn-default" data-md="italic" title="Italic (Ctrl+I)"><i>I</i></button>
              <button type="button" class="btn btn-default" data-md="strike" title="Strikethrough"><s>S</s></button>
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-default" data-md="h2" title="Heading">H2</button>
              <button type="button" class="btn btn-default" data-md="h3" title="Subheading">H3</button>
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-default" data-md="ul" title="Bulleted list">&bull; List</button>
              <button type="button" class="btn btn-default" data-md="ol" title="Numbered list">1. List</button>
              <button type="button" class="btn btn-default" data-md="quote" title="Quote">&ldquo; Quote</button>
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-default" data-md="link" title="Link (Ctrl+K)">Link</button>
              <button type="button" class="btn btn-default" data-md="image" title="Image from a URL">Image</button>
              <button type="button" class="btn btn-default" data-md="hr" title="Horizontal line">&mdash; Line</button>
            </div>
          </div>

          <textarea id="body" name="body" class="form-control" rows="16" required>{{ old('body', $article->body) }}</textarea>
          <p class="help-block">Press Enter for a new line and leave a blank line between paragraphs. Select text, then use the buttons above to format it. Raw HTML is stripped.</p>
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

  <script>
    (function () {
      var ta = document.getElementById('body');
      var bar = document.getElementById('md-toolbar');
      if (!ta || !bar) return;

      // Wrap the selection (or a placeholder) in before/after, or unwrap if already wrapped.
      function wrap(before, after, placeholder) {
        var s = ta.selectionStart, e = ta.selectionEnd, v = ta.value;
        var sel = v.slice(s, e) || placeholder;
        if (v.slice(s - before.length, s) === before && v.slice(e, e + after.length) === after && s !== e) {
          ta.setRangeText(sel, s - before.length, e + after.length, 'select');
        } else {
          ta.setRangeText(before + sel + after, s, e, 'end');
          ta.setSelectionRange(s + before.length, s + before.length + sel.length);
        }
      }

      // Prefix every selected line (numbered lists count up).
      function prefixLines(prefixFn) {
        var v = ta.value, s = ta.selectionStart, e = ta.selectionEnd;
        var start = v.lastIndexOf('\n', s - 1) + 1;
        var endIdx = v.indexOf('\n', e);
        if (endIdx === -1) endIdx = v.length;
        var lines = v.slice(start, endIdx).split('\n').map(function (l, i) {
          return prefixFn(i) + l;
        });
        ta.setRangeText(lines.join('\n'), start, endIdx, 'select');
      }

      function heading(level) {
        var hashes = new Array(level + 1).join('#') + ' ';
        var v = ta.value, s = ta.selectionStart;
        var start = v.lastIndexOf('\n', s - 1) + 1;
        var endIdx = v.indexOf('\n', s); if (endIdx === -1) endIdx = v.length;
        var line = v.slice(start, endIdx).replace(/^#{1,6}\s*/, '');
        ta.setRangeText(hashes + line, start, endIdx, 'end');
      }

      var actions = {
        bold:   function () { wrap('**', '**', 'bold text'); },
        italic: function () { wrap('*', '*', 'italic text'); },
        strike: function () { wrap('~~', '~~', 'text'); },
        h2:     function () { heading(2); },
        h3:     function () { heading(3); },
        ul:     function () { prefixLines(function () { return '- '; }); },
        ol:     function () { prefixLines(function (i) { return (i + 1) + '. '; }); },
        quote:  function () { prefixLines(function () { return '> '; }); },
        link: function () {
          var url = window.prompt('Link address (URL):', 'https://');
          if (!url) return;
          var s = ta.selectionStart, e = ta.selectionEnd;
          var text = ta.value.slice(s, e) || 'link text';
          ta.setRangeText('[' + text + '](' + url + ')', s, e, 'end');
        },
        image: function () {
          var url = window.prompt('Image address (URL):', 'https://');
          if (!url) return;
          var s = ta.selectionStart, e = ta.selectionEnd;
          var alt = ta.value.slice(s, e) || 'description';
          ta.setRangeText('![' + alt + '](' + url + ')', s, e, 'end');
        },
        hr: function () {
          var s = ta.selectionStart;
          ta.setRangeText('\n\n---\n\n', s, ta.selectionEnd, 'end');
        }
      };

      bar.addEventListener('click', function (ev) {
        var btn = ev.target.closest('[data-md]');
        if (!btn || !actions[btn.getAttribute('data-md')]) return;
        ev.preventDefault();
        actions[btn.getAttribute('data-md')]();
        ta.focus();
      });

      ta.addEventListener('keydown', function (ev) {
        if (!(ev.ctrlKey || ev.metaKey)) return;
        var k = ev.key.toLowerCase();
        if (k === 'b') { ev.preventDefault(); actions.bold(); }
        else if (k === 'i') { ev.preventDefault(); actions.italic(); }
        else if (k === 'k') { ev.preventDefault(); actions.link(); }
      });
    })();
  </script>
@endsection
