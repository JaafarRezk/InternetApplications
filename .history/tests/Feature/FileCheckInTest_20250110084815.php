<?php

namespace Tests\Feature;

use App\Jobs\CheckInFileJob;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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

        // تفعيل الطابور الوظيفي (Queue)
        Queue::fake();

        // إرسال وظائف التحقق
        CheckInFileJob::dispatch($file->id, $user1->id);
        CheckInFileJob::dispatch($file->id, $user2->id);

        // تشغيل وظائف الطابور
        Queue::assertPushed(CheckInFileJob::class, 2);

        // تحديث حالة الملف بعد تنفيذ الوظائف
        $file->refresh();

        // التحقق من أن الملف قد تم حجبه من قبل أحد المستخدمين فقط
        $this->assertEquals(1, $file->checked);
        $this->assertNotNull($file->file_holder_id);
    }
}
