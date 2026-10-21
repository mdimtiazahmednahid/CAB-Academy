<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    /**
     * Standard category images / metadata for Course Catalog layout
     */
    protected static array $categoryMeta = [
        'export-import' => [
            'name' => 'Export – Import',
            'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=600&q=80',
            'desc' => 'L/C, Banking, Customs, Bond Management, and International Trade Procedures'
        ],
        'supply-chain-management' => [
            'name' => 'Supply Chain Management',
            'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Procurement, Logistics, Warehouse, and Inventory Optimization'
        ],
        'accounting' => [
            'name' => 'Accounting & Finance',
            'image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Financial Reporting, Auditing, Corporate Tax, and Budgeting'
        ],
        'income-tax-management' => [
            'name' => 'Income Tax Management',
            'image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Tax Assessment, VAT & Customs, Returns, and Legal Compliance'
        ],
        'human-resource' => [
            'name' => 'Human Resource',
            'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=80',
            'desc' => 'HR Operations, Labor Law, Talent Acquisition, and Performance Appraisal'
        ],
        'civil-construction-project' => [
            'name' => 'Civil Construction Project',
            'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Project Planning, Site Supervision, Quality Control, and Estimating'
        ],
        'fire-safety' => [
            'name' => 'Fire Safety',
            'image' => 'https://images.unsplash.com/photo-1582139329536-e7284fece509?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Industrial Safety, Disaster Management, NFPA Codes, and Compliance'
        ],
        'leadership' => [
            'name' => 'Leadership & Soft Skills',
            'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=600&q=80',
            'desc' => 'Executive Leadership, Communication, Conflict Resolution, and Negotiations'
        ],
    ];

    public function index(Request $request, ?string $category = null)
    {
        $selectedCategory = $category ?? $request->query('category');
        
        $query = Course::where('is_published', true)
            ->with(['instructor', 'sections.lessons']);

        $categoryTitle = null;
        $categoryDesc = null;

        // Apply category filter if provided
        if ($selectedCategory) {
            $matchedName = null;
            foreach (self::$categoryMeta as $slug => $meta) {
                if ($slug === Str::slug($selectedCategory)) {
                    $matchedName = $meta['name'];
                    $categoryDesc = $meta['desc'];
                    break;
                }
            }

            $query->where(function ($q) use ($selectedCategory, $matchedName) {
                $q->where('category', $selectedCategory)
                  ->orWhere('category', 'like', '%' . str_replace('-', ' ', $selectedCategory) . '%');
                if ($matchedName) {
                    $q->orWhere('category', $matchedName);
                }
            });

            $categoryTitle = $matchedName ?? ucwords(str_replace('-', ' ', $selectedCategory));
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

        // Fetch Running Course Categories with active course counts
        $dbCategories = Course::where('is_published', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        $runningCategories = [];
        foreach ($dbCategories as $cat) {
            $slug = Str::slug($cat->category);
            $meta = self::$categoryMeta[$slug] ?? null;
            $runningCategories[] = [
                'name' => $cat->category,
                'slug' => $slug,
                'count' => $cat->count,
                'image' => $meta['image'] ?? 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
                'desc' => $meta['desc'] ?? 'Professional training and skills development',
            ];
        }

        // Fallback standard categories if DB has few
        if (count($runningCategories) < 4) {
            foreach (self::$categoryMeta as $slug => $meta) {
                if (!collect($runningCategories)->contains('slug', $slug)) {
                    $runningCategories[] = [
                        'name' => $meta['name'],
                        'slug' => $slug,
                        'count' => 0,
                        'image' => $meta['image'],
                        'desc' => $meta['desc'],
                    ];
                }
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
