<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'subcategories',
        'head_instructor_id',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'subcategories' => 'array',
        'head_instructor_id' => 'integer',
    ];

    /**
     * Official ACS 4 Core Training Categories & Independent Learning Departments Blueprint.
     */
    public static function getCoreDepartmentsBlueprint(): array
    {
        return [
            [
                'name' => 'Commercial Management',
                'slug' => 'commercial-management',
                'icon' => 'fa-ship',
                'order' => 1,
                'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'description' => 'End-to-end mastery of international trade, Letter of Credit (LC) operations, export-import documentation, customs & bond management, and global trade compliance.',
                'subcategories' => [
                    'Export & Import Management',
                    'LC & Trade Finance',
                    'Export Documentation',
                    'Customs & Bond Management',
                    'Shipping & Logistics Documentation',
                    'International Trade & Compliance',
                ],
                'legacy_aliases' => [
                    'Export – Import',
                    'Export - Import',
                    'Export-Import',
                    'Value Added Tax',
                    'Accounting',
                    'Law & Compliance',
                ],
            ],
            [
                'name' => 'Supply Chain Management',
                'slug' => 'supply-chain-management',
                'icon' => 'fa-truck-fast',
                'order' => 2,
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'description' => 'Strategic procurement, global freight logistics, warehouse optimization, production planning, demand forecasting, and data-driven supply chain analytics.',
                'subcategories' => [
                    'Procurement & Sourcing',
                    'Logistics & Freight Management',
                    'Inventory & Warehouse Management',
                    'Production Planning',
                    'Demand & Supply Planning',
                    'Supply Chain Analytics',
                ],
                'legacy_aliases' => [
                    'Production & Ops',
                    'Project Management',
                ],
            ],
            [
                'name' => 'Career Development',
                'slug' => 'career-development',
                'icon' => 'fa-compass',
                'order' => 3,
                'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=80',
                'description' => 'Structured career counselling, executive CV & LinkedIn branding, interview readiness, professional workplace communication, and corporate mentorship.',
                'subcategories' => [
                    'Career Planning & Counselling',
                    'CV & LinkedIn Development',
                    'Interview Preparation',
                    'Job Readiness',
                    'Professional Communication',
                    'Career Growth & Mentorship',
                ],
                'legacy_aliases' => [
                    'Soft Skills',
                    'Human Resource',
                ],
            ],
            [
                'name' => 'Professional Skills & Leadership Development',
                'slug' => 'professional-skills-leadership',
                'icon' => 'fa-award',
                'order' => 4,
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80',
                'description' => 'Executive business communication, team leadership, high-stakes negotiation, public speaking, strategic decision making, and corporate etiquette.',
                'subcategories' => [
                    'Business Communication',
                    'Leadership & Team Management',
                    'Presentation & Public Speaking',
                    'Negotiation Skills',
                    'Decision Making',
                    'Problem Solving & Corporate Etiquette',
                ],
                'legacy_aliases' => [
                    'Leadership',
                    'Communication Skills',
                ],
            ],
        ];
    }

    /**
     * Synchronize the 4 Core Learning Departments into the database idempotently.
     */
    public static function syncCoreDepartments(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            return;
        }

        $hasSubcategoriesCol = \Illuminate\Support\Facades\Schema::hasColumn('categories', 'subcategories');
        $blueprint = static::getCoreDepartmentsBlueprint();
        $coreSlugs = [];

        foreach ($blueprint as $dept) {
            $coreSlugs[] = $dept['slug'];
            $existing = static::where('slug', $dept['slug'])
                ->orWhere('name', $dept['name'])
                ->first();

            $payload = [
                'name' => $dept['name'],
                'slug' => $dept['slug'],
                'icon' => $dept['icon'],
                'description' => $dept['description'],
                'image' => $existing?->image ?: $dept['image'],
                'order' => $dept['order'],
                'is_active' => true,
            ];

            if ($hasSubcategoriesCol) {
                $existingSubs = is_array($existing?->subcategories) ? array_filter($existing->subcategories) : [];
                $payload['subcategories'] = !empty($existingSubs) ? array_values($existingSubs) : $dept['subcategories'];
            }

            if ($existing) {
                $existing->update($payload);
            } else {
                static::create($payload);
            }
        }

        // Deactivate legacy default categories that are not part of the 4 Core Departments
        $legacySlugs = [
            'soft-skills',
            'value-added-tax',
            'export-import',
            'accounting',
            'human-resource',
            'civil-construction-project',
            'fire-safety',
            'law',
            'leadership',
            'production',
            'project-management',
        ];

        static::whereIn('slug', $legacySlugs)
            ->whereNotIn('slug', $coreSlugs)
            ->update(['is_active' => false]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category', 'name');
    }

    public function headInstructor()
    {
        return $this->belongsTo(Instructor::class, 'head_instructor_id');
    }

    public function departmentInstructors()
    {
        return $this->hasMany(Instructor::class, 'department', 'name')->where('is_active', true)->orderBy('order');
    }

    /**
     * Get the 6 specialized sub-tracks for this Learning Department.
     */
    public function getEffectiveSubcategoriesAttribute(): array
    {
        $subs = $this->subcategories;
        if (is_array($subs) && !empty(array_filter($subs))) {
            return array_values(array_filter($subs));
        }

        foreach (static::getCoreDepartmentsBlueprint() as $dept) {
            if (strcasecmp($dept['name'], $this->name) === 0 || $dept['slug'] === $this->slug) {
                return $dept['subcategories'];
            }
        }

        return [];
    }

    /**
     * All category/sub-track strings that belong to this Learning Department for course querying & counting.
     */
    public function getMatchingCategoryTerms(): array
    {
        $terms = array_merge([$this->name], $this->effective_subcategories);

        foreach (static::getCoreDepartmentsBlueprint() as $dept) {
            if (strcasecmp($dept['name'], $this->name) === 0 || $dept['slug'] === $this->slug) {
                $terms = array_merge($terms, $dept['subcategories'], $dept['legacy_aliases'] ?? []);
                break;
            }
        }

        return array_values(array_unique(array_filter($terms)));
    }

    /**
     * Resolve the Department Head instructor (explicitly linked head_instructor_id or instructor with department_role = 'head').
     */
    public function getResolvedHeadAttribute(): ?Instructor
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('instructors')) {
            return null;
        }

        if ($this->head_instructor_id) {
            $head = $this->relationLoaded('headInstructor')
                ? $this->headInstructor
                : Instructor::find($this->head_instructor_id);
            if ($head && $head->is_active) {
                return $head;
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('instructors', 'department')) {
            return Instructor::where('is_active', true)
                ->where('department', $this->name)
                ->orderByRaw("CASE WHEN department_role = 'head' THEN 0 ELSE 1 END")
                ->orderBy('order')
                ->first();
        }

        return null;
    }

    /**
     * Resolve the dedicated faculty team members under this Learning Department.
     */
    public function getResolvedTeamAttribute()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('instructors') || !\Illuminate\Support\Facades\Schema::hasColumn('instructors', 'department')) {
            return collect();
        }

        return Instructor::where('is_active', true)
            ->where('department', $this->name)
            ->orderByRaw("CASE WHEN department_role = 'head' THEN 0 ELSE 1 END")
            ->orderBy('order')
            ->get();
    }

    public static function getActiveCategories()
    {
        return static::active()->ordered()->get();
    }
}
