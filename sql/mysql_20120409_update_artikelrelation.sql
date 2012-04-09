-- Add column for artikel to event relation
ALTER TABLE event_artikel ADD COLUMN eventid BIGINT UNSIGNED;

--
-- Remember to update old entrys in event_artikel with eventid
-- UPDATE event_artikel SET eventid=?
--

-- Add foreign key to event
ALTER TABLE `event_artikel` ADD CONSTRAINT `fk_event_artikel_event` FOREIGN KEY (`eventid`) REFERENCES `event_event` (`eventid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

