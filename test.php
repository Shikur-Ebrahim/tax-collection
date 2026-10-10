<?php
try {
  $pdo = new PDO('pgsql:host=System.Management.Automation.Internal.Host.InternalHost;port=6543;dbname=postgres', 'postgres.bpgcpdzdwdxitmlwnshu', 'Shikur@3828@', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 2]);
  echo 'SUCCESS: System.Management.Automation.Internal.Host.InternalHost';
  exit(0);
} catch (Exception $e) {
  echo 'FAIL: ' . $e->getMessage();
}
