<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('user')
            ->published()
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('member.articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        // Enforce published-only visibility for members
        if (!$article->isPublished() || ($article->published_at && $article->published_at->isFuture())) {
            abort(404, 'Artikel tidak ditemukan.');
        }

        $latestArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('member.articles.show', compact('article', 'latestArticles'));
    }
}
