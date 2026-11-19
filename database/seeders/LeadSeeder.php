<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $gestcom = Product::where('slug', 'gestcom')->first();
        $production = Product::where('slug', 'gestcom-production')->first();
        $importation = Product::where('slug', 'gestcom-importation')->first();

        $leads = [
            [
                'name' => 'Karim Brahimi',
                'company' => 'SARL Agro Mitidja',
                'email' => 'k.brahimi@agromitidja.dz',
                'phone' => '0550112233',
                'product_id' => $production?->id ?? $gestcom?->id,
                'source' => 'contact_page',
                'locale' => 'fr',
                'message' => 'Bonjour, nous sommes une unité de transformation agroalimentaire à Blida. Nous souhaitons recevoir une proposition commerciale pour 5 postes réseau avec gestion de la traçabilité des lots et ordres de fabrication.',
            ],
            [
                'name' => 'أمينة بلقاسم',
                'company' => 'مؤسسة الوفاق للأدوات المكتبية',
                'email' => 'contact@elwifak-dz.com',
                'phone' => '0661445566',
                'product_id' => $gestcom?->id,
                'source' => 'product_page',
                'locale' => 'ar',
                'message' => 'السلام عليكم، نرغب في معرفة المزيد عن إمكانيات برنامج جيستكوم في تسيير نقاط البيع المتعددة وطابعات الباركود، وهل تدعمون الربط بين فرعين في الجزائر ووهران؟ شكراً.',
            ],
            [
                'name' => 'Tarek Meziani',
                'company' => 'EURL Maghreb Logistics',
                'email' => 'tarek.meziani@maghreblogistics.dz',
                'phone' => '0770889900',
                'product_id' => $importation?->id ?? $gestcom?->id,
                'source' => 'contact_page',
                'locale' => 'en',
                'message' => 'Hello, we are an import-export and freight forwarding company based in Algiers. We would like to schedule an online demo of your importation module next Tuesday if possible.',
            ],
            [
                'name' => 'Mourad Chaouch',
                'company' => 'ETS Chaouch Quincaillerie Industrielle',
                'email' => 'chaouch.quincaillerie@gmail.com',
                'phone' => '0542334455',
                'product_id' => $gestcom?->id,
                'source' => 'product_page',
                'locale' => 'fr',
                'message' => 'Demande de contact commercial pour renouveler notre logiciel de caisse et gestion de stock actuel.',
            ],
        ];

        foreach ($leads as $data) {
            Lead::firstOrCreate(
                ['email' => $data['email'], 'company' => $data['company']],
                $data
            );
        }
    }
}
