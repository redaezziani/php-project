<?php
require_once __DIR__ . '/../../Acces_BD/connexion.php';

function seedProducts() {
    $conn = Connect();
    
    try {
        $products = [
            [
                'reference' => generateUniqueReference('VEG', 1),
                'designation' => 'الشمندر',
                'description' => 'الشمندر هو خضار جذري غني بالفيتامينات والمعادن، يُستخدم في السلطات والعصائر وله فوائد صحية عديدة.',
                'prix_unitaire' => 6.50,
                'quantite_stock' => 80,
                'image' => 'beetroot.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'شمندر طازج',
                'nutritional_info' => 'ألياف: 2.8g، فيتامين C: 4.9mg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 2),
                'designation' => 'البروكلي',
                'description' => 'البروكلي خضار غني بمضادات الأكسدة والفيتامينات، مثالي للطهي بالبخار أو إضافته إلى السلطات والأطباق الصحية.',
                'prix_unitaire' => 10.00,
                'quantite_stock' => 60,
                'image' => 'broccoli.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'بروكلي طازج',
                'nutritional_info' => 'فيتامين C: 89.2mg، ألياف: 2.6g',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 3),
                'designation' => 'الهليون',
                'description' => 'الهليون نبات مغذي ذو نكهة مميزة، يحتوي على الألياف والفيتامينات، ويستخدم في السلطات والمأكولات المشوية.',
                'prix_unitaire' => 15.00,
                'quantite_stock' => 50,
                'image' => 'asparagus.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'هليون طازج',
                'nutritional_info' => 'فيتامين K: 41.6µg، ألياف: 2.1g',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 4),
                'designation' => 'الجزر',
                'description' => 'الجزر خضار حلو ومقرمش، غني بالبيتا كاروتين الذي يتحول إلى فيتامين A، مفيد لصحة العين والجهاز المناعي.',
                'prix_unitaire' => 5.00,
                'quantite_stock' => 100,
                'image' => 'carrot.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'جزر طازج',
                'nutritional_info' => 'بيتا كاروتين: 8.3mg، ألياف: 2.8g',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 5),
                'designation' => 'الثوم',
                'description' => 'الثوم معروف بفوائده الصحية، حيث يمتلك خصائص مضادة للبكتيريا ويُستخدم في العديد من الأطباق لإضافة نكهة غنية.',
                'prix_unitaire' => 12.00,
                'quantite_stock' => 70,
                'image' => 'garlic.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'ثوم طازج',
                'nutritional_info' => 'أليسين: 5.6mg، فيتامين C: 31.2mg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 6),
                'designation' => 'خس آيسبرج',
                'description' => 'خس طازج ومقرمش، مثالي للسلطات والساندويتشات',
                'prix_unitaire' => 8.50,
                'quantite_stock' => 100,
                'image' => 'iceberg-lettuce.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'خس آيسبرج طازج',
                'nutritional_info' => 'فيتامين K: 17.4mg، فيتامين C: 3.9mg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 7),
                'designation' => 'كايل',
                'description' => 'ورق أخضر غني بالمغذيات، مثالي للسلطات والعصائر',
                'prix_unitaire' => 15.00,
                'quantite_stock' => 80,
                'image' => 'kale.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'كايل طازج',
                'nutritional_info' => 'فيتامين A: 8.90mg، فيتامين K: 704.8mg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 8),
                'designation' => 'كولرابي',
                'description' => 'خضار لذيذ من عائلة الملفوف، غني بالألياف',
                'prix_unitaire' => 10.00,
                'quantite_stock' => 60,
                'image' => 'kohlrabi.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'كولرابي طازج',
                'nutritional_info' => 'فيتامين C: 62mg، الألياف: 4.9g',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 9),
                'designation' => 'خس الحملان',
                'description' => 'خس صغير ولذيذ، مثالي للسلطات الخفيفة',
                'prix_unitaire' => 12.50,
                'quantite_stock' => 75,
                'image' => 'lambs-lettuce.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'خس الحملان طازج',
                'nutritional_info' => 'فيتامين C: 38.2mg، حديد: 2mg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 10),
                'designation' => 'كراث',
                'description' => 'خضار عطري من عائلة البصل، يستخدم في الحساء والطبخ',
                'prix_unitaire' => 9.00,
                'quantite_stock' => 90,
                'image' => 'leek.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'كراث طازج',
                'nutritional_info' => 'فيتامين K: 47μg، فيتامين A: 640IU',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 11),
                'designation' => 'خس',
                'description' => 'خس طازج متعدد الاستخدامات للسلطات والساندويتشات',
                'prix_unitaire' => 7.50,
                'quantite_stock' => 120,
                'image' => 'lettuce.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'خس طازج',
                'nutritional_info' => 'فيتامين A: 148μg، فيتامين K: 102.3μg',
                'allergens' => 'لا شيء'
            ],
            [
                'reference' => generateUniqueReference('VEG', 12),
                'designation' => 'بطاطس',
                'description' => 'بطاطس طازجة متعددة الاستخدامات للطهي',
                'prix_unitaire' => 6.00,
                'quantite_stock' => 200,
                'image' => 'potatoes.png',
                'promotion' => 0,
                'category_id' => 1,
                'ingredients' => 'بطاطس طازجة',
                'nutritional_info' => 'فيتامين C: 19.7mg، بوتاسيوم: 421mg',
                'allergens' => 'لا شيء'
            ],
        ];

        $stmt = $conn->prepare("
            INSERT INTO produits (
                reference, designation, description, prix_unitaire, 
                quantite_stock, image, promotion, category_id,
                ingredients, nutritional_info, allergens
            ) VALUES (
                :reference, :designation, :description, :prix_unitaire,
                :quantite_stock, :image, :promotion, :category_id,
                :ingredients, :nutritional_info, :allergens
            )
        ");

        foreach ($products as $product) {
            $stmt->execute($product);
        }

        echo "Products seeded successfully!\n";
    } catch (PDOException $e) {
        throw new Exception("Error seeding products: " . $e->getMessage());
    }
}

// Helper function to generate unique reference
function generateUniqueReference($prefix, $number) {
    return sprintf('%s%03d', $prefix, $number);
}

