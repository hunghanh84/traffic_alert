<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class RssNewsController extends Controller
{
    // Static cache to avoid database dependency
    private static $cachedNews = null;
    private static $cacheTime = null;

    /**
     * Fetch traffic news from RSS feeds
     */
    public function getTrafficNews()
    {
        try {
            // Simple cache: 30 minutes using static variable
            if (self::$cachedNews !== null && self::$cacheTime !== null) {
                if (time() - self::$cacheTime < 1800) { // 30 minutes
                    return response()->json([
                        'success' => true,
                        'data' => self::$cachedNews,
                        'cached' => true
                    ]);
                }
            }

            // Fetch fresh data
            $news = $this->fetchAllRssFeeds();
            
            // Update cache
            self::$cachedNews = $news;
            self::$cacheTime = time();

            return response()->json([
                'success' => true,
                'data' => $news,
                'cached' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Fetch and parse RSS feeds from multiple sources
     */
    private function fetchAllRssFeeds()
    {
        $feeds = [
            [
                'name' => 'VnExpress',
                'url' => 'https://vnexpress.net/rss/giao-thong.rss',
                'color' => '#9b2c2c'
            ],
            [
                'name' => 'Tuổi Trẻ',
                'url' => 'https://tuoitre.vn/rss/giao-thong.rss',
                'color' => '#2563eb'
            ],
        ];

        $allNews = [];

        foreach ($feeds as $feed) {
            try {
                $rssContent = $this->fetchRssFeed($feed['url']);
                if ($rssContent) {
                    $items = $this->parseRssFeed($rssContent, $feed['name'], $feed['color']);
                    $allNews = array_merge($allNews, $items);
                }
            } catch (\Exception $e) {
                // Continue with other feeds
                continue;
            }
        }

        // Sort by date (newest first)
        usort($allNews, function ($a, $b) {
            $timeA = @strtotime($a['published_at']);
            $timeB = @strtotime($b['published_at']);
            return $timeB - $timeA;
        });

        // Return only the 15 most recent news
        return array_slice($allNews, 0, 15);
    }

    /**
     * Fetch RSS feed content
     */
    private function fetchRssFeed($url)
    {
        try {
            // Use file_get_contents with timeout
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            
            $content = @file_get_contents($url, false, $context);
            
            return $content !== false ? $content : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse RSS XML content
     */
    private function parseRssFeed($xmlContent, $sourceName, $sourceColor)
    {
        $news = [];
        
        try {
            // Disable XML errors
            libxml_use_internal_errors(true);
            
            // Ensure UTF-8 encoding
            $xmlContent = mb_convert_encoding($xmlContent, 'UTF-8', 'UTF-8');
            
            $xml = simplexml_load_string($xmlContent);
            
            if ($xml === false) {
                return [];
            }

            // Handle different RSS formats
            $items = $xml->channel->item ?? $xml->item ?? [];

            $count = 0;
            foreach ($items as $item) {
                if ($count >= 10) break; // Get more items to filter
                
                $title = (string)($item->title ?? 'Không có tiêu đề');
                $title = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
                
                // Extract description and remove HTML tags
                $description = strip_tags((string)($item->description ?? ''));
                $description = mb_convert_encoding($description, 'UTF-8', 'UTF-8');
                
                // Filter: Only traffic/road related news
                if (!$this->isTrafficRelated($title, $description)) {
                    continue;
                }
                
                // Limit description length
                if (mb_strlen($description) > 200) {
                    $description = mb_substr($description, 0, 200) . '...';
                }

                // Extract image
                $imageUrl = $this->extractImageFromItem($item);

                $news[] = [
                    'title' => $title,
                    'description' => $description,
                    'link' => (string)($item->link ?? '#'),
                    'published_at' => $this->formatDate((string)($item->pubDate ?? $item->date ?? '')),
                    'source' => $sourceName,
                    'source_color' => $sourceColor,
                    'image_url' => $imageUrl,
                ];
                
                $count++;
            }
        } catch (\Exception $e) {
            // Return empty array on error
        }

        return $news;
    }

    /**
     * Check if news is traffic/road related
     */
    private function isTrafficRelated($title, $description)
    {
        $keywords = [
            'giao thông', 'tắc đường', 'ùn tắc', 'tai nạn', 'va chạm',
            'đường phố', 'cao tốc', 'quốc lộ', 'tỉnh lộ', 'đường sắt',
            'cầu đường', 'hầm đường', 'ngập lụt', 'ngập đường',
            'xe cộ', 'ô tô', 'xe máy', 'phương tiện', 'lưu thông',
            'biển báo', 'đèn tín hiệu', 'vỉa hè', 'làn đường',
            'tốc độ', 'phạt nguội', 'camera', 'csgt', 'cảnh sát giao thông',
            'đường bộ', 'hạ tầng', 'sửa chữa đường', 'nâng cấp đường'
        ];

        $text = mb_strtolower($title . ' ' . $description);
        
        foreach ($keywords as $keyword) {
            if (mb_strpos($text, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract image URL from RSS item
     */
    private function extractImageFromItem($item)
    {
        // Try media:content
        $media = $item->children('media', true);
        if (isset($media->content)) {
            $url = (string)$media->content->attributes()->url;
            if ($url) return $url;
        }

        // Try media:thumbnail
        if (isset($media->thumbnail)) {
            $url = (string)$media->thumbnail->attributes()->url;
            if ($url) return $url;
        }

        // Try enclosure
        if (isset($item->enclosure)) {
            $type = (string)$item->enclosure->attributes()->type;
            if (strpos($type, 'image') !== false) {
                return (string)$item->enclosure->attributes()->url;
            }
        }

        // Try to extract from description HTML
        $description = (string)($item->description ?? '');
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
            return $matches[1];
        }

        // Try content:encoded
        $content = $item->children('content', true);
        if (isset($content->encoded)) {
            $html = (string)$content->encoded;
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Format date to Vietnamese format
     */
    private function formatDate($dateString)
    {
        if (empty($dateString)) {
            return date('d-m-Y H:i');
        }

        try {
            $timestamp = strtotime($dateString);
            if ($timestamp === false) {
                return date('d-m-Y H:i');
            }
            return date('d-m-Y H:i', $timestamp);
        } catch (\Exception $e) {
            return date('d-m-Y H:i');
        }
    }
}
