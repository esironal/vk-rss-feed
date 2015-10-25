<?php

// Set header
header('Content-type: text/plain');

// Include FeedWriter
include 'classes/FeedWriter/Item.php';
include 'classes/FeedWriter/Feed.php';
include 'classes/FeedWriter/RSS2.php';

// Set timezone
date_default_timezone_set('Europe/Moscow');

// Set FeedWriter namespace
use \classes\FeedWriter\RSS2;

// Creating an instance of RSS2 class
$feed = new RSS2;

// Setting the channel elements
// Use wrapper functions for common channel elements
$feed->setTitle('ВКонтакте');
$feed->setLink('http://vk.com/');
$feed->setDescription('Стена группы/паблика ВКонтакте в формате RSS.');

//Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
$feed->setImage('ВКонтакте', 'http://vk.com/', 'http://vk.com/images/safari_120.png');

//GET objects
$url = 'http://api.vk.com/method/wall.get?domain=' .$_GET['domain']. '&count=' .$_GET['count'];

//Decode JSON response  
$wall = json_decode(file_get_contents($url));

// Let's add some feed item
for ($i = 1; $i <= count($wall->response)-1; $i++) {
  
  $wall->response[$i]->text = preg_replace("#&mdash;#", '', $wall->response[$i]->text);
  $wall->response[$i]->text = html_entity_decode($wall->response[$i]->text, null, 'utf-8');

  $item = $feed->createNewItem();

  $title = explode('<br>', $wall->response[$i]->text);
  $title = $title[0];
  $title = (mb_strlen($title, 'utf-8') <= 100) ? $title : mb_substr($title, 0, 100, 'utf-8') . '...';

  $item->setTitle($title);
  $item->setLink('http://vk.com/' . $_GET['domain'] . '?w=wall' . $wall->response[$i]->from_id . '_' . $wall->response[$i]->id);
  $item->setDate($wall->response[$i]->date);

  $description = $wall->response[$i]->text;
  
  // Check if empty attachments in response
  if (isset($wall->response[$i]->attachments)) {
    foreach ($wall->response[$i]->attachments as $attachment) {          
      switch ($attachment->type) {
        case 'photo': {
          $description .= "<br><img src='{$attachment->photo->src_big}'/>";
          break;
        }
        case 'audio': {
          $description .= "<br><a href='http://vk.com/wall{$owner_id}_{$wall->response[$i]->id}'>{$attachment->audio->performer} &ndash; {$attachment->audio->title}</a>";
          break;    
        }
        case 'doc': {
          $description .= "<br><a href='{$attachment->doc->url}'>{$attachment->doc->title}</a>";
          break;
        }
        case 'link': {
          $description .= "<br><a href='{$attachment->link->url}'>{$attachment->link->title}</a>";
          break;
        }
        case 'video': {
          $description .= "<br><a href='http://vk.com/video{$attachment->video->owner_id}_{$attachment->video->vid}'><img src='{$attachment->video->image_big}'/></a>";
          break;
        }
      }
    }        
  }
    
  $item->setDescription($description);
  $item->addElement('guid', $wall->response[$i]->id);
  $feed->addItem($item);
}

// Now generate the feed
$feed->printFeed();
