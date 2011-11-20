
DELETE FROM news_eintrag;
INSERT INTO news_eintrag (title,catid,short,txt,crdate,author) VALUES ('Testnews 01',1,'Kurze Version','Und die lange Version',NOW(),'Frank Agerholm');
INSERT INTO news_eintrag (title,catid,short,txt,crdate,author) VALUES ('Testnews 02',1,'Kurze Version 2','Und die lange Version',NOW(),'Frank Agerholm');
INSERT INTO news_eintrag (title,catid,short,txt,crdate,author) VALUES ('Testnews 02',1,'Kurze Version 3','Und die lange Version',NOW(),'Frank Agerholm');
