<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeneficiariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_beneficiaries_page_displays_records(): void
    {
        Beneficiary::create([
            'name' => 'John Doe',
            'age' => 34,
            'employment_status' => 'Gov Employee',
            'student_status' => 'No',
            'pwd_status' => 'No',
            'eligibility_status' => 'Eligible',
        ]);

        $response = $this->get('/beneficiaries');

        $response->assertStatus(200);
        $response->assertSee('Beneficiaries');
        $response->assertSee('John Doe');
    }
}
