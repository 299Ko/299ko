<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

/**
 * Simple lightweight template parser.
 */
class Template {

    /** @var array  Constants */
    protected static $const = [];

    /** @var string Template path */
    protected $file;

    /** @var string Template content */
    protected $content;

    /** @var array  Assigned datas to replace */
    protected $data = [];

    /**
     * Constructor
     * Check if the template exist in the current theme, else template will be
     * taken from 'default' theme
     *
     * @param string Template name with extension
     */
    public function __construct($file) {
        $this->file = $file;
    }

    /**
     * Add a var who will be added to all templates
     *
     * @static
     * @param string Var key
     * @param string Value
     */
    public static function addGlobal($key, $value) {
        self::$const[$key] = $value;
    }

    /**
     * Assign datas to this template
     * Datas can be string, numeric... or array and objects
     *
     * @param string Var key
     * @param string Value
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Return the parsed template content
     *
     * @return string Parsed content
     */
    public function output() {
        if (!file_exists($this->file)) {
            return "Error loading template file ($this->file).<br/>";
        }
        ob_start();
        $this->get_content();
        $this->addGlobalsToVars();
        $this->parse();
        // Uncomment the next line to see parsed template
         file_put_contents($this->file . '.cache.php', $this->content);
        eval('?>' . $this->content);
        return ob_get_clean();
    }

    /**
     * Get template content
     */
    protected function get_content() {
        $this->content = file_get_contents($this->file);
    }

    /**
     * Parse template
     * Allowed tags :
     * {# This is multiline allowed comments #}
     * {% NOPARSE %} ... {% ENDNOPARSE %}
     * {% HOOK.ACTION.hookName[parameters] %}
     * {% INCLUDE My_Page %}
     * {% IF MY_VAR %} {% IF MY_VAR !== 25 %} ... {% ELSEIF MY_VAR == plop %} ... {% ELSE %} ... {% ENDIF %}
     * {{ MY_VAR }}, {{ PARENT.VAR}}, {{ [ArrayVar, ArrayVar2] }}, {{Object.method}}, ...
     * {% FOR MY_VAR IN MY_VARS %} ... {{MY_VAR.name}} ... {% ENDFOR %}
     */
    protected function parse() {
        $this->content = preg_replace_callback('#\{\% *NOPARSE *\%\}(.*)\{\% *ENDNOPARSE *\%\}#isU', 'self::_no_parse', $this->content);
        $this->content = preg_replace('#\{\#(.*)\#\}#isU', '<?php /* $1 */ ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *IF +([0-9a-z_\.\-\[\]\,]+) *([\=|\<|\>|\!&]{1,3}) *([0-9a-z_\.\-\[\]\,]+) *\%\}#iU', 'self::_complexe_if_replace', $this->content);
        $this->content = preg_replace_callback('#\{\% *IF +([0-9a-z_\.\-\[\]\,]+) *\%\}#iU', 'self::_simple_if_replace', $this->content);
        $this->content = preg_replace_callback('#\{\% *HOOK.(.+) *\%\}#iU', 'self::_callHook', $this->content);
        $this->content = preg_replace_callback('#\{\% *INCLUDE +([0-9a-z_\.\-\[\]\,\/]+) *\%\}#iU', 'self::_include', $this->content);
        $this->content = preg_replace('#\{\{ *(.+) *\}\}#iU', '<?php $this->_show_var(\'$1\'); ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *FOR +([0-9a-z_\.\-\[\]\,\ ]+) +IN +([0-9a-z_\.\-\[\]\,]+) *\%\}#i', 'self::_replace_for', $this->content);
        $this->content = preg_replace('#\{\% *ENDFOR *\%\}#i', '<?php endforeach; ?>', $this->content);
        $this->content = preg_replace('#\{\% *ENDIF *\%\}#i', '<?php } ?>', $this->content);
        $this->content = preg_replace('#\{\% *ELSE *\%\}#i', '<?php }else{ ?>', $this->content);
        $this->content = preg_replace_callback('#\{\% *ELSEIF +([0-9a-z_\.\-\[\]\,]+) *([\=|\<|\>|\!&]{1,3}) *([0-9a-z_\.\-\[\]\,]+) *\%\}#iU', 'self::_complexe_elseif_replace', $this->content);
        $this->content = preg_replace_callback('#\{\% *ELSEIF +([0-9a-z_\.\-\[\]\,]+) *\%\}#iU', 'self::_simple_elseif_replace', $this->content);
        $this->content = str_replace('#/§&µ&§;#', '{', $this->content);
    }

