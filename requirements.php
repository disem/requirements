<?php
if (version_compare(PHP_VERSION, '4.3', '<')) {
    echo 'At least PHP 4.3 is required to run this script!';
    exit(1);
}

/**
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
    public $webView = 'Pz4KPCFET0NUWVBFIGh0bWw+CjxodG1sIGxhbmc9ImVuIj4KPGhlYWQ+CiAgICA8bWV0YSBjaGFyc2V0PSJ1dGYtOCIvPgogICAgPHRpdGxlPkFwcGxpY2F0aW9uIFJlcXVpcmVtZW50IENoZWNrZXI8L3RpdGxlPgogICAgPGxpbmsgcmVsPSJzdHlsZXNoZWV0IiBocmVmPSIvL25ldGRuYS5ib290c3RyYXBjZG4uY29tL2Jvb3RzdHJhcC8zLjEuMC9jc3MvYm9vdHN0cmFwLm1pbi5jc3MiPgo8L2hlYWQ+Cjxib2R5Pgo8ZGl2IGNsYXNzPSJjb250YWluZXIiPgogICAgPGRpdiBjbGFzcz0iaGVhZGVyIj4KICAgICAgICA8aDE+QXBwbGljYXRpb24gUmVxdWlyZW1lbnQgQ2hlY2tlcjwvaDE+CiAgICA8L2Rpdj4KICAgIDxocj4KCiAgICA8ZGl2IGNsYXNzPSJjb250ZW50Ij4KICAgICAgICA8aDM+RGVzY3JpcHRpb248L2gzPgogICAgICAgIDxwPgogICAgICAgICAgICBUaGlzIHNjcmlwdCBjaGVja3MgaWYgdGhlIHNlcnZlciBpcyBydW5uaW5nIHRoZSByaWdodCB2ZXJzaW9uIG9mIFBIUCwKICAgICAgICAgICAgaWYgYXBwcm9wcmlhdGUgUEhQIGV4dGVuc2lvbnMgaGF2ZSBiZWVuIGxvYWRlZCwgYW5kIGlmIHBocC5pbmkgZmlsZSBzZXR0aW5ncyBhcmUgY29ycmVjdC4KICAgICAgICA8L3A+CiAgICAgICAgPHA+CiAgICAgICAgICAgIFRoZXJlIGFyZSB0d28ga2luZHMgb2YgcmVxdWlyZW1lbnRzIGJlaW5nIGNoZWNrZWQuIE1hbmRhdG9yeSByZXF1aXJlbWVudHMgYXJlIHRob3NlIHRoYXQgaGF2ZSB0byBiZSBtZXQKICAgICAgICAgICAgdG8gYWxsb3cgcHJvamVjdCB3b3JrIGFzIGV4cGVjdGVkLiBUaGVyZSBhcmUgYWxzbyBzb21lIG9wdGlvbmFsIHJlcXVpcmVtZW50cyBiZWVpbmcgY2hlY2tlZCB3aGljaCB3aWxsCiAgICAgICAgICAgIHNob3cgeW91IGEgd2FybmluZyB3aGVuIHRoZXkgZG8gbm90IG1lZXQuCiAgICAgICAgPC9wPgogICAgICAgIDxoMz5NeVNRTCBjaGVjazwvaDM+CiAgICAgICAgPD9waHAgaWYgKGlzX3N0cmluZygkdGhpcy0+ZGJoKSk6ID8+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciI+CiAgICAgICAgICAgICAgICA8P3BocCBlY2hvICR0aGlzLT5kYmg7ID8+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgIDw/cGhwIGVuZGlmOyA/PgoKICAgICAgICA8P3BocCBpZiAoaXNfb2JqZWN0KCR0aGlzLT5kYmgpKTogPz4KICAgICAgICAgICAgPGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtc3VjY2VzcyI+CiAgICAgICAgICAgICAgICA8c3Ryb25nPkNvbm5lY3Rpb24gZXN0YWJsaXNoZWQuPC9zdHJvbmc+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgIDw/cGhwIGVuZGlmOyA/PgogICAgICAgIDxmb3JtIGFjdGlvbj0iIiBjbGFzcz0iZm9ybS1pbmxpbmUiIHJvbGU9ImZvcm0iIG1ldGhvZD0icG9zdCI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9ImZvcm0tZ3JvdXAiPgogICAgICAgICAgICAgICAgPGxhYmVsIGNsYXNzPSJzci1vbmx5IiBmb3I9Imhvc3RuYW1lIj5Ib3N0bmFtZTwvbGFiZWw+CiAgICAgICAgICAgICAgICA8aW5wdXQgY2xhc3M9ImZvcm0tY29udHJvbCIgaWQ9Imhvc3RuYW1lIiBuYW1lPSJob3N0bmFtZSIgcGxhY2Vob2xkZXI9IkVudGVyIGhvc3RuYW1lIgogICAgICAgICAgICAgICAgICAgICAgIHZhbHVlPSI8P3BocCBlY2hvICR0aGlzLT5ob3N0bmFtZTsgPz4iPgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0iZm9ybS1ncm91cCI+CiAgICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9InNyLW9ubHkiIGZvcj0idXNlcm5hbWUiPlVzZXJuYW1lPC9sYWJlbD4KICAgICAgICAgICAgICAgIDxpbnB1dCBjbGFzcz0iZm9ybS1jb250cm9sIiBpZD0idXNlcm5hbWUiIG5hbWU9InVzZXJuYW1lIiBwbGFjZWhvbGRlcj0iVXNlcm5hbWUiCiAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU9Ijw/cGhwIGVjaG8gJHRoaXMtPnVzZXJuYW1lOyA/PiI+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJmb3JtLWdyb3VwIj4KICAgICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz0ic3Itb25seSIgZm9yPSJwYXNzd29yZCI+UGFzc3dvcmQ8L2xhYmVsPgogICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9InBhc3N3b3JkIiBjbGFzcz0iZm9ybS1jb250cm9sIiBpZD0icGFzc3dvcmQiIG5hbWU9InBhc3N3b3JkIiBwbGFjZWhvbGRlcj0iUGFzc3dvcmQiCiAgICAgICAgICAgICAgICAgICAgICAgdmFsdWU9Ijw/cGhwIGVjaG8gJHRoaXMtPnBhc3N3b3JkOyA/PiI+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8YnV0dG9uIHR5cGU9InN1Ym1pdCIgY2xhc3M9ImJ0biBidG4tZGVmYXVsdCI+Q2hlY2s8L2J1dHRvbj4KICAgICAgICA8L2Zvcm0+CiAgICAgICAgPGgzPkNvbmNsdXNpb248L2gzPgogICAgICAgIDw/cGhwIGlmICgkc3VtbWFyeVsnZXJyb3JzJ10gPiAwKTogPz4KICAgICAgICAgICAgPGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtZXJyb3IiPgogICAgICAgICAgICAgICAgPHN0cm9uZz5VbmZvcnR1bmF0ZWx5IHlvdXIgc2VydmVyIGNvbmZpZ3VyYXRpb24gZG9lcyBub3Qgc2F0aXNmeSB0aGUgcmVxdWlyZW1lbnRzIGJ5IHRoaXMKICAgICAgICAgICAgICAgICAgICBhcHBsaWNhdGlvbi48YnI+UGxlYXNlIHJlZmVyIHRvIHRoZSB0YWJsZSBiZWxvdyBmb3IgZGV0YWlsZWQgZXhwbGFuYXRpb24uPC9zdHJvbmc+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgIDw/cGhwIGVsc2VpZiAoJHN1bW1hcnlbJ3dhcm5pbmdzJ10gPiAwKTogPz4KICAgICAgICAgICAgPGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtaW5mbyI+CiAgICAgICAgICAgICAgICA8c3Ryb25nPllvdXIgc2VydmVyIGNvbmZpZ3VyYXRpb24gc2F0aXNmaWVzIHRoZSBtaW5pbXVtIHJlcXVpcmVtZW50cyBieSB0aGlzIGFwcGxpY2F0aW9uLjxicj5QbGVhc2UgcGF5CiAgICAgICAgICAgICAgICAgICAgYXR0ZW50aW9uIHRvIHRoZSB3YXJuaW5ncyBsaXN0ZWQgYmVsb3cgYW5kIGNoZWNrIGlmIHlvdXIgYXBwbGljYXRpb24gd2lsbCB1c2UgdGhlIGNvcnJlc3BvbmRpbmcKICAgICAgICAgICAgICAgICAgICBmZWF0dXJlcy48L3N0cm9uZz4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPD9waHAKICAgICAgICBlbHNlOiA/PgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJhbGVydCBhbGVydC1zdWNjZXNzIj4KICAgICAgICAgICAgICAgIDxzdHJvbmc+Q29uZ3JhdHVsYXRpb25zISBZb3VyIHNlcnZlciBjb25maWd1cmF0aW9uIHNhdGlzZmllcyBhbGwgcmVxdWlyZW1lbnRzLjwvc3Ryb25nPgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8P3BocCBlbmRpZjsgPz4KICAgICAgICA8aDM+RGV0YWlsczwvaDM+CiAgICAgICAgPHRhYmxlIGNsYXNzPSJ0YWJsZSB0YWJsZS1ib3JkZXJlZCI+CiAgICAgICAgICAgIDx0cj4KICAgICAgICAgICAgICAgIDx0aD5OYW1lPC90aD4KICAgICAgICAgICAgICAgIDx0aD5SZXN1bHQ8L3RoPgogICAgICAgICAgICAgICAgPHRoPlZhbHVlPC90aD4KICAgICAgICAgICAgICAgIDx0aD5SZXF1aXJlZCBCeTwvdGg+CiAgICAgICAgICAgICAgICA8dGg+TWVtbzwvdGg+CiAgICAgICAgICAgIDwvdHI+CiAgICAgICAgICAgIDw/cGhwIGZvcmVhY2ggKCRyZXF1aXJlbWVudHMgYXMgJHJlcXVpcmVtZW50KTogPz4KICAgICAgICAgICAgICAgIDx0ciBjbGFzcz0iPD9waHAgZWNobyAkcmVxdWlyZW1lbnRbJ2NvbmRpdGlvbiddID8gJ3N1Y2Nlc3MnIDogKCRyZXF1aXJlbWVudFsnbWFuZGF0b3J5J10gPyAnZXJyb3InIDogJ3dhcm5pbmcnKSA/PiI+CiAgICAgICAgICAgICAgICAgICAgPHRkPgogICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBlY2hvICRyZXF1aXJlbWVudFsnbmFtZSddOyA/PgogICAgICAgICAgICAgICAgICAgIDwvdGQ+CiAgICAgICAgICAgICAgICAgICAgPHRkPgogICAgICAgICAgICAgICAgICAgICAgICA8c3BhbgogICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3M9InJlc3VsdCI+PD9waHAgZWNobyAkcmVxdWlyZW1lbnRbJ2NvbmRpdGlvbiddID8gJ1Bhc3NlZCcgOiAoJHJlcXVpcmVtZW50WydtYW5kYXRvcnknXSA/ICdGYWlsZWQnIDogJ1dhcm5pbmcnKSA/Pjwvc3Bhbj4KICAgICAgICAgICAgICAgICAgICA8L3RkPgogICAgICAgICAgICAgICAgICAgIDx0ZD4KICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZWNobyAkcmVxdWlyZW1lbnRbJ3ZhbHVlJ107ID8+CiAgICAgICAgICAgICAgICAgICAgPC90ZD4KICAgICAgICAgICAgICAgICAgICA8dGQ+CiAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gJHJlcXVpcmVtZW50WydieSddOyA/PgogICAgICAgICAgICAgICAgICAgIDwvdGQ+CiAgICAgICAgICAgICAgICAgICAgPHRkPgogICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBlY2hvICRyZXF1aXJlbWVudFsnbWVtbyddOyA/PgogICAgICAgICAgICAgICAgICAgIDwvdGQ+CiAgICAgICAgICAgICAgICA8L3RyPgogICAgICAgICAgICA8P3BocCBlbmRmb3JlYWNoOyA/PgogICAgICAgIDwvdGFibGU+CiAgICA8L2Rpdj4KICAgIDxocj4KICAgIDxkaXYgY2xhc3M9ImZvb3RlciI+CiAgICAgICAgPHA+U2VydmVyOiA8P3BocCBlY2hvICR0aGlzLT5nZXRTZXJ2ZXJJbmZvKCkgLiAnICcgLiAkdGhpcy0+Z2V0Tm93RGF0ZSgpID8+PC9wPgogICAgICAgIDxwPkJhc2VkIG9uIDxhCiAgICAgICAgICAgICAgICBocmVmPSJodHRwczovL2dpdGh1Yi5jb20veWlpc29mdC95aWkyLWZyYW1ld29yay9ibG9iL21hc3Rlci9yZXF1aXJlbWVudHMvWWlpUmVxdWlyZW1lbnRDaGVja2VyLnBocCIKICAgICAgICAgICAgICAgIHJlbD0iZXh0ZXJuYWwiPllpaVJlcXVpcmVtZW50Q2hlY2tlcjwvYT48L3A+CiAgICA8L2Rpdj4KPC9kaXY+CjwvYm9keT4KPC9odG1sPgo=';

    /**
     * Check the given requirements, collecting results into internal field.
     * This method can be invoked several times checking different requirement sets.
     * Use [[render()]] to get the results.
     *
     * @return static self instance.
     */
    function check()
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
        return $this;
    }

    /**
     * Connects to mysql for further checks
     */
    public function initDB()
    {
        if (isset($_POST['hostname']) && isset($_POST['username']) && isset($_POST['password'])) {
            $this->hostname = $_POST['hostname'];
            $this->username = $_POST['username'];
            $this->password = $_POST['password'];
            setcookie("hostname", $this->hostname, time() + 3600);
            setcookie("username", $this->username, time() + 3600);
            setcookie("password", $this->password, time() + 3600);
        } elseif (isset($_COOKIE['hostname']) && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
            $this->hostname = $_COOKIE['hostname'];
            $this->username = $_COOKIE['username'];
            $this->password = $_COOKIE['password'];
            setcookie("hostname", $this->hostname, time() + 3600);
            setcookie("username", $this->username, time() + 3600);
            setcookie("password", $this->password, time() + 3600);
        }

        try {
            $this->dbh = new PDO("mysql:host=$this->hostname;", $this->username, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->dbh = $e->getMessage();
        }
    }

    /**
     * Renders the requirements check result.
     * The output will vary depending is a script running from web or from console.
     */
    function render()
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
    public function getMysqlVariable($variable)
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
    function checkPhpExtensionVersion($extensionName, $version, $compare = '>=')
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
    function checkPhpIniOn($name)
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
    function checkPhpIniOff($name)
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
    function compareByteSize($a, $b, $compare = '>=')
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
    function getByteSize($verboseSize)
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
    function checkUploadMaxFileSize($min = null, $max = null)
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
    function renderViewFile($_data_ = null)
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
    function normalizeRequirement($requirement, $requirementKey = 0)
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
    function usageError($message)
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
    function evaluateExpression($expression)
    {
        return eval('return ' . $expression . ';');
    }

    /**
     * Returns the server information.
     * @return string server information.
     */
    function getServerInfo()
    {
        $info = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
        return $info;
    }

    /**
     * Returns the now date if possible in string representation.
     * @return string now date.
     */
    function getNowDate()
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
                'by' => 'Mysql connections',
                'memo' => '"max_connections" should be at least 100 at my.cnf',

            )
        );
    }
}


$requirementsChecker = new RequirementChecker();
$requirementsChecker->check()->render();
