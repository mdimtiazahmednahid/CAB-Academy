describe('Express Enroll & Apple Scrollytelling Visual Verification', () => {
    beforeEach(() => {
        cy.on('uncaught:exception', () => false);
    });

    it('Captures Course Page and Express Drawer Open State', () => {
        cy.viewport(1440, 900);
        cy.visit('/catalog');
        cy.get('article.group').first().find('h3 a').click();
        cy.contains('Price TK:').should('be.visible');
        cy.contains('Why Corporate Leaders Choose This Masterclass').should('be.visible');

        // Click Express Enroll button
        cy.contains('Express Enroll (30 Sec)').click();

        // Verify inside the Express Drawer
        cy.get('#express-enroll-drawer', { timeout: 6000 }).should('be.visible').within(() => {
            cy.contains('30-Second Express Enroll').should('be.visible');
            cy.contains('Student Information').should('be.visible');
            cy.get('input[placeholder="e.g. Tanvir Ahmed"]').should('be.visible');
            cy.get('input[placeholder="01917054664"]').should('be.visible');
        });

        cy.wait(600);
        cy.screenshot('express_enroll_viewport', { capture: 'viewport' });
        cy.get('#express-enroll-drawer').screenshot('express_enroll_drawer_element');
    });
});
