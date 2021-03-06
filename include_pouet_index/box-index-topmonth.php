<?
require_once("include_generic/sqllib.inc.php");
require_once("include_pouet/pouet-box.php");
require_once("include_pouet/pouet-prod.php");

class PouetBoxIndexTopMonth extends PouetBoxCachable {
  var $data;
  var $prods;
  function PouetBoxIndexTopMonth() {
    parent::__construct();
    $this->uniqueID = "pouetbox_topmonth";
    $this->title = "top of the month";

    $this->limit = 10;
  }

  function LoadFromCachedData($data) {
    $this->data = unserialize($data);
  }

  function GetCacheableData() {
    return serialize($this->data);
  }
  
  use PouetFrontPage;
  function SetParameters($data)
  {
    if (isset($data["limit"])) $this->limit = $data["limit"];
  }
  function GetParameterSettings()
  {
    return array(
      "limit" => array("name"=>"number of prods visible","default"=>10,"max"=>POUET_CACHE_MAX),
    );
  }

  function LoadFromDB() {
    $s = new BM_Query("prods");
    $s->AddOrder("(prods.views/((sysdate()-prods.addedDate)/100000)+prods.views)*prods.voteavg*prods.voteup DESC");
    $s->AddWhere("prods.addedDate > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $s->SetLimit(POUET_CACHE_MAX);
    $this->data = $s->perform();
    PouetCollectPlatforms($this->data);
  }

  function RenderBody() {
    echo "<ul class='boxlist'>\n";
    $n = 0;
    foreach($this->data as $p) {
      echo "<li>\n";
      $p->RenderAsEntry();
      echo "</li>\n";
      if (++$n == $this->limit) break;
    }
    echo "</ul>\n";
  }
  function RenderFooter() {
    echo "  <div class='foot'><a href='toplist.php?days=30'>more</a>...</div>\n";
    echo "</div>\n";
  }
};

$indexAvailableBoxes[] = "TopMonth";
?>
