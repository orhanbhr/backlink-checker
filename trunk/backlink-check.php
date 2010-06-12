<?php
  /**
   * Backlink Checker Tool for checking the existance of backlinks
   * on a certain website. See Documentation for more information.
   *
   * @author Martin Albrecht <martin.albrecht@javacoffee.de>
   * @version 1.0
   * @link http://javacoffee.de
   *
   * LICENSE:
   * --------
   * This program is free software; you can redistribute it and/or modify it
   * under the terms of the GNU General Public License as published by the
   * Free Software Foundation; either version 2 of the License, or (at your
   * option) any later version.
   *
   * This program is distributed in the hope that it will be useful, but
   * WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
   * Public License for more details.
   *
   * You should have received a copy of the GNU General Public License along
   * with this program; if not, write to the
   *
   * Free Software Foundation, Inc.,
   * 59 Temple Place, Suite 330,
   * Boston, MA 02111-1307, USA.
   * ------------------------------------------------------------------------
   */

  // The Backlink-Checker class
  class BacklinkChecker {
    protected $SQLUSER = "";
    protected $SQLPASS = "";
    protected $SQLSERV = "localhost";
    protected $SQLDB = "";
    protected $sqlConn = NULL;
    protected $sqlDb = NULL;

    protected $SLEEPTIME = 1800;
    private $backlinks = array();
    private $backlinkCount = 0;
    private $stat_rounds = 1;

    // Constructor
    function BacklinkChecker() {
      // Set SQL connection, if not already done
      if( $this->sqlConn === NULL ) {
        $this->sqlConn = mysql_connect($this->SQLSERV, $this->SQLUSER, $this->SQLPASS);
        if( $this->sqlConn === FALSE ) {
          Error(mysql_error());
        } else {
          $this->sqlDb = mysql_select_db($this->SQLDB);
          if( $this->sqlDb === FALSE ) {
            Error(mysql_error);
          }
        }
      }
      // Load all backlinks
      $this->loadBacklinks();
      $this->Run();
    }

    /*
     * Get all backlinks from a database
     * @return associative array with the data
    */
    protected function loadBacklinks() {
      $this->backlinks = array();
      $res = mysql_query("SELECT * FROM `links`;", $this->sqlConn);
      if( !$res ) {
        Error(mysql_error());
      }

      while( $data = mysql_fetch_assoc($res) ) {
        $this->backlinks[] = $data;
      }
      $this->backlinkCount = sizeof($this->backlinks);
    }

    // General main loop
    protected function Run() {
      if( $this->backlinkCount > 0 ) {
        for(;;) {
          echo 'Round ',$this->stat_rounds,"...\n";
          $this->loadBacklinks();

          foreach($this->backlinks as $bl) {
            echo 'Checking ',$bl['url']," on ",$bl['host'],"... ";
            $buf = "";
            $buf = file_get_contents($bl['host']);

            if( strlen($buf) <= 0 ) {
              echo "FAILED - Got no data!\n";
              $query = "UPDATE `links` SET `active`=0, `lastupdate`='".time()."' WHERE `url`='".$bl['url']."' AND `host`='".$bl['host']."';";
              if( !mysql_query($query) ) {
                Error(mysql_error());
              }
            } else {
              $matches = array();
              // TODO better regex for url
              $s = array("/","-");
              $r = array("\/","\-");
              $url = str_replace($s,$r,$bl['url']);
              preg_match("/.*<a.*href=\"($url).*\".*>.*/", $buf, $matches);
              if( strlen($matches[1]) <= 0 ) {
                echo "FAILED - Not found!\n";
                $query = "UPDATE `links` SET `active`=0, `lastupdate`='".time()."' WHERE `url`='".$bl['url']."' AND `host`='".$bl['host']."';";
                if( !mysql_query($query) ) {
                  Error(mysql_error());
                }
              } else {
                echo "CHECK!\n";
                $query = "UPDATE `links` SET `lastseen`='".time()."', `active`=1, `lastupdate`='".time()."' WHERE `url`='".$bl['url']."' AND `host`='".$bl['host']."';";
                if( !mysql_query($query) ) {
                  Error(mysql_error());
                }
              }
            }
          }
          $this->printStats();
          $this->stat_rounds++;
          sleep($this->SLEEPTIME);
        }
      }
    }

    // Statistics
    protected function printStats() {
      echo "Stats:\n------\nRound: ",$this->stat_rounds,"\nBacklinks: ",$this->backlinkCount,"\n\n";
    }

    // Public function to Quit
    public function Quit() {
      if($this->sqlConn != NULL ) {
        mysql_close($this->sqlConn);
      }
      exit(0);
    }

    // Private error function
    private function Error($message) {
      if( $this->sqlConn != NULL ) {
        mysql_close($this->sqlConn);
      }
      echo "Error: $message!\n";
      exit(1);
    }
  };


  ##### START #####
  $blChecker = new BacklinkChecker();
  $blChecker->Quit();
?>