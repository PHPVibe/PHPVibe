CREATE TABLE `#dbprefix#activity` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `object` varchar(200) DEFAULT NULL,
  `extra` mediumtext,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#dbprefix#ads` (
  `ad_id` bigint(20) UNSIGNED NOT NULL,
  `ad_spot` varchar(64) NOT NULL DEFAULT '',
  `ad_type` varchar(64) NOT NULL DEFAULT '0',
  `ad_content` longtext,
  `ad_title` varchar(64) DEFAULT NULL,
  `ad_pos` varchar(64) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#dbprefix#channels` (
  `cat_id` int(11) NOT NULL,
  `child_of` int(11) DEFAULT NULL,
  `picture` varchar(150) DEFAULT NULL,
  `cat_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `cat_desc` varchar(500) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `type` int(255) NOT NULL DEFAULT '1',
  `sub` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `#dbprefix#conversation` (
  `c_id` int(11) NOT NULL,
  `user_one` int(11) DEFAULT NULL,
  `user_two` int(11) DEFAULT NULL,
  `started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `closedby` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

CREATE TABLE `#dbprefix#con_msgs` (
  `msg_id` int(11) NOT NULL,
  `reply` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `by_user` int(11) DEFAULT NULL,
  `at_time` timestamp NULL DEFAULT NULL,
  `conv` int(11) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