    protected function _no_parse($matches) {
        //return htmlentities($matches[1]);
        return str_replace('{', '#/§&µ&§;#', htmlentities($matches[1]));
    }

    protected function _show_var($name) {
        echo $this->getVar($name, $this->data);
    }

    protected function simpleReplaceVar($var) {
        if (!is_numeric($var)) {
            return '$this->getVar(\'' . $var . '\', $this->data)';
        }
        return $var;
    }

    protected function complexReplace($matches) {
        $first = $this->simpleReplaceVar($matches[1]);
        $thirst = $this->simpleReplaceVar($matches[3]);
        return $first . ' ' . $matches[2] . ' ' . $thirst . '){ ?>';
    }

    protected function _complexe_if_replace($matches) {
        return '<?php if(' . $this->complexReplace($matches);
    }

    protected function _complexe_elseif_replace($matches) {
        return '<?php } elseif(' . $this->complexReplace($matches);
    }

    protected function _simple_if_replace($matches) {
        return '<?php if(' . $this->simpleReplaceVar($matches[1]) . '){ ?>';
    }

    protected function _simple_elseif_replace($matches) {
        return '<?php } elseif(' . $this->simpleReplaceVar($matches[1]) . '){ ?>';
    }

    protected function _include($matches) {
        $var = $matches[1];
        $file = $this->getVar($var, $this->data);
        if (file_exists($file)) {
            if (util::getFileExtension($file) === 'tpl') {
                $str = '$tpl = new Template(\'' . $file . '\'); echo $tpl->output();';
                return '<?php ' . $str . ' ?>';
            }
            return '<?php $core = core::getInstance(); include \'' . $file . '\'; ?>';
        }
    }

    protected function _callHook($matches) {
        $posAcc = strpos($matches[1], '[');
        $args = '';
        $name = $matches[1];
        if ($posAcc !== false) {
            $args = substr($name, $posAcc);
            $name = substr($name, 0, $posAcc);
        }
        $parts = explode('.', $name);
        $action = ($parts[0] === strtoupper('FILTER')) ? 'FILTER' : 'ACTION';
        $name = $parts[1];
        $args = str_replace('[', '', $args);
        $args = str_replace(']', '', $args);
        $argsParts = explode(',', $args);
        $content = '$this->getVar(\'' . $argsParts[0] . '\', $this->data)';
        if (count($argsParts) === 2) {
            // Content & Arguments
            $params = '$this->getVar(\'' . $argsParts[1] . '\', $this->data)';
        } else {
            $params = null;
        }
                
        if ($action === 'FILTER') {
            return '<?php echo core::getInstance()->executeHookFilter(\'' . $name . '\',' . $content . ',' . $params .'); ?>';
        } else {
            if ($params !== null) {
                return '<?php core::getInstance()->executeHookAction(\'' . $name . '\',[' . $content . ',' . $params .']); ?>';
            }
            return '<?php core::getInstance()->executeHookAction(\'' . $name . '\',' . $content .'); ?>';
        }
    }

    protected function _replace_for($matches) {
        $parts = explode(',', $matches[1]);
        if (count($parts) === 1) {
            return '<?php foreach ($this->getVar(\'' . $matches[2] . '\', $this->data) as $' . $matches[1] . '): $this->data[\'' . $matches[1] . '\' ] = $' . $matches[1] .
                '; ?>';
        } else {
            $parts[0] = trim($parts[0]);
            $parts[1] = trim($parts[1]);
            return '<?php foreach ($this->getVar(\'' . $matches[2] . '\', $this->data) as $' . $parts[0] . ' => $' . $parts[1] . ' ): $this->data[\'' . $parts[0] . '\' ] = $' . $parts[0] .
                '; $this->data[\'' . $parts[1] . '\' ] = $' . $parts[1] . '; ?>';
        }
        
    }

