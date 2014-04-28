-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

--
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
  `avisota_message_categories` text NULL,
  `avisota_message_layout` char(36) NOT NULL default '',
  `avisota_message_cell` varchar(32) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
