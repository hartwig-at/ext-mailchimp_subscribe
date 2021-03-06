<?php
$EM_CONF[$_EXTKEY] = array(
  'title'            => 'MailChimp Subscribe',
  'description'      => 'Displays a subscription form for Mail Chimp in the FE.',
  'category'         => 'fe',
  'author'           => 'Oliver Salzburg',
  'author_email'     => 'oliver@hartwig-at.de',
  'author_company'   => 'Hartwig Communication & Events',
  'shy'              => '',
  'priority'         => '',
  'module'           => '',
  'state'            => 'beta',
  'internal'         => '',
  'uploadfolder'     => '0',
  'createDirs'       => '',
  'modify_tables'    => '',
  'clearCacheOnLoad' => 0,
  'lockType'         => '',
  'version'          => '0.2.3',
  'constraints'      => array(
    'depends'   => array(
      'typo3'   => '4.5.0',
      'extbase' => '1.3.0',
      'fluid'   => '1.3.0',
    ),
    'conflicts' => array(
    ),
    'suggests'  => array(
    ),
  ),
);
?>