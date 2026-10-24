describe('Missing Features & Institutional Trust Suite Verification', () => {

  it('1. Verifies Contact Page, Campus Details, Hotlines and Form', () => {
    cy.visit('/contact');
    cy.contains('Connect With CAB Academy').should('be.visible');
    cy.contains('Mirpur-Pallabi, Dhaka, Bangladesh, 1216').should('be.visible');
    cy.contains('01917-054664').should('be.visible');
    cy.contains('commercialacademy868@gmail.com').should('be.visible');
    cy.contains('Mirpur-Pallabi Campus Location').should('be.visible');

    cy.screenshot('contact_page_desktop');
  });

  it('2. Verifies About Us Page & Institutional Metrics', () => {
    cy.visit('/about');
    cy.title().should('contain', 'About CAB Academy');
    cy.contains(/Trade|Mastery|Empowering|Institute/).should('be.visible');
    cy.contains('5,000+').should('be.visible');
    cy.contains('Core Philosophy').should('be.visible');

    cy.screenshot('about_page_desktop');
  });

  it('3. Verifies Student Wishlist Page and Empty State', () => {
    cy.visit('/wishlist');
    cy.contains('Saved Programs & Courses').should('be.visible');
    cy.contains('Your Wishlist is Empty').should('be.visible');
    cy.contains('Browse Course Catalog').should('be.visible');

    cy.screenshot('wishlist_page_empty');
  });

  it('4. Verifies Legal Suite (Privacy, Terms, Refund)', () => {
    cy.visit('/privacy-policy');
    cy.get('h1').contains('Privacy Policy').should('be.visible');
    cy.contains('Mirpur-Pallabi, Dhaka, Bangladesh, 1216').should('be.visible');

    cy.visit('/terms-of-service');
    cy.get('h1').contains('Terms of Service').should('be.visible');
    cy.contains('CAB Academy').should('be.visible');

    cy.visit('/refund-policy');
    cy.get('h1').contains('Refund & Cancellation Policy').should('be.visible');
    cy.contains('commercialacademy868@gmail.com').should('be.visible');
  });

  it('5. Verifies Public Certificate Verification Engine', () => {
    cy.visit('/verify-certificate/CAB-2026-DEMO0001');
    cy.contains('Certificate Verification Engine').should('be.visible');
    cy.contains('Official Credential Registry').should('be.visible');
    cy.contains('CAB-2026-DEMO0001').should('be.visible');

    cy.screenshot('certificate_verify_page');
  });

  it('6. Verifies Course Show Page has Executive FAQ Accordion and Wishlist Button', () => {
    cy.visit('/catalog');
    cy.get('article.group').first().find('h3 a').click();
    cy.contains('Frequently Asked Questions').should('be.visible');
    cy.contains('How do I receive my verified digital certificate?').should('be.visible');
    cy.contains('Save to Wishlist').should('be.visible');

    cy.screenshot('course_faq_and_wishlist');
  });
});
