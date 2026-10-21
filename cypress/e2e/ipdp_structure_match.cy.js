describe('IPDP Course Structure & Layout Parity Verification', () => {

    beforeEach(() => {
        // Prevent uncaught exception fails from external scripts if any
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
    });

    it('1. Verifies /export-import URL alias redirects to /category/export-import', () => {
        cy.visit('/export-import');
        cy.url().should('include', '/category/export-import');
        cy.title().should('contain', 'Export – Import');
    });

    it('2. Verifies IPDP Category Hero Banner & Breadcrumbs', () => {
        cy.visit('/category/export-import');

        // Green banner with centered white heading
        cy.get('section.bg-\\[\\#90c642\\]').should('be.visible').within(() => {
            cy.get('h1').should('contain.text', 'Export – Import');
            cy.contains('Home').should('be.visible');
            cy.contains('Course').should('be.visible');
        });
        cy.screenshot('ipdp_export_import');
    });

    it('3. Verifies Signature Course Card Structure (Exact IPDP Placement)', () => {
        cy.visit('/category/export-import');

        // Course card exists
        cy.get('article.group').should('have.length.at.least', 1).first().within(() => {
            // 1:1 Aspect ratio thumbnail container
            cy.get('.aspect-square').should('exist');

            // Exact IPDP Price placement: Bottom-Left on Image with black translucent overlay
            cy.get('.absolute.left-0.bottom-0')
                .should('be.visible')
                .should('contain.text', '৳');

            // Exact IPDP Teacher Avatar placement: Bottom-Right overlapping thumbnail
            cy.get('.absolute.-bottom-10.right-3')
                .should('exist')
                .find('img, div')
                .should('have.class', 'rounded-full');

            // Category tag
            cy.contains('Export – Import').should('be.visible');

            // Course title
            cy.get('h3').should('be.visible');

            // Bottom meta bar: Clock icon + date + Enroll Now button
            cy.get('.far.fa-clock').should('be.visible');
            cy.contains('Enroll Now')
                .should('be.visible')
                .should('have.class', 'btn-ipdp-primary');
        });
    });

    it('4. Verifies "Course Categories" Section (6 Columns, Circular Icons)', () => {
        cy.visit('/category/export-import');

        // Section heading
        cy.contains('Course Categories').should('be.visible');

        // 6-column grid container
        cy.get('.grid.grid-cols-2.sm\\:grid-cols-3.lg\\:grid-cols-6').should('be.visible').within(() => {
            // Should contain core categories
            cy.contains('Accounting').should('be.visible');
            cy.contains('Supply Chain Management').should('be.visible');
            cy.contains('Export – Import').should('be.visible');

            // Each category item has 80x80 circular image
            cy.get('.w-20.h-20.rounded-full').should('have.length.at.least', 4);
        });
    });

    it('5. Verifies Single Course Page (Price TK, Accordion Modules, 150px Teacher)', () => {
        cy.visit('/category/export-import');

        // Click first course card
        cy.get('article.group').first().find('h3 a').click();

        // Verify URL
        cy.url().should('include', '/courses/');

        // Verify "Price TK: ..."
        cy.contains('Price TK:').should('be.visible');

        // Verify IPDP Accordion Module with #90c642 header
        cy.get('button.bg-\\[\\#90c642\\]').first().should('be.visible').click();

        // Verify 150px circular instructor image
        cy.get('.w-\\[150px\\].h-\\[150px\\].rounded-full').should('exist');

        // Verify Enroll button
        cy.get('button[type="submit"]').contains('Enroll Now').should('be.visible');
    });

    it('6. Verifies Mobile Responsive Sticky Action Bar', () => {
        cy.viewport('iphone-x');
        cy.visit('/category/export-import');
        cy.get('article.group').first().find('h3 a').click();

        // Sticky bottom action bar on mobile
        cy.get('.lg\\:hidden.fixed.bottom-3')
            .should('be.visible')
            .within(() => {
                cy.contains('Enroll Now').should('be.visible');
            });
    });

    it('7. Verifies Root Homepage (/) Matches Dual-Column IPDP Layout, Blog Hub, and NeuraSoft Attribution', () => {
        cy.visit('/');
        
        // Browser Title
        cy.title().should('contain', 'CAB Academy');

        // CAB Academy Hero & Tagline
        cy.contains('CAB Academy').should('be.visible');
        cy.contains('Learn, Grow, Lead.').should('be.visible');

        // Corporate Clients
        cy.contains('Our Valuable Clients').should('be.visible');

        // Course Categories with smart navigation pills
        cy.contains('Course Categories').should('be.visible');
        cy.get('#home-category .category-nav-pill').should('have.length.at.least', 6);

        // Centered "View All Courses" bracket header
        cy.contains('View All Courses').should('be.visible');

        // Dual-Column Side-by-Side Containers
        // Left: Certificate Course (Light Green Box)
        cy.get('.bg-\\[\\#f0f8ec\\]').should('be.visible').within(() => {
            cy.get('h2').should('contain.text', 'Certificate Course');
            cy.contains('View All Certificate Courses').should('be.visible');
            cy.get('article.group').should('have.length.at.least', 1);
            // Verify batch badge
            cy.contains('BATCH').should('be.visible');
        });

        // Right: Day Long Course (Light Peach Box)
        cy.get('.bg-\\[\\#fef4f0\\]').should('be.visible').within(() => {
            cy.get('h2').should('contain.text', 'Day Long Course');
            cy.contains('View All Day Long Courses').should('be.visible');
            cy.get('article.group').should('have.length.at.least', 1);
        });

        // Knowledge Hub & Blog Section
        cy.contains('Knowledge Hub & Publications').should('be.visible');
        cy.contains('View All Publications').should('be.visible');
        cy.get('article.group').should('have.length.at.least', 4);

        // Need to Learn More CTA
        cy.contains('Need To Learn More ?').should('be.visible');

        // Features (Rich terminology: Distinguished Faculty & Fellows)
        cy.contains('Our Features').should('be.visible');
        cy.contains('Distinguished Faculty & Fellows').should('be.visible');

        // Official CAB Academy Circular Logo in Header
        cy.get('header img[alt="CAB Academy"]').should('be.visible');

        // Navigation uses rich words: Faculty, Blog (no generic "Teacher")
        cy.get('nav').within(() => {
            cy.contains('Faculty').should('be.visible');
            cy.contains('Blog').should('be.visible');
            cy.contains('Teacher').should('not.exist');
        });

        // Floating WhatsApp Button & Scroll to top button
        cy.get('a[aria-label="Chat on WhatsApp"]').should('be.visible');
        cy.get('button[aria-label="Scroll to Top"]').should('be.visible');

        // Footer: Verify Developed by NeuraSoft with hyperlink https://neurasoft.top
        cy.get('footer').should('contain.text', 'CAB Academy');
        cy.get('footer a[href="https://neurasoft.top"]')
            .should('be.visible')
            .should('contain.text', 'NeuraSoft');
        cy.contains('Sure It Solution').should('not.exist');

        cy.screenshot('cab_homepage');
    });

    it('8. Verifies Blog Hub & Faculty Directory Pages', () => {
        // Blog Index Page
        cy.visit('/blog');
        cy.title().should('contain', 'Insights & Knowledge Hub');
        cy.contains('Insights & Knowledge Hub').should('be.visible');
        cy.contains('All Insights').should('be.visible');
        cy.get('article.group').should('have.length.at.least', 1);

        // Faculty Directory Page
        cy.visit('/faculty');
        cy.title().should('contain', 'Distinguished Faculty & Industry Fellows');
        cy.contains('Distinguished Faculty & Fellows').should('be.visible');
        cy.contains('Md. Delwar Hossain Bhuiyan').should('be.visible');
        cy.contains('Asif Ahmed, FCS').should('be.visible');
    });

});
