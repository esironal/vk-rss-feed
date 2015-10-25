# VK.com RSS feed

RSS feed generator for walls users, groups or public pages from http://vk.com.

## Install

* Download FeedWriter from [mibe/FeedWriter](https://github.com/mibe/FeedWriter) repo and 
* Place all files to your server, ex. ``http://your-site.com/classes/FeedWriter/``;
* Download [vk-rss-feed](https://github.com/enjoyiacm/vk-rss-feed/archive/master.zip) PHP file and place to server, ex. ``http://your-site.com/vk-rss-feed.php``.

## Usage

Send response to ``vk-rss-feed.php`` like that:

```code
GET http://your-site.com/vk-rss-feed.php?domain={id}&count={count}
```

* ``{id}`` — it's user/group/public ID (or short name from URL); 
* ``{count}`` — it's number of view count.
