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
        $instructor = User::where('email', 'delwar@ipdpbd.com')->first();
        $student = User::where('email', 'student@cab-academy.com')->first();

        // 0. Ensure default BlogPosts exist
        if (BlogPost::count() === 0) {
            BlogPost::create([
                'title' => 'Complete Roadmap to Letter of Credit (L/C) & Customs Clearance Under Bangladesh Bank Directives',
                'slug' => 'complete-roadmap-to-lc-customs-clearance',
                'category' => 'Export – Import',
                'excerpt' => 'Practical blueprint for commercial managers and entrepreneurs navigating shipping documents, Bill of Entry, and HS Code harmonization.',
                'content' => "In international commercial trade, an irrevocable Letter of Credit (L/C) remains the bedrock of cross-border payment security. However, minor discrepancies in commercial invoices, packing lists, bills of lading, and certificate of origin documents routinely trigger costly demurrage and customs delays at Chittagong Port and Dhaka Airport Cargo terminals.\n\n### Essential Checkpoints for Flawless Customs Clearance\n\n1. **HS Code Precision**: Ensure harmonization between the HS Code specified in the Proforma Invoice (PI) and the National Board of Revenue (NBR) customs tariff schedule.\n2. **UCP 600 Alignment**: Verify that credit terms strictly observe ICC Uniform Customs and Practice standards.\n3. **Bonded Warehouse Discrepancies**: Maintain accurate utilization declarations (UD) to prevent audit notices and export duty liabilities.",
                'featured_image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Md. Delwar Hossain Bhuiyan',
                'author_title' => 'Principal Trade Finance Fellow',
                'author_avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Md.-Delwar-Hossain-Bhuiyan.jpg',
                'read_time' => '6 min read',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ]);

            BlogPost::create([
                'title' => 'Strategic Sourcing & Warehouse Safety Stock Modeling for Industrial Enterprises',
                'slug' => 'strategic-sourcing-warehouse-safety-stock-modeling',
                'category' => 'Supply Chain Management',
                'excerpt' => 'How to optimize vendor lead times, safety stock calculations, and warehouse storage costs in fluctuating supply markets.',
                'content' => "Optimizing inventory is a critical lever for maximizing corporate liquidity. When supply chain disruptions ripple through global shipping lanes, industrial manufacturers face stock-outs or excessive carrying costs.\n\n### Key Strategies for Lean Inventory\n\n1. **Safety Stock Formula**: Calculate safety stock based on standard deviation of lead time and demand variability.\n2. **Supplier Scorecards**: Audit vendor on-time-in-full (OTIF) metrics quarterly to negotiate preferential container rates.\n3. **Warehouse Space Utilization**: Implement ABC classification to place fast-moving raw materials near dispatch docks.",
                'featured_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Engr. Tariqul Islam',
                'author_title' => 'Supply Chain & Operations Fellow',
                'author_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                'read_time' => '5 min read',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ]);

            BlogPost::create([
                'title' => 'Corporate VAT Rebates & Withholding Tax Deductions: 2026 Finance Act Best Practices',
                'slug' => 'corporate-vat-rebates-withholding-tax-deductions',
                'category' => 'Tax & Legal Compliance',
                'excerpt' => 'Step-by-step guidance on input tax credits, Mushak 6.3 issuance, and withholding tax certificates under current Bangladesh tax regulations.',
                'content' => "Under the Value Added Tax and Supplementary Duty Act, maximizing allowable input tax rebates directly impacts the corporate bottom line. Proper documentation is paramount to survive NBR VAT audits.\n\n### Critical Compliance Procedures\n\n1. **Mushak 6.3 Issuance**: Must be generated prior to dispatch of goods; retroactive invoices are disqualified for rebates.\n2. **Bank Transfer Records**: Ensure transactions over statutory limits are routed through formal banking or approved MFS channels.\n3. **VDS Certificates**: Upload Mushak 6.6 withholding certificates within the mandated 15-day statutory window.",
                'featured_image' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Asif Ahmed, FCS',
                'author_title' => 'Distinguished Faculty & Tax Counsel',
                'author_avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Asif-Ahmmed.jpg',
                'read_time' => '7 min read',
                'is_published' => true,
                'published_at' => now()->subDays(8),
            ]);
        }

        // 1. Sync official BlogPosts into featured CommunityPosts
        $blogPosts = BlogPost::all();
        foreach ($blogPosts as $blog) {
            $post = CommunityPost::firstOrCreate(
                ['blog_post_id' => $blog->id],
                [
                    'user_id' => $instructor?->id ?: $admin?->id,
                    'author_name' => $blog->author_name ?: 'CAB Academy Faculty',
                    'author_title' => $blog->author_title ?: 'Distinguished Faculty',
                    'author_avatar' => $blog->author_avatar,
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
                    'author_name' => $student?->name ?: 'Alice Student',
                    'author_avatar' => $student?->profile_picture,
                    'content' => 'Invaluable practical guidance for working executives. Thank you for publishing this research paper!',
                ]);
            }
        }

        // 2. Add organic community discussions
        $discussions = [
            [
                'author_name' => 'Md. Delwar Hossain Bhuiyan',
                'author_title' => 'Principal Trade Finance Fellow',
                'author_avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Md.-Delwar-Hossain-Bhuiyan.jpg',
                'category' => 'Export – Import',
                'title' => 'Updated HS Code Mandatory Guidelines for L/C Applications',
                'content' => "Attention import-export professionals & commercial executives:\n\nBangladesh Bank has reiterated the mandatory 8-digit HS Code compliance on commercial invoices for all import L/Cs processed through the automated customs clearance single-window. Ensure your shipping documents and C&F packing lists align precisely before submission to avoid port demurrage holds at Chittagong or Benapole.",
                'image_url' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'likes_count' => 38,
                'comments_count' => 3,
                'user_id' => $instructor?->id,
                'comments' => [
                    [
                        'author_name' => 'Alice Student',
                        'content' => 'Sir, does this also apply to back-to-back L/C imports for 100% export-oriented RMG units?',
                    ],
                    [
                        'author_name' => 'Md. Delwar Hossain Bhuiyan',
                        'content' => 'Yes Alice, back-to-back L/Cs must carry matching 8-digit HS codes verified against the master export entitlement.',
                    ],
                    [
                        'author_name' => 'Tanvir Ahmed',
                        'content' => 'Crucial reminder, we experienced a 2-day customs delay last month over a discrepancy in chapter 85 codes.',
                    ],
                ],
            ],
            [
                'author_name' => 'Alice Student',
                'author_title' => 'Student Member • Supply Chain Cohort',
                'author_avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
                'category' => 'General Discussion',
                'title' => 'Excited to complete my first Bill of Entry simulation!',
                'content' => "Just wrapped up practical module 3 of the L/C and Customs documentation program on CAB Academy! The step-by-step breakdown of customs duty, regulatory duty (RD), and AIT calculation clarified so much confusion. Networking with fellow batchmates in Dhaka has been great. Who else is preparing for the upcoming exam?",
                'image_url' => null,
                'likes_count' => 24,
                'comments_count' => 2,
                'user_id' => $student?->id,
                'comments' => [
                    [
                        'author_name' => 'Kamrul Hasan',
                        'content' => 'Great job Alice! Working on the same module right now.',
                    ],
                    [
                        'author_name' => 'Md. Delwar Hossain Bhuiyan',
                        'content' => 'Excellent work Alice, your duty calculation methodology was spot-on.',
                    ],
                ],
            ],
            [
                'author_name' => 'Asif Ahmed, FCS',
                'author_title' => 'Distinguished Faculty & Tax Counsel',
                'author_avatar' => 'https://ipdpbd.com/wp-content/uploads/2025/06/Asif-Ahmmed.jpg',
                'category' => 'Tax, VAT & Compliance',
                'title' => 'Corporate VAT Rebate Checklist: Month-End Guidelines',
                'content' => "Essential reminder for corporate accountants:\n\nTo claim 100% input tax rebate under the VAT Act, make sure:\n1. Mushak 6.3 tax invoice is issued at the exact time of delivery\n2. Banking channel payment proofs are maintained for invoices above BDT 100,000\n3. Withholding certificates (Mushak 6.6) are uploaded to the NBR portal within 15 days.",
                'image_url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=800&q=80',
                'likes_count' => 47,
                'comments_count' => 1,
                'user_id' => $admin?->id,
                'comments' => [
                    [
                        'author_name' => 'Shakil Mahmud',
                        'content' => 'Very clear checklist Asif sir. The banking channel threshold rule is often missed.',
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
