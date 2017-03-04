<?php

class Apps {

    public function redirect($r){
        header("Location: ".$r);
    }

    public function insertSql($table) {

        global $con;

        $run_q = "INSERT INTO " . $table . " (";
        $c     = count($_POST);
        $x     = 0;
        foreach ($_POST as $name => $val) {
            $x++;
            $run_q .= htmlspecialchars($name);
            if ($x < $c) {
                $run_q .= ", ";
            }
        }

        $run_q .= ") VALUES (";
        $x = 0;
        foreach ($_POST as $name => $val) {
            $x++;
            $run_q .= "'" . htmlspecialchars($val) . "'";
            if ($x < $c) {
                $run_q .= ", ";
            }
        }
        $run_q .= ")";
        mysqli_query($con,$run_q) or die(mysql_error());
    }

    public function udpdateSql($table) {

        global $con;

        $query_q = "UPDATE " . $table . " set ";
        $c       = count($_POST) - 1;
        $x       = 0;
        foreach ($_POST as $name => $val) {
            if ($name != "id") {
                $x++;
                $query_q .= htmlspecialchars($name) . "='" . htmlspecialchars($val) . "'";
                if ($x < $c) {
                    $query_q .= ", ";
                }
            }
        }
        $query_q .= " WHERE id = '" . $_POST['id'] . "'";
        mysqli_query($con,$query_q) or die(mysqli_error($con));
    }


    public function paging($per_page, $total_results, $getName) {

        ///PAGING
        $limit    = $per_page;
        $start    = ($_GET[$getName] - 1) * $limit;
        $numpages = ceil($total_results / $limit);
        if ($numpages > 20) {
            $numpages = 20;
        }
        $lastpage = $_GET[$getName] - 1;
        $nextpage = $_GET[$getName] + 1;

        $url_prefix = $_SERVER['REQUEST_URI'];
        $url_prefix = str_replace($getName . "=" . $_GET[$getName], "", $url_prefix);
        if (!substr_count($url_prefix, "?")) {
            $url_prefix .= "?";
        } elseif (substr($url_prefix, -1, 1) <> "&" && substr($url_prefix, -1, 1) <> "?") {
            $url_prefix .= "&";
        }
        ob_start();
        if ($total_results > $limit) {

          if ($_GET[$getName] == 1) {
                $class = "disabled";
                $onclick = "onclick=\"return false;\"";
            }
          if ($lastpage > 1) {
                $href = $getName.'='.$lastpage;
            }

        $html = "<div class=\"pagination\">\n";
		$html .= "<ul class=\"pages\">\n";
		$html .= "<li class=\"prev\"><a class=\"direct " .$class.  "\" href=\"" . $url_prefix . "\" ".$onclick.">&lt;</a></li>\n";
            // figure out what pages to show (first, last, current, current+2, current-2)
            $showpages   = array();
            $showpages[] = 1;
            $showpages[] = $_GET[$getName];
            $showpages[] = $numpages;
            $showpages[] = $lastpage;
            $showpages[] = $lastpage - 1;
            $showpages[] = $nextpage;
            $showpages[] = $nextpage + 1;
            $showpages[] = $nextpage + 2;
            for ($i = 1; $i <= $numpages; $i++) {
                if (in_array($i, $showpages) || $numpages <= 5 || true) {
                    if ($i <> 1 && $lasti <> $i - 1) {
						$html .= "<div class=\"hidden\">...</div>\n";
                    }
                    $lasti = $i;
					$html .= "<li><a href=\"" . $url_prefix .($i > 1 ? $getName . "=" . $i : "" ) . "\" " .($_GET[$getName] == $i ? 'class="active"' : 'class=""'). ">" . $i . "</a></li>\n";
                }
            }
			$html .= "<li class=\"next\"><a href=\" " .$url_prefix ;
            if ($nextpage <= $numpages) {
                $html .= $getName ."=" . $nextpage;
            }
            $html .= " class=\"direct arrow";
            if ($_GET[$getName] == $numpages) {
                $html .= " disabled";
            }
            $html .= "\"";
            if ($_GET[$getName] == $numpages) {
                $html .= "onclick=\"return false;\"";
            }
                $html .= ">&gt;</a></li>\n";
			$html .= "</ul>\n";
			$html .= "</div>\n";
        }
    }


