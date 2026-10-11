<?php

namespace Modules\CrewNews\Http\Controllers;

use App\Contracts\Controller;
use App\Models\News;

class NewsController extends Controller
{
    private const PER_PAGE = 10;

    public function index()
    {
        $news = News::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        return view('crewnews::index', ['news' => $news]);
    }

    public function show(int $id)
    {
        $article = News::with('user')->findOrFail($id);

        $others = News::where('id', '!=', $article->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('crewnews::show', [
            'article' => $article,
            'others'  => $others,
        ]);
    }
}
