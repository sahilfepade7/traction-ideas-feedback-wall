<?php
// includes/functions.php
require_once __DIR__ . '/../config/config.php';

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Basic server-side validation for suggestion
 */
function validate_suggestion(array $data): array {
    $errors = [];
    $title = trim($data['title'] ?? '');
    $desc  = trim($data['description'] ?? '');
    $cat   = $data['category'] ?? '';

    if ($title === '' || mb_strlen($title) < 3) $errors[] = "Title must be at least 3 characters.";
    if ($desc === '' || mb_strlen($desc) < 6) $errors[] = "Description must be at least 6 characters.";
    $allowed = ['Feature','Design','Bug','Idea'];
    if (!in_array($cat, $allowed, true)) $errors[] = "Invalid category.";

    return $errors;
}
