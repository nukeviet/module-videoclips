<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Thu, 20 Sep 2012 04:05:46 GMT
 */

if (!defined('NV_IS_UPDATE'))
    die('Stop!!!');

$nv_update_config = array();

// Kieu nang cap 1: Update; 2: Upgrade
$nv_update_config['type'] = 1;

// ID goi cap nhat
$nv_update_config['packageID'] = 'NVUDVIDEOCLIPS4300';

// Cap nhat cho module nao, de trong neu la cap nhat NukeViet, ten thu muc module neu la cap nhat module
$nv_update_config['formodule'] = 'videoclips';

// Thong tin phien ban, tac gia, ho tro
$nv_update_config['release_date'] = 1486936962;
$nv_update_config['author'] = 'NGUYEN ANH TU (anhtunguyen71@gmail.com)';
$nv_update_config['support_website'] = 'https://github.com/nukeviet/module-videoclips/tree/to-4.3.00';
$nv_update_config['to_version'] = '4.3.00';
$nv_update_config['allow_old_version'] = array('4.0.29', '4.1.00', '4.1.01', '4.2.01', '4.2.02', '4.2.03');

// 0:Nang cap bang tay, 1:Nang cap tu dong, 2:Nang cap nua tu dong
$nv_update_config['update_auto_type'] = 1;

$nv_update_config['lang'] = array();
$nv_update_config['lang']['vi'] = array();

// Tiếng Việt
$nv_update_config['lang']['vi']['nv_up_p1'] = 'Chuyển cấu hình sang CSDL';
$nv_update_config['lang']['vi']['nv_up_p2'] = 'Xóa dữ liệu thừa';
$nv_update_config['lang']['vi']['nv_up_finish'] = 'Đánh dấu phiên bản mới';

$nv_update_config['tasklist'] = array();
$nv_update_config['tasklist'][] = array(
    'r' => '4.2.03',
    'rq' => 1,
    'l' => 'nv_up_p1',
    'f' => 'nv_up_p1'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.3.00',
    'rq' => 1,
    'l' => 'nv_up_p2',
    'f' => 'nv_up_p2'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.3.00',
    'rq' => 1,
    'l' => 'nv_up_finish',
    'f' => 'nv_up_finish'
);

// Danh sach cac function
/*
Chuan hoa tra ve:
array(
'status' =>
'complete' =>
'next' =>
'link' =>
'lang' =>
'message' =>
);
status: Trang thai tien trinh dang chay
- 0: That bai
- 1: Thanh cong
complete: Trang thai hoan thanh tat ca tien trinh
- 0: Chua hoan thanh tien trinh nay
- 1: Da hoan thanh tien trinh nay
next:
- 0: Tiep tuc ham nay voi "link"
- 1: Chuyen sang ham tiep theo
link:
- NO
- Url to next loading
lang:
- ALL: Tat ca ngon ngu
- NO: Khong co ngon ngu loi
- LangKey: Ngon ngu bi loi vi,en,fr ...
message:
- Any message
Duoc ho tro boi bien $nv_update_baseurl de load lai nhieu lan mot function
Kieu cap nhat module duoc ho tro boi bien $old_module_version
*/

$array_modlang_update = array();

// Lay danh sach ngon ngu
$result = $db->query("SELECT lang FROM " . $db_config['prefix'] . "_setup_language WHERE setup=1");
while (list($_tmp) = $result->fetch(PDO::FETCH_NUM)) {
    $array_modlang_update[$_tmp] = array("lang" => $_tmp, "mod" => array());

    // Get all module
    $result1 = $db->query("SELECT title, module_data FROM " . $db_config['prefix'] . "_" . $_tmp . "_modules WHERE module_file=" . $db->quote($nv_update_config['formodule']));
    while (list($_modt, $_modd) = $result1->fetch(PDO::FETCH_NUM)) {
        $array_modlang_update[$_tmp]['mod'][] = array("module_title" => $_modt, "module_data" => $_modd);
    }
}

/**
 * nv_up_p1()
 *
 * @return
 *
 */
