CREATE DATABASE IF NOT EXISTS miekaji;
GRANT ALL ON miekaji.* TO miekaji;

USE miekaji;
CREATE TABLE IF NOT EXISTS AuthTable (
    id bigint auto_increment unique, 
    user_id text, 
    pass_hash text, 
    token text, 
    expires bigint, 
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS ProfileTable (
    id bigint auto_increment unique,
    user_id text,
    screen_name text,
    unique_id bigint unique,
    icon_id text,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS IconTable (
    id bigint auto_increment unique,
    icon_id text,
    icon_b64 text,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);