<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileCheckInTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_concurrent_check_in_on_same_file()
    {
        // إعداد المستخدمين
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // إعداد الملف
        $file = File::factory()->create([
            'checked' => 0,
            'file_holder_id' => null,
        ]);

        // المستخدم الأول يقوم بمحاولة check-in
        $this->actingAs($user1);
        $response1 = $this->postJson("/file/checkIn/{$file->id}");

        // المستخدم الثاني يقوم بمحاولة check-in في نفس الوقت
        $this->actingAs($user2);
        $response2 = $this->postJson("/file/checkIn/{$file->id}");

        // تحديث حالة الملف بعد تنفيذ الوظائف
        $file->refresh();

        // التحقق من أن الملف قد تم حجبه من قبل أحد المستخدمين فقط
        $this->assertEquals(1, $file->checked);
        $this->assertNotNull($file->file_holder_id);

        // التحقق من ردود الـ API
        $response1->assertStatus(200);
        $response2->assertStatus(422); // المستخدم الثاني يجب أن يحصل على خطأ
    }
}
