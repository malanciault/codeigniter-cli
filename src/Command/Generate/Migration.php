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
class Migration extends Command
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
    public function __invoke($type, $classname)
    {
        if ($classname === null) {
            $this->stdio->errln(
                '<<red>>Classname is needed<<reset>>'
            );
            $this->stdio->errln(
                '  eg, generate migration CreateUserTable'
            );
            return Status::USAGE;
        }

        if (!\Kenjis\CodeIgniter_Cli\Generator::generate_file($this->stdio, ['migration'], $classname)) {
            return Status::FAILURE;
        }
    }

    private function generateFilename($migration_path, $migration_type, $classname)
    {
        if ($migration_type === 'sequential') {
            $migrations = [];

            // find max version
            foreach (glob($migration_path . '*_*.php') as $file) {
                $name = basename($file, '.php');

                if (preg_match('/^\d{3}_(\w+)$/', $name)) {
                    $number = sscanf($name, '%[0-9]+', $number) ? $number : '0';
                    $migrations[] = $number;
                }
            }

            $version = 0;
            if ($migrations !== []) {
                $version = max($migrations);
            }

            return $migration_path . sprintf('%03d', ++$version) . '_' . $classname . '.php';
        }

        return $migration_path . date('YmdHis') . '_' . $classname . '.php';
    }
}
