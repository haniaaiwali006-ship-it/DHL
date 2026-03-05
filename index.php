<?php
session_start();
require_once 'config.php';
$conn = getDBConnection();

// Fetch featured articles
$featuredQuery = $conn->prepare("
    SELECT a.*, c.name as category_name, c.color as category_color 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.is_featured = 1 
    ORDER BY a.created_at DESC 
    LIMIT 4
");
$featuredQuery->execute();
$featuredArticles = $featuredQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch breaking news
$breakingQuery = $conn->prepare("
    SELECT a.*, c.name as category_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.is_breaking = 1 
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$breakingQuery->execute();
$breakingNews = $breakingQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch trending news
$trendingQuery = $conn->prepare("
    SELECT a.*, c.name as category_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.is_trending = 1 
    ORDER BY a.views DESC 
    LIMIT 6
");
$trendingQuery->execute();
$trendingNews = $trendingQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories in specific order
$categoriesQuery = $conn->prepare("SELECT * FROM categories ORDER BY FIELD(slug, 'home', 'aviation', 'business', 'customs', 'economy', 'logistics', 'maritime', 'supply-chain', 'sustainability', 'technology', 'trade')");
$categoriesQuery->execute();
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Define thumbnail images for articles
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
    <title>DHL News Network - Global Logistics & Business News</title>
    <style>
        /* Premium DHL Style - Yellow & Red Theme */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* DHL Header */
        .dhl-header {
            background: linear-gradient(135deg, #FFCC00 0%, #D40511 100%);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dhl-logo {
            font-size: 2.8rem;
            font-weight: 900;
            letter-spacing: -1px;
            text-transform: uppercase;
            color: white;
            text-decoration: none;
            font-family: 'Arial Black', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dhl-logo span {
            color: #FFCC00;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .dhl-tagline {
            font-size: 0.9rem;
            color: white;
            opacity: 0.9;
            margin-top: 5px;
            font-weight: 500;
        }
        
        /* Navigation */
        .nav-container {
            background: rgba(0,0,0,0.9);
            padding: 0;
        }
        
        .nav-scroll {
            display: flex;
            overflow-x: auto;
            padding: 0 20px;
            max-width: 1400px;
            margin: 0 auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .nav-scroll::-webkit-scrollbar {
            display: none;
        }
        
        .nav-menu {
            display: flex;
            gap: 0;
            list-style: none;
            padding: 0;
            margin: 0;
            min-width: max-content;
        }
        
        .nav-menu li {
            flex-shrink: 0;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 15px 20px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            border-right: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
        }
        
        .nav-menu a:hover {
            background: #D40511;
            color: #FFCC00;
        }
        
        .nav-menu a.active {
            background: #FFCC00;
            color: #D40511;
        }
        
        /* Search Bar */
        .search-container {
            position: relative;
            width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 20px;
            padding-left: 45px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 25px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: white;
            background: rgba(255,255,255,0.2);
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
        }
        
        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 1.2rem;
        }
        
        /* Breaking News Ticker */
        .breaking-news-ticker {
            background: linear-gradient(90deg, #D40511, #FFCC00);
            color: white;
            padding: 12px 0;
            overflow: hidden;
            position: relative;
        }
        
        .ticker-label {
            background: #000;
            color: #FFCC00;
            padding: 4px 20px;
            font-weight: bold;
            text-transform: uppercase;
            position: absolute;
            left: 0;
            z-index: 2;
            font-size: 0.9rem;
        }
        
        .ticker-content {
            display: flex;
            animation: ticker 30s linear infinite;
            padding-left: 120px;
        }
        
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        
        .ticker-item {
            white-space: nowrap;
            margin-right: 40px;
            font-weight: 500;
        }
        
        .ticker-item a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .ticker-item a:hover {
            color: #FFCC00;
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Featured Section */
        .featured-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 2.2rem;
            color: #D40511;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #FFCC00;
            position: relative;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100px;
            height: 3px;
            background: #D40511;
        }
        
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .featured-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            border-top: 4px solid;
        }
        
        .featured-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .featured-image {
            width: 100%;
            height: 220px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(45deg, #D40511 0%, #FFCC00 100%);
        }
        
        .thumbnail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .featured-card:hover .thumbnail-image {
            transform: scale(1.05);
        }
        
        .category-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(212, 5, 17, 0.95);
            color: white;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 2;
        }
        
        .featured-content {
            padding: 25px;
        }
        
        .featured-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 15px;
            line-height: 1.4;
            transition: color 0.3s;
        }
        
        .featured-card:hover .featured-title {
            color: #D40511;
        }
        
        .featured-excerpt {
            color: #555;
            font-size: 1rem;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        
        .meta-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .read-more {
            color: #D40511;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .read-more:hover {
            color: #FFCC00;
            gap: 12px;
        }
        
        /* Categories Section */
        .categories-section {
            margin: 50px 0;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .category-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-top: 4px solid;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .category-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #000;
        }
        
        .category-count {
            color: #777;
            font-size: 0.9rem;
        }
        
        /* Trending Section */
        .trending-section {
            margin: 50px 0;
        }
        
        .trending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .trending-card {
            display: flex;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            border-left: 4px solid #FFCC00;
        }
        
        .trending-card:hover {
            transform: translateX(5px);
        }
        
        .trending-number {
            font-size: 2rem;
            font-weight: 900;
            color: #D40511;
            min-width: 40px;
            opacity: 0.8;
        }
        
        /* Logistics Updates */
        .logistics-updates {
            background: linear-gradient(135deg, #FFCC00 0%, #D40511 100%);
            padding: 40px;
            border-radius: 15px;
            margin: 50px 0;
            color: white;
        }
        
        .logistics-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .logistics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .logistics-item {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        /* Footer */
        .dhl-footer {
            background: #000;
            color: white;
            padding: 50px 0 20px;
            margin-top: 60px;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-section h3 {
            color: #FFCC00;
            font-size: 1.3rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-links a {
            color: #bbb;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #FFCC00;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #333;
            color: #999;
            font-size: 0.9rem;
        }
        
        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .nav-menu a {
                padding: 15px 15px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-menu {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .nav-menu a {
                padding: 12px 15px;
                font-size: 0.85rem;
            }
            
            .search-container {
                width: 100%;
                margin-top: 10px;
            }
            
            .featured-grid,
            .categories-grid,
            .trending-grid {
                grid-template-columns: 1fr;
            }
            
            .dhl-logo {
                font-size: 2.2rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .nav-menu a {
                padding: 10px 12px;
                font-size: 0.8rem;
            }
            
            .dhl-logo {
                font-size: 1.8rem;
            }
        }
        
        /* Premium Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .featured-card,
        .category-card,
        .trending-card {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<body>
    <!-- DHL Header -->
    <header class="dhl-header">
        <div class="header-container">
            <div>
                <a href="index.php" class="dhl-logo">
                    DHL<span>NEWS</span>
                </a>
                <div class="dhl-tagline">Global Logistics & Business News Network</div>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <form action="search.php" method="GET">
                    <input type="text" name="q" class="search-input" placeholder="Search news...">
                </form>
            </div>
        </div>
    </header>

    <!-- Fixed Navigation -->
    <nav class="nav-container">
        <div class="nav-scroll">
            <ul class="nav-menu">
                <li><a href="index.php" class="active">HOME</a></li>
                <li><a href="category.php?cat=aviation">AVIATION</a></li>
                <li><a href="category.php?cat=business">BUSINESS</a></li>
                <li><a href="category.php?cat=customs">CUSTOMS</a></li>
                <li><a href="category.php?cat=economy">ECONOMY</a></li>
                <li><a href="category.php?cat=logistics">LOGISTICS</a></li>
                <li><a href="category.php?cat=maritime">MARITIME</a></li>
                <li><a href="category.php?cat=supply-chain">SUPPLY CHAIN</a></li>
                <li><a href="category.php?cat=sustainability">SUSTAINABILITY</a></li>
                <li><a href="category.php?cat=technology">TECHNOLOGY</a></li>
                <li><a href="category.php?cat=trade">TRADE</a></li>
            </ul>
        </div>
    </nav>

    <!-- Breaking News Ticker -->
    <div class="breaking-news-ticker">
        <div class="ticker-label">Breaking</div>
        <div class="ticker-content">
            <?php foreach($breakingNews as $news): ?>
                <div class="ticker-item">
                    <a href="article.php?slug=<?php echo $news['slug']; ?>"><?php echo $news['title']; ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Featured Stories -->
        <section class="featured-section">
            <h2 class="section-title">Featured Stories</h2>
            <div class="featured-grid">
                <?php foreach($featuredArticles as $index => $article): ?>
                    <div class="featured-card" style="animation-delay: <?php echo $index * 0.1; ?>s; border-color: <?php echo $article['category_color']; ?>">
                        <div class="featured-image">
                            <?php 
                            $thumbnail = isset($articleThumbnails[$article['id']]) ? $articleThumbnails[$article['id']] : 'dhl_default.jpg';
                            ?>
                            <img src="thumbnails/<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="thumbnail-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjRDAwNTExIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJIZWx2ZXRpY2EiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiNGRkNDMDAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ESEx8TmV3czwvdGV4dD48L3N2Zz4='">
                            <div class="category-tag" style="background: <?php echo $article['category_color']; ?>">
                                <?php echo $article['category_name']; ?>
                            </div>
                        </div>
                        <div class="featured-content">
                            <h3 class="featured-title">
                                <a href="article.php?slug=<?php echo $article['slug']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo $article['title']; ?>
                                </a>
                            </h3>
                            <p class="featured-excerpt"><?php echo $article['excerpt']; ?></p>
                            <div class="meta-info">
                                <span>By <?php echo $article['author']; ?></span>
                                <a href="article.php?slug=<?php echo $article['slug']; ?>" class="read-more">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Categories -->
        <section class="categories-section">
            <h2 class="section-title">News Categories</h2>
            <div class="categories-grid">
                <?php 
                $categoryOrder = ['aviation', 'business', 'customs', 'economy', 'logistics', 'maritime', 'supply-chain', 'sustainability', 'technology', 'trade'];
                foreach($categoryOrder as $slug):
                    foreach($categories as $cat):
                        if($cat['slug'] == $slug): ?>
                            <a href="category.php?cat=<?php echo $cat['slug']; ?>" class="category-card" style="border-color: <?php echo $cat['color']; ?>">
                                <div class="category-icon"><?php echo $cat['icon']; ?></div>
                                <h3 class="category-name"><?php echo $cat['name']; ?></h3>
                                <div class="category-count">
                                    <?php 
                                    $countQuery = $conn->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                                    $countQuery->execute([$cat['id']]);
                                    echo $countQuery->fetchColumn() . " Articles";
                                    ?>
                                </div>
                            </a>
                        <?php endif;
                    endforeach;
                endforeach; ?>
            </div>
        </section>

        <!-- Logistics Updates -->
        <section class="logistics-updates">
            <h2 class="logistics-title">Global Logistics Updates</h2>
            <div class="logistics-grid">
                <div class="logistics-item">
                    <h4 style="margin-bottom: 10px; color: #FFCC00;">🚢 Shipping Routes</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">New Asia-Europe direct routes operational from Q2 2024</p>
                </div>
                <div class="logistics-item">
                    <h4 style="margin-bottom: 10px; color: #FFCC00;">📦 Delivery Times</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Express delivery guarantee now covers 95% of global destinations</p>
                </div>
                <div class="logistics-item">
                    <h4 style="margin-bottom: 10px; color: #FFCC00;">🌍 Sustainability</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Carbon-neutral shipping options available in 150+ countries</p>
                </div>
                <div class="logistics-item">
                    <h4 style="margin-bottom: 10px; color: #FFCC00;">✈️ Air Cargo</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">25% capacity increase with new freighter fleet additions</p>
                </div>
            </div>
        </section>

        <!-- Trending Now -->
        <section class="trending-section">
            <h2 class="section-title">Trending Now</h2>
            <div class="trending-grid">
                <?php foreach($trendingNews as $index => $trending): ?>
                    <div class="trending-card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="trending-number"><?php echo $index + 1; ?></div>
                        <div>
                            <h4 style="margin-bottom: 10px; font-size: 1.1rem;">
                                <a href="article.php?slug=<?php echo $trending['slug']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo $trending['title']; ?>
                                </a>
                            </h4>
                            <div style="color: #777; font-size: 0.9rem;">
                                <span>In <?php echo $trending['category_name']; ?></span> • 
                                <span><?php echo $trending['views']; ?> views</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="dhl-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>DHL NEWS NETWORK</h3>
                    <p style="color: #bbb; line-height: 1.8;">Your premier source for global logistics, business, and economic news. Delivering insights that move the world forward.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <div class="footer-links">
                        <a href="index.php">Home</a>
                        <a href="category.php?cat=business">Business</a>
                        <a href="category.php?cat=logistics">Logistics</a>
                        <a href="category.php?cat=economy">Economy</a>
                        <a href="category.php?cat=technology">Technology</a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Services</h3>
                    <div class="footer-links">
                        <a href="#">Shipping Updates</a>
                        <a href="#">Market Analysis</a>
                        <a href="#">Trade News</a>
                        <a href="#">Supply Chain</a>
                        <a href="#">Customs Regulations</a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Contact</h3>
                    <div class="footer-links">
                        <a href="#">News Desk</a>
                        <a href="#">Editorial</a>
                        <a href="#">Advertising</a>
                        <a href="#">Careers</a>
                        <a href="#">Support</a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>© 2024 DHL News Network. All rights reserved. Part of Deutsche Post DHL Group.</p>
                <p style="margin-top: 10px; font-size: 0.8rem;">This is a demonstration news website for educational purposes.</p>
            </div>
        </div>
    </footer>
</body>
</html>
