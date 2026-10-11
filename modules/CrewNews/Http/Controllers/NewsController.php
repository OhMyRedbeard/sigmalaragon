<?php

namespace Modules\CrewNews\Http\Controllers;

use App\Contracts\Controller;
use Modules\CrewNews\Models\Article;

class NewsController extends Controller
{
    private const PER_PAGE = 10;

    public function index()
    {
        $articles = Article::published()
            ->orderByDesc('published_at')
            ->paginate(self::PER_PAGE);

        // Each page: first 5 stories as large tiles, remaining 5 as small items.
        $items = $articles->getCollection();

        return view('crewnews::index', [
            'articles' => $articles,
            'large'    => $items->take(5),
            'small'    => $items->slice(5),
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        $more = Article::published()
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('crewnews::show', [
            'article' => $article,
            'more'    => $more,
        ]);
    }
}
