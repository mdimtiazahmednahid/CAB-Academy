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
                // For now, recommended is just newest, but we'll prioritize courses with cover images
                $query->orderByRaw('cover_image IS NULL ASC, created_at DESC');
                break;
        }

        $courses = $query->get();
            
        return view('catalog.index', compact('courses', 'sort'));
    }
}