    public function rrmdir($dir) {

        $eqApp = new Apps();

        if (is_dir($dir)) {
            $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir."/".$object) == "dir") {
                            $eqApp->rrmdir($dir."/".$object);
                        }
                        else {
                            unlink($dir."/".$object);
                        }
                    }
                }
            reset($objects);
            rmdir($dir);
        }
    }


    public function get_field_options($rec, $p) {

        //create dynamic variables
        global ${"record_required" . $p};
        global ${"record_disabled" . $p};
        global ${"record_email" . $p};
        global ${"record_select_vals" . $p};
        global ${"record_styles" . $p};
        global ${"record_maxlength" . $p};
        global ${"record_fkeyoptions" . $p};
        global ${"record_fkeyvalue" . $p};
        global ${"record_fkeytype" . $p};
        global ${"record_custom_url" . $p};
        global ${"record_resize_image" . $p};

        $selOptions = array();
        $selOption  = explode("\n", $rec); //split options by indent '\n'

        for ($x = 0; $x < count($selOption); $x++) {

            if (strpos($selOption[$x], '=') !== false) { //make sure '=' exists in the string, of each line...
                $singleOptions       = explode("=", $selOption[$x]);
                $singleOption["key"] = $singleOptions[0];
                $singleOption["val"] = $singleOptions[1];
                array_push($selOptions, $singleOption);
            }

        }

        for ($x = 0; $x < count($selOptions); $x++) {

            if (strtolower($selOptions[$x]['key']) == "required") {
                ${"record_required" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "disabled") {
                ${"record_disabled" . $p} = trim($selOptions[$x]['val']);
            }
            if ($selOptions[$x]['key'] == "email") {
                ${"record_email" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "options") {
                ${"record_select_vals" . $p} = explode("|", $selOptions[$x]['val']);
                ${"record_select_vals" . $p} = array_map('trim', ${"record_select_vals" . $p});
            }
            if (strtolower($selOptions[$x]['key']) == "style") {
                ${"record_styles" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "maxlength") {
                ${"record_maxlength" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "fkeyoptions") {
                ${"record_fkeyoptions" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "fkeyvalue") {
                ${"record_fkeyvalue" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "fkeytype") {
                ${"record_fkeytype" . $p} = trim($selOptions[$x]['val']);
            }
            if (strtolower($selOptions[$x]['key']) == "custom_url") {
                ${"record_custom_url" . $p} = trim($selOptions[$x]['val']);
            }
        }
        $recordInfo['record_required']     = ${"record_required" . $p};
        $recordInfo['record_disabled']     = ${"record_disabled" . $p};
        $recordInfo['record_email']        = ${"record_email" . $p};
        $recordInfo['record_maxlength']    = ${"record_maxlength" . $p};
        $recordInfo['record_select_vals']  = ${"record_select_vals" . $p};
        $recordInfo['record_styles']       = ${"record_styles" . $p};
        $recordInfo['record_fkeyoptions']  = ${"record_fkeyoptions" . $p};
        $recordInfo['record_fkeyvalue']    = ${"record_fkeyvalue" . $p};
        $recordInfo['record_fkeytype']     = ${"record_fkeytype" . $p};
        $recordInfo['record_custom_url']   = ${"record_custom_url" . $p};
        $recordInfo['record_resize_image'] = ${"record_resize_image" . $p};

        return $recordInfo;
    }


    /*
     * The permaLink function creates a clean custom url
     * from a string that is entered
     */
    public function permaLink($str) {

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_| -]+/", '-', $clean);

        return $clean;
    }

    public function url_prefix($url, $key, $value) {

        $url_prefix = $url;
        if (isset($_GET[$key])) {
            $url_prefix = str_replace($key . "=" . $_GET[$key], "", $url_prefix);
        }
        if (!substr_count($url_prefix, "?")) {
            $url_prefix .= "?";
        } elseif (substr($url_prefix, -1, 1) <> "&" && substr($url_prefix, -1, 1) <> "?") {
            $url_prefix .= "&";
        }
        return $url_prefix .= $key . "=" . $value;
    }



    public function api($recordset, $api=false, $id=false, $recordid=false, $per_page=4, $page=1, $where=false, $order=false, $print=false){

        global $con;

        $query_q = 'SELECT r.db_name "name"
                    FROM cms_records r, cms_plugin_structure p, cms_fieldsets f
                    WHERE r.fieldsetid = f.id
                    AND f.com_struc_id = p.id
                    AND p.db_name =  "' . $recordset . '"';
        $query_r = mysqli_query($con,$query_q);
        $recordId = array();
        while($d = mysqli_fetch_array($query_r,MYSQLI_ASSOC)) {
             $recordId[] = $d['name'];
        }
        $recordId[] = 'id';
        $recordId[] = 'pluginid';
        $recordId[] = 'recordset';
        $recordId[] = 'recordid';
        $recordId[] = 'sortorder';
        $recordId[] = 'timestamp';
        $recordId[] = 'search_index';
        $recordId[] = 'meta_title';
        $recordId[] = 'meta_description';
        $recordId[] = 'meta_keywords';

        if (isset($_GET['page'])){
            $page = intval($_GET['page']);
            if($page < 1) $page = 1;
        } else {
            $_GET['page'] = 1;
        }
        $start_from = ($page - 1) * $per_page;
        $query2_q = 'SELECT SQL_CALC_FOUND_ROWS * FROM cms_content WHERE recordset = "' . $recordset .'"';
        if(count($where) && $where[0] != '') {
            $g = 0;
            foreach($where as $val) {
                $g++;
                $query2_q .= ( $g > 0 ? ' AND ':' ') . $val;
            }
        }
        if($id) { $query2_q .= ' AND id = "' . $id . '"'; }
        if($recordid) { $query2_q .= ' AND recordid = "' . $recordid . '"'; }
        if($order && $order != ''){
            $query2_q .= ' ORDER BY ' . $order;
        } else {
            $query2_q .= ' ORDER BY timestamp DESC';
        }
        $query2_q .= ' LIMIT ' . $start_from.', ' . $per_page;
        if($print) { echo $query2_q; }
        $query2_r = mysqli_query($con,$query2_q) or die(mysqli_error($con));
        $data = array();
        while($query2 = mysqli_fetch_array($query2_r,MYSQLI_ASSOC)) {
            $d = array();
            foreach($recordId as $record) {
                $d[$record] = $query2[$record];
            }
            array_push($data,$d);
        }
        if($api) {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function reservedSqlWords() {

        $reservedSqlWords = array(
            'id',
            'pluginid',
            'recordset',
            'recordid',
            'sortorder',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'timestamp',
            'search_index',
            'url',
            'accessible',
            'add',
            'all',
            'alter',
            'analyze',
            'and',
            'as',
            'asc',
            'asensitive',
            'before',
            'between',
            'bigint',
            'binary',
            'blob',
            'both',
            'by',
            'call',
            'cascade',
            'case',
            'change',
            'char',
            'character',
            'check',
            'collate',
            'column',
            'condition',
            'connection',
            'constraint',
            'continue',
            'convert',
            'create',
            'cross',
            'current_date',
            'current_time',
            'current_timestamp',
            'current_user',
            'cursor',
            'database',
            'databases',
            'day_hour',
            'day_microsecond',
            'day_minute',
            'day_second',
            'dec',
            'decimal',
            'declare',
            'default',
            'delayed',
            'delete',
            'desc',
            'describe',
            'deterministic',
            'distinct',
            'distinctrow',
            'div',
            'double',
            'drop',
            'dual',
            'each',
            'else',
            'elseif',
            'enclosed',
            'escaped',
            'exists',
            'exit',
            'explain',
            'false',
            'fetch',
            'float',
            'float4',
            'float8',
            'for',
            'force',
            'foreign',
            'from',
            'fulltext',
            'goto',
            'grant',
            'group',
            'having',
            'high_priority',
            'hour_microsecond',
            'hour_minute',
            'hour_second',
            'if',
            'ignore',
            'in',
            'index',
            'infile',
            'inner',
            'inout',
            'insensitive',
            'insert',
            'int',
            'int1',
            'int2',
            'int3',
            'int4',
            'int8',
            'integer',
            'interval',
            'into',
            'is',
            'iterate',
            'join',
            'key',
            'keys',
            'kill',
            'label',
            'leading',
            'leave',
            'left',
            'like',
            'limit',
            'linear',
            'lines',
            'load',
            'localtime',
            'localtimestamp',
            'lock',
            'long',
            'longblob',
            'longtext',
            'loop',
            'low_priority',
            'master_ssl_verify_server_cert',
            'match',
            'mediumblob',
            'mediumint',
            'mediumtext',
            'middleint',
            'minute_microsecond',
            'minute_second',
            'mod',
            'modifies',
            'natural',
            'no_write_to_binlog',
            'not',
            'null',
            'numeric',
            'on',
            'optimize',
            'option',
            'optionally',
            'or',
            'order',
            'out',
            'outer',
            'outfile',
            'precision',
            'primary',
            'procedure',
            'purge',
            'range',
            'read',
            'read_only',
            'read_write',
            'reads',
            'real',
            'references',
            'regexp',
            'release',
            'rename',
            'repeat',
            'replace',
            'require',
            'reserved',
            'restrict',
            'return',
            'revoke',
            'right',
            'rlike',
            'schema',
            'schemas',
            'second_microsecond',
            'select',
            'sensitive',
            'separator',
            'set',
            'show',
            'smallint',
            'spatial',
            'specific',
            'sql',
            'sql_big_result',
            'sql_calc_found_rows',
            'sql_small_result',
            'sqlexception',
            'sqlstate',
            'sqlwarning',
            'ssl',
            'starting',
            'straight_join',
            'table',
            'terminated',
            'then',
            'tinyblob',
            'tinyint',
            'tinytext',
            'to',
            'trailing',
            'trigger',
            'true',
            'undo',
            'union',
            'unique',
            'unlock',
            'unsigned',
            'update',
            'upgrade',
            'usage',
            'use',
            'using',
            'utc_date',
            'utc_time',
            'utc_timestamp',
            'values',
            'varbinary',
            'varchar',
            'varcharacter',
            'varying',
            'when',
            'where',
            'while',
            'with',
            'write',
            'xor',
            'year_month',
            'zerofill',
            '__class__',
            '__compiler_halt_offset__',
            '__dir__',
            '__file__',
            '__function__',
            '__method__',
            '__namespace__',
            'abday_1',
            'abday_2',
            'abday_3',
            'abday_4',
            'abday_5',
            'abday_6',
            'abday_7',
            'abmon_1',
            'abmon_10',
            'abmon_11',
            'abmon_12',
            'abmon_2',
            'abmon_3',
            'abmon_4',
            'abmon_5',
            'abmon_6',
            'abmon_7',
            'abmon_8',
            'abmon_9',
            'abstract',
            'alt_digits',
            'am_str',
            'array',
            'assert_active',
            'assert_bail',
            'assert_callback',
            'assert_quiet_eval',
            'assert_warning',
            'break',
            'case_lower',
            'case_upper',
            'catch',
            'cfunction',
            'char_max',
            'class',
            'clone',
            'codeset',
            'connection_aborted',
            'connection_normal',
            'connection_timeout',
            'const',
            'count_normal',
            'count_recursive',
            'credits_all',
            'credits_docs',
            'credits_fullpage',
            'credits_general',
            'credits_group',
            'credits_modules',
            'credits_qa',
            'credits_sapi',
            'crncystr',
            'crypt_blowfish',
            'crypt_ext_des',
            'crypt_md5',
            'crypt_salt_length',
            'crypt_std_des',
            'currency_symbol',
            'd_fmt',
            'd_t_fmt',
            'day_1',
            'day_2',
            'day_3',
            'day_4',
            'day_5',
            'day_6',
            'day_7',
            'decimal_point',
            'default_include_path',
            'die',
            'directory_separator',
            'do',
            'e_all',
            'e_compile_error',
            'e_compile_warning',
            'e_core_error',
            'e_core_warning',
            'e_deprecated',
            'e_error',
            'e_notice',
            'e_parse',
            'e_strict',
            'e_user_deprecated',
            'e_user_error',
            'e_user_notice',
            'e_user_warning',
            'e_warning',
            'echo',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'ent_compat',
            'ent_noquotes',
            'ent_quotes',
            'era',
            'era_d_fmt',
            'era_d_t_fmt',
            'era_t_fmt',
            'era_year',
            'eval',
            'extends',
            'extr_if_exists',
            'extr_overwrite',
            'extr_prefix_all',
            'extr_prefix_if_exists',
            'extr_prefix_invalid',
            'extr_prefix_same',
            'extr_skip',
            'final',
            'foreach',
            'frac_digits',
            'function',
            'global',
            'grouping',
            'html_entities',
            'html_specialchars',
            'implements',
            'include',
            'include_once',
            'info_all',
            'info_configuration',
            'info_credits',
            'info_environment',
            'info_general',
            'info_license',
            'info_modules',
            'info_variables',
            'ini_all',
            'ini_perdir',
            'ini_system',
            'ini_user',
            'instanceof',
            'int_curr_symbol',
            'int_frac_digits',
            'interface',
            'isset',
            'lc_all',
            'lc_collate',
            'lc_ctype',
            'lc_messages',
            'lc_monetary',
            'lc_numeric',
            'lc_time',
            'list',
            'lock_ex',
            'lock_nb',
            'lock_sh',
            'lock_un',
            'log_alert',
            'log_auth',
            'log_authpriv',
            'log_cons',
            'log_crit',
            'log_cron',
            'log_daemon',
            'log_debug',
            'log_emerg',
            'log_err',
            'log_info',
            'log_kern',
            'log_local0',
            'log_local1',
            'log_local2',
            'log_local3',
            'log_local4',
            'log_local5',
            'log_local6',
            'log_local7',
            'log_lpr',
            'log_mail',
            'log_ndelay',
            'log_news',
            'log_notice',
            'log_nowait',
            'log_odelay',
            'log_perror',
            'log_pid',
            'log_syslog',
            'log_user',
            'log_uucp',
            'log_warning',
            'm_1_pi',
            'm_2_pi',
            'm_2_sqrtpi',
            'm_e',
            'm_ln10',
            'm_ln2',
            'm_log10e',
            'm_log2e',
            'm_pi',
            'm_pi_2',
            'm_pi_4',
            'm_sqrt1_2',
            'm_sqrt2',
            'mon_1',
            'mon_10',
            'mon_11',
            'mon_12',
            'mon_2',
            'mon_3',
            'mon_4',
            'mon_5',
            'mon_6',
            'mon_7',
            'mon_8',
            'mon_9',
            'mon_decimal_point',
            'mon_grouping',
            'mon_thousands_sep',
            'n_cs_precedes',
            'n_sep_by_space',
            'n_sign_posn',
            'namespace',
            'negative_sign',
            'new',
            'noexpr',
            'nostr',
            'old_function',
            'p_cs_precedes',
            'p_sep_by_space',
            'p_sign_posn',
            'path_separator',
            'pathinfo_basename',
            'pathinfo_dirname',
            'pathinfo_extension',
            'pear_extension_dir',
            'pear_install_dir',
            'php_bindir',
            'php_config_file_path',
            'php_config_file_scan_dir',
            'php_datadir',
            'php_debug',
            'php_eol',
            'php_extension_dir',
            'php_extra_version',
            'php_int_max',
            'php_int_size',
            'php_libdir',
            'php_localstatedir',
            'php_major_version',
            'php_maxpathlen',
            'php_minor_version',
            'php_os',
            'php_output_handler_cont',
            'php_output_handler_end',
            'php_output_handler_start',
            'php_prefix',
            'php_release_version',
            'php_sapi',
            'php_shlib_suffix',
            'php_sysconfdir',
            'php_version',
            'php_version_id',
            'php_windows_nt_domain_controller',
            'php_windows_nt_server',
            'php_windows_nt_workstation',
            'php_windows_version_build',
            'php_windows_version_major',
            'php_windows_version_minor',
            'php_windows_version_platform',
            'php_windows_version_producttype',
            'php_windows_version_sp_major',
            'php_windows_version_sp_minor',
            'php_windows_version_suitemask',
            'php_zts',
            'pm_str',
            'positive_sign',
            'print',
            'private',
            'protected',
            'public',
            'radixchar',
            'require_once',
            'seek_cur',
            'seek_end',
            'seek_set',
            'sort_asc',
            'sort_desc',
            'sort_numeric',
            'sort_regular',
            'sort_string',
            'static',
            'str_pad_both',
            'str_pad_left',
            'str_pad_right',
            'switch',
            't_fmt',
            't_fmt_ampm',
            'thousands_sep',
            'thousep',
            'throw',
            'try',
            'unset',
            'var',
            'yesexpr',
            'yesstr'
        );

        return $reservedSqlWords;
    }

    public function iset($str,$type='GET') {

        switch($type){

            case $type=='GET':
                if(isset($_GET[$str])){
                    return $_GET[$str];
                }
                break;
            case $type=='POST':
                if(isset($_GET[$str])){
                    return $_GET[$str];
                }
                break;

            default:
                return 'DNE';
        }

    }

}
?>
