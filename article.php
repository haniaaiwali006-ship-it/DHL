<?php
session_start();
require_once 'config.php';
$conn = getDBConnection();

$articleSlug = $_GET['slug'] ?? '';

if(empty($articleSlug)) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Get article details
$articleQuery = $conn->prepare("
    SELECT a.*, c.name as category_name, c.color as category_color, c.slug as category_slug 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.slug = ?
");
$articleQuery->execute([$articleSlug]);
$article = $articleQuery->fetch(PDO::FETCH_ASSOC);

if(!$article) {
    echo "<script>alert('Article not found'); window.location.href = 'index.php';</script>";
    exit;
}

// Update view count
$updateViews = $conn->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$updateViews->execute([$article['id']]);

// Get related articles
$relatedQuery = $conn->prepare("
    SELECT * FROM articles 
    WHERE category_id = ? AND id != ? 
    ORDER BY created_at DESC 
    LIMIT 3
");
$relatedQuery->execute([$article['category_id'], $article['id']]);
$relatedArticles = $relatedQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle comment submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $comment = htmlspecialchars($_POST['comment']);
    
    if(!empty($username) && !empty($comment)) {
        $insertComment = $conn->prepare("
            INSERT INTO comments (article_id, username, email, comment) 
            VALUES (?, ?, ?, ?)
        ");
        $insertComment->execute([$article['id'], $username, $email, $comment]);
        
        $_SESSION['comment_success'] = "Comment submitted successfully!";
        echo "<script>window.location.reload();</script>";
    }
}

// Get comments
$commentsQuery = $conn->prepare("
    SELECT * FROM comments 
    WHERE article_id = ? 
    ORDER BY created_at DESC
");
$commentsQuery->execute([$article['id']]);
$comments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);

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
    <title><?php echo $article['title']; ?> - CNN Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', 'Times New Roman', Times, serif;
        }
        
        body {
            background-color: #fff;
            color: #333;
            line-height: 1.8;
        }
        
        /* Header */
        .top-bar {
            background: #000;
            color: white;
            padding: 10px 0;
            font-size: 0.9rem;
        }
        
        .top-bar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .main-header {
            background: linear-gradient(135deg, #c4302b 0%, #8b0000 100%);
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
        
        /* Article Container */
        .article-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .article-header {
            margin-bottom: 40px;
        }
        
        .article-category {
            display: inline-block;
            background: <?php echo $article['category_color']; ?>;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .article-title {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.2;
            color: #000;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }
        
        .article-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .article-image {
            width: 100%;
            height: 450px;
            background: linear-gradient(45deg, <?php echo $article['category_color']; ?> 0%, #000 100%);
            border-radius: 12px;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        
        .article-content {
            font-size: 1.2rem;
            line-height: 1.9;
            color: #222;
            margin-bottom: 50px;
        }
        
        .article-content p {
            margin-bottom: 25px;
        }
        
        .article-content h3 {
            font-size: 1.8rem;
            margin: 40px 0 20px;
            color: #000;
            font-weight: 700;
        }
        
        .article-stats {
            display: flex;
            gap: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 40px 0;
            border-left: 4px solid <?php echo $article['category_color']; ?>;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            color: <?php echo $article['category_color']; ?>;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Comments Section */
        .comments-section {
            margin: 60px 0;
        }
        
        .section-title {
            font-size: 2rem;
            color: #000;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid <?php echo $article['category_color']; ?>;
        }
        
        .comment-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: <?php echo $article['category_color']; ?>;
        }
        
        .submit-btn {
            background: <?php echo $article['category_color']; ?>;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            background: #000;
            transform: translateY(-2px);
        }
        
        .comment-list {
            margin-top: 40px;
        }
        
        .comment-item {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 4px solid <?php echo $article['category_color']; ?>;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .comment-author {
            font-weight: 700;
            color: #000;
        }
        
        .comment-date {
            color: #777;
            font-size: 0.9rem;
        }
        
        .comment-text {
            color: #444;
            line-height: 1.7;
        }
        
        /* Related Articles */
        .related-articles {
            margin: 60px 0;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .related-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
        }
        
        .related-content {
            padding: 20px;
        }
        
        .related-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #000;
            line-height: 1.4;
        }
        
        /* Footer */
        .footer {
            background: #000;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .article-title {
                font-size: 2.2rem;
            }
            
            .article-image {
                height: 300px;
            }
            
            .article-content {
                font-size: 1.1rem;
            }
            
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-container">
            <span>BREAKING NEWS NETWORK</span>
            <span><?php echo date('l, F j, Y'); ?></span>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="index.php" class="logo">CNN<span>CLONE</span></a>
            <div style="display: flex; gap: 20px;">
                <?php foreach($categories as $cat): ?>
                    <a href="category.php?cat=<?php echo $cat['slug']; ?>" 
                       style="color: white; text-decoration: none; font-weight: 600;">
                        <?php echo $cat['name']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <div class="article-container">
        <div class="article-header">
            <a href="category.php?cat=<?php echo $article['category_slug']; ?>" class="article-category">
                <?php echo $article['category_name']; ?>
            </a>
            <h1 class="article-title"><?php echo $article['title']; ?></h1>
            
            <div class="article-meta">
                <span><strong>By:</strong> <?php echo $article['author']; ?></span>
                <span><strong>Published:</strong> <?php echo date('F j, Y, g:i a', strtotime($article['created_at'])); ?></span>
                <span><strong>Views:</strong> <?php echo $article['views'] + 1; ?></span>
            </div>
        </div>

        <div class="article-image">
            <!-- Featured image placeholder -->
        </div>

        <div class="article-content">
            <?php echo $article['content']; ?>
        </div>

        <div class="article-stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($article['views'] + 1); ?></div>
                <div class="stat-label">Total Views</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count($comments); ?></div>
                <div class="stat-label">Comments</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo strlen($article['content']); ?></div>
                <div class="stat-label">Words</div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2 class="section-title">Comments (<?php echo count($comments); ?>)</h2>
            
            <?php if(isset($_SESSION['comment_success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $_SESSION['comment_success']; unset($_SESSION['comment_success']); ?>
                </div>
            <?php endif; ?>

            <!-- Comment Form -->
            <form method="POST" class="comment-form">
                <div class="form-group">
                    <label class="form-label">Your Name *</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Comment *</label>
                    <textarea name="comment" class="form-input" rows="5" required></textarea>
                </div>
                
                <button type="submit" name="submit_comment" class="submit-btn">
                    Post Comment
                </button>
            </form>

            <!-- Comments List -->
            <div class="comment-list">
                <?php if(count($comments) > 0): ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-author"><?php echo $comment['username']; ?></div>
                                <div class="comment-date"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></div>
                            </div>
                            <div class="comment-text"><?php echo $comment['comment']; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; font-style: italic; padding: 40px;">
                        No comments yet. Be the first to share your thoughts!
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Articles -->
        <div class="related-articles">
            <h2 class="section-title">Related Stories</h2>
            <div class="related-grid">
                <?php foreach($relatedArticles as $related): ?>
                    <div class="related-card">
                        <div style="height: 150px; background: linear-gradient(45deg, <?php echo $article['category_color']; ?> 0%, #764ba2 100%);"></div>
                        <div class="related-content">
                            <h3 class="related-title">
                                <a href="article.php?slug=<?php echo $related['slug']; ?>" style="color: inherit; text-decoration: none;">
                                    <?php echo $related['title']; ?>
                                </a>
                            </h3>
                            <p style="color: #777; font-size: 0.9rem; margin-top: 10px;">
                                <?php echo date('M j, Y', strtotime($related['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p style="font-size: 1.2rem; margin-bottom: 20px;">CNN CLONE - Premium News Network</p>
            <p style="color: #bbb; margin-bottom: 20px;">
                <a href="index.php" style="color: #ffd700; text-decoration: none;">Home</a> | 
                <a href="category.php?cat=<?php echo $article['category_slug']; ?>" style="color: white; text-decoration: none;">More from <?php echo $article['category_name']; ?></a>
            </p>
            <p style="color: #999; font-size: 0.9rem;">
                © 2024 CNN Clone. This article is for demonstration purposes only.
            </p>
        </div>
    </footer>
</body>
</html>
