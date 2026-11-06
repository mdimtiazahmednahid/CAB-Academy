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
        $selectedSubtrack = $request->query('subtrack');
        
        $query = Course::where('is_published', true)
            ->with(['instructorProfile', 'instructor', 'sections.lessons']);

        $categoryTitle = null;
        $categoryDesc = null;
        $activeDepartment = null;

        // Apply category / department / sub-track filter if provided
        if ($selectedCategory) {
            $resolved = Category::resolveDepartmentAndSubtrack($selectedCategory);
            $activeDepartment = $resolved['department'];
            if (!$selectedSubtrack && !empty($resolved['subtrack'])) {
                $selectedSubtrack = $resolved['subtrack'];
            }

            if ($resolved['is_legacy_export_import']) {
                $categoryTitle = 'Export – Import';
                $categoryDesc = $activeDepartment?->description;
            } elseif ($activeDepartment) {
                $categoryTitle = $selectedSubtrack
                    ? $selectedSubtrack
                    : $activeDepartment->name;
                $categoryDesc = $activeDepartment->description;
            } else {
                $categoryTitle = ucwords(str_replace('-', ' ', $selectedCategory));
            }

            if ($activeDepartment) {
                $deptTerms = $activeDepartment->getMatchingCategoryTerms();

                if ($selectedSubtrack) {
                    // Check if specific courses exist for this sub-track; otherwise show department courses
                    $subtrackCourseCount = Course::where('is_published', true)
                        ->where(function ($q) use ($selectedSubtrack) {
                            $q->where('category', $selectedSubtrack)
                              ->orWhere('title', 'like', '%' . $selectedSubtrack . '%');
                        })
                        ->count();

                    if ($subtrackCourseCount > 0) {
                        $query->where(function ($q) use ($selectedSubtrack) {
                            $q->where('category', $selectedSubtrack)
                              ->orWhere('title', 'like', '%' . $selectedSubtrack . '%');
                        });
                    } else {
                        $query->where(function ($q) use ($deptTerms, $selectedCategory) {
                            $q->whereIn('category', $deptTerms)
                              ->orWhere('category', $selectedCategory)
                              ->orWhere('category', 'like', '%' . str_replace('-', ' ', $selectedCategory) . '%');
                        });
                    }
                } else {
                    $query->where(function ($q) use ($deptTerms, $selectedCategory) {
                        $q->whereIn('category', $deptTerms)
                          ->orWhere('category', $selectedCategory)
                          ->orWhere('category', 'like', '%' . str_replace('-', ' ', $selectedCategory) . '%');
                    });
                }
            } else {
                $query->where(function ($q) use ($selectedCategory) {
                    $q->where('category', $selectedCategory)
                      ->orWhere('category', 'like', '%' . str_replace('-', ' ', $selectedCategory) . '%');
                });
            }
        } elseif ($selectedSubtrack) {
            $resolved = Category::resolveDepartmentAndSubtrack($selectedSubtrack);
            $activeDepartment = $resolved['department'];
            $categoryTitle = $selectedSubtrack;
            $categoryDesc = $activeDepartment?->description;

            $subtrackCourseCount = Course::where('is_published', true)
                ->where(function ($q) use ($selectedSubtrack) {
                    $q->where('category', $selectedSubtrack)
                      ->orWhere('title', 'like', '%' . $selectedSubtrack . '%');
                })
                ->count();

            if ($subtrackCourseCount > 0) {
                $query->where(function ($q) use ($selectedSubtrack) {
                    $q->where('category', $selectedSubtrack)
                      ->orWhere('title', 'like', '%' . $selectedSubtrack . '%');
                });
            } elseif ($activeDepartment) {
                $query->whereIn('category', $activeDepartment->getMatchingCategoryTerms());
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
                        $subjectsList = implode("','", array_map('addslashes', (array) $subjects));
                        $safeLevel = addslashes((string) $level);
                        $levelQuery = $safeLevel !== '' ? "level = '{$safeLevel}' DESC," : "";
                        $subjectQuery = !empty($subjectsList) ? "category IN ('{$subjectsList}') DESC," : "";
                        $query->orderByRaw("{$levelQuery} {$subjectQuery} cover_image IS NULL ASC, created_at DESC");
                        break;
                    }
                }
                $query->orderByRaw('cover_image IS NULL ASC, created_at DESC');
                break;
        }

        $courses = $query->paginate(9)->withQueryString();

        // Count published courses per category string
        $dbCategoryCounts = Course::where('is_published', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        // Fetch dynamic Learning Departments from the database
        $dbCategories = Category::active()->ordered()->with('headInstructor')->get();
        $runningCategories = [];

        foreach ($dbCategories as $cat) {
            $terms = $cat->getMatchingCategoryTerms();
            $count = 0;
            foreach ($dbCategoryCounts as $catKey => $cVal) {
                foreach ($terms as $t) {
                    if (strcasecmp(trim($catKey), trim($t)) === 0) {
                        $count += (int) $cVal;
                        break;
                    }
                }
            }

            $head = $cat->resolved_head;
            $team = $cat->resolved_team;

            $runningCategories[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $count,
                'image' => $cat->image ?: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
                'desc' => $cat->description ?: 'Professional training and skills development',
                'icon' => $cat->icon ?: 'fa-tag',
                'subcategories' => $cat->effective_subcategories,
                'head' => $head,
                'team' => $team,
                'team_count' => $team ? $team->count() : 0,
            ];
        }

        // Graceful fallback if categories table has not been seeded yet
        if (empty($runningCategories)) {
            foreach (Category::getCoreDepartmentsBlueprint() as $idx => $dept) {
                $runningCategories[] = [
                    'id' => $idx + 1,
                    'name' => $dept['name'],
                    'slug' => $dept['slug'],
                    'count' => 0,
                    'image' => $dept['image'],
                    'desc' => $dept['description'],
                    'icon' => $dept['icon'],
                    'subcategories' => $dept['subcategories'],
                    'head' => null,
                    'team' => collect(),
                    'team_count' => 0,
                ];
            }
        }

        return view('catalog.index', compact(
            'courses',
            'sort',
            'selectedCategory',
            'selectedSubtrack',
            'activeDepartment',
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
