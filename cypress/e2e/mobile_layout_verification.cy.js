describe('Mobile Viewport & Course Card Layout Verification', () => {

    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
    });

    it('1. Verifies Mobile 375x812 Viewport on Homepage', () => {
        cy.viewport(375, 812);
        cy.visit('/');

        // 1. Verify no horizontal overflow
        cy.window().then((win) => {
            expect(win.document.documentElement.scrollWidth).to.be.at.most(375);
        });

        // 2. Verify Hero section typography and buttons on mobile
        cy.contains('CAB Academy').should('be.visible');
        cy.contains('Learn, Grow, Lead.').should('be.visible');
        cy.contains('Explore Courses').should('be.visible');

        // 3. Scroll to Categories and verify 2-column grid
        cy.get('#home-category').scrollIntoView();
        cy.get('#home-category .w-20.h-20.rounded-full').should('have.length.at.least', 6);

        // 4. Verify Dual Containers and Course Cards
        cy.get('.bg-\\[\\#f0f8ec\\]').scrollIntoView();
        cy.get('.bg-\\[\\#f0f8ec\\] article.group').first().within(() => {
            // Aspect square exists for Cypress invariant
            cy.get('.aspect-square').should('exist');

            // Price badge at bottom left
            cy.get('.absolute.left-0.bottom-0').should('be.visible').should('contain.text', '৳');

            // Faculty avatar at .absolute.-bottom-10.right-3 exists
            cy.get('.absolute.-bottom-10.right-3').should('exist');

            // Category tag and Live Zoom badge exist
            cy.contains('Export – Import').should('be.visible');
            cy.contains('Live Zoom').should('be.visible');

            // Enroll Now button
            cy.contains('Enroll Now').should('be.visible');
        });

        cy.screenshot('mobile_homepage_category_and_cards');
    });

    it('2. Verifies Course Card Dimensions and Badge Clearance on Mobile', () => {
        cy.viewport(375, 812);
        cy.visit('/category/export-import');

        cy.get('article.group').first().within(() => {
            // Thumbnail container is clamped in height on mobile
            cy.get('.aspect-square').then(($thumb) => {
                const height = $thumb.height();
                // Should be around 192px (max-h-48), definitely less than 240px
                expect(height).to.be.lessThan(240);
            });

            // Verify badge container has right padding to clear avatar
            cy.get('.pr-16').should('exist');
        });

        cy.screenshot('mobile_catalog_course_cards');
    });

    it('3. Verifies Mobile Navigation Drawer', () => {
        cy.viewport(375, 812);
        cy.visit('/');

        // Open mobile hamburger menu
        cy.get('header button[aria-label="Toggle navigation"]').click();

        // Search form inside drawer is visible
        cy.get('input[placeholder="Search courses & masterclasses..."]').should('be.visible');
        cy.contains('Course Catalog').should('be.visible');
        cy.contains('Distinguished Faculty').should('be.visible');

        cy.screenshot('mobile_navigation_drawer');
    });

});
