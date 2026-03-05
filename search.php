<?php
session_start();
require_once 'config.php';
$conn = getDBConnection();

$searchQuery = $_GET['q'] ?? '';

if(empty($searchQuery)) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Search articles
$searchStmt = $conn->prepare("
    SELECT a.*, c.name as category_name, c.color as category_color 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ?
    ORDER BY a.created_at DESC
");
$searchTerm = "%$searchQuery%";
$searchStmt->execute([$searchTerm, $searchTerm, $searchTerm]);
$results = $searchStmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories
$categoriesQuery = $conn->prepare("SELECT * FROM categories ORDER BY name");
$categoriesQuery->execute();
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>" - CNN Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #c4302b 0%, #8b0000 100%);
            color: white;
            padding: 20px 0;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
            font-family: 'Arial Black', sans-serif;
        }
        
        .logo span {
            color: #ffd700;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .search-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .search-title {
            font-size: 2.5rem;
            color: #000;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .search-query {
            color: #c4302b;
            font-weight: 700;
        }
        
        .results-count {
            color: #666;
            font-size: 1.1rem;
        }
        
        .search-form {
            max-width: 600px;
            margin: 30px auto 0;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            padding-left: 50px;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #c4302b;
            box-shadow: 0 0 20px rgba(196, 48, 43, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .result-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .result-image {
            height: 200px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }
        
        .result-category {
            position: absolute;
            top: 15px;
            left: 15px;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .result-content {
            padding: 25px;
        }
        
        .result-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .result-excerpt {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .result-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .no-results-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .back-home {
            display: inline-block;
            margin-top: 30px;
            background: #c4302b;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-home:hover {
            background: #8b0000;
            transform: scale(1.05);
        }
        
        /* Footer */
        .footer {
            background: #000;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .search-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">CNN<span>CLONE</span></a>
            <div style="display: flex; gap: 20px;">
                <a href="index.php" style="color: white; text-decoration: none; font-weight: 600;">Home</a>
                <?php foreach($categories as $cat): ?>
                    <a href="category.php?cat=<?php echo $cat['slug']; ?>" 
                       style="color: white; text-decoration: none; font-weight: 600;">
                        <?php echo $cat['name']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>

    <!-- Search Results -->
    <div class="container">
        <div class="search-header">
            <h1 class="search-title">Search Results</h1>
            <p class="results-count">
                <?php echo count($results); ?> result<?php echo count($results) !== 1 ? 's' : ''; ?> found for 
                "<span class="search-query"><?php echo htmlspecialchars($searchQuery); ?></span>"
            </p>
            
            <form method="GET" action="search.php" class="search-form">
                <span class="search-icon">🔍</span>
                <input type="text" name="q" class="search-input" 
                       value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       placeholder="Search for more news...">
            </form>
        </div>

        <?php if(count($results) > 0): ?>
            <div class="results-grid">
                <?php foreach($results as $result): ?>
                    <div class="result-card">
                        <div class="result-image">
                            <div class="result-category" style="background: <?php echo $result['category_color']; ?>;">
                                <?php echo $result['category_name']; ?>
                            </div>
                        </div>
                        <div class="result-content">
                            <h3 class="result-title">
                                <a href="article.php?slug=<?php echo $result['slug']; ?>" 
                                   style="color: inherit; text-decoration: none;">
                                    <?php echo $result['title']; ?>
                                </a>
                            </h3>
                            <p class="result-excerpt">
                                <?php echo strlen($result['excerpt']) > 150 ? 
                                    substr($result['excerpt'], 0, 150) . '...' : 
                                    $result['excerpt']; ?>
                            </p>
                            <div class="result-meta">
                                <span>By <?php echo $result['author']; ?></span>
                                <span><?php echo date('M d, Y', strtotime($result['created_at'])); ?></span>
                            </div>
                            <a href="article.php?slug=<?php echo $result['slug']; ?>" 
                               class="back-home" 
                               style="margin-top: 15px; padding: 8px 20px; font-size: 0.9rem; display: inline-block;">
                                Read Article
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">📰</div>
                <h2 style="color: #666; margin-bottom: 15px;">No articles found</h2>
                <p style="color: #888; max-width: 600px; margin: 0 auto 30px;">
                    We couldn't find any articles matching your search. Try different keywords or browse our categories.
                </p>
                <a href="index.php" class="back-home">Back to Homepage</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <p style="font-size: 1.1rem; margin-bottom: 20px;">CNN CLONE Search Results</p>
            <p style="color: #bbb;">
                Search query: "<?php echo htmlspecialchars($searchQuery); ?>"
            </p>
            <p style="color: #999; margin-top: 20px; font-size: 0.9rem;">
                © 2024 CNN Clone - Premium News Network
            </p>
        </div>
    </footer>
</body>
</html>
