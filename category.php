<?php
session_start();
require_once 'config.php';
$conn = getDBConnection();

$categorySlug = $_GET['cat'] ?? 'logistics';

// Get category info
$catQuery = $conn->prepare("SELECT * FROM categories WHERE slug = ?");
$catQuery->execute([$categorySlug]);
$category = $catQuery->fetch(PDO::FETCH_ASSOC);

if(!$category) {
    echo "<script>alert('Category not found'); window.history.back();</script>";
    exit;
}

// Get articles for this category
$articlesQuery = $conn->prepare("
    SELECT a.*, c.name as category_name, c.color as category_color 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE c.slug = ? 
    ORDER BY a.created_at DESC
");
$articlesQuery->execute([$categorySlug]);
$articles = $articlesQuery->fetchAll(PDO::FETCH_ASSOC);

// Define thumbnail images
$articleThumbnails = [
    1 => 'dhl_asia_europe.jpg',
    2 => 'supply_chain.jpg',
    3 => 'electric_fleet.jpg',
    4 => 'ai_customs.jpg',
    5 => 'global_trade.jpg',
    6 => 'automated_facility.jpg',
    7 => 'carbon_neutral.jpg',
    8 => 'ecommerce.jpg',
    9 => 'drone_delivery.jpg',
    10 => 'air_cargo.jpg'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category['name']; ?> - DHL News Network</title>
    <style>
        /* Reuse styles from index.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .dhl-header {
            background: linear-gradient(135deg, #FFCC00 0%, #D40511 100%);
            color: white;
            padding: 15px 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .category-header {
            background: linear-gradient(135deg, <?php echo $category['color']; ?> 0%, #000 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }
        
        .category-title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .article-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-top: 4px solid <?php echo $category['color']; ?>;
        }
        
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .article-image {
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .thumbnail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .article-card:hover .thumbnail-image {
            transform: scale(1.05);
        }
        
        .article-content {
            padding: 25px;
        }
        
        .article-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .article-excerpt {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        
        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .read-more-btn {
            background: <?php echo $category['color']; ?>;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .read-more-btn:hover {
            background: #000;
            transform: scale(1.05);
        }
        
        .back-to-home {
            display: inline-block;
            margin-bottom: 30px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .back-to-home:hover {
            color: #D40511;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .category-title {
                font-size: 2.2rem;
            }
            
            .articles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="dhl-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px;">
            <a href="index.php" class="dhl-logo" style="font-size: 2rem; color: white; text-decoration: none; font-weight: 900;">
                DHL<span style="color: #FFCC00;">NEWS</span>
            </a>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="index.php" style="color: white; text-decoration: none; font-weight: 600;">Home</a>
                <a href="category.php?cat=aviation" style="color: <?php echo $categorySlug == 'aviation' ? '#FFCC00' : 'white'; ?>; text-decoration: none; font-weight: 600;">Aviation</a>
                <a href="category.php?cat=business" style="color: <?php echo $categorySlug == 'business' ? '#FFCC00' : 'white'; ?>; text-decoration: none; font-weight: 600;">Business</a>
                <a href="category.php?cat=customs" style="color: <?php echo $categorySlug == 'customs' ? '#FFCC00' : 'white'; ?>; text-decoration: none; font-weight: 600;">Customs</a>
                <a href="category.php?cat=economy" style="color: <?php echo $categorySlug == 'economy' ? '#FFCC00' : 'white'; ?>; text-decoration: none; font-weight: 600;">Economy</a>
                <a href="category.php?cat=logistics" style="color: <?php echo $categorySlug == 'logistics' ? '#FFCC00' : 'white'; ?>; text-decoration: none; font-weight: 600;">Logistics</a>
            </div>
        </div>
    </header>

    <!-- Category Header -->
    <div class="category-header">
        <div class="container">
            <h1 class="category-title"><?php echo $category['name']; ?></h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                Latest news and updates from the <?php echo $category['name']; ?> section
            </p>
        </div>
    </div>

    <!-- Articles -->
    <div class="container">
        <a href="index.php" class="back-to-home">← Back to Home</a>
        
        <div class="articles-grid">
            <?php foreach($articles as $article): ?>
                <div class="article-card">
                    <div class="article-image">
                        <?php 
                        $thumbnail = isset($articleThumbnails[$article['id']]) ? $articleThumbnails[$article['id']] : 'dhl_default.jpg';
                        ?>
                        <img src="thumbnails/<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="thumbnail-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjRDAwNTExIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJIZWx2ZXRpY2EiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiNGRkNDMDAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ESEx8TmV3czwvdGV4dD48L3N2Zz4='">
                    </div>
                    <div class="article-content">
                        <h3 class="article-title">
                            <a href="article.php?slug=<?php echo $article['slug']; ?>" style="color: inherit; text-decoration: none;">
                                <?php echo $article['title']; ?>
                            </a>
                        </h3>
                        <p class="article-excerpt"><?php echo $article['excerpt']; ?></p>
                        <div class="article-meta">
                            <span>By <?php echo $article['author']; ?></span>
                            <span><?php echo date('M d, Y', strtotime($article['created_at'])); ?></span>
                        </div>
                        <a href="article.php?slug=<?php echo $article['slug']; ?>" class="read-more-btn" style="margin-top: 15px; display: inline-block;">
                            Read Full Story
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #000; color: white; padding: 40px 0; margin-top: 60px; text-align: center;">
        <div class="container">
            <p style="color: #bbb;">© 2024 DHL News Network - <?php echo $category['name']; ?> Section</p>
            <p style="color: #999; margin-top: 10px; font-size: 0.9rem;">
                <a href="index.php" style="color: #FFCC00; text-decoration: none;">Home</a> | 
                <a href="category.php?cat=business" style="color: #bbb; text-decoration: none;">Business</a> | 
                <a href="category.php?cat=logistics" style="color: #bbb; text-decoration: none;">Logistics</a> | 
                <a href="category.php?cat=economy" style="color: #bbb; text-decoration: none;">Economy</a>
            </p>
        </div>
    </footer>
</body>
</html>
