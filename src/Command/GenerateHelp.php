<?php
/**
 * Part of Cli for CodeIgniter
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-cli
 */

namespace Kenjis\CodeIgniter_Cli\Command;

use Aura\Cli\Help;

class GenerateHelp extends Help
{
    public function init()
    {
        $this->setSummary('Generate code.');
        $this->setUsage('migration <classname>');
        $this->setUsage([
            'mvc        <object_name>       Generate the model, without i18n, the admin controller, and the 3 default admin views: add, list, view',
            'mvc_i18n   <object_name>       Generate the model, with i18n, the admin controller, and the 3 default admin views: add, list, view',
            'model      <object_name>       Generate a new model without i18n',
            'model      <object_name>       Generate a new model with i18n',
            'migration  <migration_name>    Generate migration file skeleton',
        ]);
        $this->setDescr(
            '<<bold>>generate<<reset>> command generates code.' . PHP_EOL
            . '    Here are some examples:' . PHP_EOL
            . '        - "generate mvc certificate" will generate:' . PHP_EOL
            . '           - the certificate model' . PHP_EOL
            . '           - a new migration' . PHP_EOL
            . '           - the certificate admin controller as well as ' . PHP_EOL
            . '           - the admin views controller_list, controller_add and controller_view' . PHP_EOL
            . '        - "generate mvc_i18n certificate" will generate all the same files as previous command, but with i18n support' . PHP_EOL
            . '        - "generate model certificate" will generate a new model and a migration file' . PHP_EOL
            . '        - "generate model_i18n certificate" will generate the same as previous command but with i18n support' . PHP_EOL
            . '        - "generate migration Add_product_column" will generate a new migration file' . PHP_EOL
        );
    }
}
