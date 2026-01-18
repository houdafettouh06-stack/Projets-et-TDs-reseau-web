<?php
// core/CSVSmartImporter.php

class CSVSmartImporter {
    
    // Dictionnaire des synonymes pour le mapping intelligent
    private array $synonyms = [
        'nom_etudiant' => ['nom', 'name', 'family_name', 'nom_famille', 'student_name', 'lastname', 'last_name', 'eleve'],
        'prenom_etudiant' => ['prenom', 'firstname', 'first_name', 'given_name', 'prenom_eleve'],
        'classe' => ['classe', 'class', 'group', 'groupe', 'section', 'niveau'],
        'email_parent' => ['email', 'mail', 'courriel', 'e-mail', 'mail_parent', 'email_parent'],
        'telephone_parent' => ['tel', 'phone', 'mobile', 'telephone', 'gsm', 'portable', 'contact'],
        'date_absence' => ['date', 'jour', 'day', 'date_absence', 'absent_le', 'time'],
        'motif' => ['motif', 'reason', 'raison', 'justification', 'status', 'statut']
    ];

    /**
     * Analyse l'en-tête du fichier CSV.
     * Détecte le délimiteur automatiquement.
     * 
     * @param string $csvFilePath Chemin absolu du fichier
     * @return array En-têtes trouvés
     * @throws Exception Si le fichier est illisible
     */
    public function analyzeHeaders(string $csvFilePath): array {
        if (!file_exists($csvFilePath)) {
            throw new Exception("Fichier introuvable : " . $csvFilePath);
        }

        // Détection du délimiteur (simple analyse de la 1ere ligne)
        $handle = fopen($csvFilePath, 'r');
        if ($handle === false) {
            throw new Exception("Impossible d'ouvrir le fichier.");
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            throw new Exception("Fichier vide.");
        }

        $delimiter = $this->detectDelimiter($firstLine);
        
        // Lecture proprement dite
        $handle = fopen($csvFilePath, 'r');
        // Gestion BOM UTF-8
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        
        $headers = fgetcsv($handle, 0, $delimiter);
        fclose($handle); // On ferme juste après les headers pour l'analyse

        if ($headers === false || empty($headers)) {
            throw new Exception("Impossible de lire les en-têtes CSV.");
        }

        return $headers;
    }

    /**
     * Propose un mapping entre les colonnes du CSV et les champs de la BDD.
     * 
     * @param array $csvHeaders Les en-têtes extraits du CSV
     * @return array Tableau des propositions avec scores
     */
    public function detectMapping(array $csvHeaders): array {
        $mappingProposition = [];

        foreach ($csvHeaders as $index => $header) {
            $normalizedHeader = $this->normalizeString($header);
            $bestMatch = null;
            $bestScore = 0; // Pour similar_text modifiée ou logique custom
            $minDistance = 999; // Pour Levenshtein (0 = identique)

            foreach ($this->synonyms as $dbField => $variants) {
                foreach ($variants as $variant) {
                    // 1. Match Exact
                    if ($normalizedHeader === $variant) {
                        $bestMatch = $dbField;
                        $minDistance = 0; // Distance nulle
                        break 2;
                    }

                    // 2. Levenshtein
                    $distance = levenshtein($normalizedHeader, $variant);
                    
                    // On cherche la distance la plus petite
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $bestMatch = $dbField;
                    }
                    
                    // 3. Contient (strpos) - Bonus de confiance si inclus
                    if (strpos($normalizedHeader, $variant) !== false) {
                         // Si la distance est déjà correcte, ça confirme. 
                         // Hack: réduire artificiellement la distance si contain
                         $minDistance = min($minDistance, 1); 
                         $bestMatch = $dbField;
                    }
                }
            }

            // Calcul du score de confiance (0 à 100)
            // Distance 0 = 100%
            // Distance 1 = ~90%
            // Distance > 3 = Faible
            $confidence = 0;
            if ($bestMatch) {
                $len = strlen($normalizedHeader);
                if ($len === 0) $len = 1;
                 // Formule basique : plus la distance est grande par rapport à la longueur, moins c'est bon
                $confidence = max(0, 100 - ($minDistance * 20)); 
                
                // Si distance trop grande par rapport à la taille du mot, on ignore
                if ($minDistance > 3 && $len < 5) { // Mot court mais 3 fautes = non
                    $bestMatch = null;
                    $confidence = 0;
                }
            }

            $mappingProposition[] = [
                'csv_index' => $index,
                'csv_header' => $header,
                'suggested_field' => ($confidence > 50) ? $bestMatch : '', // Suggestion seulement si > 50%
                'confidence' => $confidence
            ];
        }

        return $mappingProposition;
    }

    private function detectDelimiter(string $line): string {
        $delimiters = [';', ',', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $d) {
            $count = substr_count($line, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $d;
            }
        }
        return $bestDelimiter;
    }

    private function normalizeString(string $str): string {
        $str = mb_strtolower($str, 'UTF-8');
        // Supprimer accents basiques
        $str = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'],
            $str
        );
        $str = preg_replace('/[^a-z0-9]/', '', $str); // Garde seulement alphanum pour comparaison stricte
        return $str;
    }
}
