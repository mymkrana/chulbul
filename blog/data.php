<?php
require_once dirname(__DIR__) . '/includes/blog-repository.php';

$_blog_pdo = cbd_database();
$blog_posts = cbd_published_blog_posts();
