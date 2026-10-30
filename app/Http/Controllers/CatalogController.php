<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function index(Request $request, ?string $category = null)
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();
        $selectedCategory = $category ?? $request->query('category');
        
        $query = Course::where('is_published', true)
            ->with(['instructorProfile', 'instructor', 'sections.lessons']);

        $categoryTitle = null;
        $categoryDesc = null;

        // Apply category filter if provided
        if ($selectedCategory) {
            $catRecord = Category::where('slug', $selectedCategory)
                ->orWhere('name', $selectedCategory)
                ->orWhere('slug', Str::slug($selectedCategory))
                ->first();

            $matchedName = $catRecord ? $catRecord->name : null;
            if ($catRecord) {
                $categoryDesc = $catRecord->description;
                $categoryTitle = $catRecord->name;
            }

            $query->where(function ($q) use ($selectedCategory, $matchedName) {
                $q->where('category', $selectedCategory)
                  ->orWhere('category', 'like', '%' . str_replace('-', ' ', $selectedCategory) . '%');
                if ($matchedName) {
                    $q->orWhere('category', $matchedName);
                }
            });

            if (!$categoryTitle) {
                $categoryTitle = ucwords(str_replace('-', ' ', $selectedCategory));
            }
        }

        // Filter by course type if provided (?type=certificate or ?type=day_long)
        $type = $request->query('type');
        if ($type === 'certificate') {
            $query->where(function($q) {
                $q->where('level', 'Certificate')
                  ->orWhere('title', 'like', '%Certificate%');
            });
            if (!$categoryTitle) {
                $categoryTitle = 'Certificate Programs';
                $categoryDesc = 'Comprehensive, multi-week executive certification tracks engineered for career growth and organizational impact.';
            }
        } elseif ($type === 'day_long') {
            $query->where(function($q) {
                $q->where('level', 'Day Long')
                  ->orWhere('duration', 'like', '%Day%')
                  ->orWhere('title', 'like', '%Day-Long%')
                  ->orWhere('title', 'like', '%Day Long%')
                  ->orWhere('title', 'like', '%Masterclass%');
            });
            if (!$categoryTitle) {
                $categoryTitle = 'Day Long Masterclasses';
                $categoryDesc = 'Intensive, high-impact one-day executive workshops covering practical commercial compliance and industrial operations.';
            }
        }

        // Sorting
        $sort = $request->query('sort', 'recommended');
        switch ($sort) {
            case 'newest':
                $query->latest();
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'recommended':
            default:
                if (auth()->check() && auth()->user()->preferences) {
                    $prefs = auth()->user()->preferences;
                    $level = $prefs['level'] ?? '';
                    $subjects = $prefs['subjects'] ?? [];
                    
                    if (!empty($subjects) || !empty($level)) {
                        $subjectsList = implode("','", array_map('addslashes', $subjects));
                        $levelQuery = $level ? "level = '{$level}' DESC," : "";
                        $subjectQuery = !empty($subjectsList) ? "category IN ('{$subjectsList}') DESC," : "";
                        $query->orderByRaw("{$levelQuery} {$subjectQuery} cover_image IS NULL ASC, created_at DESC");
                        break;
                    }
                }
                $query->orderByRaw('cover_image IS NULL ASC, created_at DESC');
                break;
        }

        $courses = $query->paginate(9)->withQueryString();

        // Count published courses per category
        $dbCategoryCounts = Course::where('is_published', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        // Fetch dynamic categories from the database
        $dbCategories = Category::active()->ordered()->get();
        $runningCategories = [];

        foreach ($dbCategories as $cat) {
            $count = $dbCategoryCounts->get($cat->name, 0);
            $runningCategories[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $count,
                'image' => $cat->image ?: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
                'desc' => $cat->description ?: 'Professional training and skills development',
                'icon' => $cat->icon ?: 'fa-tag',
            ];
        }

        // Graceful fallback if categories table has not been seeded yet
        if (empty($runningCategories)) {
            $coursesCats = Course::where('is_published', true)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category');

            foreach ($coursesCats as $cName) {
                $runningCategories[] = [
                    'id' => 0,
                    'name' => $cName,
                    'slug' => Str::slug($cName),
                    'count' => $dbCategoryCounts->get($cName, 0),
                    'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
                    'desc' => 'Professional training and skills development',
                    'icon' => 'fa-tag',
                ];
            }
        }

        return view('catalog.index', compact(
            'courses',
            'sort',
            'selectedCategory',
            'categoryTitle',
            'categoryDesc',
            'runningCategories'
        ));
    }

    public function category(Request $request, string $category)
    {
        return $this->index($request, $category);
    }
}
