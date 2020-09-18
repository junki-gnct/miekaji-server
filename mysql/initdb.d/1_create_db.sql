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
    icon_b64 mediumtext,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS FriendTable (
    id bigint auto_increment unique,
    user_id bigint unique,
    friends mediumtext,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS FunTable (
    id bigint auto_increment unique,
    group_id int unique,
    owner_id bigint,
    fun_state int,
    screen_name text,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS GroupTable (
    id bigint auto_increment unique,
    group_id int,
    user_id bigint unique,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS JobCategoryTable (
    id bigint auto_increment unique,
    category_id bigint unique,
    screen_name text,
    job_weight float,
    detail text,
    isActive boolean,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);

CREATE TABLE IF NOT EXISTS JobTable (
    id bigint auto_increment unique,
    job_id bigint unique,
    category_id bigint,
    user_id bigint,
    motion float,
    m_time float,
    created_at datetime  default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    index(id)
);