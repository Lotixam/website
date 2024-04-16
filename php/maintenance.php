<?php
  session_start();
  $ressource = fopen("./res/date_debut_maintenance.txt", "r");
  $depuis = fread($ressource, filesize("./res/date_debut_maintenance.txt"));
  require("../html/maintenance.html");
  echo "<p>© LOTIXAM SAS 2024. Tous droits réservés</p>";