<?php
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : false;
  switch ($method) {
    case 'tambah':
      $tugasBaru = $_POST['tugas'];
      if(isset($_SESSION['tugas'])) {
        if($tugasBaru) {
          array_push($_SESSION['tugas'], $tugasBaru);
        }
      } else {
        $_SESSION['tugas'] = [$tugasBaru];
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;
    case 'hapus':
      $untukDihapus = $_POST['id'];
      if(isset($_SESSION['tugas'][$untukDihapus])) {
        unset($_SESSION['tugas'][$untukDihapus]);
        $_SESSION['tugas'] = array_values($_SESSION['tugas']);
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;
    case 'update':
      $untukDiupdate = $_POST['id'];
      if(isset($_SESSION['tugas'][$untukDiupdate])) {
        $_SESSION['tugas'][$untukDiupdate] = $_POST['tugas'];
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;
  }
}

if($_SERVER['REQUEST_METHOD'] === 'GET') {
  $method = isset($_GET['method']) ? $_GET['method'] : false;
  switch ($method) {
    case 'hapus-semua':
      session_destroy();
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;
    case 'edit':
      if (isset($_GET['id'])) {
        $tugasDiedit = isset($_SESSION['tugas'][$_GET['id']]) ? $_SESSION['tugas'][$_GET['id']] : false;
      } else {
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
      }
      echo renderFormEdit($_GET['id'], $tugasDiedit);
      break;
    default:
      $tugas = $_SESSION['tugas'] ?? null;
      echo renderListingTugas($tugas);
      break;
  }
}

function renderListingTugas($daftarTugas) {
  if($daftarTugas) {
    $tugasTugas = "<ol>";
    foreach($daftarTugas as $idx => $tugas) {
      $tugasTugas .= <<<HTML
      <li>
        {$tugas}
        <a href="{$_SERVER['SCRIPT_NAME']}?method=edit&id={$idx}">EDIT</a>
        <form style="display:inline-block" method="post" action="{$_SERVER['SCRIPT_NAME']}?method=hapus">
          <input type="hidden" name="id" value="{$idx}" />
          <button type="submit">🗑️</button>
        </form>
      </li>
      HTML;
    }
    $tugasTugas .= "</ol>";
  } else {
    $tugasTugas = 'Belum ada tugas';
  }
  return <<<HTML
<h1>Apa lagi?</h1>
<form name="apalagi" method="post" action="{$_SERVER['SCRIPT_NAME']}?method=tambah">
  <input name="tugas" type="text" placeholder="tulis tugas" />
  <button type="submit">Simpan</button>
</form>
<h2>Daftar tugas</h2>
{$tugasTugas}
<hr />
<a href="?method=hapus-semua">HAPUS SEMUA ☢️</a>
HTML;
}


function renderFormEdit($id, $deskripsi) {
  return <<<HTML
<h1>EDIT</h1>
<form name="update" method="post" action="{$_SERVER['SCRIPT_NAME']}?method=update">
  <input type="hidden" name="id" value="{$id}" />
  <input name="tugas" value="{$deskripsi}" type="text" placeholder="tulis tugas" />
  <button type="submit">Simpan</button>
</form>
HTML;
}


