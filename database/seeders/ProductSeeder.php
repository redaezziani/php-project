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