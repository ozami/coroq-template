<?php
namespace Coroq;

class Template {
  protected $src = "";
  protected $data = [];
  protected $filters = [
    [__CLASS__, "filterPrintf"],
    [__CLASS__, "filterEcho"],
  ];

  public function __construct() {
  }

  public function getSource() {
    return $this->src;
  }

  public function setSource($src) {
    $this->src = $src;
    return $this;
  }

  public function getData() {
    return $data;
  }

  public function setData(array $data) {
    $this->data = $data;
    return $this;
  }


  public function render() {
    extract($this->data);
    if (eval("?>" . $this->filter($this->src)) === false) {
      throw new \LogicException("Syntax Error in source.");
    }
  }

  public function string() {
    try {
      ob_start();
      $this->render();
      return ob_get_clean();
    }
    catch (\Exception $e) {
      ob_end_clean();
      throw $e;
    }
  }

  public function filter($src) {
    foreach ($this->filters as $filter) {
      $src = call_user_func($filter, $src);
    }
    return $src;
  }

  public function addFilter($filter) {
    $this->filters[] = $filter;
  }

  public static function filterPrintf($src) {
    return preg_replace("/<[?]%(.*?)[?]>/s", "<?= sprintf($1) ?>", $src);
  }

  public static function filterEcho($src) {
    return preg_replace("/(<[?]=.*?[?]>)(\r\n|\r|\n)?/s", "$1$2$2", $src);
  }
}
