<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'author_name' => 'Sofiane Hamidi',
                'author_role' => 'Directeur Général, SARL Atlas Boissons',
                'quote' => [
                    'fr' => 'Depuis le déploiement de GESTCOM et du module Production, notre gestion des stocks et la traçabilité de nos fabrications sont devenues fluides et sans erreur. Un outil indispensable et parfaitement adapté au marché algérien.',
                    'ar' => 'منذ اعتماد برنامج جيستكوم ووحدة الإنتاج، أصبحت إدارة المخزون وتتبع عمليات التصنيع لدينا سلسة وخالية من الأخطاء. إنه حل تقني موثوق ومصمم بدقة ليلائم واقع السوق الجزائري.',
                    'en' => 'Since deploying GESTCOM and the Production module, our inventory management and production traceability have become seamless and error-free. An indispensable tool tailored for Algerian business needs.',
                ],
                'sort_order' => 1,
            ],
            [
                'author_name' => 'Dr. Leila Zerrouki',
                'author_role' => 'Directrice Générale, EURL MediTech Distribution',
                'quote' => [
                    'fr' => 'Le module GESTCOM Importation nous a permis de maîtriser avec précision le calcul de nos coûts de revient en devises et nos frais de douane. L\'équipe d\'accompagnement de SOFTEL est d\'un professionnalisme exemplaire.',
                    'ar' => 'مكنتنا وحدة جيستكوم للاستيراد من ضبط تكلفة الشراء بدقة متناهية وإدارة المصاريف الجمركية والعملات الأجنبية بسهولة. كما أن فريق المرافقة التقنية لشركة سوفتيل يتميز باحترافية عالية.',
                    'en' => 'GESTCOM Importation allowed us to accurately manage multi-currency landed costs and customs duties. SOFTEL\'s implementation and support team has been exemplary in professionalism.',
                ],
                'sort_order' => 2,
            ],
            [
                'author_name' => 'Samir Khelifi',
                'author_role' => 'Directeur Financier, SARL Poly-Emballage',
                'quote' => [
                    'fr' => 'La fiabilité des états financiers, la rapidité de la facturation et le suivi rigoureux des règlements clients ont transformé notre gestion quotidienne. Le support technique répond toujours présent.',
                    'ar' => 'إن دقة التقارير المالية وسرعة الفوترة والمتابعة الدقيقة لمدفوعات العملاء أحدثت فارقاً حقيقياً في عملنا اليومي. فريق الدعم الفني متواجد دائماً لتقديم المساعدة.',
                    'en' => 'Financial reporting accuracy, rapid invoicing, and rigorous receivables follow-up transformed our daily operations. Their technical support is always available and responsive.',
                ],
                'sort_order' => 3,
            ],
            [
                'author_name' => 'Yacine Benali',
                'author_role' => 'Gérant, Comptoir Moderne d\'Électricité',
                'quote' => [
                    'fr' => 'La simplicité de prise en main et la rapidité aux caisses ont immédiatement séduit nos équipes. Nos inventaires de fin d\'année se font désormais en un temps record.',
                    'ar' => 'سهولة الاستخدام وسرعة التعامل في نقاط البيع نالت إعجاب فريقنا منذ اليوم الأول. أصبحت عمليات الجرد السنوية تتم في وقت قياسي وبأعلى درجات الدقة.',
                    'en' => 'Ease of use and fast point-of-sale checkout immediately won over our teams. Our year-end inventory audits are now completed in record time.',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::updateOrCreate(
                ['author_name' => $data['author_name']],
                $data
            );
        }
    }
}
