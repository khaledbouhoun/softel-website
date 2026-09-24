<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'slug' => 'development',
                'icon' => 'code-bracket',
                'title' => [
                    'en' => 'Software Development',
                    'fr' => 'Développement Logiciel',
                    'ar' => 'تطوير البرمجيات',
                ],
                'summary' => [
                    'en' => 'Software Built Around Your Business',
                    'fr' => 'Des logiciels conçus autour de votre entreprise',
                    'ar' => 'برمجيات مصممة خصيصاً لتناسب طبيعة عملك',
                ],
                'description' => [
                    'en' => 'We develop management, business, industrial and educational software solutions designed around the real needs of our clients. Our development process is driven by continuous feedback and real-world requirements, allowing our solutions to evolve with our clients.',
                    'fr' => 'Nous développons des solutions logicielles de gestion, d\'entreprise, industrielles et éducatives conçues autour des besoins réels de nos clients. Notre processus de développement s\'appuie sur les retours continus et les exigences du terrain, permettant à nos solutions d\'évoluer avec nos clients.',
                    'ar' => 'نطور حلولاً برمجية للإدارة والأعمال والقطاعين الصناعي والتعليمي مصممة وفق الاحتياجات الفعلية لعملائنا. وتعتمد منهجيتنا التطويرية على التغذية الراجعة المستمرة والمتطلبات الميدانية الواقعية، مما يتيح لحلولنا التطور المستمر مع نمو عملائنا.',
                ],
                'sort_order' => 1,
            ],
            [
                'slug' => 'consulting-support',
                'icon' => 'chat-bubble-left-right',
                'title' => [
                    'en' => 'Consulting & Support',
                    'fr' => 'Conseil & Accompagnement',
                    'ar' => 'الاستشارات والدعم',
                ],
                'summary' => [
                    'en' => 'Practical Expertise. Reliable Guidance.',
                    'fr' => 'Expertise pratique. Conseils fiables.',
                    'ar' => 'خبرة عملية. توجيه موثوق.',
                ],
                'description' => [
                    'en' => 'We help organizations identify their digital needs and implement effective IT solutions for their daily operations. SOFTEL combines technical expertise with practical experience to help clients make the most of their digital tools.',
                    'fr' => 'Nous aidons les organisations à identifier leurs besoins numériques et à mettre en œuvre des solutions informatiques efficaces pour leurs opérations quotidiennes. SOFTEL combine expertise technique et expérience pratique pour aider ses clients à tirer le meilleur parti de leurs outils numériques.',
                    'ar' => 'نساعد المؤسسات على تحديد احتياجاتها الرقمية وتطبيق حلول تكنولوجيا معلومات فعالة لعملياتها اليومية. تجمع سوفتيل بين الكفاءة التقنية والخبرة الميدانية العملية لتمكين العملاء من الاستفادة القصوى من أدواتهم الرقمية.',
                ],
                'sort_order' => 2,
            ],
            [
                'slug' => 'training',
                'icon' => 'academic-cap',
                'title' => [
                    'en' => 'Training',
                    'fr' => 'Formation',
                    'ar' => 'التدريب والتأهيل',
                ],
                'summary' => [
                    'en' => 'Helping Your Team Get the Most From Our Software',
                    'fr' => 'Aider votre équipe à tirer le meilleur parti de nos logiciels',
                    'ar' => 'تمكين فريقك من تحقيق أقصى استفادة من برامجنا',
                ],
                'description' => [
                    'en' => 'We provide professional training on our software solutions, helping users understand the tools, workflows and features they need to work efficiently.',
                    'fr' => 'Nous dispensons des formations professionnelles sur nos solutions logicielles, aidant les utilisateurs à maîtriser les outils, les flux de travail et les fonctionnalités nécessaires pour travailler efficacement.',
                    'ar' => 'نقدم دورات تدريبية مهنية متخصصة على حلولنا البرمجية، لمساعدة المستخدمين على إتقان الأدوات ومسارات العمل والميزات التي يحتاجونها للعمل بأعلى درجات الكفاءة.',
                ],
                'sort_order' => 3,
            ],
            [
                'slug' => 'after-sales-support',
                'icon' => 'shield-check',
                'title' => [
                    'en' => 'After-Sales Support',
                    'fr' => 'Support Après-Vente',
                    'ar' => 'خدمة ما بعد البيع',
                ],
                'summary' => [
                    'en' => 'Support That Continues After Deployment',
                    'fr' => 'Un support qui continue après le déploiement',
                    'ar' => 'دعم فني مستمر لما بعد مرحلة النشر والتشغيل',
                ],
                'description' => [
                    'en' => 'Our relationship with clients does not end when software is delivered. We provide ongoing technical assistance to diagnose and resolve software issues and help keep systems stable, functional and reliable.',
                    'fr' => 'Notre relation avec les clients ne s\'arrête pas à la livraison du logiciel. Nous assurons une assistance technique continue pour diagnostiquer et résoudre les anomalies logicielles et préserver la stabilité, la performance et la fiabilité de vos systèmes.',
                    'ar' => 'لا تنتهي علاقتنا مع العملاء بمجرد تسليم البرنامج؛ بل نلتزم بتقديم مساعدة تقنية مستمرة لتشخيص ومعالجة أي استفسارات أو مشكلات برمجية، والحفاظ على استقرار الأنظمة وكفاءتها وموثوقيتها الدائمة.',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData
            );
        }
    }
}
