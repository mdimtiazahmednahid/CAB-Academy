<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityFeedSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@cab-academy.com')->first();
        $instructor = User::where('email', 'faculty@cab-academy.com')->first() ?: User::where('role', 'instructor')->first();
        $student = User::where('email', 'student@cab-academy.com')->first();

        // 0. Ensure default BlogPosts exist (Strictly Institutional Authors - No Personal / Real Names)
        BlogPost::query()->forceDelete();
        CommunityComment::query()->forceDelete();
        CommunityPost::query()->forceDelete();
            BlogPost::create([
                'title' => 'Complete Roadmap to Letter of Credit (L/C) & Customs Clearance Under Regulatory Directives',
                'slug' => 'complete-roadmap-to-lc-customs-clearance',
                'category' => 'Export – Import',
                'excerpt' => 'Practical blueprint for commercial managers and entrepreneurs navigating shipping documents, Bill of Entry, and HS Code harmonization.',
                'content' => "In international commercial trade, an irrevocable Letter of Credit (L/C) remains the bedrock of cross-border payment security. However, minor discrepancies in commercial invoices, packing lists, bills of lading, and certificate of origin documents routinely trigger costly demurrage and customs delays at port terminals.\n\n### Essential Checkpoints for Flawless Customs Clearance\n\n1. **HS Code Precision**: Ensure harmonization between the HS Code specified in the Proforma Invoice (PI) and the customs tariff schedule.\n2. **UCP 600 Alignment**: Verify that credit terms strictly observe ICC Uniform Customs and Practice standards.\n3. **Bonded Warehouse Discrepancies**: Maintain accurate utilization declarations (UD) to prevent audit notices and export duty liabilities.",
                'featured_image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Faculty Chair – Trade & Customs',
                'author_title' => 'Principal Trade Finance Fellow',
                'author_avatar' => null,
                'read_time' => '6 min read',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ]);

            BlogPost::create([
                'title' => 'Strategic Sourcing & Warehouse Safety Stock Modeling for Industrial Enterprises',
                'slug' => 'strategic-sourcing-warehouse-safety-stock-modeling',
                'category' => 'Supply Chain Management',
                'excerpt' => 'How to optimize vendor lead times, safety stock calculations, and warehouse storage costs in fluctuating supply markets.',
                'content' => "Optimizing inventory is a critical lever for corporate liquidity. When supply chain disruptions ripple through global shipping lanes, industrial manufacturers face stock-outs or excessive carrying costs.\n\n### Key Strategies for Lean Inventory\n\n1. **Safety Stock Formula**: Calculate safety stock based on standard deviation of lead time and demand variability.\n2. **Supplier Scorecards**: Audit vendor on-time-in-full (OTIF) metrics quarterly to negotiate preferential container rates.\n3. **Warehouse Space Utilization**: Implement ABC classification to place fast-moving raw materials near dispatch docks.",
                'featured_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Faculty Chair – Supply Chain',
                'author_title' => 'Executive Fellow in SCM Strategy',
                'author_avatar' => null,
                'read_time' => '5 min read',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ]);

            BlogPost::create([
                'title' => 'Corporate VAT Rebates & Withholding Tax Deductions: Finance Act Best Practices',
                'slug' => 'corporate-vat-rebates-withholding-tax-deductions',
                'category' => 'Tax & Legal Compliance',
                'excerpt' => 'Step-by-step guidance on input tax credits, tax invoice issuance, and withholding tax certificates under statutory tax regulations.',
                'content' => "Under the Value Added Tax and Supplementary Duty regulations, maximizing allowable input tax rebates directly impacts the corporate bottom line. Proper documentation is paramount during audits.\n\n### Critical Compliance Procedures\n\n1. **Tax Invoice Issuance**: Must be generated prior to dispatch of goods; retroactive invoices are disqualified for rebates.\n2. **Banking Channel Records**: Ensure transactions over statutory limits are routed through formal banking channels.\n3. **Withholding Certificates**: Upload withholding certificates within the mandated statutory window.",
                'featured_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Faculty Chair – Tax & VAT',
                'author_title' => 'Executive Fellow in Corporate Tax',
                'author_avatar' => null,
                'read_time' => '7 min read',
                'is_published' => true,
                'published_at' => now()->subDays(8),
            ]);

        // 1. Sync official BlogPosts into featured CommunityPosts
        $blogPosts = BlogPost::all();
        foreach ($blogPosts as $blog) {
            $post = CommunityPost::firstOrCreate(
                ['blog_post_id' => $blog->id],
                [
                    'user_id' => $instructor?->id ?: $admin?->id,
                    'author_name' => $blog->author_name ?: 'CAB Academy Faculty',
                    'author_title' => $blog->author_title ?: 'Faculty Fellow',
                    'author_avatar' => null,
                    'category' => $blog->category ?: 'Official Insights',
                    'title' => $blog->title,
                    'content' => $blog->excerpt . "\n\n" . substr(strip_tags($blog->content), 0, 300) . "...",
                    'image_url' => $blog->featured_image,
                    'is_pinned' => true,
                    'likes_count' => rand(15, 45),
                    'comments_count' => rand(2, 6),
                ]
            );

            // Add sample comment
            if ($post->comments()->count() === 0) {
                CommunityComment::create([
                    'community_post_id' => $post->id,
                    'user_id' => $student?->id,
                    'author_name' => $student?->name ?: 'Executive Scholar',
                    'author_avatar' => null,
                    'content' => 'Invaluable practical guidance for working executives. Thank you for publishing this research paper!',
                ]);
            }
        }

        // 2. Add organic community discussions (No Personal / Real Names)
        $discussions = [
            [
                'author_name' => 'Faculty Chair – Trade & Customs',
                'author_title' => 'Principal Trade Finance Fellow',
                'author_avatar' => null,
                'category' => 'Export – Import',
                'title' => 'Updated HS Code Mandatory Guidelines for L/C Applications',
                'content' => "Attention import-export professionals & commercial executives:\n\nRegulatory authorities have reiterated the mandatory 8-digit HS Code compliance on commercial invoices for all import L/Cs processed through the automated single-window. Ensure your shipping documents and packing lists align precisely before submission to avoid port demurrage holds.",
                'image_url' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'likes_count' => 38,
                'comments_count' => 3,
                'user_id' => $instructor?->id,
                'comments' => [
                    [
                        'author_name' => 'Executive Scholar',
                        'content' => 'Does this also apply to back-to-back L/C imports for 100% export-oriented manufacturing units?',
                    ],
                    [
                        'author_name' => 'Faculty Chair – Trade & Customs',
                        'content' => 'Confirmed, back-to-back L/Cs must carry matching 8-digit HS codes verified against the master export entitlement.',
                    ],
                    [
                        'author_name' => 'Corporate Fellow',
                        'content' => 'Crucial reminder, we experienced a 2-day customs delay last month over a discrepancy in chapter 85 codes.',
                    ],
                ],
            ],
            [
                'author_name' => 'Executive Scholar',
                'author_title' => 'Cohort Member • Trade Finance',
                'author_avatar' => null,
                'category' => 'General Discussion',
                'title' => 'Excited to complete my first Bill of Entry simulation!',
                'content' => "Just wrapped up practical module 3 of the L/C and Customs documentation program on CAB Academy! The step-by-step breakdown of customs duty, regulatory duty, and tax calculation clarified so much confusion. Networking with fellow batchmates in Dhaka has been great. Who else is preparing for the upcoming assessment?",
                'image_url' => null,
                'likes_count' => 24,
                'comments_count' => 2,
                'user_id' => $student?->id,
                'comments' => [
                    [
                        'author_name' => 'Corporate Fellow',
                        'content' => 'Great progress! Working on the same module right now.',
                    ],
                    [
                        'author_name' => 'Faculty Chair – Trade & Customs',
                        'content' => 'Excellent work, your duty calculation methodology was spot-on.',
                    ],
                ],
            ],
            [
                'author_name' => 'Faculty Chair – Tax & VAT',
                'author_title' => 'Executive Fellow in Corporate Tax',
                'author_avatar' => null,
                'category' => 'Tax, VAT & Compliance',
                'title' => 'Corporate VAT Rebate Checklist: Month-End Guidelines',
                'content' => "Essential reminder for corporate accountants:\n\nTo claim 100% input tax rebate under statutory rules, make sure:\n1. Tax invoice is issued at the exact time of delivery\n2. Banking channel payment proofs are maintained for invoices above threshold amounts\n3. Withholding certificates are uploaded to the tax portal within statutory deadlines.",
                'image_url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=800&q=80',
                'likes_count' => 47,
                'comments_count' => 1,
                'user_id' => $admin?->id,
                'comments' => [
                    [
                        'author_name' => 'Tax Practitioner',
                        'content' => 'Very clear checklist, thank you. The banking channel threshold rule is often missed.',
                    ],
                ],
            ],
        ];

        foreach ($discussions as $item) {
            $comments = $item['comments'];
            unset($item['comments']);

            $existing = CommunityPost::where('title', $item['title'])->first();
            if (!$existing) {
                $post = CommunityPost::create($item);
                foreach ($comments as $c) {
                    CommunityComment::create([
                        'community_post_id' => $post->id,
                        'author_name' => $c['author_name'],
                        'content' => $c['content'],
                    ]);
                }
            }
        }
    }
}
