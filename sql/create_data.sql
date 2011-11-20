INSERT INTO event_lug (lugid,name,abk,url,crdate) VALUES (1,'Linux User Group Flensburg e.V.','LUGFL','http://www.lugfl.de',NOW());
INSERT INTO account (username,passwd,crdate) VALUES ('admin',MD5('oinkoink'),NOW());


INSERT INTO news_cat (catid,name) VALUES (1,'Webseite');
INSERT INTO news_cat (catid,name) VALUES (2,'Camp 2008');
INSERT INTO news_cat (catid,name) VALUES (3,'Programm');

