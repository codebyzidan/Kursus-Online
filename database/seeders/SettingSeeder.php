<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Kursus Online', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Belajar keterampilan digital dari praktisi', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'halo@kursus.test', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+62 21 1234 5678', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'Jl. Merdeka No. 10, Jakarta Pusat', 'group' => 'contact'],
            ['key' => 'platform_fee_percent', 'value' => 30, 'group' => 'finance'],
            ['key' => 'refund_policy', 'value' => 'Pengembalian dana dapat diajukan maksimal 7 hari setelah pembelian selama progres belajar kurang dari 30%.', 'group' => 'finance'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']],
            );
        }
    }
}