CREATE TABLE `#dbprefix#crons` (
  `cron_id` bigint(20) UNSIGNED NOT NULL,
  `cron_type` varchar(500) DEFAULT NULL,
  `cron_name` varchar(64) NOT NULL DEFAULT '',
  `cron_period` mediumint(9) NOT NULL DEFAULT '86400',
  `cron_pages` int(11) NOT NULL DEFAULT '5',
  `cron_lastrun` timestamp NULL DEFAULT NULL,
  `cron_value` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#description` (
  `did` int(11) NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `vid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#em_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `object_id` varchar(64) DEFAULT NULL,
  `created` varchar(50) DEFAULT NULL,
  `sender_id` varchar(128) DEFAULT NULL,
  `comment_text` text,
  `reply` int(11) NOT NULL DEFAULT '0',
  `rating_cache` int(11) NOT NULL DEFAULT '0',
  `access_key` varchar(100) DEFAULT NULL,
  `visible` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#em_likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `comment_id` int(10) UNSIGNED DEFAULT NULL,
  `sender_ip` bigint(20) DEFAULT NULL,
  `vote` enum('1','-1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#hearts` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `vid` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `type` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#homepage` (
  `id` int(11) NOT NULL,
  `ord` int(11) DEFAULT NULL,
  `title` longtext,
  `type` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `ident` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `querystring` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `mtype` int(11) NOT NULL DEFAULT '1',
  `car` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#images` (
  `id` int(11) NOT NULL,
  `ispremium` int(11) NOT NULL DEFAULT '0',
  `media` int(11) NOT NULL DEFAULT '1',
  `token` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pub` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `date` text COLLATE utf8_swedish_ci,
  `featured` int(11) DEFAULT '0',
  `private` int(11) NOT NULL DEFAULT '0',
  `source` longtext COLLATE utf8_swedish_ci,
  `title` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_swedish_ci,
  `tags` varchar(500) COLLATE utf8_swedish_ci DEFAULT NULL,
  `category` varchar(250) COLLATE utf8_swedish_ci DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `liked` int(11) DEFAULT '0',
  `disliked` int(11) DEFAULT '0',
  `nsfw` int(11) DEFAULT '0',
  `privacy` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#jads` (
  `jad_id` int(20) UNSIGNED NOT NULL,
  `jad_type` varchar(64) NOT NULL DEFAULT '0',
  `jad_box` varchar(64) NOT NULL DEFAULT '0',
  `jad_start` varchar(64) NOT NULL DEFAULT '0',
  `jad_end` varchar(64) NOT NULL DEFAULT '0',
  `jad_body` longtext,
  `jad_title` varchar(64) DEFAULT NULL,
  `jad_pos` varchar(64) DEFAULT NULL,
  `jad_extra` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#langs` (
  `lang_id` bigint(20) UNSIGNED NOT NULL,
  `term` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#languages` (
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `lang_name` varchar(204) NOT NULL DEFAULT '',
  `lang_code` varchar(64) NOT NULL DEFAULT '',
  `lang_terms` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#likes` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `vid` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `type` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#noty` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `note` mediumtext,
  `read` int(10) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#pages` (
  `pid` int(11) NOT NULL,
  `menu` int(11) NOT NULL DEFAULT '0',
  `m_order` int(11) NOT NULL DEFAULT '1',
  `date` text COLLATE utf8_swedish_ci,
  `title` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pic` longtext COLLATE utf8_swedish_ci,
  `content` longtext COLLATE utf8_swedish_ci,
  `tags` varchar(500) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#playlists` (
  `id` int(11) NOT NULL,
  `ptype` int(11) NOT NULL DEFAULT '1',
  `owner` int(11) DEFAULT NULL,
  `picture` varchar(150) DEFAULT NULL,
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `views` mediumint(9) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#playlist_data` (
  `id` int(11) NOT NULL,
  `playlist` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#postcats` (
  `cat_id` int(11) NOT NULL,
  `picture` varchar(150) DEFAULT NULL,
  `cat_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `cat_desc` varchar(500) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#posts` (
  `pid` int(11) NOT NULL,
  `ch` int(11) NOT NULL DEFAULT '1',
  `date` text COLLATE utf8_swedish_ci,
  `title` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pic` longtext COLLATE utf8_swedish_ci,
  `content` longtext COLLATE utf8_swedish_ci,
  `tags` varchar(500) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#reports` (
  `r_id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `vid` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `reason` longtext CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `motive` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#tags` (
  `tagid` int(11) NOT NULL,
  `tag` bigint(20) DEFAULT NULL,
  `tcount` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#tag_names` (
  `id` int(11) NOT NULL,
  `tag_name` longtext CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `totals` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#tag_rel` (
  `tid` int(11) NOT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#users` (
  `id` int(16) NOT NULL,
  `email` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pass` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `password` mediumtext COLLATE utf8_swedish_ci,
  `lastlogin` timestamp NULL DEFAULT NULL,
  `group_id` varchar(255) COLLATE utf8_swedish_ci NOT NULL DEFAULT '4',
  `avatar` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `cover` mediumtext COLLATE utf8_swedish_ci,
  `date_registered` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `gid` mediumtext COLLATE utf8_swedish_ci,
  `fid` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `oauth_token` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `local` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `bio` longtext COLLATE utf8_swedish_ci,
  `hideactivity` int(11) NOT NULL DEFAULT '0',
  `views` mediumint(9) NOT NULL DEFAULT '0',
  `fblink` text COLLATE utf8_swedish_ci,
  `twlink` text COLLATE utf8_swedish_ci,
  `glink` text COLLATE utf8_swedish_ci,
  `iglink` text COLLATE utf8_swedish_ci,
  `gender` int(11) DEFAULT NULL,
  `lastNoty` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#users_friends` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `fid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `#dbprefix#users_groups` (
  `id` int(16) NOT NULL,
  `name` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ispremium` tinyint(1) DEFAULT NULL,
  `default_value` tinyint(1) DEFAULT NULL,
  `access_level` bigint(32) UNSIGNED DEFAULT NULL,
  `group_creative` text COLLATE utf8_swedish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#user_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `payment_method` enum('paypal') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'paypal',
  `validity` int(5) NOT NULL COMMENT 'in month(s)',
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `item_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `txn_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_gross` float(10,2) NOT NULL,
  `currency_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `subscr_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payer_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `#dbprefix#videos` (
  `id` int(11) NOT NULL,
  `ispremium` int(11) NOT NULL DEFAULT '0',
  `media` int(11) NOT NULL DEFAULT '1',
  `token` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `pub` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `date` text COLLATE utf8_swedish_ci,
  `featured` int(11) DEFAULT '0',
  `stayprivate` int(11) NOT NULL DEFAULT '0',
  `source` longtext COLLATE utf8_swedish_ci,
  `tmp_source` mediumtext COLLATE utf8_swedish_ci,
  `title` varchar(300) COLLATE utf8_swedish_ci DEFAULT NULL,
  `thumb` longtext COLLATE utf8_swedish_ci,
  `duration` int(11) DEFAULT '0',
  `category` varchar(250) COLLATE utf8_swedish_ci DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `liked` int(11) DEFAULT '0',
  `disliked` int(11) DEFAULT '0',
  `nsfw` int(11) DEFAULT '0',
  `embed` longtext COLLATE utf8_swedish_ci,
  `remote` longtext COLLATE utf8_swedish_ci,
  `srt` mediumtext COLLATE utf8_swedish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `#dbprefix#videos_tmp` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(500) DEFAULT NULL,
  `path` mediumtext CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `ext` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#dbprefix#activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#ads`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `ad_type_idx` (`ad_type`),
  ADD KEY `ad_spot_idx` (`ad_spot`);

ALTER TABLE `#dbprefix#channels`
  ADD PRIMARY KEY (`cat_id`);

ALTER TABLE `#dbprefix#conversation`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `user_one` (`user_one`),
  ADD KEY `user_two` (`user_two`);

ALTER TABLE `#dbprefix#con_msgs`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `by_user` (`by_user`),
  ADD KEY `conv` (`conv`);

ALTER TABLE `#dbprefix#crons`
  ADD PRIMARY KEY (`cron_id`),
  ADD KEY `cron_type_idx` (`cron_type`(333));

ALTER TABLE `#dbprefix#description`
  ADD PRIMARY KEY (`did`);

ALTER TABLE `#dbprefix#em_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_id` (`object_id`);

ALTER TABLE `#dbprefix#em_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `sender_ip` (`sender_ip`);

ALTER TABLE `#dbprefix#hearts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_uni` (`uid`,`vid`);

ALTER TABLE `#dbprefix#homepage`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iTitleSearch` (`title`),
  ADD KEY `iviews_idx` (`views`),
  ADD KEY `idates_idx` (`date`(50)),
  ADD KEY `ipub_idx` (`pub`);
ALTER TABLE `#dbprefix#images` ADD FULLTEXT KEY `iSearchText` (`title`,`description`,`tags`);
ALTER TABLE `#dbprefix#images` ADD FULLTEXT KEY `iSearchTitleText` (`title`);

ALTER TABLE `#dbprefix#jads`
  ADD PRIMARY KEY (`jad_id`);

ALTER TABLE `#dbprefix#langs`
  ADD PRIMARY KEY (`lang_id`);

ALTER TABLE `#dbprefix#languages`
  ADD PRIMARY KEY (`term_id`),
  ADD UNIQUE KEY `lang_code` (`lang_code`);

ALTER TABLE `#dbprefix#likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid_uni` (`uid`,`vid`);

ALTER TABLE `#dbprefix#noty`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD UNIQUE KEY `option_name_uni` (`option_name`);

ALTER TABLE `#dbprefix#pages`
  ADD PRIMARY KEY (`pid`);

ALTER TABLE `#dbprefix#playlists`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#playlist_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_idx` (`playlist`);

ALTER TABLE `#dbprefix#postcats`
  ADD PRIMARY KEY (`cat_id`);

ALTER TABLE `#dbprefix#posts`
  ADD PRIMARY KEY (`pid`);

ALTER TABLE `#dbprefix#reports`
  ADD PRIMARY KEY (`r_id`);

ALTER TABLE `#dbprefix#tags`
  ADD PRIMARY KEY (`tagid`),
  ADD KEY `tag` (`tag`);

ALTER TABLE `#dbprefix#tag_names`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#tag_rel`
  ADD PRIMARY KEY (`tid`);

ALTER TABLE `#dbprefix#users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#users_friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_idx` (`uid`),
  ADD KEY `fid_idx` (`fid`);

ALTER TABLE `#dbprefix#users_groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#user_subscriptions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `TitleSearch` (`title`),
  ADD KEY `views_idx` (`views`),
  ADD KEY `dates_idx` (`date`(50)),
  ADD KEY `pub_idx` (`pub`),
  ADD KEY `source_idx` (`source`(300)),
  ADD KEY `tmp_source_idx` (`tmp_source`(300));
ALTER TABLE `#dbprefix#videos` ADD FULLTEXT KEY `SearchText` (`title`);
ALTER TABLE `#dbprefix#videos` ADD FULLTEXT KEY `SearchTitleText` (`title`);

ALTER TABLE `#dbprefix#videos_tmp`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#dbprefix#activity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#ads`
  MODIFY `ad_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#channels`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#conversation`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#con_msgs`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#crons`
  MODIFY `cron_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#description`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#em_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#em_likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#hearts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#jads`
  MODIFY `jad_id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#langs`
  MODIFY `lang_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#languages`
  MODIFY `term_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#noty`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#pages`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#playlist_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#postcats`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#posts`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#reports`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#tags`
  MODIFY `tagid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#tag_names`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#tag_rel`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#users`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#users_friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#users_groups`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#user_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `#dbprefix#videos_tmp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
