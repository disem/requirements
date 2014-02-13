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
     * base64 representation of web/index.php with minified css included
     * use http://base64.ru/ for edit purposes
     * @var string
     */
    private $webView = 'Pz48IURPQ1RZUEUgaHRtbD48aHRtbCBsYW5nPSJlbiI+PGhlYWQ+PG1ldGEgY2hhcnNldD0idXRmLTgiLz48dGl0bGU+QXBwbGljYXRpb24gUmVxdWlyZW1lbnQgQ2hlY2tlcjwvdGl0bGU+PGxpbmsgcmVsPSJzdHlsZXNoZWV0IiBocmVmPSIvL25ldGRuYS5ib290c3RyYXBjZG4uY29tL2Jvb3RzdHJhcC8zLjEuMC9jc3MvYm9vdHN0cmFwLm1pbi5jc3MiPjwvaGVhZD48Ym9keT48ZGl2IGNsYXNzPSJjb250YWluZXIiPjxkaXYgY2xhc3M9ImhlYWRlciI+PGgxPkFwcGxpY2F0aW9uIFJlcXVpcmVtZW50IENoZWNrZXI8L2gxPjwvZGl2Pjxocj48ZGl2IGNsYXNzPSJjb250ZW50Ij48aDM+RGVzY3JpcHRpb248L2gzPjxwPiBUaGlzIHNjcmlwdCBjaGVja3MgaWYgdGhlIHNlcnZlciBpcyBydW5uaW5nIHRoZSByaWdodCB2ZXJzaW9uIG9mIFBIUCwgaWYgYXBwcm9wcmlhdGUgUEhQIGV4dGVuc2lvbnMgaGF2ZSBiZWVuIGxvYWRlZCwgYW5kIGlmIHBocC5pbmkgZmlsZSBzZXR0aW5ncyBhcmUgY29ycmVjdC4gPC9wPjxwPiBUaGVyZSBhcmUgdHdvIGtpbmRzIG9mIHJlcXVpcmVtZW50cyBiZWluZyBjaGVja2VkLiBNYW5kYXRvcnkgcmVxdWlyZW1lbnRzIGFyZSB0aG9zZSB0aGF0IGhhdmUgdG8gYmUgbWV0IHRvIGFsbG93IHByb2plY3Qgd29yayBhcyBleHBlY3RlZC4gVGhlcmUgYXJlIGFsc28gc29tZSBvcHRpb25hbCByZXF1aXJlbWVudHMgYmVlaW5nIGNoZWNrZWQgd2hpY2ggd2lsbCBzaG93IHlvdSBhIHdhcm5pbmcgd2hlbiB0aGV5IGRvIG5vdCBtZWV0LiA8L3A+PGgzPk15U1FMIGNoZWNrPC9oMz48P3BocCBpZihpc19zdHJpbmcoJHRoaXMtPmRiaCkpOj8+PGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtZGFuZ2VyIj48P3BocCBlY2hvICR0aGlzLT5kYmg7Pz48L2Rpdj48P3BocCBlbmRpZjs/Pjw/cGhwIGlmKGlzX29iamVjdCgkdGhpcy0+ZGJoKSk6Pz48ZGl2IGNsYXNzPSJhbGVydCBhbGVydC1zdWNjZXNzIj48c3Ryb25nPkNvbm5lY3Rpb24gZXN0YWJsaXNoZWQuPC9zdHJvbmc+PC9kaXY+PD9waHAgZW5kaWY7Pz48Zm9ybSBhY3Rpb249IiIgY2xhc3M9ImZvcm0taW5saW5lIiByb2xlPSJmb3JtIiBtZXRob2Q9InBvc3QiPjxkaXYgY2xhc3M9ImZvcm0tZ3JvdXAiPjxsYWJlbCBjbGFzcz0ic3Itb25seSIgZm9yPSJob3N0bmFtZSI+SG9zdG5hbWU8L2xhYmVsPjxpbnB1dCBjbGFzcz0iZm9ybS1jb250cm9sIiBpZD0iaG9zdG5hbWUiIG5hbWU9Imhvc3RuYW1lIiBwbGFjZWhvbGRlcj0iRW50ZXIgaG9zdG5hbWUiIHZhbHVlPSI8P3BocCBlY2hvICR0aGlzLT5ob3N0bmFtZTs/PiI+PC9kaXY+PGRpdiBjbGFzcz0iZm9ybS1ncm91cCI+PGxhYmVsIGNsYXNzPSJzci1vbmx5IiBmb3I9InVzZXJuYW1lIj5Vc2VybmFtZTwvbGFiZWw+PGlucHV0IGNsYXNzPSJmb3JtLWNvbnRyb2wiIGlkPSJ1c2VybmFtZSIgbmFtZT0idXNlcm5hbWUiIHBsYWNlaG9sZGVyPSJVc2VybmFtZSIgdmFsdWU9Ijw/cGhwIGVjaG8gJHRoaXMtPnVzZXJuYW1lOz8+Ij48L2Rpdj48ZGl2IGNsYXNzPSJmb3JtLWdyb3VwIj48bGFiZWwgY2xhc3M9InNyLW9ubHkiIGZvcj0icGFzc3dvcmQiPlBhc3N3b3JkPC9sYWJlbD48aW5wdXQgdHlwZT0icGFzc3dvcmQiIGNsYXNzPSJmb3JtLWNvbnRyb2wiIGlkPSJwYXNzd29yZCIgbmFtZT0icGFzc3dvcmQiIHBsYWNlaG9sZGVyPSJQYXNzd29yZCIgdmFsdWU9Ijw/cGhwIGVjaG8gJHRoaXMtPnBhc3N3b3JkOz8+Ij48L2Rpdj48YnV0dG9uIHR5cGU9InN1Ym1pdCIgY2xhc3M9ImJ0biBidG4tZGVmYXVsdCI+Q2hlY2s8L2J1dHRvbj48L2Zvcm0+PGgzPkNvbmNsdXNpb248L2gzPjw/cGhwIGlmKCRzdW1tYXJ5WydlcnJvcnMnXT4wKTo/PjxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWVycm9yIj48c3Ryb25nPlVuZm9ydHVuYXRlbHkgeW91ciBzZXJ2ZXIgY29uZmlndXJhdGlvbiBkb2VzIG5vdCBzYXRpc2Z5IHRoZSByZXF1aXJlbWVudHMgYnkgdGhpcyBhcHBsaWNhdGlvbi48YnI+UGxlYXNlIHJlZmVyIHRvIHRoZSB0YWJsZSBiZWxvdyBmb3IgZGV0YWlsZWQgZXhwbGFuYXRpb24uPC9zdHJvbmc+PC9kaXY+PD9waHAgZWxzZWlmKCRzdW1tYXJ5Wyd3YXJuaW5ncyddPjApOj8+PGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtaW5mbyI+PHN0cm9uZz5Zb3VyIHNlcnZlciBjb25maWd1cmF0aW9uIHNhdGlzZmllcyB0aGUgbWluaW11bSByZXF1aXJlbWVudHMgYnkgdGhpcyBhcHBsaWNhdGlvbi48YnI+UGxlYXNlIHBheSBhdHRlbnRpb24gdG8gdGhlIHdhcm5pbmdzIGxpc3RlZCBiZWxvdyBhbmQgY2hlY2sgaWYgeW91ciBhcHBsaWNhdGlvbiB3aWxsIHVzZSB0aGUgY29ycmVzcG9uZGluZyBmZWF0dXJlcy48L3N0cm9uZz48L2Rpdj48P3BocAogZWxzZTo/PjxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LXN1Y2Nlc3MiPjxzdHJvbmc+Q29uZ3JhdHVsYXRpb25zISBZb3VyIHNlcnZlciBjb25maWd1cmF0aW9uIHNhdGlzZmllcyBhbGwgcmVxdWlyZW1lbnRzLjwvc3Ryb25nPjwvZGl2Pjw/cGhwIGVuZGlmOz8+PGgzPkRldGFpbHM8L2gzPjx0YWJsZSBjbGFzcz0idGFibGUgdGFibGUtYm9yZGVyZWQiPjx0cj48dGg+TmFtZTwvdGg+PHRoPlJlc3VsdDwvdGg+PHRoPlZhbHVlPC90aD48dGg+UmVxdWlyZWQgQnk8L3RoPjx0aD5NZW1vPC90aD48L3RyPjw/cGhwIGZvcmVhY2goJHJlcXVpcmVtZW50cyBhcyAkcmVxdWlyZW1lbnQpOj8+PHRyIGNsYXNzPSI8P3BocCBlY2hvICRyZXF1aXJlbWVudFsnY29uZGl0aW9uJ10/J3N1Y2Nlc3MnOigkcmVxdWlyZW1lbnRbJ21hbmRhdG9yeSddPydlcnJvcic6J3dhcm5pbmcnKT8+Ij48dGQ+PD9waHAgZWNobyAkcmVxdWlyZW1lbnRbJ25hbWUnXTs/PjwvdGQ+PHRkPjxzcGFuIGNsYXNzPSJyZXN1bHQiPjw/cGhwIGVjaG8gJHJlcXVpcmVtZW50Wydjb25kaXRpb24nXT8nUGFzc2VkJzooJHJlcXVpcmVtZW50WydtYW5kYXRvcnknXT8nRmFpbGVkJzonV2FybmluZycpPz48L3NwYW4+PC90ZD48dGQ+PD9waHAgZWNobyAkcmVxdWlyZW1lbnRbJ3ZhbHVlJ107Pz48L3RkPjx0ZD48P3BocCBlY2hvICRyZXF1aXJlbWVudFsnYnknXTs/PjwvdGQ+PHRkPjw/cGhwIGVjaG8gJHJlcXVpcmVtZW50WydtZW1vJ107Pz48L3RkPjwvdHI+PD9waHAgZW5kZm9yZWFjaDs/PjwvdGFibGU+PC9kaXY+PGhyPjxkaXYgY2xhc3M9ImZvb3RlciI+PHA+U2VydmVyOiA8P3BocCBlY2hvICR0aGlzLT5nZXRTZXJ2ZXJJbmZvKCkuJyAnLiR0aGlzLT5nZXROb3dEYXRlKCk/PjwvcD48cD5CYXNlZCBvbiA8YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20veWlpc29mdC95aWkyLWZyYW1ld29yay9ibG9iL21hc3Rlci9yZXF1aXJlbWVudHMvWWlpUmVxdWlyZW1lbnRDaGVja2VyLnBocCIgcmVsPSJleHRlcm5hbCI+WWlpUmVxdWlyZW1lbnRDaGVja2VyPC9hPjwvcD48L2Rpdj48L2Rpdj48L2JvZHk+PC9odG1sPiA=';

    /**
     * Check the given requirements, collecting results into internal field.
     * This method can be invoked several times checking different requirement sets.
     * Use [[render()]] to get the results.
     *
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
        $this->renderViewFile($this->result);
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
     * @param string $version       required PHP extension version.
     * @param string $compare       comparison operator, by default '>='
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
     * @param string $a       first value.
     * @param string $b       second value.
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
            var_dump($postMaxSize, $uploadMaxFileSize, $max);
            $maxCheckResult = $this->compareByteSize($postMaxSize, $max, '<=') && $this->compareByteSize($uploadMaxFileSize, $max, '<=');
        } else {
            $maxCheckResult = true;
        }
        return ($minCheckResult && $maxCheckResult);
    }

    /**
     * Renders a view file.
     * This method includes the view file as a PHP script
     * and captures the display result if required.
     *
     * @param array $_data_ data to be extracted and made available to the view file
     *
     * @return string the rendering result. Null if the rendering result is not required.
     */
    private function renderViewFile($_data_ = null)
    {
        // we use special variable names here to avoid conflict when extracting data
        if (is_array($_data_)) {
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        } else {
            $data = $_data_;
        }
        //@debug
        //require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'index.php');
        eval(base64_decode($this->webView));

    }

    /**
     * Normalizes requirement ensuring it has correct format.
     *
     * @param array $requirement    raw requirement.
     * @param int   $requirementKey requirement key in the list.
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
                'by' => '<a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>, <a href="http://www.yiiframework.com/doc/api/CWsdlGenerator">CWsdlGenerator</a>',
            ),
            // Database :
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
            // Cache:
            array(
                'name' => 'Memcache extension',
                'mandatory' => false,
                'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
                'by' => '<a href="http://www.yiiframework.com/doc/api/CMemCache">CMemCache</a>',
                'memo' => extension_loaded('memcached') ? 'To use memcached set <a href="http://www.yiiframework.com/doc/api/CMemCache#useMemcached-detail">CMemCache::useMemcached</a> to <code>true</code>.' : ''
            ),
            // Crypt
            array(
                'name' => 'Mcrypt extension',
                'mandatory' => false,
                'condition' => extension_loaded('mcrypt'),
                'by' => '<a href="http://www.yiiframework.com/doc/api/CSecurityManager">CSecurityManager</a>',
                'memo' => 'Required by encrypt and decrypt methods.'
            ),
            // PHP extensions
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
            // PHP ini
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
