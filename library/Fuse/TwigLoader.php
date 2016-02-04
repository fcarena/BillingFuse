<?php
/*
*
* BillingFuse
*
* @copyright 2016 BillingFuse International Limited.
*
* @license Apache V2.0
*
* THIS SOURCE CODE FORM IS SUBJECT TO THE TERMS OF THE PUBLIC
* APACHE LICENSE V2.0. A COMPLETE COPY OF THE LICENSE TEXT IS
* INCLUDED IN THE LICENSE FILE. 
*
*/


class Fuse_TwigLoader extends Twig_Loader_Filesystem
{
    protected $options = array();

    /**
     * Constructor.
     *
     * @param string|array $options A path or an array of options and paths
     */
    public function __construct(array $options)
    {
        if(!isset($options['mods'])) {
            throw new \Fuse_Exception('Missing mods param for Fuse_TwigLoader');
        }

        if(!isset($options['theme'])) {
            throw new \Fuse_Exception('Missing theme param for Fuse_TwigLoader');
        }

        if(!isset($options['type'])) {
            throw new \Fuse_Exception('Missing type param for Fuse_TwigLoader');
        }

        $this->options = $options;
        $paths_arr = array($options['mods'], $options['theme']);
        $this->setPaths($paths_arr);
    }


    protected function findTemplate($name)
    {
        // normalize name
        $name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $name_split = explode("_", $name);

        $paths = array();
        $paths[] = $this->options["theme"] . DIRECTORY_SEPARATOR . "html";
        if(isset($name_split[1])) {
            $paths[] = $this->options["mods"] . DIRECTORY_SEPARATOR . ucfirst($name_split[1]). DIRECTORY_SEPARATOR . "html_" . $this->options["type"];
        }

        foreach($paths as $path) {
            if(file_exists($path . DIRECTORY_SEPARATOR . $name)) {
                return $this->cache[$name] = $path . '/' . $name;
            }
        }

        throw new Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name,  implode(', ', $paths)));
    }
}