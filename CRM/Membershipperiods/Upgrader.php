<?php

class CRM_Membershipperiods_Upgrader extends CRM_Membershipperiods_Upgrader_Base {

  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }
  
  public function uninstall() {
    $this->executeSqlFile('sql/uninstall.sql');
  }  
  
}
