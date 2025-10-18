<?php
// views/member/pages/home.php
?>
<div class="p-6">
  <h1 class="text-2xl font-semibold mb-4">Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? 'Member') ?></h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded shadow">Total Active Loans: <strong>--</strong></div>
    <div class="bg-white p-4 rounded shadow">Books Available: <strong>--</strong></div>
    <div class="bg-white p-4 rounded shadow">Books Due Soon: <strong>--</strong></div>
  </div>
  <p class="mt-6 text-gray-600">Replace placeholders with model queries when ready.</p>
</div>
