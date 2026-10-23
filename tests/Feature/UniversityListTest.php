<?php

use App\StorageLayer\UniversityStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * The university list puts "Άλλο" (other) last. The forms render every
 * entry, so the list must never hold null.
 */
class UniversityListTest extends TestCase {
    use RefreshDatabase;

    public function test_other_comes_last() {
        $names = (new UniversityStorage)->getAllUniversities()->pluck('name')->all();

        $this->assertSame('Άλλο', end($names));
        $this->assertCount(1, array_keys($names, 'Άλλο'));
    }

    public function test_the_list_holds_no_null_when_other_is_missing() {
        DB::table('university')->where('name', 'Άλλο')->delete();

        $storage = new UniversityStorage;

        $this->assertNotContains(null, $storage->getAllUniversities()->all());
        $this->assertNotContains(null, $storage->getAllUniversitiesIncludingOtherUniversities()->all());
    }
}
