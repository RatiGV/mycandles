<?php

namespace App\Http\Controllers\Client;

use App\Models\News;
use Illuminate\Http\Request;
use App\Models\NewsTranslate;
use App\Models\NewsCategories;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\NewsCategoriesTranslate;
use App\Models\Tag;
use App\Models\TagsTranslate;

class BlogsController extends Controller
{
    public $data = [];

    public function index(Request $request)
    {
        $category = $request->category;
        $search = $request->search;
        $tag = $request->tag;

        $this->data['blogs'] = News::with('trans')
            ->when($category, function ($q, $category) {
                $cat = NewsCategoriesTranslate::where('title', 'like', '%' . $category . '%')->where('lang', locale())->pluck('parent_id')->toArray();
                $q->where('category_id', $cat);
            })
            ->when($tag, function ($q, $tag) {
                $t = TagsTranslate::where('title', 'like', '%' . $tag . '%')->where('lang', locale())->pluck('parent_id')->toArray();

                $q->whereJsonContains('tag_ids', array_map('strval', $t));
            })
            ->when($search, function ($q, $search) {
                $titles = NewsTranslate::where('title', 'like', '%' . $search . '%')->where('lang', locale())->pluck('parent_id')->toArray();
                $q->whereIn('id', $titles);
            })
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $this->data['recents'] = News::with('trans')->where('status', 1)->orderBy('created_at', 'desc')->take(4)->get();

        $this->data['categories'] = NewsCategories::withCount('blogs')->get();

        $news = DB::table('news')->select('id', 'tag_ids')->get();
        
        $tagUsage = [];
        
        foreach ($news as $item) {
            $tags = json_decode($item->tag_ids, true);
        
            if (!is_array($tags)) {
                $tags = explode(',', $item->tag_ids);
            }
        
            foreach ($tags as $tagId) {
                $tagId = intval($tagId);
                if ($tagId <= 0) continue;
        
                if (!isset($tagUsage[$tagId])) {
                    $tagUsage[$tagId] = 0;
                }
        
                $tagUsage[$tagId]++;
            }
        }
        
        arsort($tagUsage);
        
        $topTagIds = array_slice(array_keys($tagUsage), 0, 6);
        
        if (empty($topTagIds)) {
            $this->data['popularTags'] = collect([]);
            return;
        }
        
        $tags = DB::table('tags as t')
            ->select(
                't.id',
                DB::raw('COALESCE(tt.title, CONCAT("Tag ", t.id)) as title')
            )
            ->leftJoin('tags_translates as tt', function($join) {
                $join->on('tt.parent_id', '=', 't.id')
                    ->where('tt.lang', locale());
            })
            ->whereIn('t.id', $topTagIds)
            ->where('t.status', 1)
            ->get();
        
        $popularTags = $tags->map(function ($tag) use ($tagUsage) {
            $tag->usage_count = $tagUsage[$tag->id] ?? 0;
            return $tag;
        })->sortByDesc('usage_count')->values();
        
        $this->data['popularTags'] = $popularTags;

        return view('client.blogs.index', $this->data);
    }

    public function inner($blog)
    {
        $this->data['blog'] = News::with(['trans'])
            ->where('status', 1)
            ->where(function ($q) use ($blog) {
                $q->where('id', (int)$blog)
                    ->orWhere('slug', $blog);
            })->firstOrFail();

        $this->data['tags'] = Tag::with('trans')->whereIn('id',json_decode($this->data['blog']->tag_ids,true))->where('status',1)->get();

        $this->data['recents'] = News::with('trans')->where('status', 1)->orderBy('created_at', 'desc')->take(4)->get();

        $this->data['categories'] = NewsCategories::withCount('blogs')->get();

        $news = DB::table('news')->select('id', 'tag_ids')->get();
        
        $tagUsage = [];
        
        foreach ($news as $item) {
            $tags = json_decode($item->tag_ids, true);
        
            if (!is_array($tags)) {
                $tags = explode(',', $item->tag_ids);
            }
        
            foreach ($tags as $tagId) {
                $tagId = intval($tagId);
                if ($tagId <= 0) continue;
        
                if (!isset($tagUsage[$tagId])) {
                    $tagUsage[$tagId] = 0;
                }
        
                $tagUsage[$tagId]++;
            }
        }
        
        arsort($tagUsage);
        
        $topTagIds = array_slice(array_keys($tagUsage), 0, 6);
        
        if (empty($topTagIds)) {
            $this->data['popularTags'] = collect([]);
            return;
        }
        
        $tags = DB::table('tags as t')
            ->select(
                't.id',
                DB::raw('COALESCE(tt.title, CONCAT("Tag ", t.id)) as title')
            )
            ->leftJoin('tags_translates as tt', function($join) {
                $join->on('tt.parent_id', '=', 't.id')
                    ->where('tt.lang', locale());
            })
            ->whereIn('t.id', $topTagIds)
            ->where('t.status', 1)
            ->get();
        
        $popularTags = $tags->map(function ($tag) use ($tagUsage) {
            $tag->usage_count = $tagUsage[$tag->id] ?? 0;
            return $tag;
        })->sortByDesc('usage_count')->values();
        
        $this->data['popularTags'] = $popularTags;

        return view('client.blogs.inner', $this->data);
    }
}
