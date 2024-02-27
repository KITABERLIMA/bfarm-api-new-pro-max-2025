<?php

namespace Tests\Feature;


use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterIndividualSuccess()
    {
        $this->post('/api/user-individuals', [
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
            "image_file" => "user_image1.jpg"
        ])->assertStatus(200);
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
