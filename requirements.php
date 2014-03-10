<?php
/**
 * Requirements checker base file.
 */

if (version_compare(PHP_VERSION, '5.0', '<')) {
    echo 'At least PHP 5.0 is required to run this script!';
    exit(1);
}

/**
 * RequirementChecker allows checking, if current system meets the requirements for running application.
 * Class RequirementChecker
 */
class RequirementChecker
{
    /**
     * @var database handler
     */
    public $dbh;
    /**
     * @var
     */
    public $hostname;
    /**
     * @var
     */
    public $username;
    /**
     * @var
     */
    public $password;

    /**
     * Check the given requirements, collecting results into internal field and rendering result.
     */
    public function check()
    {
        self::initDB();
        if (!isset($this->result) || !is_array($this->result)) {
            $this->result = array(
                'summary' => array(
                    'total' => 0,
                    'errors' => 0,
                    'warnings' => 0,
                ),
                'requirements' => array(),
            );
        }
        $requirements = self::getRequirements();
        foreach ($requirements as $key => $rawRequirement) {
            $requirement = $this->normalizeRequirement($rawRequirement, $key);
            $this->result['summary']['total']++;
            if (!$requirement['condition']) {
                if ($requirement['mandatory']) {
                    $requirement['error'] = true;
                    $requirement['warning'] = true;
                    $this->result['summary']['errors']++;
                } else {
                    $requirement['error'] = false;
                    $requirement['warning'] = true;
                    $this->result['summary']['warnings']++;
                }
            } else {
                $requirement['error'] = false;
                $requirement['warning'] = false;
            }
            $this->result['requirements'][] = $requirement;
        }
        $this->render();
    }

    /**
     * Connects to mysql for further checks
     */
    private function initDB()
    {
        if (isset($_POST['hostname']) && isset($_POST['username']) && isset($_POST['password'])) {
            $this->hostname = $_POST['hostname'];
            $this->username = $_POST['username'];
            $this->password = $_POST['password'];
        } elseif (isset($_COOKIE['hostname']) && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
            $this->hostname = $_COOKIE['hostname'];
            $this->username = $_COOKIE['username'];
            $this->password = $_COOKIE['password'];
        }
        $this->updateCookies();
        if (!empty($this->hostname) && !empty($this->username)) {
            try {
                $this->dbh = new PDO("mysql:host=$this->hostname;", $this->username, $this->password, array(
                    PDO::ATTR_TIMEOUT => "3",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ));
            } catch (PDOException $e) {
                $this->dbh = $e->getMessage();
            }
        } else {
            $this->dbh = 'No credentials provided';
        }
    }

    /**
     * Saves or update with new lifetime cookies for database access
     */
    private function updateCookies()
    {
        setcookie("hostname", $this->hostname, time() + 3600);
        setcookie("username", $this->username, time() + 3600);
        setcookie("password", $this->password, time() + 3600);
    }

    /**
     * Renders the requirements check result.
     * The output will vary depending is a script running from web or from console.
     */
    private function render()
    {
        if (!isset($this->result)) {
            $this->usageError('Nothing to render!');
        }
        $this->renderOutput($this->result);
    }

    /**
     * @param $variable
     *
     * @return bool|string
     */
    private function getMysqlVariable($variable)
    {
        if (is_object($this->dbh)) {
            $query = $this->dbh->prepare("SHOW VARIABLES LIKE :variable");
            $query->bindParam(':variable', $variable);
            $query->execute();
            $result = $query->fetchAll();
            if (isset($result[0])) {
                return $result[0]['Value'];
            }
        }
        return false;
    }

    /**
     * Checks if the given PHP extension is available and its version matches the given one.
     *
     * @param string $extensionName PHP extension name.
     * @param string $version required PHP extension version.
     * @param string $compare comparison operator, by default '>='
     *
     * @return boolean if PHP extension version matches.
     */
    private function checkPhpExtensionVersion($extensionName, $version, $compare = '>=')
    {
        if (!extension_loaded($extensionName)) {
            return false;
        }
        $extensionVersion = phpversion($extensionName);
        if (empty($extensionVersion)) {
            return false;
        }
        if (strncasecmp($extensionVersion, 'PECL-', 5) == 0) {
            $extensionVersion = substr($extensionVersion, 5);
        }
        return version_compare($extensionVersion, $version, $compare);
    }

