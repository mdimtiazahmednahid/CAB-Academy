describe('Superadmin Landing Page Customizer E2E Verification', () => {

    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
    });

    it('1. Logs in as Superadmin and verifies Landing Page Customizer layout and section tabs', () => {
        // Login as admin
        cy.visit('/login');
        cy.get('input[type="email"], input[name="email"]').type('admin@cab-academy.com');
        cy.get('input[type="password"], input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();

        // Navigate to frontend CMS
        cy.visit('/admin/frontend');
        cy.url().should('include', '/admin/frontend');

        // Header verification
        cy.contains('Landing Page Customizer').should('be.visible');
        cy.contains('8 Dynamic Sections').should('be.visible');

        // Verify all 8 section tabs in the directory
        cy.contains('1. Executive Hero').should('be.visible');
        cy.contains('2. Valuable Clients').should('be.visible');
        cy.contains('3. Sticky Categories').should('be.visible');
        cy.contains('4. Course Showcase').should('be.visible');
        cy.contains('5. Knowledge Hub').should('be.visible');
        cy.contains('6. Counseling Hotline').should('be.visible');
        cy.contains('7. Features (6 Cards)').should('be.visible');
        cy.contains('8. Career & Jobs').should('be.visible');

        // Interactive Tab Switching:
        // Tab 2: Valuable Clients
        cy.contains('2. Valuable Clients').click();
        cy.contains('Clients Section Visibility').should('be.visible');
        cy.get('textarea[name="landing_clients_data"]').should('be.visible');

        // Tab 3: Sticky Categories
        cy.contains('3. Sticky Categories').click();
        cy.contains('Sticky Smart Categories Bar Visibility').should('be.visible');
        cy.get('textarea[name="landing_categories_data"]').should('be.visible');

        // Tab 4: Course Showcase
        cy.contains('4. Course Showcase').click();
        cy.contains('Left Container: Certificate Course Track').should('be.visible');
        cy.contains('Right Container: Day Long Course Track').should('be.visible');

        // Tab 7: Features
        cy.contains('7. Features (6 Cards)').click();
        cy.contains('Features Section Visibility').should('be.visible');
        cy.contains('Feature Card 1').should('be.visible');
        cy.contains('Feature Card 6').should('be.visible');

        // Tab 8: Career & Jobs
        cy.contains('8. Career & Jobs').click();
        cy.contains('Career Opportunities Section Visibility').should('be.visible');

        cy.screenshot('admin_landing_customizer');
    });

    it('2. Dynamically updates Hero Tagline from Superadmin and verifies on Homepage', () => {
        // Login as admin
        cy.visit('/login');
        cy.get('input[name="email"]').type('admin@cab-academy.com');
        cy.get('input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();

        cy.visit('/admin/frontend');
        cy.contains('1. Executive Hero').click();

        // Update Tagline
        const customTagline = 'Learn, Grow, Lead & Transform.';
        cy.get('input[name="landing_hero_tagline"]').clear().type(customTagline);

        // Submit form
        cy.contains('Save All Sections').click();

        // Verify success flash
        cy.contains('Landing page sections updated successfully!').should('be.visible');

        // Visit public homepage and verify dynamic update
        cy.visit('/');
        cy.contains(customTagline).should('be.visible');

        // Clean up & restore standard tagline
        cy.visit('/admin/frontend');
        cy.get('input[name="landing_hero_tagline"]').clear().type('Learn, Grow, Lead.');
        cy.contains('Save All Sections').click();
        cy.contains('Landing page sections updated successfully!').should('be.visible');

        cy.visit('/');
        cy.contains('Learn, Grow, Lead.').should('be.visible');
    });

});
