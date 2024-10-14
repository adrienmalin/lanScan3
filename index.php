<?php
include_once 'config.php';
include_once 'filter_inputs.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <title>lanScan</title>
  <link rel="icon" href="favicon.ico" />
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
  <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <nav class="ui inverted teal fixed menu">
    <a class="ui teal button item" href=".">
      lan<?php include 'logo.svg'; ?>can
    </a>
    <form class="right menu" onsubmit="targetsInputDiv.classList.add('loading')">
      <div class="ui category search item">
          <div id="targetsInputDiv" class="ui icon input">
            <input class="prompt" type="text" id="targetsInput" name="lan" required
              pattern="[a-zA-Z0-9._\/ \-]+" value="<?= $targets; ?>" placeholder="Scanner un réseau..."
              title="Les cibles peuvent être spécifiées par des noms d'hôtes, des adresses IP, des adresses de réseaux, etc.
Exemples: <?= $_SERVER['REMOTE_ADDR']; ?>/24 <?= $_SERVER['SERVER_NAME']; ?> 10.0-255.0-255.1-254" />
            <i class="satellite dish icon"></i>
          </div>
          <button style="display: none;" type="submit" formmethod="get" formaction="scan.php"></button>
      </div>
      <div class="item">
          <button class="ui teal icon submit button" type="submit" formmethod="get" formaction="options.php" onclick="lanInput.required=false">
              <i class="sliders horizontal icon"></i>
          </button>
      </div>
      <div class="item">
        <a class="ui teal icon button" href="https://nmap.org/man/fr/index.html" target="_blank">
          <i class="question circle icon"></i>
        </a>
      </div>
    </form>
  </nav>

  <main class="ui main container">
    <h1 class="ui header">Précédents scans</h1>
    <div class="ui large relaxed card">
      <div class="content">
        <div class="ui divided link list">
<?php
if (!file_exists($SCANSDIR)) {
  mkdir($SCANSDIR);
}
foreach (scandir($SCANSDIR) as $filename) {
  if (substr($filename, -4) == '.xml') {
    $name = str_replace('!', '/', substr_replace($filename, '', -4));
    echo "<a class='item' href='".htmlentities("$SCANSDIR/$filename", ENT_QUOTES)."'>$name</a>\n";
  }
}
?>
        </div>
      </div>
    </div>
  </main>
  <footer class="ui footer segment">
    lanScan est basé sur <a href="https://nmap.org/" target="_blank">Nmap</a>
  </footer>
</body>

</html>