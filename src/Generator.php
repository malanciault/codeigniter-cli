<?php
/**
 * Part of Cli for CodeIgniter
 *
 * @package Threelci
 * @author  illuxi
 * @copyright   Copyright (c) 2016 - 2022, illuxi (https://illuxi.com)
 * @license Threel License
 * @link    https://illuxi.com
 * @since   Version 1.0.0
 * @filesource
 */

namespace Kenjis\CodeIgniter_Cli;

class Generator
{
    static public function generate_file($stdio, $type, $item, $migration_name = '')
    {
        if (is_array($type)) {
            foreach($type as $item_type) {
                if (!self::generate_file($stdio, $item_type, $item)) {
                    return false;
                    break;
                }
            }
            return true;
        }
        switch($type) {
            case 'model':
                $classname = ucfirst($item) . '_model';
                $files = array(
                    $file_path = APPPATH  . 'models/' . $classname . '.php'
                );
                break;
            
            case 'controller':
                $classname = ucfirst($item);
                $files = array(
                    APPPATH  . 'controllers/admin/' . $classname . '.php'
                );
                break;

            case 'view':
                $classname = $item;
                mkdir(APPPATH  . 'views/admin/' . $item);
                $files = array(
                    APPPATH  . 'views/admin/' . $item . '/' . $item . '_add.php',
                    APPPATH  . 'views/admin/' . $item . '/' . $item . '_list.php',
                    APPPATH  . 'views/admin/' . $item . '/' . $item . '_view.php',
                );
                break;

            case "migration":
                if (!$migration_name) {
                    $migration_name = "model_$item";
                }
                $classname = date('YmdHis') . '_' . $migration_name;
                $files = array(
                    APPPATH  . "migrations/$classname.php"
                );
                break;
        }
        foreach($files as $file_path) {
            // check file exist
            if (file_exists($file_path)) {
                $stdio->errln(
                    "<<red>>The file \"$file_path\" already exists<<reset>>"
                );
                return false;
            }

            $template = file_get_contents(__DIR__ . '/Command/Generate/templates/' . ucfirst($type) . '.txt');
            $search = [
                '@@classname@@',
                '@@date@@',
                '@@item@@',
                '@@Item@@',
            ];
            $replace = [
                $classname,
                date('Y/m/d H:i:s'),
                $item,
                ucfirst($item),
            ];
            $output = str_replace($search, $replace, $template);
            $generated = @file_put_contents($file_path, $output, LOCK_EX);

            if ($generated !== false) {
                $stdio->outln('<<green>>Generated: ' . $file_path . '<<reset>>');
            } else {
                $stdio->errln(
                    "<<red>>Can't write to \"$file_path\"<<reset>>"
                );
                return false;
            }
        }
        return true;
    }
}
