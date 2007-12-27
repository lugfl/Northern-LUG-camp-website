<?php

// Default DB
$DB_user = '01_lc2008_dev';
$DB_pass = 'oinkoink';
$DB_name = '01_lc2008_dev';
$DB_host = "localhost";


$DB['DEFAULT']['user'] = $DB_user;
$DB['DEFAULT']['pass'] = $DB_pass;
$DB['DEFAULT']['name'] = $DB_name;
$DB['DEFAULT']['host'] = $DB_host;

$DB_SCHEMA['news_cat']['name'] = 'News Kategorie';
$DB_SCHEMA['news_cat']['cols']['catid']['name'] = '';
$DB_SCHEMA['news_cat']['cols']['name']['name'] = 'Kategorie';
$DB_SCHEMA['news_cat']['cols']['pic']['name'] = 'Bild';

$DB_SCHEMA['news_eintrag']['name'] = 'News';

$DB_SCHEMA['news_eintrag']['cols']['eintragid']['name'] = '';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['detailview']['name'] = 'Details';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['detailview']['p'] = 'news';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['edit']['name'] = 'Editieren';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['edit']['p'] = 'news';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['delete']['name'] = 'L&ouml;schen';
$DB_SCHEMA['news_eintrag']['cols']['eintragid']['cmd']['delete']['p'] = 'news';

$DB_SCHEMA['news_eintrag']['cols']['title']['name'] = 'Titel';
$DB_SCHEMA['news_eintrag']['cols']['short']['name'] = 'Teaser';
$DB_SCHEMA['news_eintrag']['cols']['txt']['name'] = 'Newstext';
$DB_SCHEMA['news_eintrag']['cols']['crdate']['name'] = 'Erstellt am';
$DB_SCHEMA['news_eintrag']['cols']['author']['name'] = 'Autor';
?>
