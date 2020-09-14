CREATE DATABASE IF NOT EXISTS miekaji;
GRANT ALL ON miekaji.* TO miekaji;

USE miekaji;
CREATE TABLE IF NOT EXISTS AuthTable (id bigint auto_increment unique, user_id text, pass_hash text, token text, expires bigint, index(id));