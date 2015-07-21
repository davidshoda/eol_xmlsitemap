<?php
  /**
   * @file
   * Handles incoming requests to fire off regularly-scheduled tasks (cron jobs).
   */

  /**
   * Root directory of Drupal installation.
   */
define('DRUPAL_ROOT', getcwd());

include_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

eol_sitemapStatic('node');
//eol_sitemapStatic('xmlsitemap');

/**
 * Merges from table into sitemapStatic.xml
*/
function eol_sitemapStatic($dsource_tbl) {
  $file = "../html/sitemapStatic.xml";
  $types = array(
		 "testimonial"
		 /*,"press_release",*/
		 );
  if (!file_exists($file)) {
    $file = fopen($file, 'w') or die("can't open file");
    fclose($file);
  }
  eol_sitemapStatic_delete($dsource_tbl,$file);
  eol_sitemapStatic_add($dsource_tbl,$file,$types);
}

function eol_sitemapStatic_delete($dsource_tbl,$file) {
  $doc = new DOMDocument;
  $doc->preserveWhiteSpace = FALSE;
  $doc->load($file);
  $xPath = new DOMXPath($doc);
  $query = sprintf('//urlset/url[@datasource="'.$dsource_tbl.'"]');
  foreach($xPath->query($query) as $node) {
    $node->parentNode->removeChild($node);
  }
  $doc->formatOutput = TRUE;
  $doc->save($file);
  return;
}
function eol_sitemapStatic_add($dsource_tbl,$file,$types) {
  if(sizeof($types)<1) return;
  $doc = new DOMDocument();
  $doc->load( $file, LIBXML_NOBLANKS);
  $xpath = new DOMXPath($doc);
  $doc->formatOutput = true;
  $root = $doc->documentElement;

  $type_in_clause = "";
  $i=0;
  foreach($types AS $v) {
    if($i>0) $type_in_clause .= ",";
    $type_in_clause .= "'".$v."'";
    $i++;
  }  
  //from xmlsitemap
  if($dsource_tbl=='xmlsitemap') {
    /* id in xmlsitemap is actually nid */
    $query = 'SELECT id,loc,priority FROM {'.$dsource_tbl.'}  
    WHERE status = 1 
    AND access=1 
    AND subtype IN ('.$type_in_clause.')';
    $result = db_query($query);

    foreach($result as $row) {
      $url = $doc->createElement("url");
      $datasource = $doc->createAttribute("datasource");
      $datasource->value = $dsource_tbl;
      $url->appendChild($datasource);
      $xmlsitemap_id = $doc->createAttribute($dsource_tbl."_id");
      $xmlsitemap_id->value = $row->id;
      $url->appendChild($xmlsitemap_id);
      //    $loc = $doc->createElement("loc","https://".$_SERVER['HTTP_HOST']."/q/".$row->loc);
      $loc = $doc->createElement("loc",url($row->loc,array('absolute' => TRUE)));
      $url->appendChild($loc);
      $priority = $doc->createElement("priority",$row->priority);
      $url->appendChild($priority);
      $root->appendChild($url);
    }
  }else if($dsource_tbl=='node'){
    $query = 'SELECT nid,status 
    FROM {'.$dsource_tbl.'} 
    WHERE status = 1 
    AND type IN ('.$type_in_clause.')';
    $result = db_query($query);
    foreach($result as $row) {
      $url = $doc->createElement("url");
      $datasource = $doc->createAttribute("datasource");
      $datasource->value = $dsource_tbl;
      $url->appendChild($datasource);
      $nid = $doc->createAttribute($dsource_tbl."_nid");
      $nid->value = $row->nid;
      $url->appendChild($nid);
      $loc = $doc->createElement("loc",url('node/'.$row->nid,array('absolute' => TRUE)));
      $url->appendChild($loc);
      $priority = $doc->createElement("priority","0.5");
      $url->appendChild($priority);
      $root->appendChild($url);
    }
  }
  $doc->save($file);  
}
