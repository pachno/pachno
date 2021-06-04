<?php

    namespace pachno\core\framework\cli;

    use b2db\Core;
    use Error;
    use Exception;
    use pachno\core\entities\Module;
    use pachno\core\entities\tables\Scopes;
    use pachno\core\framework\Context;
    use pachno\core\framework\exceptions\ActionNotFoundException;
    use pachno\core\framework\exceptions\TemplateNotFoundException;

    /**
     * CLI command class
     *
     * @package pachno
     * @subpackage core
     */
    abstract class Command
    {

        public const COLOR_BLACK = 30;

        public const COLOR_RED = 31;

        public const COLOR_GREEN = 32;

        public const COLOR_YELLOW = 33;

        public const COLOR_BLUE = 34;

        public const COLOR_MAGENTA = 35;

        public const COLOR_CYAN = 36;

        public const COLOR_WHITE = 37;

        public const STYLE_DEFAULT = 0;

        public const STYLE_BOLD = 1;

        public const STYLE_UNDERLINE = 4;

        public const STYLE_BLINK = 5;

        public const STYLE_CONCEAL = 8;

        protected static $_available_commands = null;

        protected static $_provided_arguments = null;

        protected static $_named_arguments = [];

        /**
         * Specifies whether the output should be colored or not.
         *
         */
        protected static $_use_color_output = null;

        protected $_command_name = null;

        protected $_description = '';

        protected $_required_arguments = [];

        protected $_optional_arguments = [];

        protected $_module = null;

        protected $_scoped = false;

        final public function __construct($module = null)
        {
            $this->_module = $module;
            $this->_setup();
        }

        protected function _setup()
        {
        }

        public static function getAvailableCommands()
        {
            return self::$_available_commands;
        }

        public static function setAvailableCommands($available_commands)
        {
            if (self::$_available_commands !== null) {
                throw new Exception('You cannot change available commands');
            }
            self::$_available_commands = $available_commands;
        }

        public static function processArguments()
        {
            if (self::$_provided_arguments == null) {
                self::$_provided_arguments = [];
                foreach ($GLOBALS['argv'] as $cc => $argument) {
                    self::$_provided_arguments[$cc] = $argument;

                    $argument_parts = explode('=', $argument, 2);
                    if (count($argument_parts) == 2) {
                        $key = mb_substr($argument_parts[0], 2);
                        self::$_provided_arguments[$key] = $argument_parts[1];
                        if (!is_numeric($key)) {
                            self::$_named_arguments[$key] = $argument_parts[1];
                        }
                    }
                }
            }
        }

        public static function getCommandLineName()
        {
            return $GLOBALS['argv'][0];
        }

        public static function cliError($title, $exception, $include_sql_queries = false)
        {
            $trace_elements = null;
            if ($exception instanceof Exception || $exception instanceof Error) {
                if ($exception instanceof ActionNotFoundException) {
                    self::cli_echo("Could not find the specified action\n", 'white', 'bold');
                } elseif ($exception instanceof TemplateNotFoundException) {
                    self::cli_echo("Could not find the template file for the specified action\n", 'white', 'bold');
                } elseif ($exception instanceof \b2db\Exception) {
                    self::cli_echo("An exception was thrown in the B2DB framework\n", 'white', 'bold');
                } else {
                    self::cli_echo("An unhandled exception occurred:\n", 'white', 'bold');
                }
                echo self::cli_echo($exception->getMessage(), 'red', 'bold') . "\n";
                echo "\n";
                self::cli_echo("occured in\n");
                self::cli_echo($exception->getFile() . ', line ' . $exception->getLine(), 'blue', 'bold');
                echo "\n";
                echo "\n";
                self::cli_echo("Stack trace:\n");
                $trace_elements = $exception->getTrace();
            } else {
                if ($exception['code'] == 8) {
                    self::cli_echo('The following notice has stopped further execution:', 'white', 'bold');
                } else {
                    self::cli_echo('The following error occured:', 'white', 'bold');
                }
                echo "\n";
                echo "\n";
                self::cli_echo($title, 'red', 'bold');
                echo "\n";
                if (isset($exception['file']) && isset($exception['line'])) {
                    self::cli_echo("occured in\n");
                    self::cli_echo($exception['file'] . ', line ' . $exception['line'], 'blue', 'bold');
                    echo "\n";
                    echo "\n";
                }
                self::cli_echo("Backtrace:\n", 'white', 'bold');
                $trace_elements = debug_backtrace();
            }
            foreach ($trace_elements as $trace_element) {
                if (array_key_exists('class', $trace_element)) {
                    if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'pachno\core\framework\Context' && array_key_exists('function', $trace_element) && in_array($trace_element['function'], ['errorHandler', 'cliError']))
                        continue;
                    self::cli_echo($trace_element['class'] . $trace_element['type'] . $trace_element['function'] . '()');
                } elseif (array_key_exists('function', $trace_element)) {
                    self::cli_echo($trace_element['function'] . '()');
                } else {
                    self::cli_echo('unknown function');
                }
                echo "\n";
                if (array_key_exists('file', $trace_element)) {
                    self::cli_echo($trace_element['file'] . ', line ' . $trace_element['line'], 'blue', 'bold');
                } else {
                    self::cli_echo('unknown file', 'red', 'bold');
                }
                echo "\n";
            }
            if (class_exists('\\b2db\\Core') && $include_sql_queries) {
                echo "\n";
                $sqlhits = Core::getSQLHits();
                if (count($sqlhits)) {
                    self::cli_echo("SQL queries:\n", 'white', 'bold');
                    try {
                        $cc = 1;
                        foreach ($sqlhits as $details) {
                            self::cli_echo("(" . $cc++ . ") [");
                            $str = ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms';
                            self::cli_echo($str);
                            self::cli_echo("] from ");
                            self::cli_echo($details['filename'], 'blue');
                            self::cli_echo(", line ");
                            self::cli_echo($details['line'], 'white', 'bold');
                            self::cli_echo(":\n");
                            self::cli_echo("{$details['sql']}\n");
                        }
                        echo "\n";
                    } catch (Exception $e) {
                        self::cli_echo("Could not generate query list (there may be no database connection)", "red", "bold");
                    }
                }
            }
            echo "\n";

        }

        final public function execute()
        {
            $this->_processArguments();
            $this->_prepare();
            $this->do_execute();
        }

        final protected function _processArguments()
        {
            $cc = 1;
            foreach ($this->_required_arguments as $key => $argument) {
                $cc++;
                if ($this->hasProvidedArgument($key)) continue;
                if ($this->hasProvidedArgument($cc)) {
                    if (mb_substr(self::$_provided_arguments[$cc], 0, 2) == '--' && mb_substr(self::$_provided_arguments[$cc], 2, mb_strpos(self::$_provided_arguments[$cc], '=') - 1) != $key) continue;
                    self::$_provided_arguments[$key] = self::$_provided_arguments[$cc];
                    if (!is_numeric($key)) {
                        self::$_named_arguments[$key] = self::$_provided_arguments[$cc];
                    }
                    continue;
                }
            }
            foreach (self::$_provided_arguments as $key => $value) {
                $this->$key = $value;
            }
            $diff = array_diff(array_keys($this->_required_arguments), array_keys(self::$_named_arguments));
            if (count($diff)) {
                throw new Exception('Please include all required arguments. Missing arguments: ' . join(', ', $diff));
            }
            foreach ($this->_optional_arguments as $key => $argument) {
                $cc++;
                if ($this->hasProvidedArgument($key)) continue;
                if ($this->hasProvidedArgument($cc)) {
                    if (mb_substr(self::$_provided_arguments[$cc], 0, 2) == '--' && mb_substr(self::$_provided_arguments[$cc], 2, mb_strpos(self::$_provided_arguments[$cc], '=') - 1) != $key) continue;
                    self::$_provided_arguments[$key] = self::$_provided_arguments[$cc];
                    if (!is_numeric($key)) {
                        self::$_named_arguments[$key] = self::$_provided_arguments[$cc];
                    }
                    continue;
                }
            }
            if ($this->_scoped && array_key_exists('scope', self::$_named_arguments)) {
                $scope = Scopes::getTable()->selectById(self::$_named_arguments['scope']);
                $this->cliEcho("Using scope " . $scope->getID() . "\n");
                Context::setScope($scope);
            }
        }

        public function hasProvidedArgument($key)
        {
            return array_key_exists($key, self::$_provided_arguments);
        }

        public function cliEcho($text, $color = self::COLOR_WHITE, $style = self::STYLE_DEFAULT)
        {
            self::cli_echo($text, $color, $style);
        }

        public function cliLineUp($lines = 1)
        {
            self::cli_echo("\033[{$lines}A");
        }

        public function cliLineDown($lines = 1)
        {
            self::cli_echo("\033[{$lines}B");
        }

        public function cliMoveLeft($position = null)
        {
            $position = $position ?? 500;
            self::cli_echo("\033[{$position}D");
        }

        public function cliMoveRight($position = null)
        {
            $position = $position ?? 500;
            self::cli_echo("\033[{$position}C");
        }

        public function cliClearLine()
        {
            self::cli_echo("\033[K");
        }

        public function cliClearAll()
        {
            self::cli_echo("\033[2J");
        }

        public static function cli_echo($text, $color = 'white', $style = null)
        {
            if (self::useColorOutput() === true) {
                $fg_colors = ['black' => 29, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37];
                $op_format = ['bold' => 1, 'underline' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8];

                $color = (is_numeric($color)) ? $color : $fg_colors[$color];
                $return_text = "\033[" . $color;

                if ($style !== null) {
                    $style = (is_numeric($style)) ? $style : $op_format[$style];
                    $return_text .= ';' . $style;
                }

                $return_text .= "m" . $text . "\033[0m";
            } else {
                $return_text = $text;
            }

            echo $return_text;
            if (!empty(ob_get_status())) {
                ob_end_flush();
            }
        }

        /**
         * Checks if colored output should be used when outputting
         * messages to terminal.
         *
         * Color output needs to be disabled if user has explicitly
         * requested so, if the operating system is not supported, or
         * if output is not an interactive terminal (in case of
         * piping, for example).
         *
         *
         * @return bool
         *   true, if colored output should be used, false otherwise.
         */
        public static function useColorOutput()
        {
            // Perform check only if results have not been cached yet.
            if (self::$_use_color_output === null) {
                if (getenv("PACHNO_CLI_NO_COLOR") != 1 && self::getOS() !== 'OS_WIN' && self::getOS() !== 'OS_UNKNOWN' && function_exists('posix_isatty') && posix_isatty(STDOUT)) {
                    self::$_use_color_output = true;
                } else {
                    self::$_use_color_output = false;
                }
            }

            return self::$_use_color_output;
        }

        static public function getOS()
        {
            switch (true) {
                case stristr(PHP_OS, 'DAR'):
                    return 'OS_OSX';
                case stristr(PHP_OS, 'WIN'):
                    return 'OS_WIN';
                case stristr(PHP_OS, 'LINUX'):
                    return 'OS_LINUX';
                default :
                    return 'OS_UNKNOWN';
            }
        }

        protected function _prepare()
        {
        }

        abstract protected function do_execute();

        public function getDescription()
        {
            return $this->_description;
        }

        public function getCommandName()
        {
            return $this->_command_name;
        }

        public function getProvidedArgument($key, $default_value = null)
        {
            return (array_key_exists($key, self::$_provided_arguments)) ? self::$_provided_arguments[$key] : $default_value;
        }

        public function getRequiredArguments()
        {
            return $this->_required_arguments;
        }

        public function getOptionalArguments()
        {
            return $this->_optional_arguments;
        }

        public function getProvidedArguments()
        {
            return self::$_provided_arguments;
        }

        public function getNamedArguments()
        {
            return self::$_named_arguments;
        }

        public function getCommandAliases()
        {
            return [];
        }

        public function askToAccept()
        {
            return $this->getInputConfirmation();
        }

        public function getInputConfirmation()
        {
            $input = self::_getCliInput();

            return (bool)(mb_strtolower(trim($input)) == 'yes');
        }

        protected static function _getCliInput()
        {
            return trim(fgets(STDIN));
        }

        public function askToDecline()
        {
            $input = self::_getCliInput();

            return !(bool)(mb_strtolower(trim($input)) == 'no');
        }

        public function getInput($default = '')
        {
            return self::getUserInput($default);
        }

        public static function getUserInput($default = '')
        {
            $input = self::_getCliInput();

            return ($input == '') ? $default : $input;
        }

        public function pressEnterToContinue()
        {
            fgets(STDIN);
        }

        /**
         * Return the associated module for this command if any
         *
         * @return Module
         */
        final protected function getModule()
        {
            return $this->_module;
        }

        protected function setScoped($val = true)
        {
            $this->_scoped = $val;
            if ($this->_scoped) {
                $this->addOptionalArgument('scope', 'The scope to work with (uses default scope if not provided)');
            }
        }

        protected function addOptionalArgument($argument, $description = null)
        {
            $this->_optional_arguments[$argument] = $description;
        }

        protected function addRequiredArgument($argument, $description = null)
        {
            $this->_required_arguments[$argument] = $description;
        }
    }
