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
    $user1 = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();

    $file = \App\Models\File::factory()->create([
        'checked' => 0,
    ]);

    // المستخدم الأول يقوم بمحاولة check-in
    $this->actingAs($user1);
    $response1 = $this->postJson('/api/files/check-in', ['file_id' => $file->id]);

    // المستخدم الثاني يقوم بمحاولة check-in في نفس الوقت
    $this->actingAs($user2);
    $response2 = $this->postJson('/api/files/check-in', ['file_id' => $file->id]);

    // تأكيد أن حالة الملف أصبحت محجوزة من قبل المستخدم الأول فقط
    $file->refresh();
    $this->assertEquals(1, $file->checked);
    $this->assertEquals($user1->id, $file->file_holder_id);
}

}
