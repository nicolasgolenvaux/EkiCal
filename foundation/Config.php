<?php declare(strict_types = 1);

namespace EkiCal\foundation;
/** Cette classe va nous permettre de rÃ©cupÃ©rer les diffÃ©rentes valeurs de configuration
 *qu'on aura dans le dossier /config. Dans le fichier /config, ce ne sont que des tableaux,
 * donc il faudra indiquer la clÃ© pour rÃ©cupÃ©rer la bonne valeur.
 */
class Config
{
    /** MÃ©thode pour rÃ©cupÃ©rer une valeur de configuration. Pour pouvoir rÃ©cupÃ©rer la clÃ© et le fichier,
     * on va la mettre au format 'file.key'.
     * @param string $config
     * @return mixed
     */

    public static function get(string $config): mixed
    {
        // 'File.key' ex : 'app.env'. On utilise la dÃ©composition pour rÃ©cupÃ©rer les valeurs.
        [$file, $key] = static::getFileAndKey($config);
        // On vÃ©rifie si le fichier de configuration existe et on rÃ©cupÃ¨re son chemin
        $path = static::getPath($file);
        // On va affecter le tableau que le fichier retourne Ã  la variable $config
        $config = require $path;
        // On retourne la valeur de la clÃ© mise en paramÃ¨tre.
        return $config[$key] ?? null;
    }
    /** Cette fonction retourne un tableau Ã  partir d'une chaÃ®ne de caractÃ¨res avec
     * un sÃ©parateur ('.'). On vÃ©rifie si la chaÃ®ne a un bon format via un regex.
     * @param string $config
     * @return array
     */
    protected static function getFileAndKey(string $config): array
    {
        // Si le format est bon, on passe au retour. On utilise une expression rÃ©guliÃ¨re.
        // Voir regex101.com
        if (!preg_match('/^[a-z_]+\.[a-z_]+$/i', $config)) {
            throw new \InvalidArgumentException(
                sprintf('Mauvais format (%s au lieu de fichier.clÃ© (lettres et _ acceptÃ©s))', $config)
            );
        }
        // On crÃ©e un tableau Ã  partir d'une chaÃ®ne de caractÃ¨re
        return explode('.', $config);
    }

    /**Cette mÃ©thode retourne le chemin d'accÃ¨s vers le fichier de configuration
     * On vÃ©rifie si le fichier de configuration existe et on rÃ©cupÃ¨re son chemin absolu.
     * @param string $file
     * @return string
     */
    protected static function getPath(string $file): string
    {
        //Root ramÃ¨ne le chemin de base de mon projet.
        $path = sprintf('../config/%s.php', $file);

        if (!file_exists($path)) {
            throw new \InvalidArgumentException('Le fichier de configuration n\'existe pas');
        }
        return $path;
    }
}
