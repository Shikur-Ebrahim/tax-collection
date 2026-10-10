<?php
try {
  $pdo = new PDO('pgsql:host=db.bpgcpdzdwdxitmlwnshu.supabase.co;port=6543;dbname=postgres', 'postgres', 'Shikur@3828@', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
  echo 'Connection successful!';
} catch (Exception $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
