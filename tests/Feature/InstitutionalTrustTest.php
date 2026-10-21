<?php

namespace Tests\Feature;

use App\Models\ContactInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionalTrustTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_successfully()
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Mirpur-Pallabi');
        $response->assertSee('01917-054664');
        $response->assertSee('commercialacademy868@gmail.com');
    }

    public function test_can_submit_contact_inquiry()
    {
        $payload = [
            'name' => 'Md. Imtiaz Ahmed',
            'email' => 'imtiaz@example.com',
            'phone' => '01917-054664',
            'inquiry_type' => 'corporate',
            'organization' => 'Apex Footwear Ltd.',
            'subject' => 'Corporate Training for Commercial Division',
            'message' => 'We are interested in a 3-day customized training on L/C and Customs documentation for 15 executives.',
        ];

        $response = $this->post('/contact', $payload);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'imtiaz@example.com',
            'inquiry_type' => 'corporate',
            'status' => 'pending',
        ]);
    }

    public function test_about_page_renders_successfully()
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('CAB Academy');
        $response->assertSee('Mirpur-Pallabi');
    }

    public function test_legal_suite_pages_render_successfully()
    {
        $this->get('/privacy-policy')->assertStatus(200)->assertSee('Privacy Policy');
        $this->get('/terms-of-service')->assertStatus(200)->assertSee('Terms of Service');
        $this->get('/refund-policy')->assertStatus(200)->assertSee('Refund');
    }
}
