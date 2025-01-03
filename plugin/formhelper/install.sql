DROP TABLE IF EXISTS `formhelper_form_field_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formhelper_form_field_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_submission_id` int(10) unsigned DEFAULT NULL COMMENT '表单提交ID',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '字段标签',
  `field_type` enum('text','textarea','select','checkbox','switch','file','date','number') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '字段类型',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '字段值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=723 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formhelper_form_fields`
--

DROP TABLE IF EXISTS `formhelper_form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formhelper_form_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '字段标签',
  `field_type` enum('text','textarea','select','checkbox','switch','file','date','number') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '字段类型',
  `options` text COLLATE utf8mb4_unicode_ci COMMENT '字段选项',
  `required` tinyint(1) DEFAULT '0' COMMENT '是否必填',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formhelper_form_submissions`
--

DROP TABLE IF EXISTS `formhelper_form_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formhelper_form_submissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '提交时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formhelper_forms`
--

DROP TABLE IF EXISTS `formhelper_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formhelper_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '表单标题',
  `user_id` int(10) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '表单描述',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `expired_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `limited` int(11) DEFAULT '0' COMMENT '限制人数',
  `single` tinyint(1) DEFAULT '0' COMMENT '是否限制单次',
  `disabled` tinyint(1) DEFAULT '0' COMMENT '是否禁用',
  `logged` tinyint(1) DEFAULT '0' COMMENT '是否需要登录',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;