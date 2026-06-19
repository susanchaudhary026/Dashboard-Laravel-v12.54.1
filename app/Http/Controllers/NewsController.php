<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function index()
    {
        $apikey = env('NEWS_API_KEY');

        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey'   => $apikey,
            'country'  => 'us',
            'language' => 'en',
        ]);
        
        $news = $response->json();
        
        $art = $news['articles'] ?? []; 
        
        return view('admin.news.index', compact('art'));
    }
}