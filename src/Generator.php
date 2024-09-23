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
    static public function generate_file($stdio, $options)
    {
        $type = get_array_value($options, 'type');
        $item = get_array_value($options, 'item');
        $ml = get_array_value($options, 'ml');
        $migration_name = get_array_value($options, 'migration_name');
        
        $template_file = false;
        
        if (is_array($type)) {
            foreach($type as $item_type) {
                $single_file_options = $options;
                $options['type'] = $item_type;
                if (!self::generate_file($stdio, $options)) {
                    return false;
                    break;
                }
            }
            return true;
        }
        
        switch($type) {
            case 'model':
                $classname = ucfirst($item) . '_model';
                $blueprint_name = ucfirst($item) . '_blueprint';
                $files = array(
                    [
                        'element' => 'model',
                        'path' => APPPATH  . 'models/' . $classname . '.php',
                        'template' => 'model.txt'
                    ],
                    [
                        'element' => 'blueprint',
                        'path' => APPPATH  . 'models/blueprints/' . $blueprint_name . '.php',
                        'template' => 'blueprint.txt'
                    ],
                );
                break;
            
            case 'controller':
                $classname = ucfirst($item);
                $files = array(
                    [
                        'element' => 'controller',
                        'path' => APPPATH  . 'controllers/admin/' . $classname . '.php',
                        'template' => 'controller.txt'
                    ]
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
                $files = array(
                    [
                        'element' => 'add',
                        'path' => APPPATH  . 'views/admin/' . $item . '/' . $item . '_add.php',
                        'template' => 'add.txt'
                    ],
                    [
                        'element' => 'view',
                        'path' => APPPATH  . 'views/admin/' . $item . '/' . $item . '_view.php',
                        'template' => 'view.txt'
                    ],
                    [
                        'element' => 'list',
                        'path' => APPPATH  . 'views/admin/' . $item . '/' . $item . '_list.php',
                        'template' => 'list.txt'
                    ]
                );
                break;

            case "migration":
                if (!$migration_name) {
                    $migration_name = "model_$item";
                }
                
                $date = new \DateTime(date("Y-m-d H:i:s"), new \DateTimeZone('UTC'));
                $date->setTimezone(new \DateTimeZone('America/Toronto'));

                $class_filename = $date->format('YmdHis') . '_' . $migration_name;
                $classname = $migration_name;
                $files = array(
                    [
                        'element' => 'migration',
                        'path' => APPPATH  . "migrations/$class_filename.php",
                        'template' => 'Migration' . ($ml ? '_ml' : '') . '.txt'
                    ]
                );
                break;
            default:
                $stdio->errln(
                    "<<red>>Type $type is not recognized<<reset>>"
                );
                return false;
        }
        foreach($files as $file) {
            // check file exist
            if (file_exists($file['path'])) {
                $stdio->errln(
                    "<<red>>The file \"{$file['path']}\" already exists<<reset>>"
                );
                return false;
            }
            $template = file_get_contents(APPPATH . 'Commands/Generate/templates/' . $file['template']);
            
            $search = [
                '@@classname@@',
                '@@date@@',
                '@@item@@',
                '@@Item@@',
                '@@ml@@',
            ];
            $replace = [
                $classname,
                date('Y/m/d H:i:s'),
                str_contains($classname, 'model_') ? $item : 'item',
                ucfirst($item),
                ($ml ? 'true' : 'false'),
            ];

            $output = str_replace($search, $replace, $template);
            $generated = @file_put_contents($file['path'], $output, LOCK_EX);

            if ($generated !== false) {
                $stdio->outln('<<green>>Generated: ' . $file['path'] . '<<reset>>');
            } else {
                $stdio->errln(
                    "<<red>>Can't write to \"{$file['path']}\"<<reset>>"
                );
                return false;
            }
        }
        return true;
    }
}