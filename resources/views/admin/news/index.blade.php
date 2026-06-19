@extends('layouts.app')

@section('content')
<div style="max-w: 1000px; margin: 0 auto; padding: 16px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">
        <h1 style="font-size: 20px; font-weight: bold; color: #1f2937; margin: 0;">National Headlines</h1>
        <span style="font-size: 12px; color: #2563eb; background-color: #eff6ff; padding: 2px 8px; border-radius: 4px; font-weight: 500;">Live</span>
    </div>

    @if(empty($art))
        <div style="background-color: #f9fafb; color: #6b7280; font-size: 14px; padding: 16px; rounded: 8px; text-align: center; border: 1px solid #e5e7eb;">
            No news articles available at the moment.
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($art as $article)
                @if(empty($article['title']) || $article['title'] == '[Removed]')
                    @continue
                @endif

                <div style="background-color: #fff; border-radius: 8px; padding: 12px; border: 1px solid #e5e7eb; display: flex; gap: 16px; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    @if(!empty($article['urlToImage']))
                        <img src="{{ $article['urlToImage'] }}" alt="News" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px; max-width: 64px; max-height: 64px; border-radius: 4px; object-fit: cover; flex-shrink: 0; background-color: #f3f4f6;">
                    @else
                        <div style="width: 64px; height: 64px; min-width: 64px; border-radius: 4px; background-color: #f9fafb; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m0 10a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M21 12H13"></path></svg>
                        </div>
                    @endif
                    
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; color: #9ca3af; margin-bottom: 2px;">
                            <span style="color: #2563eb; font-weight: 600;">{{ $article['source']['name'] ?? 'Media' }}</span>
                            <span>•</span>
                            <span>{{ isset($article['publishedAt']) ? date('M d', strtotime($article['publishedAt'])) : 'Recent' }}</span>
                        </div>
                        
                        <h2 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $article['title'] }}
                        </h2>
                        
                        <p style="font-size: 12px; color: #6b7280; margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $article['description'] ?? 'No description provided.' }}
                        </p>
                    </div>
                    
                    <div style="flex-shrink: 0; text-align: right; padding-right: 4px;">
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" style="font-size: 12px; font-weight: 500; color: #3b82f6; text-decoration: none;">
                            Read &rarr;
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection