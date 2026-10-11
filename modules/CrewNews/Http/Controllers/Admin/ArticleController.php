<?php

namespace Modules\CrewNews\Http\Controllers\Admin;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\CrewNews\Models\Article;

class ArticleController extends Controller
{
    private const IMAGE_DIR = 'uploads/crewnews';

    public function index()
    {
        $articles = Article::orderByDesc('created_at')->paginate(20);

        return view('crewnews::admin.index', ['articles' => $articles]);
    }

    public function create()
    {
        return view('crewnews::admin.form', ['article' => new Article(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['slug'] = Article::makeSlug($data['title']);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        Article::create($data);

        return redirect()->route('crewnews.admin.index')->with('status', 'Article created.');
    }

    public function edit(Article $article)
    {
        return view('crewnews::admin.form', ['article' => $article]);
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($article->image_path);
            $data['image_path'] = $this->storeImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($article->image_path);
            $data['image_path'] = null;
        }

        // The slug is left alone on edit so existing links keep working.
        $article->update($data);

        return redirect()->route('crewnews.admin.index')->with('status', 'Article updated.');
    }

    public function destroy(Article $article)
    {
        $this->deleteImage($article->image_path);
        $article->delete();

        return redirect()->route('crewnews.admin.index')->with('status', 'Article deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'summary'      => ['required', 'string', 'max:600'],
            'body'         => ['required', 'string'],
            'image'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        unset($data['image']);

        $data['is_published'] = $request->boolean('is_published');

        // Publishing with no date means "now".
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    private function storeImage(UploadedFile $file): string
    {
        $name = Str::random(24).'.'.$file->extension();
        $file->move(public_path(self::IMAGE_DIR), $name);

        return self::IMAGE_DIR.'/'.$name;
    }

    private function deleteImage(?string $path): void
    {
        // Only ever delete inside our own upload folder.
        if ($path && Str::startsWith($path, self::IMAGE_DIR.'/')) {
            @unlink(public_path($path));
        }
    }
}
