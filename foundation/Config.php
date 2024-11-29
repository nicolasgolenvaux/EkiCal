<?php declare(strict_types = 1);

namespace EkiCal\foundation;
/** Cette classe va nous permettre de récupérer les différentes valeurs de configuration
 *qu'on aura dans le dossier /config. Dans le fichier /config, ce ne sont que des tableaux,
 * donc il faudra indiquer la clé pour récupérer la bonne valeur.
 */
class Config
{
    /** Méthode pour récupérer une valeur de configuration. Pour pouvoir récupérer la clé et le fichier,
     * on va la mettre au format 'file.key'.
     * @param string $config
     * @return mixed
     */

    public static function get(string $config): mixed
    {
        // 'File.key' ex : 'app.env'. On utilise la décomposition pour récupérer les valeurs.
        [$file, $key] = static::getFileAndKey($config);
        // On vérifie si le fichier de configuration existe et on récupère son chemin
        $path = static::getPath($file);
        // On va affecter le tableau que le fichier retourne à la variable $config
        $config = require $path;
        // On retourne la valeur de la clé mise en paramètre.
        return $config[$key] ?? null;
    }
    /** Cette fonction retourne un tableau à partir d'une chaîne de caractères avec
     * un séparateur ('.'). On vérifie si la chaîne a un bon format via un regex.
     * @param string $config
     * @return array
     */
    protected static function getFileAndKey(string $config): array
    {
        // Si le format est bon, on passe au retour. On utilise une expression régulière.
        // Voir regex101.com
        if (!preg_match('/^[a-z_]+\.[a-z_]+$/i', $config)) {
            throw new \InvalidArgumentException(
                sprintf('Mauvais format (%s au lieu de fichier.clé (lettres et _ acceptés))', $config)
            );
        }
        // On crée un tableau à partir d'une chaîne de caractère
        return explode('.', $config);
    }

    /**Cette méthode retourne le chemin d'accès vers le fichier de configuration
     * On vérifie si le fichier de configuration existe et on récupère son chemin absolu.
     * @param string $file
     * @return string
     */
    protected static function getPath(string $file): string
    {
        //Root ramène le chemin de base de mon projet.
        $path = sprintf('C:/Users/nicol/EkiCal/config/%s.php', $file);
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Le fichier de configuration n\'existe pas');
        }
        return $path;
    }
}