    /**
     * Checks if PHP configuration option (from php.ini) is on.
     *
     * @param string $name configuration option name.
     *
     * @return boolean option is on.
     */
    private function checkPhpIniOn($name)
    {
        $value = ini_get($name);
        if (empty($value)) {
            return false;
        }
        return ((integer)$value == 1 || strtolower($value) == 'on');
    }

    /**
     * Checks if PHP configuration option (from php.ini) is off.
     *
     * @param string $name configuration option name.
     *
     * @return boolean option is off.
     */
    private function checkPhpIniOff($name)
    {
        $value = ini_get($name);
        if (empty($value)) {
            return true;
        }
        return (strtolower($value) == 'off');
    }

    /**
     * Compare byte sizes of values given in the verbose representation,
     * like '5M', '15K' etc.
     *
     * @param string $a first value.
     * @param string $b second value.
     * @param string $compare comparison operator, by default '>='.
     *
     * @return boolean comparison result.
     */
    private function compareByteSize($a, $b, $compare = '>=')
    {
        $compareExpression = '(' . $this->getByteSize($a) . $compare . $this->getByteSize($b) . ')';
        return $this->evaluateExpression($compareExpression);
    }

    /**
     * Gets the size in bytes from verbose size representation.
     * For example: '5K' => 5*1024
     *
     * @param string $verboseSize verbose size representation.
     *
     * @return integer actual size in bytes.
     */
    private function getByteSize($verboseSize)
    {
        if (empty($verboseSize)) {
            return 0;
        }
        if (is_numeric($verboseSize)) {
            return (integer)$verboseSize;
        }
        $sizeUnit = trim($verboseSize, '0123456789');
        $size = str_replace($sizeUnit, '', $verboseSize);
        $size = trim($size);
        if (!is_numeric($size)) {
            return 0;
        }
        switch (strtolower($sizeUnit)) {
            case 'kb':
            case 'k':
                return $size * 1024;
            case 'mb':
            case 'm':
                return $size * 1024 * 1024;
            case 'gb':
            case 'g':
                return $size * 1024 * 1024 * 1024;
            default:
                return 0;
        }
    }

    /**
     * Checks if upload max file size matches the given range.
     *
     * @param string|null $min verbose file size minimum required value, pass null to skip minimum check.
     * @param string|null $max verbose file size maximum required value, pass null to skip maximum check.
     *
     * @return boolean success.
     */
    private function checkUploadMaxFileSize($min = null, $max = null)
    {
        $postMaxSize = ini_get('post_max_size');
        $uploadMaxFileSize = ini_get('upload_max_filesize');
        if ($min !== null) {
            $minCheckResult = $this->compareByteSize($postMaxSize, $min, '>=') && $this->compareByteSize($uploadMaxFileSize, $min, '>=');
        } else {
            $minCheckResult = true;
        }
        if ($max !== null) {
            $maxCheckResult = $this->compareByteSize($postMaxSize, $max, '<=') && $this->compareByteSize($uploadMaxFileSize, $max, '<=');
        } else {
            $maxCheckResult = true;
        }
        return ($minCheckResult && $maxCheckResult);
    }

    /**
     * Renders output.
     * @param array $result
     */
    private function renderOutput($result)
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8"/>
            <title>Application Requirement Checker</title>
            <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
        </head>
        <body>
        <div class="container">
            <div class="header">
                <h1>Application Requirement Checker</h1>
            </div>
            <hr>
            <div class="content">
                <h3>Description</h3>

                <p>This script checks if the server is running the right version of PHP, if appropriate PHP extensions
                    have been loaded, and if php.ini file settings are correct. </p>

                <p> There are two kinds of requirements being checked. Mandatory requirements are those that have to be
                    met to allow project work as expected. There are also some optional requirements beeing checked
                    which will show you a warning when they do not meet. </p>

