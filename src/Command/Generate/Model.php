<?php
/**
 * Part of Cli for CodeIgniter
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-cli
 */

namespace Kenjis\CodeIgniter_Cli\Command\Generate;

use Aura\Cli\Stdio;
use Aura\Cli\Context;
use Aura\Cli\Status;
use Kenjis\CodeIgniter_Cli\Command\Command;
use CI_Controller;

/**
 * @property \CI_Loader $load
 * @property \CI_Config $config
 */
class Model extends Command
{
    public function __construct(Context $context, Stdio $stdio, CI_Controller $ci)
    {
        parent::__construct($context, $stdio, $ci);
        $this->load->config('migration');
    }

    /**
     * @param string $type
     * @param string $classname
     */
    public function __invoke($type, $item)
    {
        if ($item === null) {
            $this->stdio->errln(
                '<<red>>Model name is needed<<reset>>'
            );
            $this->stdio->errln(
                '  eg, generate model New_model'
            );
            return Status::USAGE;
        }
        
        // Create model
        $this->generate_file('model', $item);

        // Create model
        $this->generate_file('controller', $item);

        // Create view
        $this->generate_file('view', $item);
    }

    private function generate_file($type, $item)
    {
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
        }

        foreach($files as $file_path) {
            // check file exist
            if (file_exists($file_path)) {
                $this->stdio->errln(
                    "<<red>>The file \"$file_path\" already exists<<reset>>"
                );
                return Status::FAILURE;
            }

            $template = file_get_contents(__DIR__ . '/templates/' . ucfirst($type) . '.txt');
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
                $this->stdio->outln('<<green>>Generated: ' . $file_path . '<<reset>>');
            } else {
                $this->stdio->errln(
                    "<<red>>Can't write to \"$file_path\"<<reset>>"
                );
                return Status::FAILURE;
            }
        }
    }
}