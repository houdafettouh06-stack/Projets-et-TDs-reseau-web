<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manipulations des variables</title>
</head>
<body>
    <?php 
      $nomModule=ProjectManagement;
      $nomEnseignant=AsmaeAbadi;
      $nombredesetudiants=30;
      $noteminimaledevalidation=10;
      echo" le module".$nomModule "enseigner par ".$nomEnseignant;
      echo "le nombre totale des etudiants est".$nombredesetudiants;
      echo"la note minimale de validation est".$noteminimaledevalidation;
      $etudiants = ["Houda", "Youssef", "Hajar", "Aya"];
      $notes = [18, 17, 14, 15];
      foreach($etudiants as $etudiants=>$notes){
        echo"l etudiant".$etudiants"a eu une note de ".$notes;
    };
      $etudiants["Aymane"] = 9;
      unset($etudiants["Hajar"]);
      $somme = 0;
      $noteMax = null;
      $noteMin = null;
      $nbEtudiants = 0;
      foreach ($etudiants as $note) {
      $somme += $note;
      $nbEtudiants++;

      if ( $note > $noteMax) {
        $noteMax = $note;
    }

      if ($note < $noteMin) {
        $noteMin = $note;
    }
}
    $moyenne = $somme / $nbEtudiants;
    $nbetudiantsvalider=0;
    for($i=0,$i<count($etudiants),$i++){
    for($j=0,$j<count($etudiants),$j++){
        if($etudiants[$i]=>$notes[$i]>10){
            echo"l etudiant".$etudiants[$i]"a valider le module"
             $nbetudiantsvalider++;
         }    
      echo [$i]"-".$etudiants[$i];
  }      
  }
  echo"le nombre d etudiant qui a valider est ".$nbetudiantsvalider;
  function calculerMoyenne($notes) {
    return array_sum($notes) / count($notes);
}
function estValide($notes,$noteminimaledevalidation) {
    return $notes >=$noteminimaledevalidation ;
}
function messageNote($notes) {
    if ($notes >= 15) {
        return "Excellent";
    } elseif ($notes>= 10) {
        return "Valide";
    } else {
        return "Non valide";
    }
}
echo "<h2>Liste des étudiants</h2>";

$valides = 0;
$non_valides = 0;

for ($i = 0; $i < count($etudiants); $i++) {
    echo ($i + 1) . ". ";
    echo $etudiants[$i] . " — Note : " . $notes[$i];
    echo " (" . messageNote($notes[$i]) . ")<br>";

    if (estValide($notes[$i], $notevalidation)) {
        $valides++;
    } else {
        $non_valides++;
    }
}
$moyenne = calculerMoyenne($notes);
$note_max = max($notes);
$note_min = min($notes);

echo "<h2>Statistiques de la classe</h2>";
echo "<p>Moyenne : " . round($moyenne, 2) . "</p>";
echo "<p>Note maximale : $note_max</p>";
echo "<p>Note minimale : $note_min</p>";

echo "<p>Étudiants validés : $valides</p>";
echo "<p>Étudiants non validés : $non_valides</p>";
echo "<h2>Étudiants validés</h2>";

for ($i = 0; $i < count($etudiants); $i++) {
    if (estValide($notes[$i], $notevalidation)) {
        echo $etudiants[$i] . " — " . $notes[$i] . "<br>";
    }
}
?>
</body>
</html>