function nv_up_p1()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    foreach ($array_modlang_update as $lang => $array_mod) {
        foreach ($array_mod['mod'] as $module_info) {
            $table_prefix = $db_config['prefix'] . "_" . $lang . "_" . $module_info['module_data'];

            $configMods['idhomeclips'] = 0;
            $configMods['otherClipsNum'] = 16;
            $configMods['playerAutostart'] = 0;
            $configMods['playerSkin'] = 0;
            $configMods['playerMaxWidth'] = 640;
            $configMods['clean_title_video'] = 0;
            $configMods['commNum'] = 20;

            if (file_exists(NV_ROOTDIR . "/" . NV_DATADIR . "/config_module-" . $module_info['module_title'] . ".php")) {
                require (NV_ROOTDIR . "/" . NV_DATADIR . "/config_module-" . $module_info['module_title'] . ".php");
            }

            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET `config_value` = '" . $configMods['idhomeclips'] . "' WHERE `lang` = '" . $lang . "' AND  `module` = '" . $module_info['module_title'] . "' AND `config_name` = 'idhomeclips'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'idhomeclips', '" . $configMods['idhomeclips'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value='" . $configMods['otherClipsNum'] . "' WHERE config_name='otherClipsNum' AND module='" . $module_info['module_title'] . "' AND lang='" . $lang . "'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'otherClipsNum', '" . $configMods['otherClipsNum'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value='" . $configMods['playerAutostart'] . "' WHERE config_name='playerAutostart' AND module='" . $module_info['module_title'] . "' AND lang='" . $lang . "'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'playerAutostart', '" . $configMods['playerAutostart'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value='" . $configMods['playerSkin'] . "' WHERE config_name='playerSkin' AND module='" . $module_info['module_title'] . "' AND lang='" . $lang . "'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'playerSkin', '" . $configMods['playerSkin'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value='" . $configMods['playerMaxWidth'] . "' WHERE config_name='playerMaxWidth' AND module='" . $module_info['module_title'] . "' AND lang='" . $lang . "'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'playerMaxWidth', '" . $configMods['playerMaxWidth'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET `config_value` = '" . $configMods['clean_title_video'] . "' WHERE `lang` = '" . $lang . "' AND  `module` = '" . $module_info['module_title'] . "' AND `config_name` = 'clean_title_video'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'clean_title_video', '" . $configMods['clean_title_video'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET `config_value` = '" . $configMods['commNum'] . "' WHERE `lang` = '" . $lang . "' AND  `module` = '" . $module_info['module_title'] . "' AND `config_name` = 'commNum'");
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'commNum', '" . $configMods['commNum'] . "')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            try {
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'allowattachcomm', '0')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_info['module_title'] . "', 'alloweditorcomm', '0')");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }

            // Xóa file thừa sinh ra do cấu hình vào file
            @nv_deletefile(NV_ROOTDIR . '/' . NV_DATADIR . '/config/config_module-' . $module_info['module_title'] . '.php');
        }
    }

    return $return;
}

/**
 * nv_up_p2()
 *
 * @return
 */
function nv_up_p2()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    // Xóa file thừa do một số site cập nhật sớm
    @nv_deletefile(NV_ROOTDIR . '/themes/default/css/fonts');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/css/flexslider.css');
    @nv_deletefile(NV_ROOTDIR . '/themes/default/css/jquery.flexslider.js');

    // Xóa file thừa bản 4.2.03
    @nv_deletefile(NV_ROOTDIR . '/modules/videoclips/admin/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/videoclips/blocks/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/videoclips/funcs/.htaccess');
    @nv_deletefile(NV_ROOTDIR . '/modules/videoclips/language/.htaccess');

    return $return;
}

/**
 * nv_up_finish()
 *
 * @return
 *
 */
function nv_up_finish()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $nv_update_config;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    try {
        $num = $db->query("SELECT COUNT(*) FROM " . $db_config['prefix'] . "_setup_extensions WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'")->fetchColumn();
        $version = $nv_update_config['to_version'] . " " . $nv_update_config['release_date'];

        if (!$num) {
            $db->query("INSERT INTO " . $db_config['prefix'] . "_setup_extensions (
                id, type, title, is_sys, is_virtual, basename, table_prefix, version, addtime, author, note
            ) VALUES (
                64, 'module', 'videoclips', 0, 1, 'Videoclips', 'videoclips', '" . $nv_update_config['to_version'] . " " . $nv_update_config['release_date'] . "', " . NV_CURRENTTIME . ", '" . $nv_update_config['author'] . "',
                'Module Video clips được viết hoàn toàn mới, đạt các chuẩn code mới nhất của NukeViet, tương thích với nhiều giao diện web khác nhau và đặc biệt: Tính năng gọn nhẹ, đủ dùng'
            )");
        } else {
            $db->query("UPDATE " . $db_config['prefix'] . "_setup_extensions SET
                id=64,
                version='" . $version . "',
                author='" . $nv_update_config['author'] . "'
            WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'");
        }
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
    }

    $nv_Cache->delAll(true);
    return $return;
}
