<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user_role = $_SESSION['role'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Traction Ideas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
  /* ===== GENERAL STYLES ===== */
  body {
    background-color: #f8f9fa;
    color: #212529;
    transition: background-color 0.3s, color 0.3s;
  }

  .navbar {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .card {
    background-color: #fff;
    color: #212529;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
    border-radius: 1rem;
  }

  a {
    text-decoration: none;
  }

  /* ===== DARK MODE ===== */
  body.dark-mode {
    background-color: #121212;
    color: #f1f1f1;
  }

  body.dark-mode .navbar {
    background-color: #1f1f1f !important;
    box-shadow: 0 2px 8px rgba(255,255,255,0.1);
  }

  body.dark-mode .card {
    background-color: #1e1e1e;
    color: #f1f1f1;
    border-color: #333;
    box-shadow: 0 0 10px rgba(255,255,255,0.05);
  }

  body.dark-mode a {
    color: #9ad3ff;
  }

  /* ===== DARK MODE BUTTON ===== */
  #darkModeToggle {
    border: none;
    background: transparent;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 50%;
    color: #333;
    transition: all 0.3s ease;
  }

  #darkModeToggle:hover {
    transform: rotate(15deg) scale(1.1);
    background-color: rgba(0,0,0,0.05);
  }

  body.dark-mode #darkModeToggle {
    color: #ffd54f;
    text-shadow: 0 0 6px rgba(255,255,0,0.5);
  }

  body.dark-mode #darkModeToggle:hover {
    background-color: rgba(255,255,255,0.1);
  }

  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
  <a class="navbar-brand fw-bold" href="index.php">🚀 Traction Ideas</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
      <li class="nav-item"><a href="resolved.php" class="nav-link">Resolved</a></li>

      <?php if ($user_role === 'admin'): ?>
        <li class="nav-item"><a href="admin/dashboard.php" class="nav-link">Dashboard</a></li>
      <?php endif; ?>

      <?php if ($user_role !== 'guest'): ?>
        <li class="nav-item"><a href="add.php" class="nav-link">Add Suggestion</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Logout</a></li>
      <?php else: ?>
        <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
      <?php endif; ?>
    </ul>

    <button id="darkModeToggle" class="ms-3" title="Toggle Dark Mode">🌙</button>
  </div>
</nav>

<div class="container py-4">
