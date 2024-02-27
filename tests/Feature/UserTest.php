<?php

namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
    }



    /** @test */
    public function it_registers_an_individual_successfully()
    {
        // Storage::fake('storage/img');

        $response = $this->postJson('/api/user-individuals', [
            "first_name" => "Ari",
            "last_name" => "Hens",
            "phone" => "+1234567890",
            "user_type" => "individual",
            "email" => "ari.hens@example.com",
            "password" => "Password123",
            "full_address" => "1234 Main St, Anytown",
            "village_id" => 1234,
            "sub_district_id" => 5678,
            "city_district_id" => 91011,
            "province_id" => 1213,
            "postal_code" => 12345,
            // "image_file" => UploadedFile::fake()->image('user_image1.jpg') // Mock file upload.
        ]);


        $response->assertJson([
            'success' => true,
            'data' => [
                'email' => 'ari.hens@example.com',
            ],
            'message' => 'User registered successfully'
        ]);


        $response->assertStatus(200);

        // // Assert the file was stored...
        // $files = Storage::disk('storage/img')->files('images');
        // $this->assertNotEmpty($files);

        // Assert the database has the expected data...
        $this->assertDatabaseHas('user_individual', ['email' => 'ari.hens@example.com']);
        // Add similar assertions for Address, UserIndividual, and UserImage as necessary.

        // // Clean up: Delete the uploaded file after the test to ensure isolation between tests.
        // Storage::disk('public')->delete('images/' . time() . '.jpg');
    }

    /**
     * A basic feature test example.
     */
    public function testRegisterCompanySuccess()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
