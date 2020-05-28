--
-- This is a sample schema for storing NBA players, teams, games and stats
--
-- Please note: there are obious deficiencies in the way this is done.
-- For example, currently players are assigned to a single team, which
-- is correct for a given point in time but does not allow us to model
-- the fact the players may change teams, etc.
--
-- Similarly, if a player were to become heavier (or taller), or even change
-- his name (not sure if this ever happened, but for example, Cassius Clay (boxer)
-- changed his name to Muhammed Ali), there is no way to track this.
--
-- Similarly, referees are entered into the games table by name,
-- a better solution would be to create a referee table and then
-- map this into the game table via a referee_id. The same goes for
-- cities and maybe even team owners (are there owners who own multiple
-- teams??).
--
-- In summary: there a are number of deficiencies with the way this
-- schema is currently designed, but I'm hoping that what we have
-- here is enough to demonstrate that I understand how to build a
-- schema and how relational databases work.
--

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `nba_teams`;
CREATE TABLE `nba_teams` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `city` varchar(32) NOT NULL DEFAULT '',
  `owner` varchar(64) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_teams__name` (`name`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `nba_players`;
CREATE TABLE `nba_players` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int unsigned NOT NULL,
  `name_first` varchar(32) NOT NULL DEFAULT '',
  `name_middle` varchar(32) NULL DEFAULT NULL,
  `name_last` varchar(32) NOT NULL DEFAULT '',
  `birthdate` date NULL DEFAULT NULL,
  `height` smallint NULL DEFAULT NULL,
  `weight` smallint NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_players__team` (`team_id`),
  KEY `idx_players__player` (`name_last`, `name_first`),
  CONSTRAINT `fk_players__team` FOREIGN KEY (`team_id`) REFERENCES `nba_teams` (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `nba_games`;
CREATE TABLE `nba_games` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `team1_id` int unsigned NOT NULL,
  `team2_id` int unsigned NOT NULL,
  `city` varchar(32) NOT NULL DEFAULT '',
  `referee` varchar(32) NOT NULL DEFAULT '',
  `score_q1` varchar(7) NOT NULL DEFAULT '',
  `score_q2` varchar(7) NOT NULL DEFAULT '',
  `score_q3` varchar(7) NOT NULL DEFAULT '',
  `score_q4` varchar(7) NOT NULL DEFAULT '',
  `score_ot1` varchar(7) NOT NULL DEFAULT '',
  `score_ot2` varchar(7) NOT NULL DEFAULT '',
  `score_final` varchar(7) NOT NULL DEFAULT '',
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx__games_date` (`date`, `city`),
  KEY `idx__games_date_team1` (`team1_id`, `date`),
  KEY `idx__games_date_team2` (`team2_id`, `date`),
  CONSTRAINT `fk_games__team1` FOREIGN KEY (`team1_id`) REFERENCES `nba_teams` (`id`),
  CONSTRAINT `fk_games__team2` FOREIGN KEY (`team2_id`) REFERENCES `nba_teams` (`id`)
) ENGINE=InnoDB;


--
-- If game_id is NULL, then we can assume that the record is for an entire season
-- If game_id is NULL AND season is NULL, then we can assume that the record is for the player's entire career
-- Not optimal, but will have to do for now
DROP TABLE IF EXISTS `nba_stats`;
CREATE TABLE `nba_stats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int unsigned NOT NULL DEFAULT 0,
  `game_id` int unsigned NULL DEFAULT NULL,
  `season` mediumint unsigned NULL DEFAULT NULL,
  `points` float unsigned NOT NULL DEFAULT 0.0,
  `rebounds` float unsigned NOT NULL DEFAULT 0.0,
  `assists` float unsigned NOT NULL DEFAULT 0.0,
  `steals` float unsigned NOT NULL DEFAULT 0.0,
  `blocks` float unsigned NOT NULL DEFAULT 0.0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stats__playerid` (`player_id`, `game_id`),
  KEY `idx_stats__gameid` (`game_id`),
  CONSTRAINT `fk_stats__player` FOREIGN KEY (`player_id`) REFERENCES `nba_players` (`id`),
  CONSTRAINT `fk_stats__game` FOREIGN KEY (`game_id`) REFERENCES `nba_game` (`id`)
) ENGINE=InnoDB;

