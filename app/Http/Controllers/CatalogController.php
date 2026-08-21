<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('is_published', true)->with('sections.lessons');

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
                        // Order by level match first, then by subject match, then newest
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

        $courses = $query->get();
            
        return view('catalog.index', compact('courses', 'sort'));
    }
}