                <h3>MySQL check</h3>
                <?php if (is_string($this->dbh)): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->dbh; ?>
                    </div>
                <?php endif; ?>
                <?php if (is_object($this->dbh)): ?>
                    <div class="alert alert-success">
                        <strong>Connection established.</strong>
                    </div>
                <?php endif; ?>
                <form action="" class="form-inline" role="form" method="post">
                    <div class="form-group">
                        <label class="sr-only" for="hostname">Hostname</label>
                        <input class="form-control" id="hostname" name="hostname" placeholder="Hostname"
                               value="<?php echo $this->hostname; ?>">
                    </div>
                    <div class="form-group"><label class="sr-only" for="username">Username</label><input
                            class="form-control" id="username" name="username" placeholder="Username"
                            value="<?php echo $this->username; ?>"></div>
                    <div class="form-group"><label class="sr-only" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                               value="<?php echo $this->password; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Check</button>
                </form>
                <h3>Conclusion</h3>
                <?php if ($result['summary']['errors'] > 0): ?>
                    <div class="alert alert-error">
                        <strong>Unfortunately your server configuration does not satisfy the requirements by this
                            application.<br>Please refer to the table below for detailed explanation.
                        </strong>
                    </div>
                <?php elseif ($result['summary']['warnings'] > 0): ?>
                    <div class="alert alert-info">
                        <strong>Your server configuration satisfies the minimum requirements by this application.
                            <br>Please pay attention to the warnings listed below and check if your application will use
                            the corresponding features.</strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <strong>Congratulations! Your server configuration satisfies all requirements.</strong>
                    </div>
                <?php endif; ?>
                <h3>Details</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Result</th>
                        <th>Value</th>
                        <th>Required By</th>
                        <th>Memo</th>
                    </tr>
                    <?php foreach ($result['requirements'] as $requirement): ?>
                    <tr class="<?php echo $requirement['condition'] ? 'success' : ($requirement['mandatory'] ? 'error' : 'warning') ?>">
                        <td><?php echo $requirement['name']; ?></td>
                        <td><span
                                class="result"><?php echo $requirement['condition'] ? 'Passed' : ($requirement['mandatory'] ? 'Failed' : 'Warning') ?></span>
                        </td>
                        <td><?php echo $requirement['value']; ?></td>
                        <td><?php echo $requirement['by']; ?></td>
                        <td><?php echo $requirement['memo']; ?></td>
                        </tr><?php endforeach; ?>
                </table>
            </div>
            <hr>
            <div class="footer">
                <p>Server: <?php echo $this->getServerInfo() . ' ' . $this->getNowDate() ?></p>
            </div>
        </div>
        </body>
        </html>
    <?php
    }

    /**
     * Normalizes requirement ensuring it has correct format.
     *
     * @param array $requirement raw requirement.
     * @param int $requirementKey requirement key in the list.
     *
     * @return array normalized requirement.
     */
    private function normalizeRequirement($requirement, $requirementKey = 0)
    {
        if (!is_array($requirement)) {
            $this->usageError('Requirement must be an array!');
        }
        if (!array_key_exists('condition', $requirement)) {
            $this->usageError("Requirement '{$requirementKey}' has no condition!");
        } else {
            $evalPrefix = 'eval:';
            if (is_string($requirement['condition']) && strpos($requirement['condition'], $evalPrefix) === 0) {
                $expression = substr($requirement['condition'], strlen($evalPrefix));
                $requirement['condition'] = $this->evaluateExpression($expression);
            }
        }
        if (!array_key_exists('name', $requirement)) {
            $requirement['name'] = is_numeric($requirementKey) ? 'Requirement #' . $requirementKey : $requirementKey;
        }
        if (!array_key_exists('mandatory', $requirement)) {
            if (array_key_exists('required', $requirement)) {
                $requirement['mandatory'] = $requirement['required'];
            } else {
                $requirement['mandatory'] = false;
            }
        }
        if (!array_key_exists('by', $requirement)) {
            $requirement['by'] = 'Unknown';
        }
        if (!array_key_exists('memo', $requirement)) {
            $requirement['memo'] = '';
        }
        if (!array_key_exists('value', $requirement)) {
            $requirement['value'] = '';
        }
        return $requirement;
    }

    /**
     * Displays a usage error.
     * This method will then terminate the execution of the current application.
     *
     * @param string $message the error message
     */
    private function usageError($message)
    {
        echo "Error: $message\n\n";
        exit(1);
    }

    /**
     * Evaluates a PHP expression under the context of this class.
     *
     * @param string $expression a PHP expression to be evaluated.
     *
     * @return mixed the expression result.
     */
    private function evaluateExpression($expression)
    {
        return eval('return ' . $expression . ';');
    }

    /**
     * Returns the server information.
     * @return string server information.
     */
    private function getServerInfo()
    {
        $info = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        return $info;
    }

    /**
     * Returns the now date if possible in string representation.
     * @return string now date.
     */
    private function getNowDate()
    {
        $nowDate = @strftime('%Y-%m-%d %H:%M', time());
        return $nowDate;
    }

    /**
     * Requirements array. All checks goes here.
     * array(
     *         'name' => 'PHP Some Extension',
     *         'mandatory' => true,
     *         'condition' => extension_loaded('some_extension'),
     *         'value' => 'get_value',
     *         'by' => 'Some application feature',
     *         'memo' => 'PHP extension "some_extension" required',
     *     ),
     * @return array
     */
    private function getRequirements()
    {
        return array(
            array(
                'name' => 'PHP version',
                'mandatory' => true,
                'condition' => version_compare(PHP_VERSION, '5.3.0', '>='),
                'value' => phpversion(),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
                'memo' => 'PHP 5.3.0 or higher is required.',
            ),
            array(
                'name' => 'Reflection extension',
                'mandatory' => true,
                'condition' => class_exists('Reflection', false),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => 'PCRE extension',
                'mandatory' => true,
                'condition' => extension_loaded('pcre'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => 'SPL extension',
                'mandatory' => true,
                'condition' => extension_loaded('SPL'),
                'by' => '<a href="http://www.yiiframework.com">Yii Framework</a>',
            ),
            array(
                'name' => 'MBString extension',
                'mandatory' => true,
                'condition' => extension_loaded('mbstring'),
                'by' => '<a href="http://www.php.net/manual/en/book.mbstring.php">Multibyte string</a> processing',
                'memo' => 'Required for multibyte encoding string processing.'
            ),
            array(
                'name' => 'Intl extension',
                'mandatory' => false,
                'condition' => $this->checkPhpExtensionVersion('intl', '1.0.2', '>='),
                'by' => '<a href="http://www.php.net/manual/en/book.intl.php">Internationalization</a> support',
                'memo' => 'PHP Intl extension 1.0.2 or higher is required when you want to use advanced parameters formatting
in <code>Yii::t()</code>, <abbr title="Internationalized domain names">IDN</abbr>-feature of
<code>EmailValidator</code> or <code>UrlValidator</code> or the <code>yii\i18n\Formatter</code> class.'
            ),
            array(
                'name' => 'DOM extension',
                'mandatory' => true,
                'condition' => class_exists('DOMDocument', false),
                'by' => '<a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>, <a
    href="http://www.yiiframework.com/doc/api/CWsdlGenerator">CWsdlGenerator</a>',
            ),
            array(
                'name' => 'PDO extension',
                'mandatory' => true,
                'condition' => extension_loaded('pdo'),
                'by' => 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>',
            ),
            array(
                'name' => 'PDO MySQL extension',
                'mandatory' => true,
                'condition' => extension_loaded('pdo_mysql'),
                'by' => 'All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>',
                'memo' => 'Required for MySQL database.',
            ),
            array(
                'name' => 'Memcache extension',
                'mandatory' => false,
                'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
                'by' => '<a href="http://www.yiiframework.com/doc/api/CMemCache">CMemCache</a>',
                'memo' => extension_loaded('memcached') ? 'To use memcached set <a
    href="http://www.yiiframework.com/doc/api/CMemCache#useMemcached-detail">CMemCache::useMemcached</a> to
<code>true</code>.' : ''
            ),
            array(
                'name' => 'Mcrypt extension',
                'mandatory' => false,
                'condition' => extension_loaded('mcrypt'),
                'by' => '<a href="http://www.yiiframework.com/doc/api/CSecurityManager">CSecurityManager</a>',
                'memo' => 'Required by encrypt and decrypt methods.'
            ),
            array(
                'name' => 'Mbstring extension',
                'mandatory' => false,
                'condition' => extension_loaded('mbstring'),
                'by' => '<a href="http://www.php.net/manual/en/book.mbstring.php">Multibyte string</a> processing',
                'memo' => 'Required for multibyte encoding string processing.'
            ),
            array(
                'name' => 'Curl extension',
                'mandatory' => true,
                'condition' => extension_loaded('curl'),
                'by' => '<a href="http://en.wikipedia.org/wiki/Web_service">Web Services</a> usage',
                'memo' => 'Required if application uses Web Service or performs HTTP requests.'
            ),
            array(
                'name' => 'SimpleXml extension',
                'mandatory' => false,
                'condition' => extension_loaded('SimpleXml'),
                'by' => 'XML parsing',
                'memo' => 'Required if application parses XML.'
            ),
            'phpSmtp' => array(
                'name' => 'PHP mail SMTP',
                'mandatory' => false,
                'condition' => strlen(ini_get('SMTP')) > 0,
                'by' => 'Email sending',
                'memo' => 'PHP mail SMTP server required',
            ),
            'phpMemoryLimit' => array(
                'name' => 'PHP memory limit',
                'mandatory' => true,
                'condition' => ini_get('memory_limit') == -1 || $this->compareByteSize(ini_get('memory_limit'), '128M'),
                'by' => 'Processing requests',
                'memo' => '"memory_limit" should be at least 5M',
            ),
            'phpMaxPostSize' => array(
                'name' => 'Max POST size',
                'mandatory' => true,
                'condition' => $this->compareByteSize(ini_get('post_max_size'), '8M'),
                'by' => 'Send POST request',
                'memo' => '"post_max_size" should be at least 64M',
            ),
            'phpMaxInputVars' => array(
                'name' => 'Max input vars',
                'mandatory' => false,
                'condition' => (ini_get('max_input_vars') === false) || (ini_get('max_input_vars') >= 1000),
                'by' => 'Form submission',
                'memo' => '"max_input_vars" should be at least 1000 at php.ini',
            ),
            'phpFileUploads' => array(
                'name' => 'PHP file uploads',
                'mandatory' => false,
                'condition' => $this->checkPhpIniOn('file_uploads'),
                'by' => 'Upload files from web',
                'memo' => '"file_uploads" should be enabled at php.ini',
            ),
            'phpMaxFileUploads' => array(
                'name' => 'PHP file uploads',
                'mandatory' => false,
                'condition' => ini_get('max_file_uploads') >= 20,
                'by' => 'Multiply files upload from web',
                'memo' => '"max_file_uploads" should be at least 20 at php.ini',
            ),
            'phpUploadMaxFileSize' => array(
                'name' => 'Upload max file size',
                'mandatory' => false,
                'condition' => $this->checkUploadMaxFileSize('2M'),
                'by' => 'File uploading',
                'memo' => '"upload_max_filesize" and "post_max_size" should be at least 5M at php.ini',
            ),
            'mysqlMaxConnections' => array(
                'name' => 'Mysql Max Connections',
                'mandatory' => false,
                'condition' => $this->getMysqlVariable('max_connections') >= 100,
                'value' => $this->getMysqlVariable('max_connections'),
                'by' => 'Mysql',
                'memo' => '"max_connections" should be at least 100 at my.cnf',
            ),
            'mysqlMaxHeapTableSize' => array(
                'name' => 'Mysql Max Heap Table Size',
                'mandatory' => false,
                'condition' => $this->getMysqlVariable('max_heap_table_size') >= 16777216,
                'value' => $this->getMysqlVariable('max_heap_table_size'),
                'by' => 'Mysql',
                'memo' => 'Maximum size in rows for user-created MEMORY tables. Setting the variable while the server is active has no effect on existing tables unless they are recreated or altered. The smaller of max_heap_table_size and tmp_table_size also limits internal in-memory tables.',
            ),
            'mysqlTmpTableSize' => array(
                'name' => 'Mysql Tmp Table Size',
                'mandatory' => false,
                'condition' => $this->getMysqlVariable('tmp_table_size') >= 16777216,
                'value' => $this->getMysqlVariable('tmp_table_size'),
                'by' => 'Mysql',
                'memo' => 'The largest size for temporary tables in memory (not MEMORY tables) although if max_heap_table_size is smaller the lower limit will apply. If a table exceeds the limit, MariaDB converts it to a MyISAM or Aria table. You can see if it\'s necessary to increase by comparing the status variables Created_tmp_disk_tables and Created_tmp_tables to see how many temporary tables out of the total created needed to be converted to disk. Often complex GROUP BY queries are responsible for exceeding',
            ),
            'mysqlQueryCacheSize' => array(
                'name' => 'Mysql Query Cache Size',
                'mandatory' => false,
                'condition' => $this->getMysqlVariable('query_cache_size') >= 1,
                'value' => $this->getMysqlVariable('query_cache_size'),
                'by' => 'Mysql',
                'memo' => 'Size in bytes available to the query cache. About 40KB is needed for query cache structures, so setting a size lower than this will result in a warning. The default, 0, effectively disables the query cache.',
            ),
        );
    }
}

$requirementsChecker = new RequirementChecker();
$requirementsChecker->check();
