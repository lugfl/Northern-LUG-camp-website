--
-- 2018-01-02, fager, Column to disable Account-Registrations for a domain
-- https://github.com/lugfl/Northern-LUG-camp-website/issues/17
-- 

ALTER TABLE content_domain ADD COLUMN accountregistration TINYINT DEFAULT 1 COMMENT '1=enabled, 0=disabled';

