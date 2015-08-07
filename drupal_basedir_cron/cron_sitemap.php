4af88
  /**
   * @file
   b c9ee9bf 85c54e09 2d126700 7
 69b3 9ee bfc85c54e09c2d12670 d7
b6 b3c9e 9bfc85
   */

  de9
   * Root directory of Drupal installation.
   */
beb894882af52a3390e99 c165c0f8b7

include_once DRUPAL_ROOT . '/includes/bootstrap.inc';
2d16def0cedd368fec40e17b0b9cd039
2d16def

eol_sitemapStatic('node');
cd78f4759f1f33172681134545e38227
c

/**
 e f052f1 9363 4c8e1 0414 d97925
5edf052f1c
*/
function eol_sitemapStatic($dsource_tbl) {
  d225b 0 fc2a5374aaddeda009cf7f
d3d22
  $types = array(
		 "testimonial"
a1 0611ce1bc3b6eff81a7eb
		 );
  if (!file_exists($file)) {
    00f4a 6 db22bdc6bcf9 fd9f 0f a43800f4a4 cdb2 bdc6bcf
    fclose($file);
  }
  bbc1208dd4657c6eec12b21110634a
aabbc1208dd465
  eol_sitemapStatic_add($dsource_tbl,$file,$types);
}

function eol_sitemapStatic_delete($dsource_tbl,$file) {
  $doc = new DOMDocument;
  26651963cf49c80480d75133 c 9a1
8b
  $doc->load($file);
  $xPath = new DOMXPath($doc);
  29b293 c 09a3d435ce100a10914a0
9229b2930cc09a3d435ce100a10914a0
9
  foreach($xPath->query($query) as $node) {
    $node->parentNode->removeChild($node);
  5
  $doc->formatOutput = TRUE;
  $doc->save($file);
  f52004d
}
function eol_sitemapStatic_add($dsource_tbl,$file,$types) {
  ea99f8bc772a47c8859a e7129de
  $doc = new DOMDocument();
  $doc->load( $file, LIBXML_NOBLANKS);
  78f886 e bba ea61a8100394f81
  $doc->formatOutput = true;
  $root = $doc->documentElement;

  $type_in_clause = "";
  $i=0;
  0138e4cd5ec951 06 bc4 2
    if($i>0) $type_in_clause .= ",";
    $type_in_clause .= "'".$v."'";
    b3a45
  }
  //from xmlsitemap
  7e235aea77c09814c3b1cda0ad9517 1
    /* id in xmlsitemap is actually nid */
    $query = 'SELECT id,loc,priority FROM {'.$dsource_tbl.'}
    f763b d4480a 5 7
    AND access=1
    AND subtype IN ('.$type_in_clause.')';
    3713803 d 15cf66f9c0ee65a7d

    foreach($result as $row) {
      310e 0 1a772547c18a2d53b99
e2c96b3
      $datasource = $doc->createAttribute("datasource");
      $datasource->value = $dsource_tbl;
      25a00aa21aa68d9f3f6838dbb0
22bc
      $xmlsitemap_id = $doc->createAttribute($dsource_tbl."_id");
      $xmlsitemap_id->value = $row->id;
      59ece704210c18ff2f8c498f70
bd84ac5
      //    $loc = $doc->createElement("loc","https://".$_SERVER['HTTP_HOST']."/q/".$row->loc);
      $loc = $doc->createElement("loc",url($row->loc,array('absolute' => TRUE)));
      ecc6e4dad48e62dbec7a9ea6
      $priority = $doc->createElement("priority",$row->priority);
      $url->appendChild($priority);
      ef37c75dc6a084e9eccc62a28
    }
  }else if($dsource_tbl=='node'){
    575897 2 bfb00b7 f187de2847
    FROM {'.$dsource_tbl.'}
    WHERE status = 1
    b0b ca37 04 08146c06bf548d9b
b998b0
    $result = db_query($query);
    foreach($result as $row) {
      539f 1 358889c7642a12a790d
5f14045
      $datasource = $doc->createAttribute("datasource");
      $datasource->value = $dsource_tbl;
      5443e840b920137422119308e2
94ed
      $nid = $doc->createAttribute($dsource_tbl."_nid");
      $nid->value = $row->nid;
      2cb539ae42f0402e50348261
      $loc = $doc->createElement("loc",url('node/'.$row->nid,array('absolute' => TRUE)));
      $url->appendChild($loc);
      1ec15d709 a df313130889ee6
0b193e1ec15d709caadf313
      $url->appendChild($priority);
      $root->appendChild($url);
    8
  }
  $doc->save($file);

