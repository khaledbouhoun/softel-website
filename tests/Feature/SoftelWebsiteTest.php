<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Product;
use App\Models\Service;
use Tests\TestCase;

class SoftelWebsiteTest extends TestCase
{
    public function test_root_redirects_to_default_locale(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/en');
    }

    public function test_english_homepage_renders_successfully(): void
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('Software Development &amp; Digital Solutions', false);
        $response->assertSee('images/softel_logo_black.svg');
        $response->assertSee('images/softel_logo_white.svg');
        $response->assertSee('Business &amp; Industrial Management Solutions', false);
        $response->assertSee('Education &amp; School Management Solutions', false);
        $response->assertSee('Mobile Applications');
        $response->assertSee('GESTCOM');
        $response->assertSee('info@softel.dz');
        $response->assertSee('0798 75 73 80');
        $response->assertSee('Mohammadia, Algiers');
    }

    public function test_french_homepage_renders_successfully(): void
    {
        $response = $this->get('/fr');

        $response->assertStatus(200);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('Développement Logiciel &amp; Solutions Numériques', false);
        $response->assertSee('Nos solutions');
        $response->assertSee('Solutions de Gestion d&#039;Entreprise &amp; Industrielle', false);
        $response->assertSee('Solutions Éducatives &amp; Gestion Scolaire', false);
        $response->assertSee('Applications Mobiles');
        $response->assertSee('images/softel_logo_black.svg');
        $response->assertSee('images/softel_logo_white.svg');
        $response->assertSee('info@softel.dz');
    }

    public function test_arabic_homepage_renders_with_rtl(): void
    {
        $response = $this->get('/ar');

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('تطوير البرمجيات والحلول الرقمية');
        $response->assertSee('حلولنا');
        $response->assertSee('حلول إدارة الأعمال والمؤسسات الصناعية');
        $response->assertSee('حلول التعليم والتسيير المدرسي');
        $response->assertSee('تطبيقات الهواتف الذكية');
        $response->assertSee('images/softel_logo_black.svg');
        $response->assertSee('images/softel_logo_white.svg');
        $response->assertSee('سوفتيل');
    }

    public function test_all_four_services_are_accessible_by_slug(): void
    {
        $services = ['development', 'consulting-support', 'training', 'after-sales-support'];

        foreach ($services as $slug) {
            $service = Service::where('slug', $slug)->first();
            $this->assertNotNull($service, "Service {$slug} should exist in database");

            $response = $this->get("/en/services/{$slug}");
            $response->assertStatus(200);
            $response->assertSee($service->getLocalized('title'));
        }
    }

    public function test_about_page_renders_successfully(): void
    {
        $response = $this->get('/en/about');

        $response->assertStatus(200);
        $response->assertSee('About SOFTEL');
        $response->assertSee('2010');
        $response->assertSee('SOFTEL SARL');
    }

    public function test_products_page_renders_and_gestcom_is_accessible(): void
    {
        $response = $this->get('/en/products');
        $response->assertStatus(200);
        $response->assertSee('GESTCOM');

        $productResponse = $this->get('/en/products/gestcom');
        $productResponse->assertStatus(200);
        $productResponse->assertSee('GESTCOM');
        $productResponse->assertSee('Purchasing');
    }

    public function test_tech_stack_renders_in_all_three_languages(): void
    {
        // 1. English
        $enResponse = $this->get('/en/products');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Purchasing');
        $enResponse->assertSee('Sales');
        $enResponse->assertSee('Inventory');
        $enResponse->assertSee('Treasury');

        $enDetail = $this->get('/en/products/gestcom');
        $enDetail->assertStatus(200);
        $enDetail->assertSee('Purchasing');
        $enDetail->assertSee('Sales');
        $enDetail->assertSee('Inventory');
        $enDetail->assertSee('Treasury');

        // 2. French
        $frResponse = $this->get('/fr/products');
        $frResponse->assertStatus(200);
        $frResponse->assertSee('Achats');
        $frResponse->assertSee('Ventes');
        $frResponse->assertSee('Gestion des stocks');
        $frResponse->assertSee('Trésorerie');

        $frDetail = $this->get('/fr/products/gestcom');
        $frDetail->assertStatus(200);
        $frDetail->assertSee('Achats');
        $frDetail->assertSee('Ventes');
        $frDetail->assertSee('Gestion des stocks');
        $frDetail->assertSee('Trésorerie');

        // 3. Arabic
        $arResponse = $this->get('/ar/products');
        $arResponse->assertStatus(200);
        $arResponse->assertSee('dir="rtl"', false);
        $arResponse->assertSee('المشتريات');
        $arResponse->assertSee('المبيعات');
        $arResponse->assertSee('إدارة المخزون');
        $arResponse->assertSee('الخزينة');

        $arDetail = $this->get('/ar/products/gestcom');
        $arDetail->assertStatus(200);
        $arDetail->assertSee('dir="rtl"', false);
        $arDetail->assertSee('المشتريات');
        $arDetail->assertSee('المبيعات');
        $arDetail->assertSee('إدارة المخزون');
        $arDetail->assertSee('الخزينة');

        // Ensure no "Array" or "null" or "undefined" is rendered
        $enResponse->assertDontSee('Array');
        $frResponse->assertDontSee('Array');
        $arResponse->assertDontSee('Array');
    }

    public function test_tech_stack_safely_falls_back_to_english_when_locale_missing(): void
    {
        $tempProduct = Product::create([
            'slug' => 'test-tech-fallback',
            'image' => 'images/gestcom.png',
            'title' => [
                'en' => 'Test Tech Fallback Solution',
                'fr' => 'Solution Test Fallback Tech',
                'ar' => 'حل تجريبي بديل',
            ],
            'summary' => [
                'en' => 'Test summary',
                'fr' => 'Résumé test',
                'ar' => 'ملخص تجريبي',
            ],
            'description' => [
                'en' => 'Test description',
                'fr' => 'Description test',
                'ar' => 'وصف تجريبي',
            ],
            'tech_stack' => [
                'en' => ['English Exclusive Tech'],
                // 'fr' and 'ar' deliberately omitted to test fallback
            ],
            'sort_order' => 100,
        ]);

        try {
            // French request should fall back to English
            $frResponse = $this->get('/fr/products');
            $frResponse->assertStatus(200);
            $frResponse->assertSee('English Exclusive Tech');

            // Arabic request should fall back to English
            $arResponse = $this->get('/ar/products');
            $arResponse->assertStatus(200);
            $arResponse->assertSee('English Exclusive Tech');

            // Detail pages fallback
            $frDetail = $this->get('/fr/products/test-tech-fallback');
            $frDetail->assertStatus(200);
            $frDetail->assertSee('English Exclusive Tech');

            $arDetail = $this->get('/ar/products/test-tech-fallback');
            $arDetail->assertStatus(200);
            $arDetail->assertSee('English Exclusive Tech');
        } finally {
            $tempProduct->delete();
        }
    }

    public function test_product_image_assets_exist_in_public_directory(): void
    {
        $expectedFiles = [
            'images/gestcom.png',
            'images/gestcom-importation.png',
            'images/gestcom-production.png',
            'images/gestcom-paie.png',
        ];

        foreach ($expectedFiles as $relPath) {
            $fullPath = public_path($relPath);
            $this->assertFileExists($fullPath, "Image asset {$relPath} must exist in public directory");
            $this->assertGreaterThan(0, filesize($fullPath), "Image asset {$relPath} must not be empty");
        }
    }

    public function test_products_page_renders_all_four_products_with_database_images(): void
    {
        $locales = ['en', 'fr', 'ar'];

        $products = Product::orderBy('sort_order')->get();
        $this->assertCount(4, $products, 'Expected exactly 4 commercial products in the database');

        foreach ($locales as $locale) {
            $response = $this->get("/{$locale}/products");
            $response->assertStatus(200);

            foreach ($products as $product) {
                // Must see product localized title
                $response->assertSee($product->getLocalized('title'));

                // Must see the exact dynamic database image URL generated via asset($product->image)
                $this->assertNotEmpty($product->image);
                $expectedImageUrl = asset($product->image);
                $response->assertSee($expectedImageUrl, false);
            }
        }
    }

    public function test_all_product_detail_pages_render_dynamic_database_images(): void
    {
        $products = Product::orderBy('sort_order')->get();

        foreach ($products as $product) {
            $response = $this->get("/en/products/{$product->slug}");
            $response->assertStatus(200);
            $response->assertSee($product->getLocalized('title'));

            // Detail page must use the database image URL
            $expectedImageUrl = asset($product->image);
            $response->assertSee($expectedImageUrl, false);
        }
    }

    public function test_homepage_products_section_renders_all_database_product_images(): void
    {
        $response = $this->get('/en');
        $response->assertStatus(200);

        $products = Product::orderBy('sort_order')->get();
        foreach ($products as $product) {
            $response->assertSee($product->getLocalized('title'));
            $response->assertSee(asset($product->image), false);
        }
    }

    public function test_product_fallback_logo_rendered_when_image_is_null(): void
    {
        $tempProduct = Product::create([
            'slug' => 'test-fallback-product',
            'image' => null,
            'title' => [
                'en' => 'Test Fallback Solution',
                'fr' => 'Solution Test Fallback',
                'ar' => 'حل تجريبي بديل',
            ],
            'summary' => [
                'en' => 'A test solution with no image.',
                'fr' => 'Une solution test sans image.',
                'ar' => 'حل تجريبي بدون صورة.',
            ],
            'description' => [
                'en' => 'Description for fallback product.',
                'fr' => 'Description pour produit fallback.',
                'ar' => 'وصف المنتج البديل.',
            ],
            'tech_stack' => ['Test Module'],
            'sort_order' => 99,
        ]);

        try {
            $response = $this->get('/en/products');
            $response->assertStatus(200);
            $response->assertSee('Test Fallback Solution');
            $response->assertSee('images/softel_logo_black.svg');

            $detailResponse = $this->get('/en/products/test-fallback-product');
            $detailResponse->assertStatus(200);
            $detailResponse->assertSee('images/softel_logo_black.svg');
        } finally {
            $tempProduct->delete();
        }
    }

    public function test_changing_database_image_path_immediately_updates_rendered_view(): void
    {
        $product = Product::where('slug', 'gestcom')->first();
        $originalImage = $product->image;

        try {
            $product->image = 'images/gestcom-custom-test.png';
            $product->save();

            $response = $this->get('/en/products');
            $response->assertStatus(200);
            $response->assertSee(asset('images/gestcom-custom-test.png'), false);

            $detailResponse = $this->get('/en/products/gestcom');
            $detailResponse->assertStatus(200);
            $detailResponse->assertSee(asset('images/gestcom-custom-test.png'), false);
        } finally {
            $product->image = $originalImage;
            $product->save();
        }
    }

    public function test_contact_page_renders_and_form_submits_lead_with_company(): void
    {
        $pageResponse = $this->get('/en/contact');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Mohammadia, Algiers');

        $postData = [
            'name' => 'Karim Test',
            'email' => 'karim@example.dz',
            'phone' => '0555 12 34 56',
            'company' => 'SARL Example Industrial',
            'message' => 'Interested in enterprise management software demo.',
        ];

        $submitResponse = $this->post('/en/contact', $postData);
        $submitResponse->assertSessionHas('success', 'Thank you. Your message has been received.');

        $this->assertDatabaseHas('leads', [
            'name' => 'Karim Test',
            'email' => 'karim@example.dz',
            'phone' => '0555 12 34 56',
            'company' => 'SARL Example Industrial',
            'locale' => 'en',
        ]);

        // Clean up test lead
        Lead::where('email', 'karim@example.dz')->delete();
    }

    public function test_localized_subpages_and_routes_render(): void
    {
        // French subpages
        $this->get('/fr/about')->assertStatus(200)->assertSee('À propos de SOFTEL');
        $this->get('/fr/services')->assertStatus(200)->assertSee('Nos Services');
        $this->get('/fr/contact')->assertStatus(200)->assertSee('Contactez-nous');

        // Arabic subpages
        $this->get('/ar/about')->assertStatus(200)->assertSee('عن شركة سوفتيل')->assertSee('dir="rtl"', false);
        $this->get('/ar/services')->assertStatus(200)->assertSee('خدماتنا')->assertSee('dir="rtl"', false);
        $this->get('/ar/contact')->assertStatus(200)->assertSee('تواصل معنا')->assertSee('dir="rtl"', false);
    }

    public function test_logo_assets_exist_in_public_directory(): void
    {
        $this->assertFileExists(public_path('images/softel_logo_black.svg'));
        $this->assertFileExists(public_path('images/softel_logo_white.svg'));
        $this->assertGreaterThan(0, filesize(public_path('images/softel_logo_black.svg')));
        $this->assertGreaterThan(0, filesize(public_path('images/softel_logo_white.svg')));
    }

    public function test_theme_system_and_toggle_are_rendered(): void
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
        // Anti-FOUC script and dark mode configuration
        $response->assertSee("localStorage.getItem('theme')", false);
        $response->assertSee("darkMode: 'class'", false);
        $response->assertSee("dark: '#0B0F14'", false);

        // Selection contrast styling
        $response->assertSee('*::selection', false);
        $response->assertSee('.bg-softel-blue::selection', false);

        // Theme switch buttons (desktop and mobile)
        $response->assertSee('theme-toggle-btn', false);
        $response->assertSee('data-logo-light', false);
        $response->assertSee('data-logo-dark', false);
    }

    public function test_product_detail_page_renders_product_specific_whatsapp_cta_and_phone_link(): void
    {
        $locales = ['en', 'fr', 'ar'];
        $productSlugs = ['gestcom', 'gestcom-importation', 'gestcom-production', 'gestcom-paie'];

        $expectedButtonLabels = [
            'en' => 'Contact us about this product',
            'fr' => 'Contactez-nous à propos de ce produit',
            'ar' => 'تواصل معنا حول هذا المنتج',
        ];

        foreach ($productSlugs as $slug) {
            $product = Product::where('slug', $slug)->first();
            $this->assertNotNull($product, "Product {$slug} must exist in the database");

            foreach ($locales as $locale) {
                $response = $this->get("/{$locale}/products/{$slug}");
                $response->assertStatus(200);

                $title = $product->getLocalized('title', $locale);
                $this->assertNotEmpty($title);
                $response->assertSee($title);

                // Verify primary contact button exists and is localized
                $response->assertSee($expectedButtonLabels[$locale]);

                // Verify phone fallback link
                $response->assertSee('href="tel:+213558925355"', false);
                $response->assertSee('0558 92 53 55');

                // Verify WhatsApp URL
                $expectedUrl = $product->getWhatsAppUrl($locale);
                $this->assertStringStartsWith('https://wa.me/213558925355?text=', $expectedUrl);
                $response->assertSee(e($expectedUrl), false);

                // Extract query string parameter and decode
                parse_str(parse_url($expectedUrl, PHP_URL_QUERY), $queryParams);
                $this->assertArrayHasKey('text', $queryParams);
                $decodedMessage = $queryParams['text'];

                // Verify decoded message contains product title
                $this->assertStringContainsString($title, $decodedMessage);

                // Verify language-specific content
                if ($locale === 'en') {
                    $this->assertStringContainsString('Hello SOFTEL,', $decodedMessage);
                    $this->assertStringContainsString("I am interested in your product \"{$title}\".", $decodedMessage);
                    $this->assertStringContainsString('I would like to get more information about this solution.', $decodedMessage);
                    $this->assertStringContainsString('Thank you.', $decodedMessage);
                } elseif ($locale === 'fr') {
                    $this->assertStringContainsString('Bonjour SOFTEL,', $decodedMessage);
                    $this->assertStringContainsString("Je suis intéressé(e) par votre solution « {$title} ».", $decodedMessage);
                    $this->assertStringContainsString("Je souhaiterais obtenir plus d'informations sur cette solution.", $decodedMessage);
                    $this->assertStringContainsString('Merci.', $decodedMessage);
                } elseif ($locale === 'ar') {
                    $response->assertSee('dir="rtl"', false);
                    $this->assertStringContainsString('مرحباً SOFTEL،', $decodedMessage);
                    $this->assertStringContainsString("أنا مهتم بمنتج « {$title}».", $decodedMessage);
                    $this->assertStringContainsString('أرغب في الحصول على مزيد من المعلومات حول هذا الحل.', $decodedMessage);
                    $this->assertStringContainsString('شكراً لكم.', $decodedMessage);
                }
            }
        }
    }

    public function test_whatsapp_message_falls_back_to_english_for_unknown_locale(): void
    {
        $product = Product::where('slug', 'gestcom')->first();
        $this->assertNotNull($product);

        $message = $product->getWhatsAppMessage('de');
        $this->assertStringContainsString('Hello SOFTEL,', $message);
        $this->assertStringContainsString($product->getLocalized('title', 'en'), $message);
    }
}