    /**
     * Recursive method to get asked var, with capacity to determine children
     * like : parent.child.var
     * or : object.method[parameter1, parameter2]
     * or : array.object.propertie
     * ....
     *
     * @param string    Name of the asked var
     * @param mixed     Parent of the var
     * @return mixed    Asked var
     */
    protected function getVar($var, $parent) {
        $var = trim($var);
        $posAcc = strpos($var, '[');
        $args = '';
        if ($posAcc !== false) {
            $args = substr($var, $posAcc);
            $var = substr($var, 0, $posAcc);
        }
        $vars = explode(',', $var);
        if (count($vars)===1) {
            // One var only
        } else {
            $arr = [];
            foreach ($vars as $v) {
                $arr[] = $this->getVar($v, $this->data);
            }
            return $arr;
        }
        
        $parts = explode('.', $var);
        if (count($parts) === 1) {
            // No child
            return $this->getSubVar($var . $args, $parent);
        } else {
            // At least 1 child
            $name = array_shift($parts);
            if (!is_array($name) && !is_callable($name) && !is_object($name) && !isset($this->data[$name]) && !class_exists($name))
                return false;
            $new_parent = $this->getSubVar($name, $parent);
            $var = join('.', $parts) . $args;
            // call recursive
            return $this->getVar($var, $new_parent);
        }
    }

    /**
     * Determine and return if asked var is var, attribut or method if parent
     * is array or object
     *
     * @param string    Name of the asked var
     * @param mixed     Parent of the var
     * @return mixed    Asked var
     */
    protected function getSubVar($var, $parent) {
        if (is_array($parent) && isset($parent[$var])) {
            return $parent[$var];
        }
        if (isset($parent->$var)) {
            // Attribut
            return $parent->$var;
        }
        // Test if var contain parameters
        $manyArgs = false;
        preg_match('#\[(.+)\]#i', $var, $match);
        if (isset($match[1])) {
            $var = str_replace($match[0], "", $var);
            $args = true;
            $parts = explode(',', $match[1]);
            if (count($parts) > 1)
                $manyArgs = true;
        }
        if ($manyArgs) {
            $arrArgs = [];
            foreach ($parts as $part) {
                $arrArgs[] = $this->getVar(trim($part), $this->data);
            }
            $args = $arrArgs;
        } elseif (isset($args) && $args == true) {
            $args = $this->getVar($match[1], $this->data);
        }     
        if (is_object($parent) || (is_string($parent) && class_exists($parent))) {
            if (is_callable([$parent, $var])) {
                $rm = new \ReflectionMethod($parent, $var);
                if ($rm->isStatic()) {
                    if ($manyArgs)
                        return forward_static_call_array([$parent, $var], $args);
                    if (isset($args))
                        return forward_static_call_array([$parent, $var], [$args]);
                    return $parent::$var();
                }
                // Method
                if ($manyArgs)
                    return call_user_func_array([$parent, $var], $args);
                if (isset($args))
                    return call_user_func_array([$parent, $var], [$args]);
                return $parent->$var();
            }

            return false;
        }
        if (is_callable($var)) {
            // Function
            if ($manyArgs)
                return call_user_func_array($var, $args);
            if (isset($args))
                return call_user_func($var, $args);
            return call_user_func($var);
        }
        // Nothing
        if ($var === '') {
            // Only Args
            if ($manyArgs)
                return $args;
            return $args;
        }
        if (is_numeric($var)) {
            return (int) $var;
        }
        return $var;
    }

    /**
     * Add Globals vars to datas template
     */
    protected function addGlobalsToVars() {
        foreach (self::$const as $key => $value) {
            $this->data[$key] = $value;
        }
    }

}
