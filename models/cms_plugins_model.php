<?php

class Cms_plugins_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        global $con;

        $plugins = array();
        $query_q = 'SELECT * FROM cms_plugins
                    ORDER BY sortorder ASC';
        $query   = mysqli_query($con,$query_q);
        while ($row = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
            $plugin = array();

            $plugin['id']        = $row['id'];
            $plugin['name']      = $row['name'];
            $plugin['location']  = $row['location'];
            $plugin['type']      = $row['type'];
            $plugin['sortorder'] = $row['sortorder'];

            array_push($plugins, $plugin);
        }
        return $plugins;
    }

    public function add() {

        if (isset($_POST['name'])) {

            global $con;

            $eqapp = new Apps();

            $getnumC_q          = 'SELECT * FROM cms_plugins';
            $getnumC_r          = mysqli_query($getnumC_q);
            $getnumC_n          = mysqli_num_rows($getnumC_r);
            $_POST['sortorder'] = $getnumC_n + 1;

            $result = mysqli_query($con,"SHOW TABLE STATUS LIKE 'cms_plugins'");
            $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
            $latest = $row['Auto_increment'];

            $eqapp->insertSql("cms_plugins");

            header('Location:' . _EQROOT_ . 'cms_plugins/edit/' . $latest);
        }
    }

    public function edit($id) {

        if (isset($_POST['name'])) {

            $eqapp = new Apps();

            $eqapp->udpdateSql("cms_plugins");

            header('Location:' . _EQROOT_ . 'cms_plugins/edit/' . $_POST['id']);

        } else {

            global $con;
            $query_q = 'SELECT * FROM cms_plugins
                        WHERE
                        id = "' . $id . '"';
            $query   = mysqli_query($con,$query_q);
            if (mysqli_num_rows($query) == 0) {
                header('Location:' . _EQROOT_ . 'cms_plugins');
            }
            $row = mysqli_fetch_array($query,MYSQLI_ASSOC);
            return $row;
        }
    }

    public function delete($id) {

        global $con;
        $query_q = 'DELETE FROM cms_plugins
                    WHERE
                    id = "' . $id . '"';
        $query   = mysqli_query($con,$query_q);

        $query_q = 'SELECT * FROM cms_plugin_structure
                    WHERE
                    pluginid = "' . $id . '"';

        $query = mysqli_query($con,$query_q);
        while ($row = mysqli_fetch_array($query,MYSQLI_ASSOC)) {

            $query2_q = 'SELECT * FROM cms_fieldsets
                         WHERE
                         com_struc_id = "' . $row['id'] . '"';
            $query2   = mysqli_query($con,$query2_q);
            while ($row2 = mysqli_fetch_array($query2,MYSQLI_ASSOC)) {

                $deleteR_q = 'DELETE FROM cms_records
                              WHERE
                              fieldsetid = "' . $row2['id'] . '"';
                $deleteR   = mysqli_query($con,$deleteR_q);
            }
            $deleteF_q = 'DELETE FROM cms_fieldsets
                          WHERE
                          com_struc_id = "' . $row['id'] . '"';
            $deleteF   = mysqli_query($con,$deleteF_q);
        }
        $deleteC_q = 'DELETE FROM cms_plugin_structure
                      WHERE
                      pluginid = "' . $id . '"';
        $deleteC   = mysqli_query($con,$deleteC_q);
        header('Location:' . _EQROOT_ . 'cms_plugins');
    }

    public function fetchPluginStructureParents($pluginId, $pluginStructureId) {

        global $con;

        $pluginStructure = array();
        $getPs_q         = 'SELECT * FROM cms_plugin_structure
                            WHERE
                            pluginid = "' . $pluginId . '"
                            AND id <> "' . $pluginStructureId . '"
                            AND parentid = "0"';

        $getPs_r = mysqli_query($con,$getPs_q);
        while ($getPs = mysqli_fetch_array($getPs_r,MYSQLI_ASSOC)) {

            $pluginStruc             = array();
            $pluginStruc['id']       = $getPs['id'];
            $pluginStruc['pluginid'] = $getPs['pluginid'];
            $pluginStruc['name']     = $getPs['name'];
            array_push($pluginStructure, $pluginStruc);
        }
        return $pluginStructure;
    }

    public function pluginStructureInfo($pluginId, $pluginStrucId) {

        $getPs_q = 'SELECT * FROM cms_plugin_structure
                    WHERE
                    id = "' . $pluginStrucId . '"';

        $getPs_r = mysqli_query($con,$getPs_q);
        $getPs = mysqli_fetch_array($getPs_r,MYSQLI_ASSOC);

        $pluginStruc               = array();
        $pluginStruc['id']         = $getPs['id'];
        $pluginStruc['pluginid']   = $getPs['pluginid'];
        $pluginStruc['parentid']   = $getPs['parentid'];
        $pluginStruc['name']       = $getPs['name'];
        $pluginStruc['recordname'] = $getPs['recordname'];
        $pluginStruc['db_name']    = $getPs['db_name'];
        $pluginStruc['listorder']  = $getPs['listorder'];
        $pluginStruc['listsearch'] = $getPs['listsearch'];
        $pluginStruc['listfields'] = $getPs['listfields'];
        return $pluginStruc;
    }

    public function fieldsetInfo($pluginId,$pluginStrucId,$fieldsetId) {

        $fieldset_q = 'SELECT * FROM cms_fieldsets
                       WHERE
                       id = "'.$fieldsetId.'"';
        $fieldset_r = mysqli_query($con,$fieldset_q) or die(mysqli_error($con));
        $fieldset = mysqli_fetch_array($fieldset_r,MYSQLI_ASSOC);

        $fset               = array();
        $fset['id']         = $fieldset['id'];
        $fset['name']       = $fieldset['name'];
        $fset['pluginid']   = $pluginId;
        $fset['plugstruid'] = $pluginStrucId;
        return $fset;
    }

    public function recordsetInfo($pluginId,$pluginStrucId,$fieldsetId,$recordId) {

        global $con;
        $recordset_q = 'SELECT * FROM cms_records
                        WHERE
                        id = "'.$recordId.'"';
        $recordset_r = mysqli_query($con,$recordset_q) or die(mysqli_error($con));
        $recordset = mysqli_fetch_array($recordset_r,MYSQLI_ASSOC);

        $rset                = array();
        $rset['id']          = $recordset['id'];
        $rset['type']        = $recordset['type'];
        $rset['name']        = $recordset['name'];
        $rset['db_name']     = $recordset['db_name'];
        $rset['helper']      = $recordset['helper'];
        $rset['fkey']        = $recordset['fkey'];
        $rset['options']     = $recordset['options'];
        $rset['photoresize'] = $recordset['photoresize'];
        $rset['custom_url']  = $recordset['custom_url'];
        $rset['pluginid']    = $pluginId;
        $rset['plugstrucid'] = $pluginStrucId;
        $rset['fieldsetId']  = $fieldsetId;
        return $rset;
    }

    public function setPluginClassFolder($id) {

        mkdir('../plugins/classes/', 0777);
        mkdir('../plugins/classes/' . $id , 0777);
    }
}
