describe('Community Feed & Blogs Feature Verification', () => {

    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
    });

    it('1. Verifies Student Layout Navigation: "Learn" replaced by "Feed"', () => {
        // Login as student
        cy.visit('/login');
        cy.get('input[name="email"]').type('student@cab-academy.com');
        cy.get('input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();

        // Redirected to dashboard
        cy.url().should('include', '/dashboard');

        // Check Desktop Sidebar / Mobile Nav: "Feed" exists, "Learn" is replaced
        cy.get('nav').first().within(() => {
            cy.contains('Feed').should('exist');
            cy.contains('Learn').should('not.exist');
        });

        // Visit Feed tab directly or via click
        cy.visit('/feed');
        cy.title().should('contain', 'Community Feed');
    });

    it('2. Verifies Facebook-Style Post Composer & Feed Stream', () => {
        cy.visit('/feed');

        // Verify Header Heading
        cy.get('h1').should('contain.text', 'Community Feed');

        // Verify Composer Elements
        cy.get('textarea[name="content"]').should('be.visible');
        cy.get('textarea[name="content"]').click(); // expands composer

        cy.get('select[name="category"]').should('be.visible');
        cy.contains('Publish Post').should('be.visible');

        // Verify Filter Tabs
        cy.contains('All Posts').should('be.visible');
        cy.contains('Official Insights & Blogs').should('be.visible');
        cy.contains('Peer Discussions').should('be.visible');

        // Verify Feed Posts Stream
        cy.get('article.bg-white').should('have.length.at.least', 2);
    });

    it('3. Creates a New Community Post and Verifies it in Feed', () => {
        // Login as student
        cy.visit('/login');
        cy.get('input[name="email"]').type('student@cab-academy.com');
        cy.get('input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();

        cy.visit('/feed');

        const testContent = 'Testing the new CAB Academy community feed! Anyone working on customs clearance at Chittagong Port? ' + Date.now();

        // Focus & Type in Composer
        cy.get('textarea[name="content"]').click().type(testContent);
        cy.get('select[name="category"]').select('Export – Import');
        cy.contains('Publish Post').click();

        // Verify presence in feed
        cy.url().should('include', '/feed');
        cy.contains(testContent).should('be.visible');
        cy.contains('Export – Import').should('exist');
    });

    it('4. Verifies Interactive Like and Comment Threads', () => {
        // Login as student
        cy.visit('/login');
        cy.get('input[name="email"]').type('student@cab-academy.com');
        cy.get('input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();

        cy.visit('/feed');

        // First post
        cy.get('article.bg-white').first().within(() => {
            // Like button
            cy.get('button').find('.fa-heart').should('exist');

            // Open comment thread
            cy.contains(/Comments?/).click();

            // Comment input is visible
            cy.get('input[name="content"]').should('be.visible');
            cy.get('input[name="content"]').type('Helpful discussion! Thanks for sharing.');
            cy.contains('Reply').click();
        });

        // Re-open comment thread to view submitted comment
        cy.get('article.bg-white').first().within(() => {
            cy.contains(/Comments?/).click();
            cy.contains('Helpful discussion! Thanks for sharing.').should('be.visible');
        });

        cy.screenshot('community_feed_mobile');
    });

    it('5. Verifies Public/Guest Access to Community Feed', () => {
        // Direct visit as guest
        cy.visit('/feed');
        cy.get('h1').should('contain.text', 'Community Feed');
        cy.get('textarea[name="content"]').should('be.visible');
        cy.get('article.bg-white').should('have.length.at.least', 2);

        cy.screenshot('community_feed_desktop');
    });

});
