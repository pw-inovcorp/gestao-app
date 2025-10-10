<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    //
    public function index(Request $request)
    {
        $articles = Article::with('ivaRate')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Article/Index', [
            'articles' => $articles
        ]);
    }

}
